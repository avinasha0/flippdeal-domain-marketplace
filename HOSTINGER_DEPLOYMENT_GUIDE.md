# Hostinger Deployment Guide

## Prerequisites

1. **SSH Access**: Make sure you have SSH access enabled in your Hostinger control panel
2. **SCP/SFTP Client**: You can use built-in Windows tools or download WinSCP/PuTTY
3. **Domain Setup**: Your domain should be pointing to Hostinger

## Step 1: Prepare Your Project

1. Run the deployment preparation script:
   ```cmd
   deploy_to_hostinger.bat
   ```
   This will create a `deployment` folder with only the necessary files.

## Step 2: Upload Files

### Option A: Using the provided script
1. Run the upload script:
   ```cmd
   upload.bat
   ```
2. Enter your Hostinger details when prompted

### Option B: Manual upload using SCP
```cmd
scp -r deployment/* yourusername@yourdomain.com:/public_html/
```

### Option C: Using WinSCP (GUI)
1. Download and install WinSCP
2. Connect to your Hostinger server
3. Navigate to `/public_html/`
4. Upload all contents from the `deployment` folder

## Step 3: Configure Server

SSH into your Hostinger server and run these commands:

```bash
# Navigate to your domain directory
cd /public_html

# Set proper permissions
chmod -R 755 storage bootstrap/cache
chmod 644 .env

# Install dependencies (if needed)
composer install --optimize-autoloader --no-dev

# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Step 4: Configure Environment

1. Edit your `.env` file with production settings:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

2. Set up your database in Hostinger control panel

## Step 5: Set Document Root

In your Hostinger control panel:
1. Go to "Advanced" → "File Manager"
2. Navigate to `/public_html/`
3. Move all files from `public/` folder to the root of `public_html/`
4. Update your `.htaccess` file if needed

## Important Notes

- Make sure your Laravel application is configured for the correct document root
- Test your application thoroughly after deployment
- Keep backups of your files
- Monitor your application logs for any issues

## Troubleshooting

### Common Issues:
1. **500 Error**: Check file permissions and .env configuration
2. **Database Connection**: Verify database credentials in .env
3. **File Not Found**: Ensure document root is set correctly
4. **Permission Denied**: Run `chmod -R 755 storage bootstrap/cache`

### Getting Help:
- Check Hostinger's documentation
- Review Laravel deployment guides
- Check your application logs in `storage/logs/`
