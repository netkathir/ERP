<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

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
    Route::get('role-permissions/select', [App\Http\Controllers\RolePermissionController::class, 'select'])->name('role-permissions.select');
    Route::get('role-permissions/create', [App\Http\Controllers\RolePermissionController::class, 'create'])->name('role-permissions.create');
    Route::post('role-permissions', [App\Http\Controllers\RolePermissionController::class, 'store'])->name('role-permissions.store');
    Route::get('role-permissions/{role}/edit', [App\Http\Controllers\RolePermissionController::class, 'edit'])->name('role-permissions.edit');
    Route::post('role-permissions/{role}/update', [App\Http\Controllers\RolePermissionController::class, 'update'])->name('role-permissions.update');
    // Legacy routes for backward compatibility
    Route::get('roles/{role}/permissions', [App\Http\Controllers\RolePermissionController::class, 'edit'])->name('roles.permissions.edit');
    Route::post('roles/{role}/permissions', [App\Http\Controllers\RolePermissionController::class, 'update'])->name('roles.permissions.update');
    Route::get('roles/audit', [App\Http\Controllers\RolePermissionAuditController::class, 'index'])->name('roles.audit.index');
    Route::get('roles/{role}/audit', [App\Http\Controllers\RolePermissionAuditController::class, 'showRole'])->name('roles.audit.show');
    Route::get('roles/report/permissions', [App\Http\Controllers\RolePermissionAuditController::class, 'report'])->name('roles.report.permissions');
    
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
    Route::resource('suppliers', App\Http\Controllers\SupplierController::class);
    Route::resource('supplier-evaluations', App\Http\Controllers\SupplierEvaluationController::class);
    Route::resource('subcontractor-evaluations', App\Http\Controllers\SubcontractorEvaluationController::class);
    Route::resource('purchase-indents', App\Http\Controllers\PurchaseIndentController::class);
    Route::post('purchase-indents/{id}/approve', [App\Http\Controllers\PurchaseIndentController::class, 'approve'])->name('purchase-indents.approve');
    Route::resource('purchase-orders', App\Http\Controllers\PurchaseOrderController::class);
    Route::get('notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::get('purchase-orders/purchase-indent/{id}/items', [App\Http\Controllers\PurchaseOrderController::class, 'getPurchaseIndentItems'])->name('purchase-orders.purchase-indent.items');
    Route::get('purchase-orders/purchase-indent/{id}/items/display', [App\Http\Controllers\PurchaseOrderController::class, 'getPurchaseIndentItemsForDisplay'])->name('purchase-orders.purchase-indent.items.display');
    Route::get('purchase-orders/customer/{id}', [App\Http\Controllers\PurchaseOrderController::class, 'getCustomerDetails'])->name('purchase-orders.customer');
    Route::get('purchase-orders/supplier/{id}', [App\Http\Controllers\PurchaseOrderController::class, 'getSupplierDetails'])->name('purchase-orders.supplier');
    Route::get('purchase-orders/billing-address/{id}', [App\Http\Controllers\PurchaseOrderController::class, 'getBillingAddressDetails'])->name('purchase-orders.billing-address');

    // Material Inward Routes (Store Module)
    Route::resource('material-inwards', App\Http\Controllers\MaterialInwardController::class);
    Route::get('material-inwards/purchase-order/{id}/items', [App\Http\Controllers\MaterialInwardController::class, 'getPurchaseOrderItems'])->name('material-inwards.purchase-order.items');

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

    // Customer Orders (Tender Module)
    Route::resource('customer-orders', App\Http\Controllers\CustomerOrderController::class);
    Route::post('customer-orders/{id}/approve', [App\Http\Controllers\CustomerOrderController::class, 'approve'])->name('customer-orders.approve');
    Route::get('customer-orders/tender/{id}/items/display', [App\Http\Controllers\CustomerOrderController::class, 'getTenderItemsForDisplay'])->name('customer-orders.tender.items.display');
    
    // Check approval tables status (temporary - remove after testing)
    Route::get('/check-approval-tables', function() {
        $user = auth()->user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403);
        }
        
        $results = [];
        $results['approval_masters'] = \Illuminate\Support\Facades\Schema::hasTable('approval_masters');
        $results['approval_mappings'] = \Illuminate\Support\Facades\Schema::hasTable('approval_mappings');
        
        if (\Illuminate\Support\Facades\Schema::hasTable('customer_orders')) {
            $results['customer_orders.approval_status'] = \Illuminate\Support\Facades\Schema::hasColumn('customer_orders', 'approval_status');
            $results['customer_orders.approved_by'] = \Illuminate\Support\Facades\Schema::hasColumn('customer_orders', 'approved_by');
        }
        
        $html = '<h2>Approval Tables Status</h2><ul>';
        foreach ($results as $key => $exists) {
            $html .= '<li>' . $key . ': ' . ($exists ? '<span style="color:green">✓ EXISTS</span>' : '<span style="color:red">✗ NOT FOUND</span>') . '</li>';
        }
        $html .= '</ul>';
        
        if (!$results['approval_masters']) {
            $html .= '<p style="color:red;"><strong>Action Required:</strong> Run: <code>php artisan migrate</code></p>';
        }
        
        return $html;
    })->name('check-approval-tables');

    // Test approval notification (temporary - remove after testing)
    Route::get('/test-approval-notification', function() {
        $user = auth()->user();
        if (!$user || !$user->isSuperAdmin()) {
            abort(403);
        }
        
        $order = \App\Models\CustomerOrder::latest()->first();
        if (!$order) {
            return 'No customer order found to test with.';
        }
        
        try {
            $user->notify(new \App\Notifications\CustomerOrderApprovalRequest($order));
            return 'Test notification sent successfully! Check your notifications.';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage() . '<br>Stack: ' . $e->getTraceAsString();
        }
    })->name('test-approval-notification');

    // Temporary route to make special_instructions nullable (remove after use)
    Route::get('/fix-special-instructions-nullable', function() {
        try {
            DB::statement("ALTER TABLE `purchase_indent_items` MODIFY COLUMN `special_instructions` TEXT NULL");
            echo "special_instructions column is now nullable!<br>";
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                echo "Column already nullable.<br>";
            } else {
                echo "Error: " . $e->getMessage() . "<br>";
            }
        }
        
        echo "<br>Done! You can now try creating a Purchase Indent again.";
    })->name('fix-special-instructions-nullable');

    // Temporary route to add status column (remove after use)
    Route::get('/add-status-column', function() {
        try {
            DB::statement("ALTER TABLE `customer_orders` ADD COLUMN `status` VARCHAR(255) NOT NULL DEFAULT 'Pending'");
            echo "Status column added successfully!<br>";
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "Status column already exists.<br>";
            } else {
                echo "Error adding status column: " . $e->getMessage() . "<br>";
            }
        }

        try {
            DB::statement("ALTER TABLE `customer_orders` ADD COLUMN `updated_by_id` BIGINT UNSIGNED NULL");
            echo "updated_by_id column added successfully!<br>";
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "updated_by_id column already exists.<br>";
            } else {
                echo "Error adding updated_by_id column: " . $e->getMessage() . "<br>";
            }
        }

        try {
            DB::statement("ALTER TABLE `customer_orders` ADD CONSTRAINT `customer_orders_updated_by_id_foreign` FOREIGN KEY (`updated_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL");
            echo "Foreign key added successfully!<br>";
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate key name') !== false || strpos($e->getMessage(), 'already exists') !== false) {
                echo "Foreign key already exists.<br>";
            } else {
                echo "Error adding foreign key: " . $e->getMessage() . "<br>";
            }
        }
        
        echo "<br>Done! You can now try creating a Customer Order again.";
    })->name('add-status-column');

    // Temporary route to fix customer_order_items table (remove after use)
    Route::get('/fix-customer-order-items', function() {
        try {
            // Make tender_item_id nullable first
            try {
                // Drop foreign key if exists
                DB::statement('ALTER TABLE customer_order_items DROP FOREIGN KEY customer_order_items_tender_item_id_foreign');
            } catch (\Exception $e) {
                // Ignore if doesn't exist
            }
            
            // Make column nullable
            DB::statement('ALTER TABLE customer_order_items MODIFY tender_item_id BIGINT UNSIGNED NULL');
            echo "tender_item_id made nullable<br>";
            
            // Re-add foreign key
            try {
                DB::statement('ALTER TABLE customer_order_items ADD CONSTRAINT customer_order_items_tender_item_id_foreign FOREIGN KEY (tender_item_id) REFERENCES tender_items(id) ON DELETE CASCADE');
                echo "Foreign key for tender_item_id re-added<br>";
            } catch (\Exception $e) {
                echo "Foreign key for tender_item_id: " . $e->getMessage() . "<br>";
            }
            
            if (!Schema::hasColumn('customer_order_items', 'product_id')) {
                DB::statement('ALTER TABLE customer_order_items ADD COLUMN product_id BIGINT UNSIGNED NULL AFTER tender_item_id');
                echo "product_id column added<br>";
            } else {
                echo "product_id column already exists<br>";
            }
            
            if (!Schema::hasColumn('customer_order_items', 'unit_id')) {
                DB::statement('ALTER TABLE customer_order_items ADD COLUMN unit_id BIGINT UNSIGNED NULL AFTER product_id');
                echo "unit_id column added<br>";
            } else {
                echo "unit_id column already exists<br>";
            }
            
            // Add foreign keys
            try {
                $fkExists = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_order_items' AND CONSTRAINT_NAME = 'customer_order_items_product_id_foreign'");
                if (empty($fkExists)) {
                    DB::statement('ALTER TABLE customer_order_items ADD CONSTRAINT customer_order_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE');
                    echo "Foreign key for product_id added<br>";
                } else {
                    echo "Foreign key for product_id already exists<br>";
                }
            } catch (\Exception $e) {
                echo "Foreign key for product_id: " . $e->getMessage() . "<br>";
            }
            
            try {
                $fkExists = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'customer_order_items' AND CONSTRAINT_NAME = 'customer_order_items_unit_id_foreign'");
                if (empty($fkExists)) {
                    DB::statement('ALTER TABLE customer_order_items ADD CONSTRAINT customer_order_items_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL');
                    echo "Foreign key for unit_id added<br>";
                } else {
                    echo "Foreign key for unit_id already exists<br>";
                }
            } catch (\Exception $e) {
                echo "Foreign key for unit_id: " . $e->getMessage() . "<br>";
            }
            
            echo "<br>Done! You can now try creating a Customer Order again.";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    })->name('fix-customer-order-items');

    // Approval System Routes
    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/', [App\Http\Controllers\ApprovalController::class, 'index'])->name('index');
        Route::post('/{form}/{id}/approve', [App\Http\Controllers\ApprovalController::class, 'approve'])->name('approve');
        Route::post('/{form}/{id}/reject', [App\Http\Controllers\ApprovalController::class, 'reject'])->name('reject');
    });


    // Tender Evaluation Routes
    Route::resource('tender-evaluations', App\Http\Controllers\TenderEvaluationController::class);

    // Customer Complaint Register (Tender Sales Master)
    Route::resource('customer-complaints', App\Http\Controllers\CustomerComplaintController::class);
});
