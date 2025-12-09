<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Designation;
use App\Models\Department;

class DesignationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('designations', 'view')) {
            abort(403, 'You do not have permission to view designations.');
        }

        $query = Designation::with('department');
        $query = $this->applyBranchFilter($query, Designation::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('department', function($deptQuery) use ($search) {
                      $deptQuery->where('name', 'like', "%{$search}%");
                  })
                  // Search in dates
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        $designations = $query->latest()->paginate(15)->withQueryString();
        return view('masters.designations.index', compact('designations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('designations', 'create')) {
            abort(403, 'You do not have permission to create designations.');
        }

        // Get departments filtered by branch
        $departmentQuery = Department::query();
        $departmentQuery = $this->applyBranchFilter($departmentQuery, Department::class);
        $departments = $departmentQuery->get();

        return view('masters.designations.create', compact('departments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('designations', 'create')) {
            abort(403, 'You do not have permission to create designations.');
        }

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'department_id.required' => 'Department Name is required.',
            'department_id.exists' => 'Selected department does not exist.',
            'name.required' => 'Designation Name is required.',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        Designation::create($data);

        return redirect()->route('designations.index')->with('success', 'Designation created successfully.');
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
        if (!$user->isSuperAdmin() && !$user->hasPermission('designations', 'edit')) {
            abort(403, 'You do not have permission to edit designations.');
        }

        $query = Designation::query();
        $query = $this->applyBranchFilter($query, Designation::class);
        $designation = $query->findOrFail($id);

        // Get departments filtered by branch
        $departmentQuery = Department::query();
        $departmentQuery = $this->applyBranchFilter($departmentQuery, Department::class);
        $departments = $departmentQuery->get();

        return view('masters.designations.edit', compact('designation', 'departments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('designations', 'edit')) {
            abort(403, 'You do not have permission to edit designations.');
        }

        $query = Designation::query();
        $query = $this->applyBranchFilter($query, Designation::class);
        $designation = $query->findOrFail($id);

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ], [
            'department_id.required' => 'Department Name is required.',
            'department_id.exists' => 'Selected department does not exist.',
            'name.required' => 'Designation Name is required.',
        ]);

        $designation->update($request->all());

        return redirect()->route('designations.index')->with('success', 'Designation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('designations', 'delete')) {
            abort(403, 'You do not have permission to delete designations.');
        }

        $query = Designation::query();
        $query = $this->applyBranchFilter($query, Designation::class);
        $designation = $query->findOrFail($id);
        $designation->delete();

        return redirect()->route('designations.index')->with('success', 'Designation deleted successfully.');
    }
}
