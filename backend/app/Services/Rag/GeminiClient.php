<?php

namespace App\Services\Rag;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;

class GeminiClient
{
    private string $apiKey;
    private string $embedModel;
    private string $chatModel;
    private int $embedDim;
    private int $maxOutputTokens;
    private float $temperature;

    public function __construct()
    {
        $this->apiKey = (string) config('rag.gemini.api_key');
        $this->embedModel = (string) config('rag.gemini.embed_model', 'gemini-embedding-001');
        $this->chatModel = (string) config('rag.gemini.chat_model', 'gemini-3-flash-preview');
        $this->embedDim = (int) config('rag.gemini.embed_dim', 768);
        $this->maxOutputTokens = (int) config('rag.gemini.max_output_tokens', 256);
        $this->temperature = (float) config('rag.gemini.temperature', 0.2);
    }

    private function assertConfigured(): void
    {
        if ($this->apiKey === '') {
            throw new \RuntimeException('Missing GEMINI_API_KEY in environment.');
        }
    }

    private function ensureUtf8(string $text): string
    {
        if (mb_check_encoding($text, 'UTF-8')) {
            return $text;
        }

        $fixed = @iconv('UTF-8', 'UTF-8//IGNORE', $text);
        if (is_string($fixed) && $fixed !== '' && mb_check_encoding($fixed, 'UTF-8')) {
            return $fixed;
        }

        return mb_convert_encoding($text, 'UTF-8');
    }

    /**
     * @return array<int, array<int, float>>
     */
    public function embedTexts(array $texts, string $taskType): array
    {
        $this->assertConfigured();

        $texts = array_values(array_filter(array_map(function ($t) {
            if (!is_string($t)) {
                return '';
            }
            return trim($this->ensureUtf8($t));
        }, $texts)));
        if ($texts === []) {
            return [];
        }

        // Batch endpoint
        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:batchEmbedContents?key=%s',
            rawurlencode($this->embedModel),
            rawurlencode($this->apiKey)
        );

        $requests = array_map(function (string $text) use ($taskType) {
            return [
                'model' => 'models/'.$this->embedModel,
                'content' => [
                    'parts' => [
                        ['text' => $text],
                    ],
                ],
                'taskType' => $taskType,
                'outputDimensionality' => $this->embedDim,
            ];
        }, $texts);

        $resp = Http::timeout(45)->acceptJson()->post($url, [
            'requests' => $requests,
        ]);

        try {
            $resp->throw();
        } catch (RequestException $e) {
            $body = $resp->json();
            throw new \RuntimeException('Gemini embedding request failed: '.json_encode($body, JSON_UNESCAPED_SLASHES), previous: $e);
        }

        $embeddings = $resp->json('embeddings');
        if (!is_array($embeddings)) {
            throw new \RuntimeException('Unexpected Gemini embedding response.');
        }

        $vectors = [];
        foreach ($embeddings as $emb) {
            $values = $emb['values'] ?? null;
            if (!is_array($values)) {
                continue;
            }

            $vector = array_map('floatval', $values);
            if ($this->embedDim !== 3072) {
                $vector = self::l2Normalize($vector);
            }
            $vectors[] = $vector;
        }

