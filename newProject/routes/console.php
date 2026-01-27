<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('rag:doctor', function () {
    $this->info('RAG configuration:');
    $this->line('  QDRANT_URL: '.(config('rag.qdrant.url') ?: '(missing)'));
    $this->line('  QDRANT_COLLECTION: '.(config('rag.qdrant.collection') ?: '(missing)'));
    $this->line('  GEMINI_EMBED_MODEL: '.(config('rag.gemini.embed_model') ?: '(missing)'));
    $this->line('  GEMINI_CHAT_MODEL: '.(config('rag.gemini.chat_model') ?: '(missing)'));
    $this->line('  GEMINI_EMBED_DIM: '.(string) config('rag.gemini.embed_dim'));
    $this->line('  GEMINI_API_KEY: '.(config('rag.gemini.api_key') ? '[set]' : '(missing)'));

    try {
        $qdrant = app(\App\Services\Rag\QdrantClient::class);
        $this->line('Qdrant health: '.($qdrant->health() ? 'OK' : 'NOT OK'));
    } catch (\Throwable $e) {
        $this->error('Qdrant check failed: '.$e->getMessage());
    }
})->purpose('Check RAG/Gemini/Qdrant configuration');

Artisan::command('rag:index
    {--reset : Drop & recreate the Qdrant collection}
    {--views : Also index Blade views (costs more embeddings)}
', function () {
    $reset = (bool) $this->option('reset');
    $includeViews = (bool) $this->option('views');

    $documents = [];

    // 1) Markdown docs in repo
    $paths = [];
    foreach ([base_path('README.md'), base_path('documentation/RAG_CHATBOT.md')] as $path) {
        if (is_file($path)) {
            $paths[] = $path;
        }
    }
    $paths = array_values(array_unique($paths));

    foreach ($paths as $path) {
        $text = @file_get_contents($path);
        if (!is_string($text) || trim($text) === '') {
            continue;
        }

        $relative = str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
        $documents[] = [
            'title' => $relative,
            'url' => $relative,
            'text' => $text,
            'role' => 'any',
            'source_type' => 'doc',
        ];
    }

    // 1b) Blade views (helps with “what does this page say / where is this button”)
    if ($includeViews) {
        $viewsDir = resource_path('views');
        if (is_dir($viewsDir)) {
            $it = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($viewsDir, \FilesystemIterator::SKIP_DOTS)
            );

            $extractBladeText = static function (string $blade): string {
                $blade = preg_replace('/\\{\\-\\-.*?\\-\\-\\}/s', ' ', $blade) ?? $blade;
                $blade = preg_replace('/^\\s*@[^\\n]+$/m', ' ', $blade) ?? $blade;
                $blade = preg_replace('/\\{\\{.*?\\}\\}/s', ' ', $blade) ?? $blade;
                $blade = preg_replace('/\\{\\!\\!.*?\\!\\!\\}/s', ' ', $blade) ?? $blade;
                $blade = preg_replace('/<[^>]+>/', ' ', $blade) ?? $blade;
                $blade = preg_replace('/\\s+/', ' ', $blade) ?? $blade;
                return trim($blade);
            };

            foreach ($it as $file) {
                /** @var \SplFileInfo $file */
                if (!$file->isFile() || !str_ends_with($file->getFilename(), '.blade.php')) {
                    continue;
                }

                $path = $file->getPathname();
                $raw = @file_get_contents($path);
                if (!is_string($raw) || trim($raw) === '') {
                    continue;
                }

                $text = $extractBladeText($raw);
                if ($text === '') {
                    continue;
                }

                $relative = str_replace(base_path().DIRECTORY_SEPARATOR, '', $path);
                $documents[] = [
                    'title' => $relative,
                    'url' => $relative,
                    'text' => $text,
                    'role' => 'any',
                    'source_type' => 'view',
                ];
            }
        }
    }

    // 2) Web route index (best for “where do I click” navigation questions)
    $routeLines = [];
    foreach (Route::getRoutes() as $route) {
        if (!in_array('GET', $route->methods(), true)) {
            continue;
        }

        if (str_starts_with($route->uri(), 'api/')) {
            continue;
        }

        // Skip internal / debug routes
        if (in_array($route->uri(), ['up'], true) || str_starts_with($route->uri(), '_')) {
            continue;
        }

        $uri = '/'.ltrim($route->uri(), '/');
        $name = $route->getName() ?: '';
        $middlewares = $route->gatherMiddleware();

        // Skip non-navigation endpoints
        if (str_starts_with($uri, '/chatbot/') || str_starts_with($uri, '/accessibility/')) {
            continue;
        }

        $deriveRole = static function (array $middlewares): string {
            foreach ($middlewares as $m) {
                if (!is_string($m)) {
                    continue;
                }
                if (!str_starts_with($m, 'auth:')) {
                    continue;
                }

                $guardsRaw = trim((string) explode(':', $m, 2)[1] ?? '');
                $guards = array_values(array_filter(array_map('trim', explode(',', $guardsRaw))));

                if (in_array('admin', $guards, true)) {
                    return 'admin';
                }
                if (in_array('provider', $guards, true)) {
                    return 'provider';
                }
                if (in_array('web', $guards, true)) {
                    return 'user';
                }

                return 'any';
            }

            return 'guest';
        };

        $action = $route->getActionName();
        $routeLines[] = sprintf(
            'Page: %s | URL: %s | Name: %s | Middleware: %s | Handler: %s',
            $name !== '' ? $name : $uri,
            $uri,
            $name !== '' ? $name : '(none)',
            $middlewares ? implode(',', $middlewares) : '(none)',
            $action
        );

        // Per-page doc for clickable sources. Skip parameterized routes.
        if (!preg_match('/\\{[^}]+\\}/', $uri)) {
            $role = $deriveRole($middlewares);
            $documents[] = [
                'title' => $name !== '' ? $name : "Page {$uri}",
                'url' => $uri,
                'text' => implode("\n", array_filter([
                    "URL: {$uri}",
                    $name !== '' ? "Name: {$name}" : null,
                    "Access: {$role}",
                    $middlewares ? "Middleware: ".implode(',', $middlewares) : null,
                    $action ? "Handler: {$action}" : null,
                ])),
                'role' => $role,
                'source_type' => 'route',
            ];
        }
    }

    if ($routeLines !== []) {
        $documents[] = [
            'title' => 'Web Routes Index',
            'url' => '/routes',
            'text' => implode("\n", $routeLines),
            'role' => 'any',
            'source_type' => 'routes',
        ];
    }

    $this->info('Indexing '.count($documents).' documents…');

    try {
        $indexer = app(\App\Services\Rag\RagIndexer::class);
        $chunks = $indexer->index($documents, $reset);
        $this->info("Done. Indexed {$chunks} chunks into Qdrant.");
    } catch (\Throwable $e) {
        $this->error('Indexing failed: '.$e->getMessage());
        if (config('app.debug')) {
            $this->line($e->getTraceAsString());
        }
        return 1;
    }
})->purpose('Index local docs + routes into Qdrant for the chatbot');
