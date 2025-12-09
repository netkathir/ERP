# Permission System - Simplified Approach

## Overview

The permission system uses a simplified, streamlined approach with just **three permission types** that cover all necessary actions:

## Permission Types

### 1. **Read** (View Access)
- **Grants:** View/List data
- **Actions Covered:** 
  - View list of records
  - View individual record details
  - Read-only access to forms
- **Example:** User with Read permission on "branches" can view the branches list and see branch details, but cannot make any changes.

### 2. **Write** (Create & Edit Access)
- **Grants:** View, Create, and Edit data
- **Actions Covered:**
  - View/List data (includes Read)
  - Create new records
  - Edit existing records
- **Example:** User with Write permission on "branches" can:
  - View branches list
  - Create new branches
  - Edit existing branches
  - **Cannot:** Delete branches

### 3. **Delete** (Full Access)
- **Grants:** View, Create, Edit, and Delete data
- **Actions Covered:**
  - All Read actions
  - All Write actions (Create & Edit)
  - Delete records
- **Example:** User with Delete permission on "branches" has full access:
  - View branches
  - Create branches
  - Edit branches
  - Delete branches

## Permission Mapping in Code

The system automatically maps common action names to these three permission types:

```php
// In User model's hasPermission() method
$typeMap = [
    'view' => 'read',      // View action → Read permission
    'read' => 'read',      // Read action → Read permission
    'create' => 'write',   // Create action → Write permission
    'edit' => 'write',     // Edit action → Write permission
    'write' => 'write',    // Write action → Write permission
    'delete' => 'delete',  // Delete action → Delete permission
    'destroy' => 'delete', // Destroy action → Delete permission
];
```

## Usage in Controllers

### Example: Department Controller

```php
public function index()
{
    $user = auth()->user();
    
    // Check Read permission (for viewing list)
    if (!$user->isSuperAdmin() && !$user->hasPermission('departments', 'view')) {
        abort(403, 'You do not have permission to view departments.');
    }
    // ... show list
}

public function store(Request $request)
{
    $user = auth()->user();
    
    // Check Write permission (for creating)
    if (!$user->isSuperAdmin() && !$user->hasPermission('departments', 'create')) {
        abort(403, 'You do not have permission to create departments.');
    }
    // ... create department
}

public function update(Request $request, $id)
{
    $user = auth()->user();
    
    // Check Write permission (for editing)
    if (!$user->isSuperAdmin() && !$user->hasPermission('departments', 'edit')) {
        abort(403, 'You do not have permission to edit departments.');
    }
    // ... update department
}

public function destroy($id)
{
    $user = auth()->user();
    
    // Check Delete permission (for deleting)
    if (!$user->isSuperAdmin() && !$user->hasPermission('departments', 'delete')) {
        abort(403, 'You do not have permission to delete departments.');
    }
    // ... delete department
}
```

## Permission Hierarchy

The permissions follow a natural hierarchy:

```
Delete (Full Access)
  ├── Includes Write permissions
  │     ├── Includes Read permissions
  │     │     └── View only
  │     └── Create & Edit
  └── Delete records
```

**Important:** When assigning permissions:
- **Delete** automatically implies Write and Read
- **Write** automatically implies Read
- **Read** is the base level

However, in the UI, you can assign them independently. The system checks the specific permission level required for each action.

## Benefits of This Approach

1. **Simplified UI:** Only 3 checkboxes per form instead of 4-5 separate permissions
2. **Intuitive:** Matches natural workflow (Read → Write → Delete)
3. **Flexible:** Can assign any combination of permissions
4. **Maintainable:** Less complexity in code and database
5. **Clear:** Easy to understand what each permission level grants

## Examples

### Example 1: Branch Management

**Role: Branch Viewer**
- Read: ✓ (can view branches)
- Write: ✗ (cannot create/edit)
- Delete: ✗ (cannot delete)

**Result:** User can only view the branches list and details.

---

**Role: Branch Manager**
- Read: ✓ (can view)
- Write: ✓ (can create/edit)
- Delete: ✗ (cannot delete)

**Result:** User can view, create, and edit branches, but cannot delete them.

---

**Role: Branch Admin**
- Read: ✓ (can view)
- Write: ✓ (can create/edit)
- Delete: ✓ (can delete)

**Result:** User has full access to all branch operations.

### Example 2: Customer Management

**Role: Sales Representative**
- Customers - Read: ✓
- Customers - Write: ✓
- Customers - Delete: ✗

**Result:** Can view, create, and edit customers, but cannot delete customer records.

## Implementation Notes

- All controllers use `hasPermission($form, $action)` method
- Actions are automatically mapped to Read/Write/Delete
- Super Admin bypasses all permission checks
- Permissions are checked at controller level for each action
- Audit trail logs all permission changes

## Best Practices

1. **Assign permissions based on job roles:**
   - Viewers: Read only
   - Operators: Read + Write
   - Administrators: Read + Write + Delete

2. **Use descriptive role names:**
   - "Customer Viewer"
   - "Product Manager"
   - "System Administrator"

3. **Regular audits:**
   - Review permission assignments periodically
   - Use audit trail to track changes
   - Ensure users have appropriate access levels

