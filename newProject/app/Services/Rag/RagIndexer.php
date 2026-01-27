<?php

namespace App\Services\Rag;

use Ramsey\Uuid\Uuid;

class RagIndexer
{
    public function __construct(
        private readonly GeminiClient $gemini,
        private readonly QdrantClient $qdrant
    ) {}

    /**
     * @param array<int, array{title:string, url:string, text:string, role?:string, source_type?:string, payload?:array<string,mixed>}> $documents
     */
    public function index(array $documents, bool $reset = false): int
    {
        $collection = (string) config('rag.qdrant.collection', 'site_knowledge');
        $dim = (int) config('rag.gemini.embed_dim', 768);

        if ($reset) {
            $this->qdrant->recreateCollection($collection, $dim);
        } else {
            $this->qdrant->createCollectionIfMissing($collection, $dim);
        }

        $pointCount = 0;
        foreach ($documents as $doc) {
            $title = (string) ($doc['title'] ?? '');
            $url = (string) ($doc['url'] ?? '');
            $text = (string) ($doc['text'] ?? '');
            $role = (string) ($doc['role'] ?? 'any');
            $sourceType = (string) ($doc['source_type'] ?? 'doc');
            $extraPayload = $doc['payload'] ?? [];
            $extraPayload = is_array($extraPayload) ? $extraPayload : [];

            if ($title === '' || $url === '' || trim($text) === '') {
                continue;
            }

            $chunks = self::chunkText($text);
            $vectors = $this->gemini->embedTexts($chunks, 'RETRIEVAL_DOCUMENT');

            $points = [];
            foreach ($chunks as $i => $chunk) {
                $vec = $vectors[$i] ?? null;
                if (!is_array($vec)) {
                    continue;
                }

                $points[] = [
                    'id' => self::stableUuid($url.'|'.$role.'|'.$i),
                    'vector' => $vec,
                    'payload' => [
                        'title' => $title,
                        'url' => $url,
                        'chunk_index' => $i,
                        'text' => $chunk,
                        'role' => $role,
                        'source_type' => $sourceType,
                        ...$extraPayload,
                    ],
                ];
            }

            $this->qdrant->upsert($collection, $points);
            $pointCount += count($points);
        }

        return $pointCount;
    }

    private static function stableUuid(string $name): string
    {
        return Uuid::uuid5(Uuid::NAMESPACE_URL, $name)->toString();
    }

    /**
     * @return array<int, string>
     */
    public static function chunkText(string $text, int $maxChars = 1800, int $overlap = 250): array
    {
        $text = self::ensureUtf8($text);
        $text = preg_replace('/\\s+/', ' ', trim($text)) ?? '';
        if ($text === '') {
            return [];
        }

        $chunks = [];
        $i = 0;
        $len = mb_strlen($text, 'UTF-8');
        $step = max(1, $maxChars - $overlap);

        while ($i < $len) {
            $chunk = mb_substr($text, $i, $maxChars, 'UTF-8');
            if ($chunk === '') {
                break;
            }
            $chunks[] = $chunk;
            $i += $step;
        }

        return $chunks;
    }

    private static function ensureUtf8(string $text): string
    {
        if (mb_check_encoding($text, 'UTF-8')) {
            return $text;
        }

        $fixed = @iconv('UTF-8', 'UTF-8//IGNORE', $text);
        if (is_string($fixed) && $fixed !== '' && mb_check_encoding($fixed, 'UTF-8')) {
            return $fixed;
        }

        // Fallback: best-effort re-encode as UTF-8.
        return mb_convert_encoding($text, 'UTF-8');
    }
}
