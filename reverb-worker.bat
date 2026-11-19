@echo off
setlocal

REM ==========================================
REM Laravel Reverb - Auto Restart Loop
REM Windows Task Scheduler
REM ==========================================

cd /d "D:\Program\app-publichost\hrd_apps_server"

:loop

echo [%date% %time%] Starting Laravel Reverb... >> "D:\Program\app-publichost\hrd_apps_server\storage\logs\reverb.log"

"D:\Program\php\php.exe" artisan reverb:start >> "D:\Program\app-publichost\hrd_apps_server\storage\logs\reverb.log" 2>&1

echo [%date% %time%] Reverb stopped. Restarting in 5 seconds... >> "D:\Program\app-publichost\hrd_apps_server\storage\logs\reverb.log"

timeout /t 5 /nobreak >nul

goto loop

endlocal