# Laravel CRUD Architecture - Quick Reference

## 🎯 Overview

This document provides a quick reference for the Laravel CRUD application architecture using a **Product Management System** as an example.

---

## 📁 Essential Files Structure

```
app/
├── Http/Controllers/ProductController.php    # Business Logic
├── Models/Product.php                        # Data Model
└── ...

database/
└── migrations/YYYY_MM_DD_create_products_table.php  # Database Schema

resources/views/
├── layouts/app.blade.php                     # Master Layout
└── products/
    ├── index.blade.php                      # List View
    ├── create.blade.php                     # Create Form
    ├── edit.blade.php                       # Edit Form
    └── show.blade.php                       # Detail View

routes/
└── web.php                                   # Route Definitions

config/
└── database.php                             # DB Configuration

.env                                         # Environment Variables
```

---

## 🔄 CRUD Operations Mapping

| Operation | HTTP Method | Route | Controller Method | View | SQL |
|-----------|-------------|-------|-------------------|------|-----|
| **List** | GET | `/products` | `index()` | `index.blade.php` | `SELECT *` |
| **Create Form** | GET | `/products/create` | `create()` | `create.blade.php` | - |
| **Store** | POST | `/products` | `store()` | Redirect | `INSERT` |
| **Show** | GET | `/products/{id}` | `show()` | `show.blade.php` | `SELECT WHERE id` |
| **Edit Form** | GET | `/products/{id}/edit` | `edit()` | `edit.blade.php` | `SELECT WHERE id` |
| **Update** | PUT | `/products/{id}` | `update()` | Redirect | `UPDATE WHERE id` |
| **Delete** | DELETE | `/products/{id}` | `destroy()` | Redirect | `DELETE WHERE id` |

---

## 🔌 Database Connection Chain

```
.env
  ↓
config/database.php
  ↓
DatabaseServiceProvider
  ↓
PDO Connection
  ↓
MySQL Database
```

**Key Configuration Points:**
- `.env`: `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `config/database.php`: Connection array and default connection
- Models use default connection automatically

---

## 🏗️ Request Flow (Simplified)

```
Browser Request
    ↓
routes/web.php (Route Matching)
    ↓
ProductController (Method Execution)
    ↓
Product Model (Data Access)
    ↓
Database (Query Execution)
    ↓
Product Model (Data Return)
    ↓
ProductController (Process & Return View)
    ↓
Blade Template (Render HTML)
    ↓
Browser Response
```

---

## 📊 MVC Pattern in Laravel

### Model (M)
- **Location**: `app/Models/Product.php`
- **Purpose**: Data structure, database interaction
- **Features**: Eloquent ORM, relationships, accessors/mutators

### View (V)
- **Location**: `resources/views/products/*.blade.php`
- **Purpose**: User interface, data presentation
- **Features**: Blade templating, layouts, components

### Controller (C)
- **Location**: `app/Http/Controllers/ProductController.php`
- **Purpose**: Business logic, request handling
- **Features**: Validation, data processing, response generation

---

## 🔐 Security Features

1. **CSRF Protection**: Automatic token validation
2. **SQL Injection**: Protected by Eloquent ORM (prepared statements)
3. **XSS Protection**: Blade auto-escaping
4. **Mass Assignment**: `$fillable` or `$guarded` in models
5. **Input Validation**: Request validation rules

---

## 📝 Route Definition Options

### Option 1: Resource Route (Recommended)
```php
Route::resource('products', ProductController::class);
```
Creates all 7 CRUD routes automatically.

### Option 2: Individual Routes
```php
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/create', [ProductController::class, 'create']);
Route::post('/products', [ProductController::class, 'store']);
// ... etc
```

---

## 🗄️ Database Schema Example

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();                    // Primary key
    $table->string('name');          // Product name
    $table->text('description');     // Description
    $table->decimal('price', 10, 2); // Price
    $table->integer('quantity');     // Stock quantity
    $table->timestamps();            // created_at, updated_at
});
```

---

## 🎨 View Structure

### Master Layout (`layouts/app.blade.php`)
```blade
<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
</head>
<body>
    @include('partials.header')
    <main>
        @yield('content')
    </main>
    @include('partials.footer')
</body>
</html>
```

### Child View (`products/index.blade.php`)
```blade
@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <!-- Product list content -->
@endsection
```

---

## 🔄 Complete CRUD Cycle

```
┌─────────┐
│  CREATE │ → User fills form → Store in DB → Redirect to list
└─────────┘

┌─────────┐
│   READ  │ → Fetch from DB → Display in view
└─────────┘

┌─────────┐
│  UPDATE │ → Load existing → Edit form → Update in DB → Redirect
└─────────┘

┌─────────┐
│  DELETE │ → Confirm → Delete from DB → Redirect to list
└─────────┘
```

---

## 📚 Key Laravel Concepts Used

1. **Eloquent ORM**: Object-relational mapping for database
2. **Route Model Binding**: Automatic model resolution from route parameters
3. **Blade Templating**: Server-side templating engine
4. **Service Container**: Dependency injection
5. **Middleware**: Request filtering (CSRF, auth, etc.)
6. **Migrations**: Version control for database schema
7. **Validation**: Request validation rules

---

## 🚀 Implementation Checklist

- [ ] Create database migration
- [ ] Create Product model
- [ ] Create ProductController
- [ ] Define routes (web.php)
- [ ] Create master layout
- [ ] Create index view (list)
- [ ] Create create view (form)
- [ ] Create show view (details)
- [ ] Create edit view (form)
- [ ] Add validation
- [ ] Add success/error messages
- [ ] Test all CRUD operations

---

## 📖 Related Documents

1. **ARCHITECTURE.md** - Detailed architecture documentation
2. **FOLDER_STRUCTURE.md** - Complete folder structure with descriptions
3. **FLOWCHART.md** - Detailed flowcharts for all operations

---

**Quick Reference Version**: 1.0  
**Last Updated**: 2024

