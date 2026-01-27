# RAG Chatbot (Gemini + Qdrant)

This project includes a streaming in-app assistant that:

1) Embeds documents + routes into Qdrant (vector DB)
2) Embeds each user question
3) Retrieves the most relevant chunks
4) Uses Gemini to generate an answer grounded in that context

## Security rule

Do **not** put `GEMINI_API_KEY` in browser JavaScript. Keep it in `.env` on the server.

## Setup

1) Start Qdrant + MySQL:

```bash
docker compose up -d
```

2) Set env vars in `.env`:

- `GEMINI_API_KEY=...`
- `QDRANT_URL=http://127.0.0.1:6335`
- `QDRANT_COLLECTION=newproject_knowledge`

3) Check config:

```bash
php artisan rag:doctor
```

## Indexing

Indexes local docs + routes into Qdrant:

```bash
php artisan rag:index --reset --views
```

Notes:
- `--reset` recreates the Qdrant collection.
- `--views` also indexes Blade view text (costs more embeddings).

## Chat Endpoints

- `GET /chatbot/history` (session history)
- `POST /chatbot/stream` (SSE streaming)
- `POST /chatbot/ask` (non-streaming JSON)

## Role-safe links

When indexing routes, each route is tagged with an access role (`guest`, `user`, `provider`, `admin`).
The chatbot filters sources so guests won’t receive private/admin routes as “Quick Links”.
