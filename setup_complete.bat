@echo off
cls
echo ==========================================
echo ERP System - Complete Database Setup
echo ==========================================
echo.

echo This script will:
echo 1. Create the database
echo 2. Run migrations
echo 3. Seed the database
echo.
pause

echo.
echo Starting setup...
echo.

php artisan db:create-and-setup

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ==========================================
    echo Setup completed successfully!
    echo ==========================================
    echo.
    echo You can now start the server with:
    echo   php artisan serve
    echo.
    echo Then visit: http://localhost:8000/login
    echo.
) else (
    echo.
    echo ==========================================
    echo Setup failed. Please check the errors above.
    echo ==========================================
    echo.
    echo Manual setup:
    echo 1. Create database in MySQL
    echo 2. Update .env file with database credentials
    echo 3. Run: php artisan migrate
    echo 4. Run: php artisan db:seed
    echo.
)

pause

