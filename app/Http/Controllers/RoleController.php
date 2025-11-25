<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Only Super Admin can manage roles
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                abort(403, 'Only Super Admin can manage roles.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $roles = Role::with('permissions')->latest()->paginate(15);
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $permissions = Permission::where('is_active', true)
            ->orderBy('module')
            ->orderBy('action')
            ->get()
            ->groupBy('module');
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'slug' => 'required|string|max:255|unique:roles|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string|max:500',
            'is_system_role' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and hyphens.',
        ]);

        $data = $request->only(['name', 'slug', 'description', 'is_active', 'is_system_role']);
        // Prevent creating system roles through UI (only via seeders)
        $data['is_system_role'] = false;
        
        $role = Role::create($data);

        if ($request->has('permissions')) {
            $role->permissions()->attach($request->permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $role = Role::with('permissions')->findOrFail($id);
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::where('is_active', true)
            ->orderBy('module')
            ->orderBy('action')
            ->get()
            ->groupBy('module');
        return view('roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);

        // Prevent editing system roles
        if ($role->isSystemRole() || $role->slug === 'super-admin') {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot edit system roles.');
        }

        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'slug' => 'required|string|max:255|unique:roles,slug,' . $id . '|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and hyphens.',
        ]);

        $data = $request->only(['name', 'slug', 'description', 'is_active']);
        // Prevent changing system_role flag through UI
        $data['is_system_role'] = $role->is_system_role;
        
        $role->update($data);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->detach();
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $role = Role::findOrFail($id);
        
        // Prevent deleting system roles
        if (!$role->canBeDeleted()) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete system roles.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
