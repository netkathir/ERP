# Role Permission System - Complete Implementation

## Overview

This document describes the complete Role Permission system implementation that matches all the requirements specified.

## Features Implemented

### 1. Role Master ✅
- **Create Roles**: Admin can create roles with name and description
- **Edit Roles**: Admin can update role details
- **Delete Roles**: Admin can delete roles
- **List Roles**: View all roles in the system
- **Validation**: Role name must be unique

**Location:**
- Controller: `app/Http/Controllers/RoleController.php`
- Views: `resources/views/masters/roles/`
- Routes: `GET/POST /roles`, `GET/PUT /roles/{id}`, `DELETE /roles/{id}`

### 2. Role Permission Mapping ✅
- **Assign Permissions**: Admin can assign Read/Write/Delete permissions to roles for each form
- **Permission Types**:
  - **Read**: User can view data but cannot edit or delete
  - **Write**: User can view, add, and edit data but cannot delete
  - **Delete (Full Access)**: User can view, add, edit, and delete data
- **Form-based Permissions**: Each form in the system can have different permission levels

**Location:**
- Controller: `app/Http/Controllers/RolePermissionController.php`
- View: `resources/views/masters/roles/permissions.blade.php`
- Routes: `GET/POST /roles/{role}/permissions`

### 3. User Role Mapping ✅
- **Multiple Roles**: Users can be assigned one or more roles
- **Permission Inheritance**: Users inherit permissions from all assigned roles
- **User Creation**: During user creation, admin can assign roles
- **User Edit**: Admin can modify user roles

**Location:**
- Controller: `app/Http/Controllers/UserController.php`
- Model: `app/Models/User.php` (has `roles()` relationship)

### 4. Permission Enforcement ✅
- **Middleware**: `CheckPermission` middleware enforces permissions
- **Controller Checks**: Controllers check permissions using `hasPermission()` method
- **Super Admin**: Super Admin bypasses all permission checks
- **Permission Checking**: `$user->hasPermission($form, $type)`

**Location:**
- Middleware: `app/Http/Middleware/CheckPermission.php`
- User Model: `app/Models/User.php` (has `hasPermission()` method)

### 5. Audit Trail ✅
- **Role Changes**: Logs when roles are created, updated, or deleted
- **Permission Changes**: Logs when permissions are assigned or removed
- **Field-level Tracking**: Tracks changes to individual fields (read/write/delete)
- **User Tracking**: Records which admin made the change
- **Timestamp**: Records date and time of changes
- **Audit Reports**: View audit trail and generate reports

**Location:**
- Model: `app/Models/RolePermissionAudit.php`
- Controller: `app/Http/Controllers/RolePermissionAuditController.php`
- Migration: `database/migrations/2025_11_26_173823_create_role_permission_audit_table.php`
- Routes: `GET /roles/audit`, `GET /roles/{role}/audit`, `GET /roles/report/permissions`

## Database Structure

### Tables

1. **roles**
   - `id`, `name` (unique), `description`, `created_at`, `updated_at`

2. **permissions**
   - `id`, `form_name` (unique), `created_at`, `updated_at`

3. **role_permission** (Pivot Table)
   - `role_id`, `permission_id`, `read` (boolean), `write` (boolean), `delete` (boolean), `created_at`, `updated_at`

4. **user_role** (Pivot Table)
   - `user_id`, `role_id`, `created_at`, `updated_at`

5. **role_permission_audit**
   - `id`, `role_id`, `permission_id`, `changed_by`, `action`, `field_name`, `old_value`, `new_value`, `description`, `created_at`, `updated_at`

## Usage Examples

### Check Permissions in Controller
```php
public function index()
{
    $user = auth()->user();
    
    // Super Admin has access by default, but check permission for other users
    if (!$user->isSuperAdmin() && !$user->hasPermission('customers', 'view')) {
        abort(403, 'You do not have permission to view customers.');
    }
    
    // Your code here...
}
```

### Assign Permissions to Role
1. Go to Roles list: `/roles`
2. Click "Manage Permissions" (key icon) for a role
3. Check/uncheck Read/Write/Delete for each form
4. Click "Save Permissions"

### Assign Roles to User
1. Go to Users: `/users`
2. Create or Edit a user
3. Select one or more roles from the roles dropdown
4. Save the user

