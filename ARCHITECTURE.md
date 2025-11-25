# Laravel CRUD Application Architecture Documentation

## 📋 Table of Contents
1. [Overview](#overview)
2. [Folder Structure](#folder-structure)
3. [Database Connectivity](#database-connectivity)
4. [MVC Architecture Flow](#mvc-architecture-flow)
5. [CRUD Operations Flow](#crud-operations-flow)
6. [Flowchart](#flowchart)
7. [Component Details](#component-details)

---

## 🎯 Overview

This document describes the architecture for a complete Laravel CRUD (Create, Read, Update, Delete) application with database connectivity. We'll use a **Product Management System** as an example.

### Technology Stack
- **Framework**: Laravel (PHP)
- **Database**: MySQL (configurable to PostgreSQL, SQLite, SQL Server)
- **Frontend**: Blade Templates
- **Architecture Pattern**: MVC (Model-View-Controller)

---

## 📁 Folder Structure

```
basic_template/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php (Base Controller)
│   │   │   └── ProductController.php ✨ (CRUD Controller)
│   │   │
│   │   ├── Middleware/
│   │   │   └── [Authentication, CSRF, etc.]
│   │   │
│   │   └── Kernel.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   └── Product.php ✨ (Eloquent Model)
│   │
│   └── Providers/
│       └── [Service Providers]
│
├── config/
│   ├── database.php ✨ (Database Configuration)
│   └── app.php
│
├── database/
│   ├── migrations/
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   └── YYYY_MM_DD_HHMMSS_create_products_table.php ✨ (Product Migration)
│   │
│   ├── factories/
│   │   └── ProductFactory.php ✨ (Optional - for testing)
│   │
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── ProductSeeder.php ✨ (Optional - for sample data)
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   └── app.blade.php ✨ (Master Layout)
│   │   │
│   │   ├── products/ ✨ (Product Views)
│   │   │   ├── index.blade.php (List all products)
│   │   │   ├── create.blade.php (Create form)
│   │   │   ├── edit.blade.php (Edit form)
│   │   │   └── show.blade.php (View single product)
│   │   │
│   │   └── welcome.blade.php
│   │
│   ├── css/
│   │   └── app.css
│   │
│   └── js/
│       └── app.js
│
├── routes/
│   ├── web.php ✨ (Web Routes - CRUD Routes)
│   └── api.php
│
├── .env ✨ (Environment Configuration - DB Credentials)
│
└── public/
    └── index.php (Entry Point)
```

**✨ = New files/components for CRUD example**

---

## 🔌 Database Connectivity

### 1. Environment Configuration (.env)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_crud
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Database Configuration (config/database.php)

**Location**: `config/database.php`

**Key Configuration Points**:
- **Default Connection**: `env('DB_CONNECTION', 'mysql')`
- **Connection Array**: Contains settings for MySQL, PostgreSQL, SQLite, SQL Server
- **Connection Pooling**: Managed by Laravel's database abstraction layer

**Connection Flow**:
```
.env file → config/database.php → Illuminate\Database\DatabaseManager → PDO Connection
```

### 3. Database Connection Usage

Laravel provides multiple ways to connect:

**Method 1: Using Eloquent Model (Recommended)**
```php
// In Model
class Product extends Model
{
    protected $connection = 'mysql'; // Optional, uses default if not specified
}
```

**Method 2: Using Query Builder**
```php
DB::connection('mysql')->table('products')->get();
```

**Method 3: Using DB Facade (Default Connection)**
```php
DB::table('products')->get();
```

---

## 🏗️ MVC Architecture Flow

### Request Flow Diagram

```
┌─────────────┐
│   Browser   │
│  (Client)   │
└──────┬──────┘
       │ HTTP Request
       │ (GET /products)
       ▼
┌─────────────────────────────────────┐
│         routes/web.php             │
│  Route::get('/products', ...)      │
└──────┬──────────────────────────────┘
       │
       │ Route Resolution
       ▼
┌─────────────────────────────────────┐
│    ProductController@index         │
│    (app/Http/Controllers/)         │
└──────┬──────────────────────────────┘
       │
       │ Business Logic
       │ $products = Product::all();
       ▼
┌─────────────────────────────────────┐
│         Product Model               │
│    (app/Models/Product.php)         │
└──────┬──────────────────────────────┘
       │
       │ Eloquent Query
       │ SELECT * FROM products
       ▼
┌─────────────────────────────────────┐
│      Database (MySQL)               │
│    (Connection via PDO)             │
└──────┬──────────────────────────────┘
       │
       │ Data Return
       ▼
┌─────────────────────────────────────┐
│    ProductController@index          │
│    return view('products.index',    │
│              ['products' => $data])│
└──────┬──────────────────────────────┘
       │
       │ View Rendering
       ▼
┌─────────────────────────────────────┐
│   resources/views/products/         │
│         index.blade.php             │
└──────┬──────────────────────────────┘
       │
       │ HTML Response
       ▼
┌─────────────┐
│   Browser   │
│  (Display)  │
└─────────────┘
```

---

## 🔄 CRUD Operations Flow

### 1. CREATE Operation Flow

```
User Action: Click "Create Product" Button
    │
    ▼
Route: GET /products/create
    │
    ▼
Controller: ProductController@create
    │
    ▼
View: products/create.blade.php (Form Display)
    │
    ▼
User Action: Fill Form & Submit
    │
    ▼
Route: POST /products
    │
    ▼
Controller: ProductController@store
    │
    ├─► Validation (Request Data)
    │
    ├─► Model: Product::create($validatedData)
    │
    ├─► Database: INSERT INTO products
    │
    └─► Redirect: /products (with success message)
```

### 2. READ Operation Flow

**List All (Index)**
```
Route: GET /products
    │
    ▼
Controller: ProductController@index
    │
    ▼
Model: Product::all() or Product::paginate(10)
    │
    ▼
Database: SELECT * FROM products
    │
    ▼
View: products/index.blade.php (Display List)
```

**View Single (Show)**
```
Route: GET /products/{id}
    │
    ▼
Controller: ProductController@show($id)
    │
    ▼
Model: Product::findOrFail($id)
    │
    ▼
Database: SELECT * FROM products WHERE id = ?
    │
    ▼
View: products/show.blade.php (Display Details)
```

### 3. UPDATE Operation Flow

```
Route: GET /products/{id}/edit
    │
    ▼
Controller: ProductController@edit($id)
    │
    ▼
Model: Product::findOrFail($id)
    │
    ▼
View: products/edit.blade.php (Pre-filled Form)
    │
    ▼
User Action: Modify & Submit
    │
    ▼
Route: PUT/PATCH /products/{id}
    │
    ▼
Controller: ProductController@update($id)
    │
    ├─► Validation
    │
    ├─► Model: $product->update($validatedData)
    │
    ├─► Database: UPDATE products SET ... WHERE id = ?
    │
    └─► Redirect: /products/{id}
```

### 4. DELETE Operation Flow

```
User Action: Click "Delete" Button
    │
    ▼
Route: DELETE /products/{id}
    │
    ▼
Controller: ProductController@destroy($id)
    │
    ├─► Model: Product::findOrFail($id)
    │
    ├─► Model: $product->delete()
    │
    ├─► Database: DELETE FROM products WHERE id = ?
    │
    └─► Redirect: /products (with success message)
```

---

## 📊 Flowchart

### Complete CRUD Application Flowchart

```
                    ┌─────────────────────┐
                    │   START: User        │
                    │   Opens Application  │
                    └──────────┬───────────┘
                               │
                               ▼
                    ┌─────────────────────┐
                    │   Route: /products  │
                    │   (web.php)         │
                    └──────────┬───────────┘
                               │
                               ▼
            ┌──────────────────┴──────────────────┐
            │                                     │
            ▼                                     ▼
    ┌───────────────┐                    ┌───────────────┐
    │   GET Request │                    │  POST/PUT/    │
    │   (View Data) │                    │  DELETE       │
    └───────┬───────┘                    │  (Modify Data)│
            │                            └───────┬───────┘
            │                                    │
    ┌───────┴────────┐                  ┌───────┴────────┐
    │                │                  │                │
    ▼                ▼                  ▼                ▼
┌─────────┐   ┌──────────┐    ┌──────────┐    ┌──────────┐
│  index  │   │   show   │    │  store   │    │  update  │
│ (List)  │   │  (View)  │    │ (Create) │    │  (Edit)  │
└────┬────┘   └────┬─────┘    └────┬─────┘    └────┬─────┘
     │             │               │               │
     │             │               │               │
     └─────┬───────┴───────┬───────┴───────┬───────┘
           │               │               │
           ▼               ▼               ▼
    ┌─────────────────────────────────────────┐
    │   ProductController                     │
    │   (app/Http/Controllers/)               │
    └──────────────┬──────────────────────────┘
                   │
                   │ Business Logic
                   │
                   ▼
    ┌─────────────────────────────────────────┐
    │   Product Model                         │
    │   (app/Models/Product.php)              │
    │   - Eloquent ORM                        │
    │   - Relationships                       │
    │   - Accessors/Mutators                  │
    └──────────────┬──────────────────────────┘
                   │
                   │ SQL Queries
                   │
                   ▼
    ┌─────────────────────────────────────────┐
    │   Database Connection                   │
    │   (config/database.php)                 │
    │   - MySQL Connection                    │
    │   - PDO Driver                          │
    └──────────────┬──────────────────────────┘
                   │
                   │ Query Execution
                   │
                   ▼
    ┌─────────────────────────────────────────┐
    │   MySQL Database                        │
    │   - products table                      │
    │   - Data Storage                        │
    └──────────────┬──────────────────────────┘
                   │
                   │ Data Return
                   │
                   ▼
    ┌─────────────────────────────────────────┐
    │   ProductController                     │
    │   - Process Data                        │
    │   - Return Response                     │
    └──────────────┬──────────────────────────┘
                   │
        ┌──────────┴──────────┐
        │                     │
        ▼                     ▼
┌───────────────┐    ┌───────────────┐
│  JSON Response│    │  View Render  │
│  (API)        │    │  (Blade)      │
└───────┬───────┘    └───────┬───────┘
        │                    │
        │                    ▼
        │           ┌─────────────────┐
        │           │  Blade Template │
        │           │  (resources/    │
        │           │   views/)      │
        │           └────────┬────────┘
        │                    │
        └──────────┬─────────┘
                   │
                   ▼
        ┌─────────────────────┐
        │   HTML Response      │
        │   (Browser Display)  │
        └─────────────────────┘
```

---

## 🧩 Component Details

### 1. Routes (routes/web.php)

**Resource Routes** (Recommended):
```php
Route::resource('products', ProductController::class);
```

**This single line creates 7 routes:**
- `GET /products` → index (list all)
- `GET /products/create` → create (show form)
- `POST /products` → store (save new)
- `GET /products/{id}` → show (view single)
- `GET /products/{id}/edit` → edit (show edit form)
- `PUT/PATCH /products/{id}` → update (save changes)
- `DELETE /products/{id}` → destroy (delete)

### 2. Controller (app/Http/Controllers/ProductController.php)

**Methods Structure:**
```php
class ProductController extends Controller
{
    public function index()      // List all products
    public function create()     // Show create form
    public function store()      // Save new product
    public function show($id)    // Display single product
    public function edit($id)    // Show edit form
    public function update($id)  // Update existing product
    public function destroy($id) // Delete product
}
```

### 3. Model (app/Models/Product.php)

**Key Features:**
- Extends `Illuminate\Database\Eloquent\Model`
- Automatic table name detection (`products`)
- Mass assignment protection (`$fillable` or `$guarded`)
- Timestamps (`created_at`, `updated_at`)
- Relationships (hasMany, belongsTo, etc.)

### 4. Migration (database/migrations/YYYY_MM_DD_HHMMSS_create_products_table.php)

**Structure:**
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description');
    $table->decimal('price', 10, 2);
    $table->integer('quantity');
    $table->timestamps();
});
```

### 5. Views (resources/views/products/)

**View Files:**
- `index.blade.php` - Table/list of all products
- `create.blade.php` - Form to create new product
- `edit.blade.php` - Form to edit existing product
- `show.blade.php` - Display single product details

**Layout Structure:**
- `layouts/app.blade.php` - Master layout (header, footer, navigation)

---

## 🔐 Security Considerations

1. **CSRF Protection**: Laravel automatically includes CSRF tokens in forms
2. **Mass Assignment**: Use `$fillable` or `$guarded` in models
3. **Validation**: Validate all user input in controllers
4. **SQL Injection**: Protected by Eloquent ORM and prepared statements
5. **XSS Protection**: Blade templates escape output by default

---

## 📝 Summary

### Key Components:
1. **Routes** → Define URL endpoints
2. **Controller** → Handle business logic
3. **Model** → Interact with database
4. **Migration** → Define database schema
5. **Views** → Display data to users
6. **Database Config** → Connection settings

### Data Flow:
```
User Request → Route → Controller → Model → Database
                                 ↓
                            View ← Response
```

---

## 🚀 Next Steps

After reviewing this architecture, we can implement:
1. Database migration for products table
2. Product model with relationships
3. ProductController with all CRUD methods
4. Blade views for all operations
5. Routes configuration
6. Form validation
7. Success/error messages

---

**Document Version**: 1.0  
**Last Updated**: 2024  
**Framework**: Laravel

