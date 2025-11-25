# Laravel CRUD Application - Detailed Folder Structure

## 📂 Complete Folder Structure with File Descriptions

```
basic_template/
│
├── 📁 app/                                    # Application Core
│   ├── 📁 Console/
│   │   └── Kernel.php                        # Console command scheduling
│   │
│   ├── 📁 Exceptions/
│   │   └── Handler.php                       # Exception handling
│   │
│   ├── 📁 Http/                               # HTTP Layer
│   │   ├── 📁 Controllers/                    # Request Handlers
│   │   │   ├── Controller.php                # Base controller class
│   │   │   └── ProductController.php         # ✨ Product CRUD controller
│   │   │       ├── index()                   # List all products
│   │   │       ├── create()                  # Show create form
│   │   │       ├── store()                   # Save new product
│   │   │       ├── show($id)                 # Display single product
│   │   │       ├── edit($id)                 # Show edit form
│   │   │       ├── update($id)               # Update product
│   │   │       └── destroy($id)              # Delete product
│   │   │
│   │   ├── 📁 Middleware/                     # Request Filters
│   │   │   ├── Authenticate.php
│   │   │   ├── EncryptCookies.php
│   │   │   ├── PreventRequestsDuringMaintenance.php
│   │   │   ├── RedirectIfAuthenticated.php
│   │   │   ├── TrimStrings.php
│   │   │   ├── TrustHosts.php
│   │   │   ├── TrustProxies.php
│   │   │   └── VerifyCsrfToken.php           # CSRF protection
│   │   │
│   │   └── Kernel.php                        # Middleware registration
│   │
│   ├── 📁 Models/                             # Data Models (Eloquent ORM)
│   │   ├── User.php                          # User model
│   │   └── Product.php                       # ✨ Product model
│   │       ├── $fillable                     # Mass assignable fields
│   │       ├── $guarded                      # Protected fields
│   │       ├── $casts                        # Type casting
│   │       └── relationships()               # Model relationships
│   │
│   └── 📁 Providers/                          # Service Providers
│       ├── AppServiceProvider.php
│       ├── AuthServiceProvider.php
│       ├── BroadcastServiceProvider.php
│       ├── EventServiceProvider.php
│       └── RouteServiceProvider.php          # Route configuration
│
├── 📁 bootstrap/                              # Bootstrap Files
│   ├── app.php                               # Application bootstrap
│   └── 📁 cache/                              # Bootstrap cache
│
├── 📁 config/                                 # Configuration Files
│   ├── app.php                               # Application config
│   ├── auth.php                              # Authentication config
│   ├── database.php                          # ✨ Database connections
│   │   ├── 'default' => 'mysql'
│   │   ├── 'connections' => [
│   │   │   ├── 'mysql' => [...]
│   │   │   ├── 'pgsql' => [...]
│   │   │   ├── 'sqlite' => [...]
│   │   │   └── 'sqlsrv' => [...]
│   │   └── 'migrations' => 'migrations'
│   ├── filesystems.php
│   └── [other config files]
│
├── 📁 database/                               # Database Files
│   ├── 📁 factories/                         # Model Factories (Testing)
│   │   ├── UserFactory.php
│   │   └── ProductFactory.php               # ✨ Product factory
│   │
│   ├── 📁 migrations/                        # Database Schema
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2014_10_12_100000_create_password_resets_table.php
│   │   ├── 2019_08_19_000000_create_failed_jobs_table.php
│   │   ├── 2019_12_14_000001_create_personal_access_tokens_table.php
│   │   └── YYYY_MM_DD_HHMMSS_create_products_table.php  # ✨ Product table
│   │       ├── up()                          # Create table
│   │       └── down()                        # Drop table
│   │
│   └── 📁 seeders/                           # Database Seeders
│       ├── DatabaseSeeder.php                # Main seeder
│       └── ProductSeeder.php                # ✨ Product seeder
│
├── 📁 public/                                 # Public Assets (Web Root)
│   ├── index.php                             # Entry point
│   ├── favicon.ico
│   └── robots.txt
│
├── 📁 resources/                              # Frontend Resources
│   ├── 📁 css/
│   │   └── app.css                          # Stylesheet
│   │
│   ├── 📁 js/
│   │   ├── app.js                           # JavaScript
│   │   └── bootstrap.js
│   │
│   ├── 📁 lang/                              # Language Files
│   │   └── 📁 en/
│   │
│   └── 📁 views/                             # Blade Templates
│       ├── 📁 layouts/                       # Layout Templates
│       │   └── app.blade.php                # ✨ Master layout
│       │       ├── @yield('title')
│       │       ├── @yield('content')
│       │       ├── Header
│       │       ├── Navigation
│       │       └── Footer
│       │
│       ├── 📁 products/                      # ✨ Product Views
│       │   ├── index.blade.php              # List all products
│       │   │   ├── Table/List view
│       │   │   ├── Create button
│       │   │   ├── Edit links
│       │   │   └── Delete buttons
│       │   │
│       │   ├── create.blade.php             # Create form
│       │   │   ├── Form fields
│       │   │   ├── CSRF token
│       │   │   ├── Validation errors
│       │   │   └── Submit button
│       │   │
│       │   ├── edit.blade.php               # Edit form
│       │   │   ├── Pre-filled form
│       │   │   ├── CSRF token
│       │   │   ├── Method spoofing (PUT)
│       │   │   └── Update button
│       │   │
│       │   └── show.blade.php               # View single product
│       │       ├── Product details
│       │       ├── Edit link
│       │       └── Delete button
│       │
│       └── welcome.blade.php                # Welcome page
│
├── 📁 routes/                                 # Route Definitions
│   ├── api.php                              # API routes
│   ├── channels.php                         # Broadcast channels
│   ├── console.php                          # Console commands
│   └── web.php                              # ✨ Web routes (CRUD routes)
│       ├── Route::get('/products', ...)
│       ├── Route::get('/products/create', ...)
│       ├── Route::post('/products', ...)
│       ├── Route::get('/products/{id}', ...)
│       ├── Route::get('/products/{id}/edit', ...)
│       ├── Route::put('/products/{id}', ...)
│       └── Route::delete('/products/{id}', ...)
│       # OR simply: Route::resource('products', ProductController::class)
│
├── 📁 storage/                                # Storage (Logs, Cache, Files)
│   ├── 📁 app/
│   │   └── 📁 public/
│   ├── 📁 framework/
│   │   ├── 📁 cache/
│   │   ├── 📁 sessions/
│   │   ├── 📁 testing/
│   │   └── 📁 views/
│   └── 📁 logs/
│
├── 📁 tests/                                  # Test Files
│   ├── 📁 Feature/
│   │   └── ExampleTest.php
│   ├── 📁 Unit/
│   │   └── ExampleTest.php
│   └── TestCase.php
│
├── 📁 vendor/                                 # Composer Dependencies
│   └── [Laravel and third-party packages]
│
├── .env                                       # ✨ Environment Configuration
│   ├── DB_CONNECTION=mysql
│   ├── DB_HOST=127.0.0.1
│   ├── DB_PORT=3306
│   ├── DB_DATABASE=laravel_crud
│   ├── DB_USERNAME=root
│   └── DB_PASSWORD=
│
├── .env.example                              # Environment template
├── artisan                                   # Laravel CLI tool
├── composer.json                             # PHP dependencies
├── composer.lock                             # Locked versions
├── package.json                              # NPM dependencies
├── phpunit.xml                               # PHPUnit config
├── webpack.mix.js                            # Laravel Mix config
└── README.md                                 # Project documentation
```

