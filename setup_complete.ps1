Clear-Host
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "ERP System - Complete Database Setup" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "This script will:" -ForegroundColor Yellow
Write-Host "1. Create the database" -ForegroundColor White
Write-Host "2. Run migrations" -ForegroundColor White
Write-Host "3. Seed the database" -ForegroundColor White
Write-Host ""

$response = Read-Host "Press Enter to continue or Ctrl+C to cancel"

Write-Host ""
Write-Host "Starting setup..." -ForegroundColor Yellow
Write-Host ""

php artisan db:create-and-setup

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "==========================================" -ForegroundColor Green
    Write-Host "Setup completed successfully!" -ForegroundColor Green
    Write-Host "==========================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "You can now start the server with:" -ForegroundColor Cyan
    Write-Host "  php artisan serve" -ForegroundColor White
    Write-Host ""
    Write-Host "Then visit: http://localhost:8000/login" -ForegroundColor Cyan
    Write-Host ""
} else {
    Write-Host ""
    Write-Host "==========================================" -ForegroundColor Red
    Write-Host "Setup failed. Please check the errors above." -ForegroundColor Red
    Write-Host "==========================================" -ForegroundColor Red
    Write-Host ""
    Write-Host "Manual setup:" -ForegroundColor Yellow
    Write-Host "1. Create database in MySQL" -ForegroundColor White
    Write-Host "2. Update .env file with database credentials" -ForegroundColor White
    Write-Host "3. Run: php artisan migrate" -ForegroundColor White
    Write-Host "4. Run: php artisan db:seed" -ForegroundColor White
    Write-Host ""
}

Read-Host "Press Enter to continue"

