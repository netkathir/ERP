# Debugging Guide

This guide explains how to use the debugging helpers in the application.

## Quick Start

### Simple Debug Logging
```php
// Log data with an index for easy tracking
debug_log($data, 'MY_DEBUG_INDEX');

// Example:
debug_log($request->all(), 'USER_CREATE_REQUEST');
debug_log($user->toArray(), 'USER_CREATED');
```

### Debug and Die
```php
// Stop execution and show data
debug_dd($data, 'STOP_HERE');

// Example:
debug_dd($user->roles, 'CHECK_ROLES');
```

### Debug Dump
```php
// Dump data without stopping execution
debug_dump($data, 'INSPECT_DATA');

// Example:
debug_dump($permissions, 'PERMISSIONS_LIST');
```

### Debug with Context
```php
// Automatically includes file, line, function info
debug_context($data, 'CONTEXT_DEBUG');

// Example:
debug_context($role->permissions, 'ROLE_PERMISSIONS');
```

### Debug User Permissions
```php
// Debug all permissions for a user
debug_permissions($user, null, 'USER_PERMS');

// Debug specific form permission
debug_permissions($user, 'customers', 'CUSTOMER_PERMS');
```

## Usage Examples

### In Controllers
```php
use App\Helpers\DebugHelper;

public function store(Request $request)
{
    // Debug request data
    debug_log($request->all(), 'STORE_REQUEST');
    
    // Debug with context
    debug_context($request->input('name'), 'NAME_INPUT');
    
    // Check permissions
    debug_permissions(auth()->user(), 'customers', 'CHECK_PERMS');
    
    // Your code here...
}
```

### In Models
```php
protected static function boot()
{
    parent::boot();
    
    static::created(function ($model) {
        debug_log($model->toArray(), 'MODEL_CREATED');
    });
}
```

### Performance Timing
```php
use App\Helpers\DebugHelper;

$timer = DebugHelper::timer('MY_OPERATION');
// ... your code ...
$timer(); // Logs time and memory usage
```

### SQL Query Debugging
```php
use App\Helpers\DebugHelper;

DebugHelper::debugQueries('BEFORE_QUERY');
$users = User::with('roles')->get();
DebugHelper::debugQueries('AFTER_QUERY');
```

## Debug Index Best Practices

Use descriptive, unique indexes:
- `USER_CREATE_REQUEST` - User creation request
- `PERMISSION_CHECK` - Permission verification
- `ROLE_UPDATE` - Role update operation
- `AUDIT_LOG` - Audit trail logging

## Viewing Debug Logs

Debug logs are written to Laravel's log file:
- **Location:** `storage/logs/laravel.log`
- **View in real-time:** `tail -f storage/logs/laravel.log` (Linux/Mac)
- **Or use:** `php artisan tail` (if installed)

## Production Considerations

⚠️ **Important:** Debug functions log to files. In production:
1. Use `debug_log()` sparingly
2. Never use `debug_dd()` in production (stops execution)
3. Consider using environment checks:
   ```php
   if (config('app.debug')) {
       debug_log($data, 'INDEX');
   }
   ```

## Advanced Usage

### Custom Debug Helper
```php
use App\Helpers\DebugHelper;

// Custom debug with multiple data points
DebugHelper::debug([
    'user_id' => auth()->id(),
    'action' => 'create',
    'data' => $request->all(),
], 'CUSTOM_DEBUG');
```

### Debug Request
```php
use App\Helpers\DebugHelper;

DebugHelper::debugRequest($request, 'API_REQUEST');
```

## Common Debug Scenarios

### 1. Debugging Permission Issues
```php
debug_permissions(auth()->user(), 'customers', 'DEBUG_PERMS');
// Check if user has permission
if (!auth()->user()->hasPermission('customers', 'write')) {
    debug_log('Permission denied', 'PERM_DENIED');
}
```

### 2. Debugging Role Assignment
```php
$user = User::find(1);
debug_log($user->roles->pluck('name'), 'USER_ROLES');
debug_permissions($user, null, 'USER_ALL_PERMS');
```

### 3. Debugging Form Data
```php
public function store(Request $request)
{
    debug_log($request->all(), 'FORM_SUBMIT');
    debug_context($request->input('email'), 'EMAIL_VALIDATION');
    // ... validation and save
}
```

### 4. Debugging Database Queries
```php
DebugHelper::debugQueries('BEFORE');
$results = Model::where('status', 'active')->get();
DebugHelper::debugQueries('AFTER');
```

## Tips

1. **Use unique indexes** - Makes searching logs easier
2. **Group related debugs** - Use same prefix (e.g., `USER_*`, `ROLE_*`)
3. **Remove debug calls** - Clean up before production deployment
4. **Use context debug** - When you need to know where the debug was called from
5. **Log important operations** - Helps track down issues

## Example: Complete Debug Flow

```php
public function updateRolePermissions(Request $request, Role $role)
{
    // 1. Debug incoming request
    debug_log($request->all(), 'ROLE_PERM_UPDATE_REQUEST');
    
    // 2. Debug current state
    debug_log($role->permissions->toArray(), 'CURRENT_PERMISSIONS');
    
    // 3. Debug user making change
    debug_permissions(auth()->user(), 'roles', 'ADMIN_PERMS');
    
    // 4. Process changes
    // ... your code ...
    
    // 5. Debug final state
    $role->refresh();
    debug_log($role->permissions->toArray(), 'UPDATED_PERMISSIONS');
    
    return redirect()->back();
}
```

