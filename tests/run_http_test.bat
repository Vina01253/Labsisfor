@echo off
REM ===========================================================================
REM  [XAMPP-TWEAK] Jalankan test HTTP single-device lock.
REM  Skrip ini menyalakan PHP built-in server dengan bypass DIMATIKAN,
REM  menjalankan test, lalu mematikan servernya kembali.
REM ===========================================================================
cd /d "%~dp0\.."

set DEVICE_LOCK_DISABLE_BYPASS=1
start "PHPSRV_LOCKTEST" /b "C:\Xamppp\php\php.exe" -S 127.0.0.1:8099 tests\router.php
timeout /t 2 /nobreak >nul

set DEVICE_LOCK_DISABLE_BYPASS=1
"C:\Xamppp\php\php.exe" tests\http_device_lock.php
set RESULT=%ERRORLEVEL%

taskkill /f /im php.exe >nul 2>&1
exit /b %RESULT%
