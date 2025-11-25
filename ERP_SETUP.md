# ERP System Setup Guide

## 📋 Project Structure

This ERP system includes:
1. **Login Screen** - User authentication
2. **OTP Verification** - Two-factor authentication with auto-fill
3. **Dashboard** - Main interface with sidebar, logo, hamburger menu, role and entity display

## 🗂️ Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── LoginController.php      # Login functionality
│   │   │   └── OtpController.php        # OTP verification
│   │   └── DashboardController.php      # Dashboard
│   └── Middleware/
│       └── VerifyOtp.php                # OTP middleware (optional)
├── Models/
│   ├── User.php                         # User model with role/entity
│   ├── Role.php                         # Role model
│   ├── Entity.php                       # Entity model
│   └── OtpVerification.php              # OTP model
└── Services/
    └── Auth/
        └── OtpService.php               # OTP generation & verification

resources/views/
├── layouts/
│   ├── auth.blade.php                   # Auth layout (login/OTP)
│   └── dashboard.blade.php              # Dashboard layout
├── auth/
│   ├── login.blade.php                  # Login screen
│   └── otp.blade.php                    # OTP verification screen
└── dashboard/
    └── index.blade.php                  # Dashboard page

database/migrations/
├── create_roles_table.php
├── create_entities_table.php
├── create_otp_verifications_table.php
└── add_role_entity_to_users_table.php
```

## 🚀 Setup Instructions

### Quick Setup (Automated)

**Windows (PowerShell):**
```powershell
.\setup_database.ps1
```

**Windows (Command Prompt):**
```cmd
setup_database.bat
```

**Manual Setup:**

### 1. Create Database

**Option A: Using Artisan Command**
```bash
php artisan db:setup
```

**Option B: Manual MySQL**
```sql
CREATE DATABASE basic_template CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

**Option C: Using SQL File**
```bash
mysql -u root -p < database/create_database.sql
```

### 2. Configure .env File

Make sure your `.env` file has correct database settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=basic_template
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Seed Database

```bash
php artisan db:seed
```

This will create:
- **Roles**: Admin, Manager, User
- **Entities**: Head Office, Branch Office 1, Branch Office 2
- **Users**: 
  - admin@erp.com / password
  - manager@erp.com / password
  - user@erp.com / password

### 3. Start Server

```bash
php artisan serve
```

## 🔐 Login Flow

1. **Login Screen** (`/login`)
   - Enter email and password
   - System validates credentials
   - Generates OTP and redirects to OTP screen

2. **OTP Screen** (`/otp`)
   - OTP is auto-filled in the input field
   - User can manually enter or use auto-filled OTP
   - Click "Verify & Continue" button
   - System verifies OTP and logs in user

3. **Dashboard** (`/dashboard`)
   - Left sidebar with logo and hamburger menu (☰)
   - Top right shows Role name and Entity name
   - Responsive design

## 📝 Features

### Login Screen
- Clean, modern design
- Email and password fields
- Form validation
- Error handling

### OTP Screen
- **Auto-fill OTP** in input field
- 6-digit OTP format
- Auto-submit capability (optional)
- Input validation
- Expiration handling (10 minutes)

### Dashboard
- **Sidebar Navigation**
  - Logo on top left
  - Hamburger menu (☰) to toggle sidebar
  - Menu items: Dashboard, Reports, Settings, Logout
- **Top Header**
  - Hamburger menu button
  - Role badge (right side)
  - Entity badge (right side)
- **Responsive Design**
  - Mobile-friendly
  - Sidebar collapses on mobile

## 🔧 Configuration

### OTP Settings
- OTP length: 6 digits
- Expiration: 10 minutes
- Auto-fill: Enabled

### User Roles & Entities
- Users can have one role and one entity
- Displayed as badges in dashboard header

## 📧 Test Credentials

| Email | Password | Role | Entity |
|-------|----------|------|--------|
| admin@erp.com | password | Admin | Head Office |
| manager@erp.com | password | Manager | Branch Office 1 |
| user@erp.com | password | User | Branch Office 1 |

## 🎨 UI Features

- **Gradient Background** - Purple gradient for auth pages
- **Card-based Design** - Clean white cards
- **Color-coded Badges** - Role (purple) and Entity (green)
- **Smooth Transitions** - Sidebar toggle animations
- **Responsive Layout** - Works on all devices

## 🔄 Routes

- `GET /` - Redirects to login
- `GET /login` - Show login form
- `POST /login` - Process login
- `GET /otp` - Show OTP form
- `POST /otp/verify` - Verify OTP
- `GET /dashboard` - Dashboard (protected)
- `POST /logout` - Logout

## 📦 Dependencies

All standard Laravel dependencies. No additional packages required.

---

**Version**: 1.0  
**Last Updated**: 2024

