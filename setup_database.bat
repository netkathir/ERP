@echo off
echo ========================================
echo ERP System - Database Setup
echo ========================================
echo.

echo Step 1: Creating database...
php artisan db:setup

if %ERRORLEVEL% EQU 0 (
    echo.
    echo Step 2: Running migrations...
    php artisan migrate
    
    if %ERRORLEVEL% EQU 0 (
        echo.
        echo Step 3: Seeding database...
        php artisan db:seed
        
        if %ERRORLEVEL% EQU 0 (
            echo.
            echo ========================================
            echo Database setup completed successfully!
            echo ========================================
            echo.
            echo You can now login with:
            echo Email: admin@erp.com
            echo Password: password
            echo.
        ) else (
            echo.
            echo Error: Database seeding failed
        )
    ) else (
        echo.
        echo Error: Migration failed
    )
) else (
    echo.
    echo Error: Database creation failed
    echo.
    echo Please create the database manually:
    echo 1. Open MySQL command line or phpMyAdmin
    echo 2. Run: CREATE DATABASE basic_template CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
    echo 3. Then run: php artisan migrate
    echo 4. Then run: php artisan db:seed
)

pause

