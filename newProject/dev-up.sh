#!/usr/bin/env bash
set -euo pipefail

DEVUP_PAUSE_ON_EXIT="${DEVUP_PAUSE_ON_EXIT:-1}"

pause_if_tty() {
  if [ "$DEVUP_PAUSE_ON_EXIT" != "1" ]; then
    return 0
  fi

  # If we have a controlling TTY (even when stdout is redirected via `tee`),
  # pause so a double-clicked terminal window doesn't instantly close.
  if exec 9<>/dev/tty 2>/dev/null; then
    echo >&9
    read -rp "Press Enter to close..." _ <&9 || true
    exec 9>&-
  fi
}

on_exit() {
  exit_code=$?
  if [ "$exit_code" -ne 0 ]; then
    echo
    echo "[newProject] Failed (exit code: $exit_code)."
  else
    echo
    echo "[newProject] Done."
  fi
  # Pause only on real errors (not Ctrl+C / SIGTERM), so the window stays open
  # long enough to read what went wrong when double-clicked.
  if [ "$exit_code" -ne 0 ] && [ "$exit_code" -ne 130 ] && [ "$exit_code" -ne 143 ]; then
    pause_if_tty
  fi
  return "$exit_code"
}

trap on_exit EXIT

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$ROOT_DIR"

LOG_FILE="${ROOT_DIR}/dev-up.log"
exec > >(tee -a "$LOG_FILE") 2>&1

echo
echo "[newProject] Starting dev environment..."
echo "[newProject] Logs: $LOG_FILE"

need_cmd() {
  if ! command -v "$1" >/dev/null 2>&1; then
    echo "Missing required command: $1"
    exit 1
  fi
}

need_cmd php
need_cmd composer
need_cmd node
need_cmd npm
need_cmd docker

node_ver="$(node -v | sed 's/^v//')"
node_major="${node_ver%%.*}"
if [ "$node_major" -lt 18 ]; then
  echo
  echo "Node.js 18+ is required (found v$node_ver)."
  echo "Install a newer Node.js (Node 18, 20, or 22) and try again."
  exit 1
fi

if ! php -r 'exit(extension_loaded("pdo_mysql") ? 0 : 1);' >/dev/null 2>&1; then
  php_mm="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')"
  pkg_versioned="php${php_mm}-mysql"
  echo
  echo "PHP is missing the MySQL PDO driver (pdo_mysql)."
  echo "On Linux Mint/Ubuntu, install it with:"
  echo "  sudo apt update && sudo apt install ${pkg_versioned}"
  echo
  echo "If that package isn't available, try:"
  echo "  sudo apt update && sudo apt install php-mysql"
  echo
  exit 1
fi

DOCKER_COMPOSE_CMD=""
if docker compose version >/dev/null 2>&1; then
  DOCKER_COMPOSE_CMD="docker compose"
elif command -v docker-compose >/dev/null 2>&1; then
  DOCKER_COMPOSE_CMD="docker-compose"
else
  echo "Missing Docker Compose (try installing Docker Desktop / docker-compose)."
  exit 1
fi

echo
echo "[newProject] Starting MySQL + Qdrant (Docker)..."
set +e
$DOCKER_COMPOSE_CMD up -d
status=$?
set -e
if [ $status -ne 0 ]; then
  echo "Docker command failed (maybe you need sudo). Retrying with sudo..."
  sudo $DOCKER_COMPOSE_CMD up -d
fi

if [ ! -f .env ]; then
  echo
  echo "[newProject] Creating .env from .env.example..."
  cp .env.example .env
fi

if grep -qE '^APP_KEY=$' .env; then
  echo
  echo "[newProject] Generating APP_KEY..."
  php artisan key:generate
fi

if [ ! -d vendor ]; then
  echo
  echo "[newProject] Installing PHP dependencies..."
  composer install
fi

need_node_install=0
lock_hash="$(php -r "echo hash_file('sha256', 'package-lock.json');")"
lock_hash_file="node_modules/.devup-package-lock.sha256"
if [ ! -d node_modules ]; then
  need_node_install=1
elif [ ! -f "$lock_hash_file" ]; then
  need_node_install=1
elif [ "$(cat "$lock_hash_file" 2>/dev/null || true)" != "$lock_hash" ]; then
  need_node_install=1
fi

if [ "$need_node_install" = "1" ]; then
  echo
  echo "[newProject] Installing Node dependencies..."
  npm install
  echo "$lock_hash" > "$lock_hash_file"
fi

if php -r 'exit(@fsockopen("127.0.0.1", 8001) ? 0 : 1);' >/dev/null 2>&1; then
  echo
  echo "Port 8001 is already in use."
  echo "Stop the process using it, or change the port in:"
  echo "  - .env (APP_URL)"
  echo "  - composer.json (php artisan serve --port=...)"
  exit 1
fi

echo
echo "[newProject] Waiting for MySQL on 127.0.0.1:3307..."
start_ts="$(date +%s)"
timeout_s="${MYSQL_WAIT_TIMEOUT_SECONDS:-180}"
while true; do
  php -r 'try { new PDO("mysql:host=127.0.0.1;port=3307;dbname=newproject", "newproject", "newproject", [PDO::ATTR_TIMEOUT => 2]); } catch (Throwable $e) { exit(1); } exit(0);' >/dev/null 2>&1 && break
  now="$(date +%s)"
  if [ $((now - start_ts)) -ge "$timeout_s" ]; then
    echo
    echo
    echo "Timed out waiting for MySQL."
    echo
    echo "Try these commands in a terminal:"
    echo "  cd \"$ROOT_DIR\""
    echo "  $DOCKER_COMPOSE_CMD ps"
    echo "  $DOCKER_COMPOSE_CMD logs mysql --tail=80"
    echo
    exit 1
  fi
  printf "."
  sleep 2
done
echo " OK"

echo
echo "[newProject] Running migrations..."
php artisan migrate

echo
echo "[newProject] Dev server starting on http://127.0.0.1:8001"
echo "Tip: Set GEMINI_API_KEY in .env, then run: php artisan rag:index --reset --views"
echo

composer run dev