### View Audit Trail
1. Go to `/roles/audit` to view all audit logs
2. Filter by role, action, or date range
3. View specific role audit: `/roles/{role}/audit`
4. Generate permission report: `/roles/report/permissions`

## Debugging

### Debug User Permissions
```php
use function App\Helpers\debug_permissions;

// Debug all permissions for a user
debug_permissions($user, null, 'USER_PERMS');

// Debug specific form permission
debug_permissions($user, 'customers', 'CUSTOMER_PERMS');
```

### Debug Permission Check
```php
use function App\Helpers\debug_log;

$hasPermission = $user->hasPermission('customers', 'write');
debug_log([
    'user_id' => $user->id,
    'form' => 'customers',
    'type' => 'write',
    'has_permission' => $hasPermission
], 'PERMISSION_CHECK');
```

See `DEBUG_GUIDE.md` for complete debugging documentation.

## Security

1. **Admin Only**: Only Super Admin can manage roles and permissions
2. **Authentication**: All routes require authentication
3. **Authorization**: Permission checks are enforced at middleware and controller levels
4. **Audit Trail**: All changes are logged for security and compliance

## Validation Rules

1. **Role Name**: Must be unique
2. **Permissions**: At least one permission (Read/Write/Delete) must be selected for each form
3. **User Roles**: User must have at least one role assigned
4. **Form Names**: Permission form names must be unique

## Reports Available

1. **Audit Trail Report**: View all role and permission changes
   - Filter by role, action, date range
   - Shows who made the change, what changed, and when

2. **Permission Report**: View role assignments and permissions for each user
   - Shows all users with their assigned roles
   - Shows permissions for each form
   - Shows merged permissions from all roles

## API/Usage

### Check if User Has Permission
```php
$user->hasPermission('customers', 'read');   // Check read permission
$user->hasPermission('customers', 'write');  // Check write permission
$user->hasPermission('customers', 'delete'); // Check delete permission
```

### Get User Roles
```php
$user->roles; // Collection of Role models
$user->roles->pluck('name'); // Array of role names
```

### Get Role Permissions
```php
$role->permissions; // Collection of Permission models with pivot data
foreach ($role->permissions as $permission) {
    $read = $permission->pivot->read;
    $write = $permission->pivot->write;
    $delete = $permission->pivot->delete;
}
```

## Routes Summary

| Method | Route | Controller | Description |
|--------|-------|------------|-------------|
| GET | `/roles` | RoleController@index | List all roles |
| GET | `/roles/create` | RoleController@create | Create role form |
| POST | `/roles` | RoleController@store | Store new role |
| GET | `/roles/{id}/edit` | RoleController@edit | Edit role form |
| PUT | `/roles/{id}` | RoleController@update | Update role |
| DELETE | `/roles/{id}` | RoleController@destroy | Delete role |
| GET | `/roles/{role}/permissions` | RolePermissionController@edit | Manage permissions |
| POST | `/roles/{role}/permissions` | RolePermissionController@update | Update permissions |
| GET | `/roles/audit` | RolePermissionAuditController@index | View audit trail |
| GET | `/roles/{role}/audit` | RolePermissionAuditController@showRole | View role audit |
| GET | `/roles/report/permissions` | RolePermissionAuditController@report | Permission report |

## Testing

### Test Permission Enforcement
1. Create a role with only "Read" permission for "customers"
2. Assign role to a user
3. Login as that user
4. Try to edit/delete a customer - should be denied
5. Try to view customers - should be allowed

### Test Multiple Roles
1. Create two roles with different permissions
2. Assign both roles to a user
3. User should have merged permissions (highest level from any role)

### Test Audit Trail
1. Create/update/delete a role
2. Assign/remove permissions
3. Check audit trail at `/roles/audit`
4. Verify all changes are logged

## Future Enhancements

1. **Permission Groups**: Group related permissions together
2. **Role Templates**: Pre-defined role templates for common roles
3. **Permission Inheritance**: Hierarchical permission structure
4. **Time-based Permissions**: Permissions that expire after a certain time
5. **IP-based Restrictions**: Restrict permissions based on IP address

## Support

For issues or questions:
1. Check the audit trail to see what changed
2. Use debugging helpers to trace permission checks
3. Review `DEBUG_GUIDE.md` for debugging techniques
4. Check logs in `storage/logs/laravel.log`

