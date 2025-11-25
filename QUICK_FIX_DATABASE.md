# Quick Fix: Database Error

## Error Message
```
SQLSTATE[HY000] [1049] Unknown database 'basic_template'
```

## ✅ Solution (Choose One)

### Solution 1: Automatic Setup (Recommended)

Run this command to automatically create the database:

```bash
php artisan db:setup
```

Then run migrations and seeders:
```bash
php artisan migrate
php artisan db:seed
```

### Solution 2: Manual MySQL Setup

1. **Open MySQL Command Line or phpMyAdmin**

2. **Create the database:**
   ```sql
   CREATE DATABASE basic_template CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Verify your `.env` file has correct settings:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=basic_template
   DB_USERNAME=root
   DB_PASSWORD=your_password_here
   ```

4. **Run migrations:**
   ```bash
   php artisan migrate
   ```

5. **Seed the database:**
   ```bash
   php artisan db:seed
   ```

### Solution 3: Use Setup Scripts

**Windows PowerShell:**
```powershell
.\setup_database.ps1
```

**Windows Command Prompt:**
```cmd
setup_database.bat
```

### Solution 4: Use SQL File

```bash
mysql -u root -p < database/create_database.sql
```

Then:
```bash
php artisan migrate
php artisan db:seed
```

## 🔍 Verify Database Connection

Test your database connection:
```bash
php artisan tinker
```

Then type:
```php
DB::connection()->getPdo();
```

If it connects successfully, you'll see no errors.

## 📝 After Setup

Once the database is created and seeded, you can login with:

- **Email:** admin@erp.com
- **Password:** password

---

**Note:** Make sure MySQL is running and your credentials in `.env` are correct!

