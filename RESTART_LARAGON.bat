@echo off
REM ==============================================================
REM LARAGON FORCE RESTART & CACHE CLEANUP
REM ==============================================================

echo [*] Killing all Apache and MySQL processes...
taskkill /F /IM apache2.exe 2>nul
taskkill /F /IM httpd.exe 2>nul
taskkill /F /IM mysqld.exe 2>nul
taskkill /F /IM php.exe 2>nul

echo [*] Waiting 5 seconds...
timeout /T 5 /NOBREAK

echo [*] Clearing Laravel caches...
cd /d C:\laragon\www\perjalanan-dinas
C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe artisan cache:clear
C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe artisan config:clear
C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe artisan view:clear
C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe artisan route:clear

echo [*] Deleting log files...
del /Q storage\logs\*.log 2>nul

echo [*] Checking php.ini configuration...
C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe -i | find "curl.cainfo"
C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe -i | find "openssl.cafile"

echo [*] Starting Laragon services...
cd /d C:\laragon
call laragon stop
timeout /T 3 /NOBREAK
call laragon start

echo [*] Waiting for services to start...
timeout /T 5 /NOBREAK

echo [*] Testing Google Drive connection...
cd /d C:\laragon\www\perjalanan-dinas
C:\laragon\bin\php\php-8.3.16-Win32-vs16-x64\php.exe artisan test:google-drive

echo.
echo ===== DONE =====
echo If test passed, try upload again in browser
pause
