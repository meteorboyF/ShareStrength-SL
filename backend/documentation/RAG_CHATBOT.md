# RAG Chatbot (Gemini + Qdrant)

This project includes a chatbot widget (`resources/views/components/chatbot.blade.php`) that can be backed by a RAG pipeline:

- Gemini Embeddings → semantic search
- Qdrant → vector database
- Gemini Text Generation → final answer grounded in retrieved sources

## 1) Required environment variables

Set these in your `.env` (do not put API keys in frontend JS):

```bash
GEMINI_API_KEY="..."
GEMINI_EMBED_MODEL="gemini-embedding-001"
GEMINI_CHAT_MODEL="gemini-3-flash-preview"
GEMINI_EMBED_DIM=768

QDRANT_URL="http://localhost:6333"
QDRANT_COLLECTION="site_knowledge"
QDRANT_TOP_K=6
```

## 2) Start Qdrant

Local Docker example:

```bash
docker run -p 6333:6333 -p 6334:6334 qdrant/qdrant
```

## 3) Index knowledge into Qdrant

This indexes:
- local Markdown docs (repo root + `documentation/`)
- a generated “Web Routes Index” (best for navigation questions)

```bash
php artisan rag:index --reset
```

Optional (costs more embeddings, but gives better “what does this page say / where is this button” answers):

```bash
php artisan rag:index --reset --views
```

Optional DB indexing (public-ish content only; avoids user-private data):

```bash
php artisan rag:index --reset --db=resources --db=products
```

You can run it again any time content changes.

## 3b) Crawl and index all pages (best-effort)

This crawls **GET** web routes and indexes the rendered HTML text. It will:
- run `rag:index --views` first
- attempt to crawl as `guest`, `pwd`, `helpmate`, and `admin`
- skip pages that error (often due to missing DB/migrations)

```bash
php artisan rag:crawl --reset
```

If you don’t have accounts yet, seed them first:

```bash
php artisan migrate --force
php artisan db:seed --class=TestCredentialsSeeder
```

Privacy note: crawling authenticated pages can embed whatever data appears on-screen (including user-entered task descriptions). Prefer using seed/demo data.

## 4) Run the app

```bash
php artisan serve
npm run dev
```

Open the site and use the chat widget in the bottom-right.

## Notes

- If you see “no sources found”, run `php artisan rag:index --reset` and verify Qdrant is running.
- Use `php artisan rag:doctor` to sanity-check configuration/connectivity.
- The chat endpoint also adds a small “live data” context for logged-in users (e.g. recent tasks/payments/applications) without embedding it into Qdrant.
