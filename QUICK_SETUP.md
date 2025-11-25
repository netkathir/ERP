# 🚀 Quick Setup - Database Connection

## ⚡ Fastest Solution (One Command)

```bash
php artisan db:create-and-setup
```

This single command will:
1. ✅ Create the database
2. ✅ Run all migrations
3. ✅ Seed the database with sample data

**That's it!** You're ready to go.

---

## 🔍 Test Your Connection

Before or after setup, test your database connection:

```bash
php test_database_connection.php
```

This will show you:
- Current database configuration
- Connection status
- Whether database exists
- List of tables

---

## 📋 Manual Setup (If Needed)

### 1. Create Database in MySQL

**Option A: Command Line**
```bash
mysql -u root -p
```
Then:
```sql
CREATE DATABASE basic_template CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Option B: SQL File**
```bash
mysql -u root -p < database/create_database_complete.sql
```

### 2. Update .env File

Make sure your `.env` has:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=basic_template
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Run Setup

```bash
php artisan migrate
php artisan db:seed
```

---

## 🎯 Setup Scripts

**Windows PowerShell:**
```powershell
.\setup_complete.ps1
```

**Windows Command Prompt:**
```cmd
setup_complete.bat
```

---

## ✅ Verify Everything Works

1. **Test Connection:**
   ```bash
   php test_database_connection.php
   ```

2. **Start Server:**
   ```bash
   php artisan serve
   ```

3. **Visit:**
   ```
   http://localhost:8000/login
   ```

4. **Login:**
   - Email: `admin@erp.com`
   - Password: `password`

---

## 🐛 Common Issues

### "Access Denied"
→ Check `.env` file - wrong username/password

### "Can't connect to MySQL"
→ Start MySQL service

### "Unknown database"
→ Run: `php artisan db:setup`

---

**Need more help?** See `CONNECT_DATABASE.md` for detailed instructions.

