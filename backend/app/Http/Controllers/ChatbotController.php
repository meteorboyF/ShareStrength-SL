<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Helper;
use App\Models\Payment;
use App\Models\Task;
use App\Services\Rag\GeminiClient;
use App\Services\Rag\QdrantClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatbotController extends Controller
{
    private const SESSION_KEY = 'chatbot.messages';
    private const MAX_SESSION_MESSAGES = 60;

    private function nextMessageId(array $messages): int
    {
        $max = 0;
        foreach ($messages as $m) {
            if (!is_array($m)) {
                continue;
            }
            $id = $m['id'] ?? null;
            if (is_int($id) || is_float($id) || (is_string($id) && ctype_digit($id))) {
                $max = max($max, (int) $id);
            }
        }

        return max($max + 1, (int) (microtime(true) * 1000));
    }

    private function defaultMessages(): array
    {
        return [
            [
                'id' => (int) (microtime(true) * 1000),
                'sender' => 'bot',
                'text' => 'Hi! I am your HelpMate Assistant. How can I help today?',
                'links' => [],
            ],
        ];
    }

    private function getSessionMessages(): array
    {
        $messages = session()->get(self::SESSION_KEY);
        if (!is_array($messages) || $messages === []) {
            $messages = $this->defaultMessages();
            session()->put(self::SESSION_KEY, $messages);
        }

        // Normalize
        $normalized = [];
        $seenIds = [];
        foreach ($messages as $m) {
            if (!is_array($m)) {
                continue;
            }
            $sender = (string) ($m['sender'] ?? '');
            $text = (string) ($m['text'] ?? '');
            if (!in_array($sender, ['user', 'bot'], true) || trim($text) === '') {
                continue;
            }

            $id = is_numeric($m['id'] ?? null) ? (int) $m['id'] : (int) (microtime(true) * 1000);
            while (isset($seenIds[$id])) {
                $id++;
            }
            $seenIds[$id] = true;

            $normalized[] = [
                'id' => $id,
                'sender' => $sender,
                'text' => $text,
                'links' => is_array($m['links'] ?? null) ? $m['links'] : [],
            ];
        }

        if ($normalized === []) {
            $normalized = $this->defaultMessages();
        }

        session()->put(self::SESSION_KEY, $normalized);
        return $normalized;
    }

    private function putSessionMessages(array $messages): void
    {
        $messages = array_values($messages);
        if (count($messages) > self::MAX_SESSION_MESSAGES) {
            $messages = array_slice($messages, -self::MAX_SESSION_MESSAGES);
        }
        session()->put(self::SESSION_KEY, $messages);
    }

    public function history()
    {
        return response()->json([
            'messages' => $this->getSessionMessages(),
        ]);
    }

    private function computeLinksAndContext(QdrantClient $qdrant, array $qvec, int $topK, string $collection, string $userRole): array
    {
        $searchLimit = max($topK, $topK * 4);
        $hits = $qdrant->search($collection, $qvec, $searchLimit);

        $sources = [];
        $contextBlocks = [];
        foreach ($hits as $hit) {
            $p = $hit['payload'] ?? [];
            if (!is_array($p)) {
                continue;
            }

            $title = (string) ($p['title'] ?? '');
            $url = (string) ($p['url'] ?? '');
            $chunkIndex = $p['chunk_index'] ?? null;
            $text = (string) ($p['text'] ?? '');
            $docRole = (string) ($p['role'] ?? '');
            $sourceType = (string) ($p['source_type'] ?? '');

            if ($url === '' || trim($text) === '') {
                continue;
            }

            if ($docRole !== '' && !in_array($docRole, [$userRole, 'any', 'guest'], true)) {
                continue;
            }

            if (str_starts_with($url, '/api/')) {
                continue;
            }

            $priority = 10;
            if (str_starts_with($url, '/')) {
                $priority = 2;
                if ($url === '/routes') {
                    $priority = 6;
                }
                if (in_array($sourceType, ['route', 'page'], true)) {
                    $priority = 0;
                }
            }

            $sources[] = [
                'title' => $title !== '' ? $title : $url,
                'url' => $url,
                'chunk_index' => is_numeric($chunkIndex) ? (int) $chunkIndex : null,
                'score' => (float) ($hit['score'] ?? 0.0),
                'source_type' => $sourceType,
                'priority' => $priority,
            ];

            $snippet = mb_substr($text, 0, 1200);
            $contextBlocks[] = "[Source: {$title} | {$url} | chunk ".(is_numeric($chunkIndex) ? (int) $chunkIndex : '?')."]\n{$snippet}";
        }

        $seenUrls = [];
        $sources = array_values(array_filter($sources, function ($s) use (&$seenUrls) {
            $u = (string) ($s['url'] ?? '');
            if ($u === '' || isset($seenUrls[$u])) {
                return false;
            }
            $seenUrls[$u] = true;
            return true;
        }));

        usort($sources, function ($a, $b) {
            $pa = (int) ($a['priority'] ?? 10);
            $pb = (int) ($b['priority'] ?? 10);
            if ($pa !== $pb) {
                return $pa <=> $pb;
            }
            return ((float) ($b['score'] ?? 0.0)) <=> ((float) ($a['score'] ?? 0.0));
        });

        $links = array_values(array_filter($sources, function ($s) {
            $url = (string) ($s['url'] ?? '');
            if ($url === '' || $url === '/routes') {
                return false;
            }
            return str_starts_with($url, '/') || str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
        }));
        $links = array_slice($links, 0, 3);

        $context = $contextBlocks ? implode("\n\n---\n\n", $contextBlocks) : '(no sources found)';

        return [$context, $sources, $links];
    }

    public function stream(Request $request, GeminiClient $gemini, QdrantClient $qdrant): StreamedResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $question = trim((string) $validated['message']);

        $embedDim = (int) config('rag.gemini.embed_dim', 768);
        $collection = (string) config('rag.qdrant.collection', 'site_knowledge');
        $topK = (int) config('rag.qdrant.top_k', 6);

        $messages = $this->getSessionMessages();
        $messages[] = [
            'id' => $this->nextMessageId($messages),
            'sender' => 'user',
            'text' => $question,
            'links' => [],
        ];
        $this->putSessionMessages($messages);

        $historyLines = [];
        foreach (array_slice($messages, -12) as $m) {
            $historyLines[] = strtoupper($m['sender']).': '.$m['text'];
        }
        $historyText = $historyLines ? ("Conversation so far:\n".implode("\n", $historyLines)."\n\n") : '';

        $currentUser = Auth::guard('helpmate')->user() ?: Auth::guard('pwd')->user() ?: Auth::guard('admin')->user();
        $userRole = 'guest';
        if ($currentUser instanceof Helper) {
            $userRole = 'helpmate';
        } elseif ($currentUser) {
            $userRole = Auth::guard('admin')->check() ? 'admin' : 'pwd';
        }

        $liveContext = $this->buildLiveContextForUser($currentUser, $userRole);

        // 1) Embed query
        $vectors = $gemini->embedTexts([$question], 'RETRIEVAL_QUERY');
        $qvec = $vectors[0] ?? null;
        if (!is_array($qvec) || count($qvec) !== $embedDim) {
            return new StreamedResponse(function () {
                echo "event: error\n";
                echo 'data: {"message":"Failed to embed query."}'."\n\n";
                @ob_flush();
                @flush();
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
            ]);
        }

        // 2) Retrieval
        [$context, $sources, $links] = $this->computeLinksAndContext($qdrant, $qvec, $topK, $collection, $userRole);

        $systemInstruction = implode(' ', [
            'You are the ShareStrength in-app assistant.',
            'Help people navigate the site and complete tasks.',
            'Be concise and friendly: 1 short sentence + up to 4 short bullet steps.',
            'Do not mention "sources", "context", or the user role unless asked.',
            'Do not include raw API paths like "/api/...". Prefer website page paths like "/dashboard".',
            'Use ONLY the provided context to answer.',
            'If the answer is not in the context, say you do not know and ask what page/section to add to the knowledge base.',
            'If you mention a page, include at most one page path at the end of the bullet (e.g. "(Go to /messages)").',
            'If live data is provided, treat it as authoritative for the current user.',
        ]);

        $userText = $historyText
            ."User role: {$userRole}\n"
            ."User question:\n{$question}\n\n"
            .$liveContext."\n"
            ."Context:\n{$context}\n";

        return new StreamedResponse(function () use ($gemini, $links, $question, $userText, $systemInstruction) {
            // Disable buffering where possible.
            while (ob_get_level() > 0) {
                @ob_end_flush();
            }
            @ini_set('output_buffering', '0');
            @ini_set('zlib.output_compression', '0');
            @ini_set('implicit_flush', '1');
            if (function_exists('apache_setenv')) {
                @apache_setenv('no-gzip', '1');
            }

            $sendEvent = static function (string $event, array $data) {
                echo "event: {$event}\n";
                echo 'data: '.json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n\n";
                @flush();
            };

            // Kick-start the stream for proxies/buffers.
            echo ": stream-start\n\n";
            @flush();

            // Send links immediately for clickability.
            $sendEvent('links', ['links' => $links]);

            $final = '';
            try {
                $final = $gemini->streamGenerate($systemInstruction, $userText, function (string $delta) use ($sendEvent) {
                    $sendEvent('delta', ['text' => $delta]);
                });

                $messages = session()->get(self::SESSION_KEY, []);
                if (!is_array($messages)) {
                    $messages = [];
                }
                $messages[] = [
                    'id' => $this->nextMessageId($messages),
                    'sender' => 'bot',
                    'text' => $final !== '' ? $final : 'Sorry — I did not get a response.',
                    'links' => $links,
                ];
                $this->putSessionMessages($messages);
                session()->save();

                $sendEvent('done', ['ok' => true]);
            } catch (\Throwable $e) {
                $sendEvent('error', ['message' => $e->getMessage()]);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream; charset=utf-8',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    private function buildLiveContextForUser($user, string $role): string
    {
        if (!$user) {
            return '';
        }

        $blocks = [];

        try {
            if ($role === 'pwd') {
                $tasks = Task::query()
                    ->where('created_by', $user->getKey())
                    ->latest()
                    ->limit(10)
                    ->get(['id', 'title', 'status', 'location', 'scheduled_at', 'created_at']);

                if ($tasks->isNotEmpty()) {
                    $lines = $tasks->map(function ($t) {
                        $scheduled = $t->scheduled_at ? $t->scheduled_at->toDateTimeString() : null;
                        return trim(implode(' | ', array_filter([
                            "Task #{$t->id}",
                            $t->title,
                            "status={$t->status}",
                            $t->location ? "location={$t->location}" : null,
                            $scheduled ? "scheduled_at={$scheduled}" : null,
                        ])));
                    })->all();

                    $blocks[] = "My recent tasks:\n".implode("\n", $lines);
                }

                $pendingPayments = Payment::query()
                    ->with(['task:id,title'])
                    ->where('payer_id', $user->getKey())
                    ->where('status', 'pending')
                    ->latest()
                    ->limit(10)
                    ->get(['id', 'task_id', 'amount', 'status', 'created_at']);

                if ($pendingPayments->isNotEmpty()) {
                    $lines = $pendingPayments->map(function ($p) {
                        $title = $p->task?->title ?? ('Task #'.$p->task_id);
                        return trim(implode(' | ', array_filter([
                            "Payment #{$p->id}",
                            $title,
                            "amount={$p->amount}",
                            "status={$p->status}",
                        ])));
                    })->all();

                    $blocks[] = "My pending payments:\n".implode("\n", $lines);
                }
            }

            if ($role === 'helpmate') {
                $applications = Application::query()
                    ->where('helper_id', $user->getKey())
                    ->latest()
                    ->limit(10)
                    ->get(['id', 'task_id', 'status', 'created_at']);

                if ($applications->isNotEmpty()) {
                    $lines = $applications->map(function ($a) {
                        return trim(implode(' | ', array_filter([
                            "Application #{$a->id}",
                            "task_id={$a->task_id}",
                            "status={$a->status}",
                        ])));
                    })->all();

                    $blocks[] = "My recent applications:\n".implode("\n", $lines);
                }

                $assigned = Task::query()
                    ->where('caregiver_id', $user->getKey())
                    ->whereIn('status', ['accepted', 'in_progress'])
                    ->latest()
                    ->limit(10)
                    ->get(['id', 'title', 'status', 'location', 'scheduled_at', 'started_at']);

                if ($assigned->isNotEmpty()) {
                    $lines = $assigned->map(function ($t) {
                        $scheduled = $t->scheduled_at ? $t->scheduled_at->toDateTimeString() : null;
                        $started = $t->started_at ? $t->started_at->toDateTimeString() : null;
                        return trim(implode(' | ', array_filter([
                            "Task #{$t->id}",
                            $t->title,
                            "status={$t->status}",
                            $t->location ? "location={$t->location}" : null,
                            $scheduled ? "scheduled_at={$scheduled}" : null,
                            $started ? "started_at={$started}" : null,
                        ])));
                    })->all();

                    $blocks[] = "My active assigned tasks:\n".implode("\n", $lines);
                }
            }
        } catch (\Throwable) {
            // Live context is best-effort; ignore failures (e.g. migrations not run).
        }

        if ($blocks === []) {
            return '';
        }

        return "[Live data - real-time]\n".implode("\n\n", $blocks)."\n";
    }

    public function ask(Request $request, GeminiClient $gemini, QdrantClient $qdrant)
    {
        try {
            $validated = $request->validate([
                'message' => 'required|string|max:2000',
            ]);

            $question = trim((string) $validated['message']);
            if ($question === '') {
                return response()->json(['message' => 'Message is required.'], 422);
            }

            $embedDim = (int) config('rag.gemini.embed_dim', 768);
            $collection = (string) config('rag.qdrant.collection', 'site_knowledge');
            $topK = (int) config('rag.qdrant.top_k', 6);

            $messages = $this->getSessionMessages();
            $messages[] = [
                'id' => $this->nextMessageId($messages),
                'sender' => 'user',
                'text' => $question,
                'links' => [],
            ];
            $this->putSessionMessages($messages);

            $historyLines = [];
            foreach (array_slice($messages, -12) as $m) {
                $historyLines[] = strtoupper($m['sender']).': '.$m['text'];
            }
            $historyText = $historyLines ? ("Conversation so far:\n".implode("\n", $historyLines)."\n\n") : '';

            $currentUser = Auth::guard('helpmate')->user() ?: Auth::guard('pwd')->user() ?: Auth::guard('admin')->user();
            $userRole = 'guest';
            if ($currentUser instanceof Helper) {
                $userRole = 'helpmate';
            } elseif ($currentUser) {
                $userRole = Auth::guard('admin')->check() ? 'admin' : 'pwd';
            }

            $liveContext = $this->buildLiveContextForUser($currentUser, $userRole);

            // 1) Embed query
            $vectors = $gemini->embedTexts([$question], 'RETRIEVAL_QUERY');
            $qvec = $vectors[0] ?? null;
            if (!is_array($qvec) || count($qvec) !== $embedDim) {
                return response()->json(['message' => 'Failed to embed query.'], 500);
            }

            // 2) Vector search
            [$context, $sources, $links] = $this->computeLinksAndContext($qdrant, $qvec, $topK, $collection, $userRole);

            // 3) Generate answer
            $systemInstruction = implode(' ', [
                'You are the ShareStrength in-app assistant.',
                'Help people navigate the site and complete tasks.',
                'Be concise and friendly: 1 short sentence + up to 4 short bullet steps.',
                'Do not mention "sources", "context", or the user role unless asked.',
                'Do not include raw API paths like "/api/...". Prefer website page paths like "/dashboard".',
                'Use ONLY the provided context to answer.',
                'If the answer is not in the context, say you do not know and ask what page/section to add to the knowledge base.',
                'If you mention a page, include at most one page path at the end of the bullet (e.g. "(Go to /messages)").',
                'If live data is provided, treat it as authoritative for the current user.',
            ]);

            $userText = $historyText
                ."User role: {$userRole}\n"
                ."User question:\n{$question}\n\n"
                .$liveContext."\n"
                ."Context:\n{$context}\n";

            $answer = $gemini->generate($systemInstruction, $userText);

            $messages = $this->getSessionMessages();
            $messages[] = [
                'id' => $this->nextMessageId($messages),
                'sender' => 'bot',
                'text' => $answer,
                'links' => $links,
            ];
            $this->putSessionMessages($messages);

            return response()->json([
                'answer' => $answer,
                'sources' => $sources,
                'links' => $links,
                'messages' => $messages,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
