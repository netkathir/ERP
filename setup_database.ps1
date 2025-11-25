Write-Host "========================================" -ForegroundColor Cyan
Write-Host "ERP System - Database Setup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Step 1: Creating database..." -ForegroundColor Yellow
php artisan db:setup

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "Step 2: Running migrations..." -ForegroundColor Yellow
    php artisan migrate
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "Step 3: Seeding database..." -ForegroundColor Yellow
        php artisan db:seed
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host ""
            Write-Host "========================================" -ForegroundColor Green
            Write-Host "Database setup completed successfully!" -ForegroundColor Green
            Write-Host "========================================" -ForegroundColor Green
            Write-Host ""
            Write-Host "You can now login with:" -ForegroundColor Cyan
            Write-Host "Email: admin@erp.com" -ForegroundColor White
            Write-Host "Password: password" -ForegroundColor White
            Write-Host ""
        } else {
            Write-Host ""
            Write-Host "Error: Database seeding failed" -ForegroundColor Red
        }
    } else {
        Write-Host ""
        Write-Host "Error: Migration failed" -ForegroundColor Red
    }
} else {
    Write-Host ""
    Write-Host "Error: Database creation failed" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please create the database manually:" -ForegroundColor Yellow
    Write-Host "1. Open MySQL command line or phpMyAdmin" -ForegroundColor White
    Write-Host "2. Run: CREATE DATABASE basic_template CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" -ForegroundColor White
    Write-Host "3. Then run: php artisan migrate" -ForegroundColor White
    Write-Host "4. Then run: php artisan db:seed" -ForegroundColor White
}

Write-Host ""
Read-Host "Press Enter to continue"

