
@echo off
chcp 65001 > nul

set "APP_DIR=D:\Program\app-publichost\hrd_apps_server"
set "PHP=D:\Program\php\php.exe"
set "LOG=%APP_DIR%\storage\logs\auto-close-scheduler.log"

cd /d "%APP_DIR%"

echo ==== SCHEDULER START %date% %time% ==== >> "%LOG%" 2>&1

"%PHP%" artisan schedule:run >> "%LOG%" 2>&1

echo Scheduler exit code: %errorlevel% >> "%LOG%" 2>&1
echo ==== SCHEDULER END ==== >> "%LOG%" 2>&1
