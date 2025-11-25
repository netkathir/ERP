# Database Setup Instructions

## Issue: Unknown database 'basic_template'

The error occurs because the database doesn't exist. Follow these steps to fix it:

## Solution 1: Create Database Manually (MySQL)

1. Open MySQL command line or phpMyAdmin
2. Create the database:
   ```sql
   CREATE DATABASE basic_template CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Update `.env` file with correct database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=basic_template
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

4. Run migrations:
   ```bash
   php artisan migrate
   ```

5. Seed the database:
   ```bash
   php artisan db:seed
   ```

## Solution 2: Use Different Database Name

If you want to use a different database name:

1. Update `.env` file:
   ```env
   DB_DATABASE=your_database_name
   ```

2. Create the database in MySQL:
   ```sql
   CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. Run migrations and seeders:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

## Solution 3: Use SQLite (No Setup Required)

1. Update `.env` file:
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   ```

2. Create the SQLite file:
   ```bash
   touch database/database.sqlite
   ```

3. Run migrations:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

## Quick Fix Script

Run this command to check your current database configuration:
```bash
php artisan tinker
```
Then type:
```php
config('database.connections.mysql.database')
```

This will show you the current database name from your `.env` file.

