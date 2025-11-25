# 🔌 Connect Laravel to Database - Complete Guide

## ✅ Quick Solution (All-in-One)

Run this single command to create database, migrate, and seed:

```bash
php artisan db:create-and-setup
```

Or use the setup script:

**Windows PowerShell:**
```powershell
.\setup_complete.ps1
```

**Windows Command Prompt:**
```cmd
setup_complete.bat
```

---

## 📋 Step-by-Step Manual Setup

### Step 1: Create Database in MySQL

**Option A: Using MySQL Command Line**
```bash
mysql -u root -p
```

Then run:
```sql
CREATE DATABASE basic_template CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Option B: Using SQL File**
```bash
mysql -u root -p < database/create_database_complete.sql
```

**Option C: Using phpMyAdmin**
1. Open phpMyAdmin
2. Click "New" to create database
3. Name: `basic_template`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Step 2: Configure .env File

Open `.env` file in the root directory and update these lines:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=basic_template
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

**Important:** Replace `your_mysql_password` with your actual MySQL root password.

### Step 3: Test Database Connection

```bash
php artisan tinker
```

Then type:
```php
DB::connection()->getPdo();
```

If you see no errors, the connection is successful!

### Step 4: Run Migrations

```bash
php artisan migrate
```

This creates all tables:
- users
- roles
- entities
- otp_verifications
- migrations

### Step 5: Seed Database

```bash
php artisan db:seed
```

This creates:
- 3 Roles (Admin, Manager, User)
- 3 Entities (Head Office, Branch Office 1, Branch Office 2)
- 3 Users (admin@erp.com, manager@erp.com, user@erp.com)

---

## 🔍 Verify Connection

### Check Database Configuration

```bash
php artisan tinker
```

```php
config('database.connections.mysql.database');
config('database.connections.mysql.username');
config('database.connections.mysql.host');
```

### Test Query

```php
DB::select('SELECT DATABASE() as current_db');
```

---

## 🐛 Troubleshooting

### Error: Access Denied

**Problem:** Wrong username/password

**Solution:** Update `.env` file with correct MySQL credentials:
```env
DB_USERNAME=root
DB_PASSWORD=correct_password
```

### Error: Can't connect to MySQL server

**Problem:** MySQL service not running

**Solution:** 
- Windows: Start MySQL service from Services
- Or: `net start MySQL` (as administrator)

### Error: Unknown database

**Problem:** Database doesn't exist

**Solution:** Create database first:
```sql
CREATE DATABASE basic_template CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Error: Table already exists

**Problem:** Migrations already run

**Solution:** 
```bash
php artisan migrate:fresh --seed
```
⚠️ **Warning:** This will drop all tables and recreate them!

---

## 📝 Database Structure

After setup, you'll have:

```
basic_template
├── users (id, name, email, password, role_id, entity_id, ...)
├── roles (id, name, slug, description, ...)
├── entities (id, name, code, description, ...)
├── otp_verifications (id, user_id, otp, type, ...)
└── migrations (Laravel migration tracking)
```

---

## ✅ After Setup

1. **Start Server:**
   ```bash
   php artisan serve
   ```

2. **Visit Login Page:**
   ```
   http://localhost:8000/login
   ```

3. **Login Credentials:**
   - Email: `admin@erp.com`
   - Password: `password`

---

## 🎯 Quick Commands Reference

```bash
# Create database only
php artisan db:setup

# Create database + migrate + seed (all-in-one)
php artisan db:create-and-setup

# Run migrations only
php artisan migrate

# Seed database only
php artisan db:seed

# Fresh start (drop all tables and recreate)
php artisan migrate:fresh --seed

# Check database connection
php artisan tinker
# Then: DB::connection()->getPdo();
```

---

**Need help?** Make sure:
1. ✅ MySQL is running
2. ✅ Database credentials in `.env` are correct
3. ✅ Database `basic_template` exists
4. ✅ User has permissions to create databases

