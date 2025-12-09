# Permission System - Final Structure

## Overview

The permission system uses a simplified, streamlined approach with **three permission types** that cover all necessary actions for each resource/form.

## Permission Structure

### Resource-Based Permissions

Instead of having separate permissions like:
- ❌ "Units - Create"
- ❌ "Units - Delete"  
- ❌ "Units - Edit"
- ❌ "Units - View"

We use **resource-based permissions**:
- ✅ **"Units"** with Read/Write/Delete actions

This simplifies the UI and makes permission management more intuitive.

## Three Permission Types

### 1. **Read (View)**
- **Grants:** View access only
- **Actions Allowed:**
  - View list of records
  - View individual record details
- **Actions Denied:**
  - Cannot edit records
  - Cannot add new records
  - Cannot delete records

**Example:** User with Read permission on "Units" can view the units list and see unit details, but cannot make any changes.

---

### 2. **Write (Edit/Add)**
- **Grants:** Edit or Add access
- **Actions Allowed:**
  - View list of records (includes Read)
  - View individual record details
  - Edit existing records
  - Add new records
- **Actions Denied:**
  - Cannot delete records

**Example:** User with Write permission on "Units" can:
- View units list
- View unit details
- Create new units
- Edit existing units
- **Cannot:** Delete units

---

### 3. **Delete (Full Access)**
- **Grants:** Complete control
- **Actions Allowed:**
  - All Read actions (View)
  - All Write actions (Edit, Add)
  - Delete records

**Example:** User with Delete permission on "Units" has full access:
- View units
- Create units
- Edit units
- Delete units

**Note:** Delete permission is the most powerful and includes Read + Write + Delete capabilities.

## Permission Hierarchy

```
Delete (Full Access)
  ├── Includes Write permissions
  │     ├── Includes Read permissions
  │     │     └── View only
  │     └── Edit + Add
  └── Delete records
```

## Permission Mapping in Code

The system automatically maps action names to permission types:

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

## Usage Examples

### Example 1: Units Management

**Role: Units Viewer**
- Units - Read: ✓
- Units - Write: ✗
- Units - Delete: ✗

**Result:** User can only view the units list and details. Cannot create, edit, or delete.

---

**Role: Units Manager**
- Units - Read: ✓
- Units - Write: ✓
- Units - Delete: ✗

**Result:** User can view, create, and edit units, but cannot delete them.

---

**Role: Units Administrator**
- Units - Read: ✓
- Units - Write: ✓
- Units - Delete: ✓

**Result:** User has full control over units (view, create, edit, delete).

### Example 2: Mixed Permissions

**Role: Sales Representative**
- Customers - Read: ✓
- Customers - Write: ✓
- Customers - Delete: ✗
- Products - Read: ✓
- Products - Write: ✗
- Products - Delete: ✗

**Result:** 
- Can fully manage customers (view, create, edit) but cannot delete
- Can only view products (read-only)

## Controller Implementation

### Standard CRUD Permission Checks

```php
public function index()
{
    // Check Read permission for viewing list
    if (!$user->isSuperAdmin() && !$user->hasPermission('units', 'view')) {
        abort(403, 'You do not have permission to view units.');
    }
    // ... show list
}

public function create()
{
    // Check Write permission for creating
    if (!$user->isSuperAdmin() && !$user->hasPermission('units', 'create')) {
        abort(403, 'You do not have permission to create units.');
    }
    // ... show create form
}

public function store(Request $request)
{
    // Check Write permission for creating
    if (!$user->isSuperAdmin() && !$user->hasPermission('units', 'create')) {
        abort(403, 'You do not have permission to create units.');
    }
    // ... create unit
}

public function edit($id)
{
    // Check Write permission for editing
    if (!$user->isSuperAdmin() && !$user->hasPermission('units', 'edit')) {
        abort(403, 'You do not have permission to edit units.');
    }
    // ... show edit form
}

public function update(Request $request, $id)
{
    // Check Write permission for editing
    if (!$user->isSuperAdmin() && !$user->hasPermission('units', 'edit')) {
        abort(403, 'You do not have permission to edit units.');
    }
    // ... update unit
}

public function destroy($id)
{
    // Check Delete permission for deleting
    if (!$user->isSuperAdmin() && !$user->hasPermission('units', 'delete')) {
        abort(403, 'You do not have permission to delete units.');
    }
    // ... delete unit
}
```

## Benefits

1. **Simplified UI:** Only 3 checkboxes per form instead of 4-5 separate permissions
2. **Resource-Based:** Focus on the resource (Units, Customers) rather than individual actions
3. **Intuitive:** Matches natural workflow (Read → Write → Delete)
4. **Flexible:** Can assign any combination of permissions per form
5. **Maintainable:** Less complexity in code and database
6. **Clear:** Easy to understand what each permission level grants

## Permission Assignment Workflow

1. Admin goes to **Role Permissions** menu
2. Selects a role from the list
3. Sees all forms/resources (Units, Customers, Products, etc.)
4. For each form, assigns:
   - **Read** checkbox (View only)
   - **Write** checkbox (Edit/Add)
   - **Delete** checkbox (Full access)
5. Saves permissions

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

4. **Principle of least privilege:**
   - Start with Read only
   - Add Write only when needed
   - Grant Delete only for administrators

## Summary

- **Read** = View only (no changes allowed)
- **Write** = Edit or Add (but no delete)
- **Delete** = Full control (Read + Write + Delete)

This simplified approach makes permission management easier and more intuitive while maintaining full control over access levels.

