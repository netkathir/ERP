<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('departments', 'view')) {
            abort(403, 'You do not have permission to view departments.');
        }

        $query = Department::query();
        $query = $this->applyBranchFilter($query, Department::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  // Search in dates
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        $departments = $query->latest()->paginate(15)->withQueryString();
        return view('masters.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('departments', 'create')) {
            abort(403, 'You do not have permission to create departments.');
        }

        return view('masters.departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('departments', 'create')) {
            abort(403, 'You do not have permission to create departments.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Department Name is required.',
            'name.unique' => 'This Department Name already exists.',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        Department::create($data);

        return redirect()->route('departments.index')->with('success', 'Department created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('departments', 'edit')) {
            abort(403, 'You do not have permission to edit departments.');
        }

        $query = Department::query();
        $query = $this->applyBranchFilter($query, Department::class);
        $department = $query->findOrFail($id);
        return view('masters.departments.edit', compact('department'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('departments', 'edit')) {
            abort(403, 'You do not have permission to edit departments.');
        }

        $query = Department::query();
        $query = $this->applyBranchFilter($query, Department::class);
        $department = $query->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name,' . $id,
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Department Name is required.',
            'name.unique' => 'This Department Name already exists.',
        ]);

        $department->update($request->all());

        return redirect()->route('departments.index')->with('success', 'Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('departments', 'delete')) {
            abort(403, 'You do not have permission to delete departments.');
        }

        $query = Department::query();
        $query = $this->applyBranchFilter($query, Department::class);
        $department = $query->findOrFail($id);
        $department->delete();

        return redirect()->route('departments.index')->with('success', 'Department deleted successfully.');
    }
}
