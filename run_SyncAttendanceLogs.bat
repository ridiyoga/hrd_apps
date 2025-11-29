@echo off
chcp 65001 > nul

echo ==== DEBUG START ==== > D:\Program\app-publichost\hrd_apps_server\storage\logs\finger_sync.log 2>&1

cd /d D:\Program\app-publichost\hrd_apps_server
echo cd exit code: %errorlevel% >> D:\Program\app-publichost\hrd_apps_server\storage\logs\finger_sync.log 2>&1

"D:\Program\php\php.exe" --version >> D:\Program\app-publichost\hrd_apps_server\storage\logs\finger_sync.log 2>&1
echo php version exit code: %errorlevel% >> D:\Program\app-publichost\hrd_apps_server\storage\logs\finger_sync.log 2>&1

"D:\Program\php\php.exe" artisan finger:sync >> D:\Program\app-publichost\hrd_apps_server\storage\logs\finger_sync.log 2>&1
echo artisan exit code: %errorlevel% >> D:\Program\app-publichost\hrd_apps_server\storage\logs\finger_sync.log 2>&1

echo ==== DEBUG END ==== >> D:\Program\app-publichost\hrd_apps_server\storage\logs\finger_sync.log 2>&1
