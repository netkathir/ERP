<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Submenu;
use App\Models\Form;

class MenuFormSeeder extends Seeder
{
    public function run(): void
    {
        // Clean up any legacy menus that no longer match the sidebar (e.g. old "Tenders" or "Transactions" menus)
        Menu::whereIn('code', ['tenders', 'transactions'])->delete();

        // High-level menus aligned with left sidebar
        $systemAdminMenu = Menu::firstOrCreate(
            ['code' => 'system_admin'],
            ['name' => 'System Admin', 'is_active' => true]
        );

        $masters      = Menu::firstOrCreate(['code' => 'masters'],        ['name' => 'Masters',        'is_active' => true]);
        $purchase     = Menu::firstOrCreate(['code' => 'purchase'],       ['name' => 'Purchase',       'is_active' => true]);
        $store        = Menu::firstOrCreate(['code' => 'store'],          ['name' => 'Store',          'is_active' => true]);
        $sales        = Menu::firstOrCreate(['code' => 'sales'],          ['name' => 'Sales',          'is_active' => true]);
        $tenderSales  = Menu::firstOrCreate(['code' => 'tender_sales'],   ['name' => 'Tender Sales',   'is_active' => true]);
        $enquirySales = Menu::firstOrCreate(['code' => 'enquiry_sales'],  ['name' => 'Enquiry Sales',  'is_active' => true]);
        $supplierMenu = Menu::firstOrCreate(['code' => 'supplier'],       ['name' => 'Supplier',       'is_active' => true]);
        $settings     = Menu::firstOrCreate(['code' => 'settings'],       ['name' => 'Settings',       'is_active' => true]);
        $system       = Menu::firstOrCreate(['code' => 'system'],         ['name' => 'System',         'is_active' => true]);

        // Submenus to mirror grouped headers
        $masterSetupSubmenu   = Submenu::firstOrCreate(
            ['menu_id' => $masters->id, 'code' => 'master_setup'],
            ['name' => 'Master Setup', 'is_active' => true]
        );

        $purchaseTxnSubmenu   = Submenu::firstOrCreate(
            ['menu_id' => $purchase->id, 'code' => 'purchase_txn'],
            ['name' => 'Transactions', 'is_active' => true]
        );

        $storeTxnSubmenu      = Submenu::firstOrCreate(
            ['menu_id' => $store->id, 'code' => 'store_txn'],
            ['name' => 'Transactions', 'is_active' => true]
        );

        $salesTxnSubmenu      = Submenu::firstOrCreate(
            ['menu_id' => $sales->id, 'code' => 'sales_txn'],
            ['name' => 'Transactions', 'is_active' => true]
        );

        $tenderTxnSubmenu     = Submenu::firstOrCreate(
            ['menu_id' => $tenderSales->id, 'code' => 'tender_txn'],
            ['name' => 'Transactions', 'is_active' => true]
        );

        $enquiryTxnSubmenu    = Submenu::firstOrCreate(
            ['menu_id' => $enquirySales->id, 'code' => 'enquiry_txn'],
            ['name' => 'Transactions', 'is_active' => true]
        );

        $supplierSubmenu      = Submenu::firstOrCreate(
            ['menu_id' => $supplierMenu->id, 'code' => 'supplier_txn'],
            ['name' => 'Supplier', 'is_active' => true]
        );

        // Helper to create/update forms
        $makeForm = function (Menu $menu, ?Submenu $submenu, string $name, string $code, ?string $routeName = null) {
            $form = Form::firstOrNew(['code' => $code]);
            $form->menu_id = $menu->id;
            $form->submenu_id = $submenu ? $submenu->id : null;
            $form->name = $name;
            $form->route_name = $routeName;
            $form->is_active = true;
            $form->save();
        };

        // System Admin (matches System Admin group in sidebar)
        $makeForm($systemAdminMenu, null, 'Branches',          'branches_form',          'branches.index');
        $makeForm($systemAdminMenu, null, 'Users',             'users_form',             'users.index');
        $makeForm($systemAdminMenu, null, 'Roles',             'roles_form',             'roles.index');
        $makeForm($systemAdminMenu, null, 'Role Permissions',  'role_permissions_form',  'role-permissions.select');

        // Masters (Masters group)
        $makeForm($masters, $masterSetupSubmenu, 'Units',              'units_form',              'units.index');
        $makeForm($masters, $masterSetupSubmenu, 'Customers',          'customers_form',          'customers.index');
        $makeForm($masters, $masterSetupSubmenu, 'Products',           'products_form',           'products.index');
        $makeForm($masters, $masterSetupSubmenu, 'Raw Materials',      'raw_materials_form',      'raw-materials.index');
        $makeForm($masters, $masterSetupSubmenu, 'Product Categories', 'product_categories_form', 'product-categories.index');
        $makeForm($masters, $masterSetupSubmenu, 'Departments',        'departments_form',        'departments.index');
        $makeForm($masters, $masterSetupSubmenu, 'Designations',       'designations_form',       'designations.index');
        $makeForm($masters, $masterSetupSubmenu, 'Production Depts',   'production_departments_form', 'production-departments.index');
        $makeForm($masters, $masterSetupSubmenu, 'Employees',          'employees_form',          'employees.index');
        $makeForm($masters, $masterSetupSubmenu, 'Billing Addresses',  'billing_addresses_form',  'billing-addresses.index');
        // supplier-related forms are under Supplier menu, see below

        // Purchase (Purchase group)
        $makeForm($purchase, $purchaseTxnSubmenu, 'Purchase Indents',  'purchase_indents_form',   'purchase-indents.index');
        $makeForm($purchase, $purchaseTxnSubmenu, 'Purchase Orders',   'purchase_orders_form',    'purchase-orders.index');

        // Sales (Customer Orders / Complaints etc.)
        $makeForm($sales, $salesTxnSubmenu, 'Customer Orders',         'customer_orders_form',    'customer-orders.index');
        $makeForm($sales, $salesTxnSubmenu, 'Customer Complaints',     'customer_complaints_form','customer-complaints.index');

        // Enquiry Sales (Quotations / Proforma etc.)
        $makeForm($enquirySales, $enquiryTxnSubmenu, 'Quotations',        'quotations_form',        'quotations.index');
        $makeForm($enquirySales, $enquiryTxnSubmenu, 'Proforma Invoices', 'proforma_invoices_form', 'proforma-invoices.index');

        // Tender Sales group (Tenders + Tender Evaluation + Customer Orders + Complaints)
        $makeForm($tenderSales, $tenderTxnSubmenu, 'Tenders',              'tenders_form',            'tenders.index');
        $makeForm($tenderSales, $tenderTxnSubmenu, 'Customer Orders',      'ts_customer_orders_form', 'customer-orders.index');
        $makeForm($tenderSales, $tenderTxnSubmenu, 'Tender Evaluation',    'tender_evaluations_form', 'tender-evaluations.index');
        $makeForm($tenderSales, $tenderTxnSubmenu, 'Customer Complaints',  'ts_customer_complaints_form','customer-complaints.index');

        // Supplier group
        $makeForm($supplierMenu, $supplierSubmenu, 'Suppliers',               'suppliers_form',               'suppliers.index');
        $makeForm($supplierMenu, $supplierSubmenu, 'Supplier Evaluation',     'supplier_evaluations_form',    'supplier-evaluations.index');
        $makeForm($supplierMenu, $supplierSubmenu, 'Subcontractor Evaluation','subcontractor_evaluations_form','subcontractor-evaluations.index');

        // Settings / Company (Settings group)
        $makeForm($settings, null, 'Company Information', 'company_information_form', 'company-information.index');

        // System (header notifications)
        $makeForm($system, null, 'Notifications', 'notifications_form', 'notifications.index');
    }
}


