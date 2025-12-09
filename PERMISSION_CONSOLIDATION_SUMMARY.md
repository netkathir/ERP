# Permission Consolidation Summary

## Overview
Permissions have been consolidated from action-based to resource-based structure.

## Before (Action-Based)
Each resource had multiple permission entries:
- `Branches - View`
- `Branches - Create`
- `Branches - Edit`
- `Branches - Delete`

## After (Resource-Based)
Each resource now has a single permission entry:
- `branches` (with Read/Write/Delete flags in `role_permission` pivot table)

## Changes Made

### 1. Database Consolidation
- Created `ConsolidatePermissionsSeeder` to:
  - Create resource-based permissions (one per resource)
  - Map old permissions to new ones in `role_permission` table
  - Preserve Read/Write/Delete flags from old permissions
  - Delete duplicate old permissions

### 2. Updated Seeders
- **`ModuleActionPermissionSeeder.php`**: Now creates resource-based permissions instead of action-based
- **`PermissionSeeder.php`**: Updated to create resource-based permissions

### 3. Permission Structure
Each permission now has:
- `form_name`: Resource name (e.g., "branches", "users", "products")
- `name`: Display name (e.g., "Branches", "Users", "Products")
- `slug`: Resource slug (e.g., "branches", "users", "products")

### 4. Role Permission Mapping
The `role_permission` pivot table stores:
- `read`: Boolean flag for read/view access
- `write`: Boolean flag for write/create/edit access
- `delete`: Boolean flag for delete access

## Resources Consolidated
The following resources now have single permission entries:
- branches
- users
- roles
- permissions
- units
- customers
- products
- quotations
- proforma-invoices
- tax
- company-info
- reports
- raw-material-categories
- raw-material-sub-categories
- raw-materials
- product-categories
- processes
- bom-processes
- departments
- designations
- production-departments
- employees
- billing-addresses
- tenders
- discounts

## How It Works

### Permission Checking
The `User` model's `hasPermission()` method:
1. Checks if user is Super Admin (returns true)
2. Maps action types:
   - `view`, `read` → checks `read` flag
   - `create`, `edit`, `write` → checks `write` flag
   - `delete`, `destroy` → checks `delete` flag
3. Checks user's roles for the permission with the appropriate flag

### Example Usage
```php
// Check if user can view branches
$user->hasPermission('branches', 'view'); // Checks 'read' flag

// Check if user can create branches
$user->hasPermission('branches', 'create'); // Checks 'write' flag

// Check if user can delete branches
$user->hasPermission('branches', 'delete'); // Checks 'delete' flag
```

## Role Permission UI
The Role Permission assignment page (`/role-permissions/{role}/edit`) shows:
- One row per resource/form
- Three checkboxes: Read, Write, Delete
- Clear explanation of what each permission type allows

## Migration
To run the consolidation:
```bash
php artisan db:seed --class=ConsolidatePermissionsSeeder
```

## Notes
- Super Admin users bypass all permission checks
- Multiple roles combine permissions (OR logic)
- All existing role-permission mappings were preserved during consolidation
- Old duplicate permissions were automatically deleted

