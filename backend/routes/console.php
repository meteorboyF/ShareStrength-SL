<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

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

Artisan::command('rag:index {--reset : Drop & recreate the Qdrant collection} {--views : Also index Blade views (costs more embeddings)} {--db=* : Index DB tables (e.g. --db=resources --db=products)}', function () {
    $reset = (bool) $this->option('reset');
    $includeViews = (bool) $this->option('views');
    $dbTargets = $this->option('db') ?? [];
    $dbTargets = is_array($dbTargets) ? array_values(array_filter(array_map('strval', $dbTargets))) : [];

    $documents = [];

    // 1) Markdown docs in repo
    $paths = [];

    foreach ([
        base_path('README.md'),
        base_path('QUICK_REFERENCE.md'),
        base_path('FRONTEND_BACKEND_INTEGRATION.md'),
    ] as $path) {
        if (is_file($path)) {
            $paths[] = $path;
        }
    }

    $docsDir = base_path('documentation');
    if (is_dir($docsDir)) {
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($docsDir, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            /** @var \SplFileInfo $file */
            if ($file->isFile() && str_ends_with($file->getFilename(), '.md')) {
                $paths[] = $file->getPathname();
            }
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
                // Remove Blade comments
                $blade = preg_replace('/\\{\\-\\-.*?\\-\\-\\}/s', ' ', $blade) ?? $blade;
                // Remove Blade directives like @if/@foreach lines (keep inner text)
                $blade = preg_replace('/^\\s*@[^\\n]+$/m', ' ', $blade) ?? $blade;
                // Replace Blade echo blocks with space
                $blade = preg_replace('/\\{\\{.*?\\}\\}/s', ' ', $blade) ?? $blade;
                $blade = preg_replace('/\\{\\!\\!.*?\\!\\!\\}/s', ' ', $blade) ?? $blade;
                // Drop HTML tags
                $blade = preg_replace('/<[^>]+>/', ' ', $blade) ?? $blade;
                // Collapse whitespace
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
    $routeDocs = 0;
    foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
        if (!in_array('GET', $route->methods(), true)) {
            continue;
        }

        if (str_starts_with($route->uri(), 'api/')) {
            continue;
        }

        $uri = '/'.ltrim($route->uri(), '/');
        $name = $route->getName() ?: '';
        $middlewares = $route->gatherMiddleware();

        // Skip internal / debug routes
        if (in_array($route->uri(), ['up'], true) || str_starts_with($route->uri(), '_')) {
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
                $hasPwd = in_array('pwd', $guards, true);
                $hasHelpmate = in_array('helpmate', $guards, true);
                if ($hasPwd && $hasHelpmate) {
                    return 'any';
                }
                if ($hasHelpmate) {
                    return 'helpmate';
                }
                if ($hasPwd) {
                    return 'pwd';
                }

                return 'any';
            }

            return 'guest';
        };

        $action = $route->getActionName(); // class/method or Closure
        $routeLines[] = sprintf(
            'Page: %s | URL: %s | Name: %s | Middleware: %s | Handler: %s',
            $name !== '' ? $name : $uri,
            $uri,
            $name !== '' ? $name : '(none)',
            $middlewares ? implode(',', $middlewares) : '(none)',
            $action
        );

        // Create per-page route docs so sources can be direct clickable URLs.
        // Skip parameterized routes, since they are not directly navigable.
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
            $routeDocs++;
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

    // 3) Optional DB tables (public-ish content only; avoid user-private data)
    if ($dbTargets !== []) {
        try {
            foreach ($dbTargets as $t) {
                $t = strtolower(trim($t));
                if ($t === 'resources' && \Illuminate\Support\Facades\Schema::hasTable('resources')) {
                    foreach (\App\Models\Resource::query()->select(['id', 'title', 'type', 'description', 'language', 'author'])->get() as $r) {
                        $documents[] = [
                            'title' => "Resource: {$r->title}",
                            'url' => '/resources',
                            'text' => trim(implode("\n", array_filter([
                                "Title: {$r->title}",
                                "Type: {$r->type}",
                                $r->language ? "Language: {$r->language}" : null,
                                $r->author ? "Author: {$r->author}" : null,
                                $r->description ? "Description: {$r->description}" : null,
                            ]))),
                            'role' => 'any',
                            'source_type' => 'db:resources',
                        ];
                    }
                }

                if ($t === 'products' && \Illuminate\Support\Facades\Schema::hasTable('products')) {
                    foreach (\App\Models\Product::query()->select(['id', 'name', 'description', 'price', 'category', 'vendor'])->get() as $p) {
                        $documents[] = [
                            'title' => "Product: {$p->name}",
                            'url' => "/marketplace/product/{$p->id}",
                            'text' => trim(implode("\n", array_filter([
                                "Name: {$p->name}",
                                $p->category ? "Category: {$p->category}" : null,
                                $p->vendor ? "Vendor: {$p->vendor}" : null,
                                is_numeric($p->price) ? "Price: {$p->price}" : null,
                                $p->description ? "Description: {$p->description}" : null,
                            ]))),
                            'role' => 'any',
                            'source_type' => 'db:products',
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->warn('DB indexing skipped due to error: '.$e->getMessage());
        }
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

Artisan::command('rag:crawl
    {--reset : Drop & recreate the Qdrant collection}
    {--roles=guest,pwd,helpmate,admin : Comma-separated roles to crawl}
    {--max=250 : Max URLs per role}
    {--per-param=3 : Max examples for parameterized routes}
    {--no-redact : Disable redaction of emails/phones}
', function () {
    $reset = (bool) $this->option('reset');
    $maxUrls = (int) $this->option('max');
    $perParam = (int) $this->option('per-param');
    $noRedact = (bool) $this->option('no-redact');

    $roles = array_map('trim', explode(',', (string) $this->option('roles')));
    $roles = array_values(array_filter($roles, fn ($r) => in_array($r, ['guest', 'pwd', 'helpmate', 'admin'], true)));
    if ($roles === []) {
        $roles = ['guest', 'pwd', 'helpmate', 'admin'];
    }

    // Use in-memory session so crawling doesn't require sessions table.
    config(['session.driver' => 'array']);

    $kernel = app(\Illuminate\Contracts\Http\Kernel::class);
    $indexer = app(\App\Services\Rag\RagIndexer::class);

    // Index repo docs/routes/views first, then add crawled pages on top.
    $this->call('rag:index', [
        '--reset' => $reset,
        '--views' => true,
    ]);

    $collectionResetDone = true;
    $totalChunks = 0;

    $extractTextFromHtml = static function (string $html): string {
        $html = preg_replace('/<(script|style|noscript|svg)[^>]*>.*?<\\/\\1>/is', ' ', $html) ?? $html;
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\\s+/', ' ', $text) ?? $text;
        return trim($text);
    };

    $redact = static function (string $text) use ($noRedact): string {
        if ($noRedact) {
            return $text;
        }
        $text = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,}/i', '[redacted-email]', $text) ?? $text;
        $text = preg_replace('/\\+?\\d[\\d\\s().-]{7,}\\d/', '[redacted-phone]', $text) ?? $text;
        return $text;
    };

    $pickUserForRole = function (string $role) {
        try {
            return match ($role) {
                'pwd' => \App\Models\User::query()->where('is_active', true)->first(),
                'helpmate' => \App\Models\Helper::query()->where('is_active', true)->first(),
                'admin' => \App\Models\Admin::query()->where('is_active', true)->first(),
                default => null,
            };
        } catch (\Throwable) {
            return null;
        }
    };

    $exampleIds = function (int $limit): array {
        $ids = [];
        try {
            $ids['product'] = \App\Models\Product::query()->limit($limit)->pluck('id')->all();
        } catch (\Throwable) {
            $ids['product'] = [];
        }
        try {
            $ids['payment'] = \App\Models\Payment::query()->limit($limit)->pluck('id')->all();
        } catch (\Throwable) {
            $ids['payment'] = [];
        }
        try {
            $ids['user'] = \App\Models\User::query()->limit($limit)->pluck('id')->all();
        } catch (\Throwable) {
            $ids['user'] = [];
        }
        try {
            $ids['helper'] = \App\Models\Helper::query()->limit($limit)->pluck('id')->all();
        } catch (\Throwable) {
            $ids['helper'] = [];
        }
        try {
            $ids['conversation'] = \App\Models\Conversation::query()->limit($limit)->pluck('id')->all();
        } catch (\Throwable) {
            $ids['conversation'] = [];
        }
        return $ids;
    };

    $ids = $exampleIds(max(1, $perParam));

    // Build URL list from web routes
    $staticUrls = [];
    $paramRoutes = [];
    foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
        if (!in_array('GET', $route->methods(), true)) {
            continue;
        }

        $uri = '/'.ltrim($route->uri(), '/');
        if (str_starts_with($uri, '/api/')) {
            continue;
        }
        if ($uri === '/up' || str_starts_with($uri, '/_')) {
            continue;
        }

        if (preg_match('/\\{[^}]+\\}/', $uri)) {
            $paramRoutes[] = $uri;
        } else {
            $staticUrls[] = $uri;
        }
    }
    $staticUrls = array_values(array_unique($staticUrls));

    $expandParamRoute = function (string $uri) use ($ids): array {
        // Best-effort expansions for known params.
        if (str_contains($uri, '{paymentId}')) {
            return array_map(fn ($id) => str_replace('{paymentId}', (string) $id, $uri), $ids['payment'] ?? []);
        }
        if (str_contains($uri, '{id}')) {
            // Disambiguate by prefix where possible
            if (str_starts_with($uri, '/marketplace/product/')) {
                return array_map(fn ($id) => str_replace('{id}', (string) $id, $uri), $ids['product'] ?? []);
            }
            if (str_starts_with($uri, '/profile/pwd/')) {
                return array_map(fn ($id) => str_replace('{id}', (string) $id, $uri), $ids['user'] ?? []);
            }
            if (str_starts_with($uri, '/profile/helpmate/')) {
                return array_map(fn ($id) => str_replace('{id}', (string) $id, $uri), $ids['helper'] ?? []);
            }
        }
        if (str_contains($uri, '{conversationId?}')) {
            $out = [str_replace('{conversationId?}', '', $uri)];
            foreach (($ids['conversation'] ?? []) as $id) {
                $out[] = str_replace('{conversationId?}', (string) $id, $uri);
            }
            return $out;
        }
        return [];
    };

    $expanded = [];
    foreach (array_values(array_unique($paramRoutes)) as $p) {
        foreach ($expandParamRoute($p) as $u) {
            $u = preg_replace('#//+#', '/', $u) ?? $u;
            if ($u !== '') {
                $expanded[] = $u;
            }
        }
    }
    $expanded = array_values(array_unique($expanded));

    $allUrls = array_values(array_unique(array_merge($staticUrls, $expanded)));

    $this->info('Discovered '.count($allUrls).' GET routes to crawl.');

    foreach ($roles as $role) {
        $this->info("Crawling as {$role}…");

        $session = app('session')->driver();
        $session->start();

        $guard = match ($role) {
            'pwd' => 'pwd',
            'helpmate' => 'helpmate',
            'admin' => 'admin',
            default => null,
        };

        $user = $guard ? $pickUserForRole($role) : null;
        if ($guard && !$user) {
            $this->warn("No {$role} account found; skipping authenticated crawl for {$role}. Seed test users with: php artisan db:seed --class=TestCredentialsSeeder");
            continue;
        }

        $docs = [];
        $seen = 0;

        foreach ($allUrls as $url) {
            if ($seen >= $maxUrls) {
                break;
            }

            $response = null;
            $request = \Illuminate\Http\Request::create($url, 'GET', [], [], [], [
                'HTTP_ACCEPT' => 'text/html',
                'HTTP_USER_AGENT' => 'ShareStrength-RagCrawler',
            ]);
            $request->setLaravelSession($session);
            $request->cookies->set($session->getName(), $session->getId());

            app()->instance('request', $request);
            \Illuminate\Support\Facades\Auth::forgetGuards();

            if ($guard && $user) {
                \Illuminate\Support\Facades\Auth::shouldUse($guard);
                $g = \Illuminate\Support\Facades\Auth::guard($guard);
                if (method_exists($g, 'setUser')) {
                    $g->setUser($user);
                } else {
                    $g->login($user);
                }
            } else {
                \Illuminate\Support\Facades\Auth::shouldUse(config('auth.defaults.guard', 'web'));
            }

            try {
                $response = $kernel->handle($request);
                $status = $response->getStatusCode();

                if ($status !== 200) {
                    continue;
                }

                $html = $response->getContent();
                if (!is_string($html) || trim($html) === '') {
                    continue;
                }

                $text = $extractTextFromHtml($html);
                $text = $redact($text);

                // Skip tiny pages (usually empty shells/redirect stubs)
                if (mb_strlen($text) < 200) {
                    continue;
                }

                $docs[] = [
                    'title' => "Page: {$url} ({$role})",
                    'url' => $url,
                    'text' => $text,
                    'role' => $role,
                    'source_type' => 'page',
                ];
                $seen++;

                if (count($docs) >= 15) {
                    if (!$collectionResetDone) {
                        $totalChunks += $indexer->index($docs, $reset);
                        $collectionResetDone = true;
                    } else {
                        $totalChunks += $indexer->index($docs, false);
                    }
                    $docs = [];
                }
            } catch (\Throwable $e) {
                // Skip pages that error due to missing DB/migrations/etc.
                continue;
            } finally {
                if ($response) {
                    $kernel->terminate($request, $response);
                }
            }
        }

        if ($docs !== []) {
            $totalChunks += $indexer->index($docs, false);
        }
    }

    $this->info("Done. Indexed {$totalChunks} chunks (crawled pages).");
})->purpose('Crawl GET web pages (guest + roles) and index into Qdrant');
