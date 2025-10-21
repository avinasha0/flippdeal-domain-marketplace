# PowerShell script to upload files to Hostinger via SCP
# Make sure you have the deployment folder ready first

param(
    [Parameter(Mandatory=$true)]
    [string]$HostingerHost,
    
    [Parameter(Mandatory=$true)]
    [string]$Username,
    
    [Parameter(Mandatory=$false)]
    [int]$Port = 65002,
    
    [Parameter(Mandatory=$true)]
    [string]$RemotePath = "/public_html"
)

Write-Host "Starting upload to Hostinger..." -ForegroundColor Green

# Check if deployment folder exists
if (-not (Test-Path "deployment")) {
    Write-Host "Error: 'deployment' folder not found. Please run deploy_to_hostinger.bat first." -ForegroundColor Red
    exit 1
}

# Upload files using SCP
Write-Host "Uploading files to $HostingerHost`:$RemotePath" -ForegroundColor Yellow

try {
    # Upload the entire deployment folder contents
    scp -P $Port -r deployment/* "$Username@$HostingerHost`:$RemotePath"
    
    Write-Host "Upload completed successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps on your Hostinger server:" -ForegroundColor Cyan
    Write-Host "1. SSH into your server: ssh -p $Port $Username@$HostingerHost" -ForegroundColor White
    Write-Host "2. Navigate to your domain directory: cd $RemotePath" -ForegroundColor White
    Write-Host "3. Set proper permissions:" -ForegroundColor White
    Write-Host "   chmod -R 755 storage bootstrap/cache" -ForegroundColor Gray
    Write-Host "   chmod 644 .env" -ForegroundColor Gray
    Write-Host "4. Configure your .env file with production settings" -ForegroundColor White
    Write-Host "5. Run: php artisan config:cache" -ForegroundColor White
    Write-Host "6. Run: php artisan route:cache" -ForegroundColor White
    Write-Host "7. Run: php artisan view:cache" -ForegroundColor White
    
} catch {
    Write-Host "Error during upload: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "Make sure you have:" -ForegroundColor Yellow
    Write-Host "- SSH access to your Hostinger account" -ForegroundColor White
    Write-Host "- SCP installed on your system" -ForegroundColor White
    Write-Host "- Correct hostname, username, and path" -ForegroundColor White
}
