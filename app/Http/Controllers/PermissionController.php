<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $permissions = Permission::where('module', '!=', 'organizations')
            ->latest()
            ->paginate(15);
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'slug' => 'required|string|max:255|unique:permissions|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string|max:500',
            'module' => 'nullable|string|max:255|regex:/^[a-z0-9-]+$/',
        ], [
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and hyphens.',
            'module.regex' => 'Module must contain only lowercase letters, numbers, and hyphens.',
        ]);

        Permission::create($request->all());

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $permission = Permission::with('roles')->findOrFail($id);
        return view('permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $permission = Permission::findOrFail($id);
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'slug' => 'required|string|max:255|unique:permissions,slug,' . $id . '|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string|max:500',
            'module' => 'nullable|string|max:255|regex:/^[a-z0-9-]+$/',
        ], [
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and hyphens.',
            'module.regex' => 'Module must contain only lowercase letters, numbers, and hyphens.',
        ]);

        $permission->update($request->all());

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