---

## 🔑 Key Files for CRUD Operations

### 1. **Routes** (`routes/web.php`)
```php
// Resource route (creates all 7 CRUD routes)
Route::resource('products', ProductController::class);

// OR individual routes:
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
```

### 2. **Controller** (`app/Http/Controllers/ProductController.php`)
```php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // List all products
    public function index() { }
    
    // Show create form
    public function create() { }
    
    // Store new product
    public function store(Request $request) { }
    
    // Show single product
    public function show(Product $product) { }
    
    // Show edit form
    public function edit(Product $product) { }
    
    // Update product
    public function update(Request $request, Product $product) { }
    
    // Delete product
    public function destroy(Product $product) { }
}
```

### 3. **Model** (`app/Models/Product.php`)
```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'quantity'
    ];
    
    // Relationships, accessors, mutators, etc.
}
```

### 4. **Migration** (`database/migrations/YYYY_MM_DD_HHMMSS_create_products_table.php`)
```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
```

### 5. **Views** (`resources/views/products/`)
- `index.blade.php` - Display list
- `create.blade.php` - Create form
- `edit.blade.php` - Edit form
- `show.blade.php` - Single view

---

## 🔄 File Interaction Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    REQUEST CYCLE                             │
└─────────────────────────────────────────────────────────────┘

1. User Request
   └─► routes/web.php (Route Definition)

2. Route Resolution
   └─► app/Http/Controllers/ProductController.php

3. Controller Logic
   ├─► app/Models/Product.php (Data Access)
   │   └─► config/database.php (Connection)
   │       └─► .env (Credentials)
   │           └─► MySQL Database
   │
   └─► resources/views/products/*.blade.php (Display)

4. Response
   └─► Browser (HTML Output)
```

---

## 📊 Database Connection Flow

```
Application Start
    │
    ▼
Load .env file
    │
    ├─► DB_CONNECTION=mysql
    ├─► DB_HOST=127.0.0.1
    ├─► DB_PORT=3306
    ├─► DB_DATABASE=laravel_crud
    ├─► DB_USERNAME=root
    └─► DB_PASSWORD=
    │
    ▼
config/database.php
    │
    ├─► Read environment variables
    ├─► Configure connection array
    └─► Set default connection
    │
    ▼
Illuminate\Database\DatabaseManager
    │
    ├─► Create PDO connection
    ├─► Connection pooling
    └─► Query builder setup
    │
    ▼
Model/Query Execution
    │
    ├─► Eloquent ORM (Product Model)
    └─► Query Builder (DB facade)
    │
    ▼
MySQL Database
    └─► Execute SQL queries
```

---

## 🎯 CRUD File Mapping

| Operation | Route | Controller Method | View File | Database Action |
|-----------|-------|-------------------|-----------|-----------------|
| **List** | GET /products | `index()` | `index.blade.php` | SELECT * FROM products |
| **Create Form** | GET /products/create | `create()` | `create.blade.php` | - |
| **Store** | POST /products | `store()` | Redirect | INSERT INTO products |
| **Show** | GET /products/{id} | `show()` | `show.blade.php` | SELECT * WHERE id = ? |
| **Edit Form** | GET /products/{id}/edit | `edit()` | `edit.blade.php` | SELECT * WHERE id = ? |
| **Update** | PUT /products/{id} | `update()` | Redirect | UPDATE products SET ... |
| **Delete** | DELETE /products/{id} | `destroy()` | Redirect | DELETE FROM products |

---

**✨ = New files/components for CRUD example**

