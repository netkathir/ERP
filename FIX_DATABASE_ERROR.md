# 🔧 Fix Database Error - Unknown database 'basic_template'

## Problem
You're seeing this error:
```
SQLSTATE[HY000] [1049] Unknown database 'basic_template'
```

This happens because the database doesn't exist yet.

## ✅ Quick Fix (3 Steps)

### Step 1: Create Database
Run this command:
```bash
php artisan db:setup
```

This will automatically create the `basic_template` database.

### Step 2: Run Migrations
```bash
php artisan migrate
```

This creates all the necessary tables.

### Step 3: Seed Database
```bash
php artisan db:seed
```

This creates sample users, roles, and entities.

## 🎉 Done!

After these 3 steps, you can:
1. Start the server: `php artisan serve`
2. Go to: `http://localhost:8000/login`
3. Login with: `admin@erp.com` / `password`

---

## Alternative: Manual Setup

If the automatic command doesn't work:

1. **Open MySQL** (command line or phpMyAdmin)

2. **Run this SQL:**
   ```sql
   CREATE DATABASE basic_template CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Check your `.env` file:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=basic_template
   DB_USERNAME=root
   DB_PASSWORD=your_mysql_password
   ```

4. **Then run:**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

---

## 📋 What Was Fixed

1. ✅ Created `db:setup` command to auto-create database
2. ✅ Added error handling in LoginController for database errors
3. ✅ Created setup scripts (`.bat` and `.ps1`) for Windows
4. ✅ Created SQL file for manual database creation
5. ✅ Updated documentation with clear instructions

---

**Need help?** Check `QUICK_FIX_DATABASE.md` for more details.

