<?php

namespace App\Services\Rag;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class QdrantClient
{
    private string $url;

    public function __construct()
    {
        $this->url = rtrim((string) config('rag.qdrant.url', 'http://localhost:6333'), '/');
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function health(): bool
    {
        $resp = Http::timeout(5)->get($this->url.'/healthz');
        return $resp->successful();
    }

    public function recreateCollection(string $collection, int $dim): void
    {
        Http::timeout(10)->delete($this->url.'/collections/'.rawurlencode($collection));
        $this->createCollectionIfMissing($collection, $dim);
    }

    public function createCollectionIfMissing(string $collection, int $dim): void
    {
        $get = Http::timeout(10)->get($this->url.'/collections/'.rawurlencode($collection));
        if ($get->successful()) {
            return;
        }

        $resp = Http::timeout(20)->put($this->url.'/collections/'.rawurlencode($collection), [
            'vectors' => [
                'size' => $dim,
                'distance' => 'Cosine',
            ],
        ]);

        if (!$resp->successful()) {
            $status = $resp->status();
            $body = $resp->body();
            throw new \RuntimeException("Qdrant create collection failed (HTTP {$status}): {$body}");
        }

        // Verify collection was created
        $verify = Http::timeout(10)->get($this->url.'/collections/'.rawurlencode($collection));
        if (!$verify->successful()) {
            throw new \RuntimeException("Collection '{$collection}' creation reported success but collection not found. Qdrant may need more time to initialize.");
        }
    }

    /**
     * @param array<int, array{id:string, vector:array<int,float>, payload:array<string,mixed>}> $points
     */
    public function upsert(string $collection, array $points): void
    {
        if ($points === []) {
            return;
        }

        $url = $this->url.'/collections/'.rawurlencode($collection).'/points?wait=true';
        \Log::info("Qdrant upsert URL: {$url}");
        \Log::info("First point structure: " . json_encode($points[0] ?? []));
        
        $resp = Http::timeout(60)->put($url, [
            'points' => $points,
        ]);

        if (!$resp->successful()) {
            $status = $resp->status();
            $body = $resp->body();
            $errorMsg = "Qdrant upsert failed (HTTP {$status})";
            
            if (!empty($body)) {
                $errorMsg .= ": {$body}";
            } else {
                $errorMsg .= ". Empty response body. Check if Qdrant is properly initialized. URL was: {$url}";
            }
            
            throw new \RuntimeException($errorMsg);
        }
    }

    /**
     * @param array<string,mixed>|null $filter
     * @return array<int, array{score:float, payload:array<string,mixed>}>
     */
    public function search(string $collection, array $vector, int $limit, ?array $filter = null): array
    {
        $body = [
            'vector' => $vector,
            'limit' => $limit,
            'with_payload' => true,
        ];
        if (is_array($filter) && $filter !== []) {
            $body['filter'] = $filter;
        }

        $resp = Http::timeout(20)->post($this->url.'/collections/'.rawurlencode($collection).'/points/search', $body);

        try {
            $resp->throw();
        } catch (RequestException $e) {
            throw new \RuntimeException('Qdrant search failed: '.$resp->body(), previous: $e);
        }

        $result = $resp->json('result');
        if (!is_array($result)) {
            return [];
        }

        $hits = [];
        foreach ($result as $hit) {
            $hits[] = [
                'score' => (float) ($hit['score'] ?? 0.0),
                'payload' => is_array($hit['payload'] ?? null) ? $hit['payload'] : [],
            ];
        }

        return $hits;
    }
}