        return $vectors;
    }

    public function generate(string $systemInstruction, string $userText): string
    {
        $this->assertConfigured();

        $systemInstruction = $this->ensureUtf8($systemInstruction);
        $userText = $this->ensureUtf8($userText);

        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            rawurlencode($this->chatModel),
            rawurlencode($this->apiKey)
        );

        $resp = Http::timeout(60)->acceptJson()->post($url, [
            'generationConfig' => [
                'maxOutputTokens' => max(64, $this->maxOutputTokens),
                'temperature' => max(0.0, min(1.0, $this->temperature)),
            ],
            'systemInstruction' => [
                'parts' => [
                    ['text' => $systemInstruction],
                ],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $userText],
                    ],
                ],
            ],
        ]);

        try {
            $resp->throw();
        } catch (RequestException $e) {
            $body = $resp->json();
            throw new \RuntimeException('Gemini generate request failed: '.json_encode($body, JSON_UNESCAPED_SLASHES), previous: $e);
        }

        $candidates = $resp->json('candidates');
        if (!is_array($candidates) || ($candidates[0]['content']['parts'][0]['text'] ?? null) === null) {
            throw new \RuntimeException('Unexpected Gemini generate response.');
        }

        return (string) $candidates[0]['content']['parts'][0]['text'];
    }

    /**
     * Streams model output and returns the final concatenated text.
     *
     * @param callable(string):void $onDelta
     */
    public function streamGenerate(string $systemInstruction, string $userText, callable $onDelta): string
    {
        $this->assertConfigured();

        $systemInstruction = $this->ensureUtf8($systemInstruction);
        $userText = $this->ensureUtf8($userText);

        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:streamGenerateContent?key=%s',
            rawurlencode($this->chatModel),
            rawurlencode($this->apiKey)
        );

        $client = new GuzzleClient([
            'timeout' => 0,
            'connect_timeout' => 30,
        ]);

        $resp = $client->request('POST', $url, [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'generationConfig' => [
                    'maxOutputTokens' => max(64, $this->maxOutputTokens),
                    'temperature' => max(0.0, min(1.0, $this->temperature)),
                ],
                'systemInstruction' => [
                    'parts' => [
                        ['text' => $systemInstruction],
                    ],
                ],
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $userText],
                        ],
                    ],
                ],
            ],
            'stream' => true,
        ]);

        $body = $resp->getBody();
        $final = '';

        $depth = 0;
        $inString = false;
        $escape = false;
        $obj = '';

        $handleObject = function (string $jsonText) use (&$final, $onDelta): void {
            $json = json_decode($jsonText, true);
            if (!is_array($json)) {
                return;
            }

            if (isset($json['error'])) {
                throw new \RuntimeException('Gemini stream request failed: '.json_encode($json['error'], JSON_UNESCAPED_SLASHES));
            }

            $candidates = $json['candidates'] ?? null;
            if (!is_array($candidates) || $candidates === []) {
                return;
            }

            $parts = $candidates[0]['content']['parts'] ?? null;
            if (!is_array($parts) || $parts === []) {
                return;
            }

            $text = '';
            foreach ($parts as $part) {
                if (is_array($part) && is_string($part['text'] ?? null)) {
                    $text .= (string) $part['text'];
                }
            }

            if ($text === '') {
                return;
            }

            // Handle both delta-style and full-text-so-far style streams.
            if ($final !== '' && str_starts_with($text, $final)) {
                $delta = mb_substr($text, mb_strlen($final, 'UTF-8'), null, 'UTF-8');
            } else {
                $delta = $text;
            }

            if ($delta !== '') {
                $delta = $this->ensureUtf8($delta);
                $final .= $delta;
                $onDelta($delta);
            }
        };

        while (!$body->eof()) {
            $chunk = $body->read(8192);
            if ($chunk === '') {
                usleep(10_000);
                continue;
            }

            $len = strlen($chunk);
            for ($i = 0; $i < $len; $i++) {
                $ch = $chunk[$i];

                if ($depth === 0) {
                    if ($ch === '{') {
                        $depth = 1;
                        $inString = false;
                        $escape = false;
                        $obj = '{';
                    }
                    continue;
                }

                $obj .= $ch;

                if ($inString) {
                    if ($escape) {
                        $escape = false;
                        continue;
                    }

                    if ($ch === '\\') {
                        $escape = true;
                        continue;
                    }

                    if ($ch === '"') {
                        $inString = false;
                    }

                    continue;
                }

                if ($ch === '"') {
                    $inString = true;
                    continue;
                }

                if ($ch === '{') {
                    $depth++;
                    continue;
                }

                if ($ch === '}') {
                    $depth--;
                    if ($depth === 0) {
                        $handleObject($obj);
                        $obj = '';
                    }
                    continue;
                }
            }
        }

        return trim($final);
    }

    /**
     * @param array<int, float> $vector
     * @return array<int, float>
     */
    public static function l2Normalize(array $vector): array
    {
        $sumSq = 0.0;
        foreach ($vector as $v) {
            $sumSq += ((float) $v) * ((float) $v);
        }
        $norm = sqrt($sumSq);
        if ($norm <= 0.0) {
            return $vector;
        }

        return array_map(static fn ($v) => (float) $v / $norm, $vector);
    }
}
