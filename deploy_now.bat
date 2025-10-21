@echo off
echo ========================================
echo    FlippDeal - Hostinger Deployment
echo ========================================
echo.

echo Step 1: Preparing project for deployment...
call deploy_to_hostinger.bat

echo.
echo Step 2: Uploading to Hostinger...
echo Server: 157.173.216.254:65002
echo User: u248666255
echo.

REM Run the PowerShell script with your specific details
powershell -ExecutionPolicy Bypass -File "upload_to_hostinger.ps1" -HostingerHost "157.173.216.254" -Username "u248666255" -Port 65002 -RemotePath "/home/u248666255/domains/flippdeal.com/public_html"

echo.
echo ========================================
echo Deployment process completed!
echo ========================================
echo.
echo Next steps:
echo 1. SSH into your server: ssh -p 65002 u248666255@157.173.216.254
echo 2. Navigate to: cd /public_html
echo 3. Set permissions: chmod -R 755 storage bootstrap/cache
echo 4. Configure .env file with your database details
echo 5. Run: php artisan config:cache
echo.
pause
