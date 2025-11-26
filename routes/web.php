<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);

// Password Reset Routes
Route::get('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Logout
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Dashboard Routes (Protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // User Management Routes
    Route::resource('users', App\Http\Controllers\UserController::class);
    
    // Account Settings Routes (for users to change their own password)
    Route::get('/account/change-password', [App\Http\Controllers\UserController::class, 'showChangePasswordForm'])->name('account.change-password');
    Route::post('/account/change-password', [App\Http\Controllers\UserController::class, 'changePassword']);
    
    // Admin Password Change Route (for admins to change any user's password)
    Route::post('/users/{id}/change-password', [App\Http\Controllers\UserController::class, 'adminChangePassword'])->name('users.change-password');
    
    // Organization Switching Routes (Super Admin only) - Must come before resource routes
    Route::get('/organizations/switch/clear', [App\Http\Controllers\OrganizationSwitchController::class, 'clear'])->name('organization.switch.clear');
    Route::get('/organizations/{organization}/switch', [App\Http\Controllers\OrganizationSwitchController::class, 'switch'])->name('organization.switch');
    
    // Organization Management Routes (Super Admin only)
    Route::resource('organizations', App\Http\Controllers\OrganizationController::class);
    
    // Branch Management Routes
    Route::resource('branches', App\Http\Controllers\BranchController::class);
    
    // Branch Selection Routes
    Route::get('/branch/select', [App\Http\Controllers\BranchSelectionController::class, 'show'])->name('branch.select');
    Route::post('/branch/select', [App\Http\Controllers\BranchSelectionController::class, 'select'])->name('branch.select.post');
    Route::get('/branches/{branch}/switch', [App\Http\Controllers\BranchSelectionController::class, 'switch'])->name('branch.switch');
    Route::get('/branches/switch/clear', [App\Http\Controllers\BranchSelectionController::class, 'clear'])->name('branch.switch.clear');
    
    // Role & Permission Management Routes (Super Admin only)
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::resource('permissions', App\Http\Controllers\PermissionController::class);
    
    // Transaction Management Routes (Branch Users only)
    Route::resource('transactions', App\Http\Controllers\TransactionController::class);

    // Master Data Routes
    Route::resource('units', App\Http\Controllers\UnitController::class);
    Route::resource('customers', App\Http\Controllers\CustomerController::class);
    Route::resource('products', App\Http\Controllers\ProductController::class);
    Route::resource('raw-material-categories', App\Http\Controllers\RawMaterialCategoryController::class);
    Route::resource('raw-material-sub-categories', App\Http\Controllers\RawMaterialSubCategoryController::class);
    Route::resource('product-categories', App\Http\Controllers\ProductCategoryController::class);
    Route::resource('processes', App\Http\Controllers\ProcessController::class);
    Route::resource('bom-processes', App\Http\Controllers\BOMProcessController::class);
    // Custom route must come before resource route to avoid conflicts
    Route::get('raw-materials/sub-categories', [App\Http\Controllers\RawMaterialController::class, 'getSubCategories'])->name('raw-materials.sub-categories');
    Route::resource('raw-materials', App\Http\Controllers\RawMaterialController::class);
    Route::resource('departments', App\Http\Controllers\DepartmentController::class);
    Route::resource('designations', App\Http\Controllers\DesignationController::class);
    Route::resource('production-departments', App\Http\Controllers\ProductionDepartmentController::class);
    Route::resource('employees', App\Http\Controllers\EmployeeController::class);
    Route::get('employees/designations', [App\Http\Controllers\EmployeeController::class, 'getDesignations'])->name('employees.designations');
    Route::resource('billing-addresses', App\Http\Controllers\BillingAddressController::class);
    Route::post('billing-addresses/bulk-delete', [App\Http\Controllers\BillingAddressController::class, 'bulkDelete'])->name('billing-addresses.bulk-delete');

    // Quotation Routes
    Route::resource('quotations', App\Http\Controllers\QuotationController::class);
    Route::get('quotations/customer/{id}', [App\Http\Controllers\QuotationController::class, 'getCustomerDetails'])->name('quotations.customer');
    Route::get('quotations/product/{id}', [App\Http\Controllers\QuotationController::class, 'getProductDetails'])->name('quotations.product');
    Route::get('quotations/{id}/pdf', [App\Http\Controllers\QuotationController::class, 'pdf'])->name('quotations.pdf');

    // Proforma Invoice Routes
    Route::resource('proforma-invoices', App\Http\Controllers\ProformaInvoiceController::class);
    Route::get('proforma-invoices/customer/{id}', [App\Http\Controllers\ProformaInvoiceController::class, 'getCustomerDetails'])->name('proforma-invoices.customer');
    Route::get('proforma-invoices/product/{id}', [App\Http\Controllers\ProformaInvoiceController::class, 'getProductDetails'])->name('proforma-invoices.product');
    Route::get('proforma-invoices/{id}/pdf', [App\Http\Controllers\ProformaInvoiceController::class, 'pdf'])->name('proforma-invoices.pdf');

    // Settings Routes (Super Admin only)
    Route::resource('company-information', App\Http\Controllers\CompanyInformationController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

    // Tender Routes
    Route::resource('tenders', App\Http\Controllers\TenderController::class);
    Route::get('tenders/customer/{id}', [App\Http\Controllers\TenderController::class, 'getCustomerDetails'])->name('tenders.customer');
});
