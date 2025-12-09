# Fix Missing Permissions in Role Permission Form

## Issue
Some forms are not showing up in the "Assign Permissions to Role" form.

## Solution Applied

### 1. Updated RolePermissionController
- Added filter for `is_active = true` to ensure only active permissions are shown
- Added error handling for the COALESCE query
- Added logging to detect permission count mismatches

### 2. SQL Query to Check Permissions

Run this query to see all permissions:
```sql
SELECT 
    id,
    form_name,
    name,
    is_active,
    created_at
FROM permissions
WHERE is_active = 1
ORDER BY COALESCE(form_name, name);
```

### 3. SQL Query to Ensure All Expected Permissions Exist

If some permissions are missing, run this to create them:
```sql
-- Check which permissions from the seeder are missing
-- Expected permissions (from ModuleActionPermissionSeeder):
-- branches, users, roles, permissions, products, units, customers, 
-- quotations, proforma-invoices, tax, company-info, reports,
-- raw-material-categories, raw-material-sub-categories, raw-materials,
-- product-categories, processes, bom-processes, departments,
-- designations, production-departments, employees, billing-addresses,
-- tenders, discounts
```

### 4. Run Seeder to Create Missing Permissions

If permissions are missing, run:
```bash
php artisan db:seed --class=ModuleActionPermissionSeeder
```

### 5. Verify All Permissions Are Active

```sql
-- Check for inactive permissions
SELECT id, form_name, name, is_active 
FROM permissions 
WHERE is_active = 0;

-- Activate all permissions if needed
UPDATE permissions SET is_active = 1 WHERE is_active = 0;
```

## Changes Made to Code

1. **app/Http/Controllers/RolePermissionController.php**:
   - Line 79-81: Added `where('is_active', true)` filter in `create()` method
   - Line 110-125: Added `where('is_active', true)` filter and error handling in `edit()` method

## Testing

After the fix, verify:
1. Go to "Assign Permissions to Role" form
2. Select a role
3. Check that all 25 permissions are displayed in the table
4. All forms should be visible: branches, users, roles, permissions, products, units, customers, quotations, proforma-invoices, tax, company-info, reports, raw-material-categories, raw-material-sub-categories, raw-materials, product-categories, processes, bom-processes, departments, designations, production-departments, employees, billing-addresses, tenders, discounts

