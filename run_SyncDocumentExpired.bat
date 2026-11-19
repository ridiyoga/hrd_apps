@echo off
chcp 65001 > nul

echo ==== DEBUG START ==== > D:\Program\app-publichost\hrd_apps_server\storage\logs\document_expired.log 2>&1

cd /d D:\Program\app-publichost\hrd_apps_server
echo cd exit code: %errorlevel% >> D:\Program\app-publichost\hrd_apps_server\storage\logs\document_expired.log 2>&1

"D:\Program\php\php.exe" --version >> D:\Program\app-publichost\hrd_apps_server\storage\logs\document_expired.log 2>&1
echo php version exit code: %errorlevel% >> D:\Program\app-publichost\hrd_apps_server\storage\logs\document_expired.log 2>&1

"D:\Program\php\php.exe" artisan dokumen:send-expiry-reminder-email >> D:\Program\app-publichost\hrd_apps_server\storage\logs\document_expired.log 2>&1
echo artisan exit code: %errorlevel% >> D:\Program\app-publichost\hrd_apps_server\storage\logs\document_expired.log 2>&1

"D:\Program\php\php.exe" artisan dokumen:sync-expired >> D:\Program\app-publichost\hrd_apps_server\storage\logs\document_expired.log 2>&1
echo artisan exit code: %errorlevel% >> D:\Program\app-publichost\hrd_apps_server\storage\logs\document_expired.log 2>&1

echo ==== DEBUG END ==== >> D:\Program\app-publichost\hrd_apps_server\storage\logs\document_expired.log 2>&1
