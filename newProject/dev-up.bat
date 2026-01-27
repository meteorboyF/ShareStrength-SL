@echo off
setlocal enabledelayedexpansion

cd /d %~dp0

for /f "delims=" %%v in ('node -p "process.versions.node" 2^>nul') do set NODE_VER=%%v
if "%NODE_VER%"=="" (
  echo Node.js is required but was not found in PATH.
  pause
  exit /b 1
)
for /f "tokens=1 delims=." %%a in ("%NODE_VER%") do set NODE_MAJOR=%%a
if %NODE_MAJOR% LSS 18 (
  echo Node.js 18+ is required. Found %NODE_VER%.
  pause
  exit /b 1
)

echo.
echo [newProject] Starting MySQL + Qdrant (Docker)...
docker compose up -d
if errorlevel 1 (
  docker-compose up -d
)

if not exist .env (
  echo Copying .env.example to .env...
  copy .env.example .env >nul
)

findstr /R "^APP_KEY=$" .env >nul
if %errorlevel%==0 (
  echo Generating APP_KEY...
  php artisan key:generate
)

echo.
echo Waiting for MySQL on 127.0.0.1:3307...
:waitmysql
php -r "try{new PDO('mysql:host=127.0.0.1;port=3307;dbname=newproject','newproject','newproject');}catch(Exception $e){exit(1);} exit(0);" >nul 2>nul
if errorlevel 1 (
  timeout /t 2 /nobreak >nul
  goto waitmysql
)

echo Running migrations...
php artisan migrate

echo Installing npm dependencies...
npm install

echo.
echo Starting Vite + Laravel (two windows)...
start "Vite" cmd /k "npm run dev"
start "Laravel" cmd /k "php artisan serve --host=127.0.0.1 --port=8001"

echo.
echo Done. Open http://127.0.0.1:8001
echo Reminder: set GEMINI_API_KEY in .env for the chatbot.

endlocal
