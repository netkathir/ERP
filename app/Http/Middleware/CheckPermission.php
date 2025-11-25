<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $module
     * @param  string  $action
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $module, string $action = 'view')
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Super Admin has all permissions
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Check if user's role has the required permission
        $role = $user->role;
        if (!$role) {
            abort(403, 'No role assigned.');
        }

        $permission = \App\Models\Permission::where('module', $module)
            ->where('action', $action)
            ->where('is_active', true)
            ->first();

        if (!$permission) {
            abort(403, "Permission not found: {$module}.{$action}");
        }

        $hasPermission = $role->permissions()->where('permissions.id', $permission->id)->exists();

        if (!$hasPermission) {
            abort(403, "You don't have permission to {$action} {$module}.");
        }

        return $next($request);
    }
}
