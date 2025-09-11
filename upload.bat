@echo off
echo Hostinger Upload Script
echo =====================
echo.

REM Pre-configured Hostinger details
set HOST=157.173.216.254
set USER=u248666255
set PORT=65002
set REMOTE_PATH=/home/u248666255/domains/flippdeal.com/public_html

echo.
echo Uploading to: %USER%@%HOST%:%PORT%:%REMOTE_PATH%
echo.

REM Run the PowerShell script
powershell -ExecutionPolicy Bypass -File "upload_to_hostinger.ps1" -HostingerHost "%HOST%" -Username "%USER%" -Port "%PORT%" -RemotePath "%REMOTE_PATH%"

pause
