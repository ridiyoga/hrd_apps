@echo off
setlocal
REM ==========================================
REM Laravel Queue Worker - Auto Restart Loop
REM Windows Task Scheduler
REM ==========================================

cd /d "D:\Program\app-publichost\hrd_apps_server"

:loop
"D:\Program\php\php.exe" artisan queue:work database ^
    --sleep=3 ^
    --tries=3 ^
    --timeout=60 ^
    --backoff=10,30,60 ^
    --max-time=3600 >> "D:\Program\app-publichost\hrd_apps_server\storage\logs\queue-worker.log" 2>&1

REM jeda kecil biar tidak restart terlalu cepat kalau langsung error
timeout /t 5 /nobreak >nul

goto loop

endlocal