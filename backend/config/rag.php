<?php

return [
    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'embed_model' => env('GEMINI_EMBED_MODEL', 'gemini-embedding-001'),
        'chat_model' => env('GEMINI_CHAT_MODEL', 'gemini-3-flash-preview'),
        'embed_dim' => (int) env('GEMINI_EMBED_DIM', 768),
        'max_output_tokens' => (int) env('GEMINI_MAX_OUTPUT_TOKENS', 1256),
        'temperature' => (float) env('GEMINI_TEMPERATURE', 0.2),
    ],

    'qdrant' => [
        'url' => env('QDRANT_URL', 'http://localhost:6333'),
        'collection' => env('QDRANT_COLLECTION', 'site_knowledge'),
        'top_k' => (int) env('QDRANT_TOP_K', 6),
    ],
];
