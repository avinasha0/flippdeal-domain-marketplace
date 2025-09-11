@echo off
echo Preparing Laravel project for Hostinger deployment...

REM Create deployment directory
if exist "deployment" rmdir /s /q "deployment"
mkdir "deployment"

REM Copy essential files and directories
echo Copying application files...
xcopy "app" "deployment\app" /E /I /Y
xcopy "bootstrap" "deployment\bootstrap" /E /I /Y
xcopy "config" "deployment\config" /E /I /Y
xcopy "database" "deployment\database" /E /I /Y
xcopy "public" "deployment\public" /E /I /Y
xcopy "resources" "deployment\resources" /E /I /Y
xcopy "routes" "deployment\routes" /E /I /Y
xcopy "storage" "deployment\storage" /E /I /Y
xcopy "vendor" "deployment\vendor" /E /I /Y

REM Copy essential root files
copy "artisan" "deployment\artisan" /Y
copy "composer.json" "deployment\composer.json" /Y
copy "composer.lock" "deployment\composer.lock" /Y
copy "package.json" "deployment\package.json" /Y
copy "package-lock.json" "deployment\package-lock.json" /Y
copy "vite.config.js" "deployment\vite.config.js" /Y
copy "tailwind.config.js" "deployment\tailwind.config.js" /Y
copy "postcss.config.js" "deployment\postcss.config.js" /Y
copy "phpunit.xml" "deployment\phpunit.xml" /Y

REM Copy .env.example as .env for production
if exist ".env.example" copy ".env.example" "deployment\.env" /Y

REM Create .htaccess for public directory if it doesn't exist
if not exist "deployment\public\.htaccess" (
    echo Creating .htaccess file...
    echo RewriteEngine On > "deployment\public\.htaccess"
    echo RewriteRule ^(.*)$ index.php [QSA,L] >> "deployment\public\.htaccess"
)

echo.
echo Deployment package created in 'deployment' folder
echo.
echo Next steps:
echo 1. Connect to your Hostinger server via SSH
echo 2. Navigate to your domain's public_html directory
echo 3. Upload the contents of the 'deployment' folder
echo 4. Set proper permissions and configure .env file
echo.
pause
