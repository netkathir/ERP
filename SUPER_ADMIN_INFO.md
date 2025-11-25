# 👤 Super Admin User - Default Credentials

## ✅ Super Admin Created

A default **Super Admin** user has been created in the database.

### Login Credentials

- **Email:** `admin@gmail.com`
- **Password:** `Admin@123`
- **Name:** Super Admin
- **Role:** Admin
- **Entity:** Head Office

---

## 🔄 How It Works

### Automatic Creation

The Super Admin user is automatically created when you run:

```bash
php artisan db:seed
```

Or when using the all-in-one setup:

```bash
php artisan db:create-and-setup
```

### Seeder File

The Super Admin is created by:
- `database/seeders/SuperAdminSeeder.php` - Creates Super Admin user
- `database/seeders/DatabaseSeeder.php` - Calls SuperAdminSeeder

### Update or Create

The seeder uses `updateOrCreate()` which means:
- ✅ If user doesn't exist → Creates new user
- ✅ If user exists → Updates password and details
- ✅ Safe to run multiple times

---

## 🚀 Usage

1. **Run migrations and seeders:**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

2. **Start server:**
   ```bash
   php artisan serve
   ```

3. **Login:**
   - Visit: `http://localhost:8000/login`
   - Email: `admin@gmail.com`
   - Password: `Admin@123`

---

## 📝 All Available Users

After seeding, you'll have these users:

| Email | Password | Role | Entity |
|-------|----------|------|--------|
| **admin@gmail.com** | **Admin@123** | **Admin** | **Head Office** |
| admin@erp.com | password | Admin | Head Office |
| manager@erp.com | password | Manager | Branch Office 1 |
| user@erp.com | password | User | Branch Office 1 |

---

## 🔧 Update Super Admin

To update the Super Admin password or details, edit:

`database/seeders/SuperAdminSeeder.php`

Then run:
```bash
php artisan db:seed --class=SuperAdminSeeder
```

---

## ✅ Verification

To verify Super Admin was created:

```bash
php artisan tinker
```

Then:
```php
$user = \App\Models\User::where('email', 'admin@gmail.com')->first();
$user->name; // Should show "Super Admin"
$user->role->name; // Should show "Admin"
```

---

**Note:** The Super Admin user is created automatically on every `db:seed` run, ensuring it always exists with the correct credentials.

