# ✅ Database Connection - Complete Solution

## 🎯 Problem Solved

**Error:** `Database not found. Please run: php artisan db:setup`

**Solution:** Created automated setup that creates database, connects Laravel, and sets up everything.

---

## ⚡ QUICK FIX (Recommended)

### Run This One Command:

```bash
php artisan db:create-and-setup
```

**This will:**
1. ✅ Create the `basic_template` database in MySQL
2. ✅ Connect Laravel to the database
3. ✅ Run all migrations (create tables)
4. ✅ Seed the database (create users, roles, entities)

**Done!** Your ERP system is ready to use.

---

## 🔧 Alternative: Step-by-Step

### Step 1: Create Database in MySQL

**Method 1: Using Artisan**
```bash
php artisan db:setup
```

**Method 2: Using MySQL Command Line**
```bash
mysql -u root -p
```
Then type:
```sql
CREATE DATABASE basic_template CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Method 3: Using SQL File**
```bash
mysql -u root -p < database/create_database_complete.sql
```

**Method 4: Using phpMyAdmin**
1. Open phpMyAdmin
2. Click "New"
3. Database name: `basic_template`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

### Step 2: Configure .env File

Open `.env` file and make sure it has:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=basic_template
DB_USERNAME=root
DB_PASSWORD=your_mysql_password
```

**Important:** Replace `your_mysql_password` with your actual MySQL password.

### Step 3: Test Connection

```bash
php test_database_connection.php
```

This will show you if the connection works.

### Step 4: Run Migrations

```bash
php artisan migrate
```

### Step 5: Seed Database

```bash
php artisan db:seed
```

---

## 📋 Available Commands

| Command | Description |
|---------|-------------|
| `php artisan db:create-and-setup` | **All-in-one:** Create DB + Migrate + Seed |
| `php artisan db:setup` | Create database only |
| `php artisan migrate` | Run migrations only |
| `php artisan db:seed` | Seed database only |
| `php test_database_connection.php` | Test database connection |

---

## 🎬 Setup Scripts

**Windows PowerShell:**
```powershell
.\setup_complete.ps1
```

**Windows Command Prompt:**
```cmd
setup_complete.bat
```

Both scripts do everything automatically!

---

## ✅ Verify Setup

After running the setup, verify everything works:

1. **Test Connection:**
   ```bash
   php test_database_connection.php
   ```

2. **Check Tables:**
   ```bash
   php artisan tinker
   ```
   Then:
   ```php
   DB::select('SHOW TABLES');
   ```

3. **Start Server:**
   ```bash
   php artisan serve
   ```

4. **Visit Login:**
   ```
   http://localhost:8000/login
   ```

5. **Login:**
   - Email: `admin@erp.com`
   - Password: `password`

---

## 🗄️ Database Structure

After setup, you'll have:

```
basic_template
├── users (with role_id, entity_id)
├── roles (Admin, Manager, User)
├── entities (Head Office, Branch Offices)
├── otp_verifications
└── migrations
```

---

## 🐛 Troubleshooting

### Error: Access Denied
**Fix:** Update `.env` with correct MySQL username/password

### Error: Can't connect to MySQL
**Fix:** Start MySQL service
- Windows: Services → MySQL → Start
- Or: `net start MySQL` (as admin)

### Error: Unknown database
**Fix:** Run `php artisan db:setup`

### Error: Table already exists
**Fix:** 
```bash
php artisan migrate:fresh --seed
```
⚠️ This deletes all data!

---

## 📚 Documentation Files

- `QUICK_SETUP.md` - Quick reference
- `CONNECT_DATABASE.md` - Detailed guide
- `SOLUTION_DATABASE.md` - This file (complete solution)

---

## 🎉 Success!

Once you see:
- ✅ Database created
- ✅ Migrations completed
- ✅ Database seeded

You're all set! The ERP system is ready to use.

---

**Next Steps:**
1. Run: `php artisan db:create-and-setup`
2. Start: `php artisan serve`
3. Login: `http://localhost:8000/login`

