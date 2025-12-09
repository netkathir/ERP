<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductionDepartment;

class ProductionDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('production-departments', 'view')) {
            abort(403, 'You do not have permission to view production departments.');
        }

        $query = ProductionDepartment::query();
        $query = $this->applyBranchFilter($query, ProductionDepartment::class);

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

        $productionDepartments = $query->latest()->paginate(15)->withQueryString();
        return view('masters.production-departments.index', compact('productionDepartments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('production-departments', 'create')) {
            abort(403, 'You do not have permission to create production departments.');
        }

        return view('masters.production-departments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('production-departments', 'create')) {
            abort(403, 'You do not have permission to create production departments.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:production_departments,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Production Department is required.',
            'name.unique' => 'This Production Department already exists.',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        ProductionDepartment::create($data);

        return redirect()->route('production-departments.index')->with('success', 'Production Department created successfully.');
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
        if (!$user->isSuperAdmin() && !$user->hasPermission('production-departments', 'edit')) {
            abort(403, 'You do not have permission to edit production departments.');
        }

        $query = ProductionDepartment::query();
        $query = $this->applyBranchFilter($query, ProductionDepartment::class);
        $productionDepartment = $query->findOrFail($id);
        return view('masters.production-departments.edit', compact('productionDepartment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('production-departments', 'edit')) {
            abort(403, 'You do not have permission to edit production departments.');
        }

        $query = ProductionDepartment::query();
        $query = $this->applyBranchFilter($query, ProductionDepartment::class);
        $productionDepartment = $query->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:production_departments,name,' . $id,
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Production Department is required.',
            'name.unique' => 'This Production Department already exists.',
        ]);

        $productionDepartment->update($request->all());

        return redirect()->route('production-departments.index')->with('success', 'Production Department updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('production-departments', 'delete')) {
            abort(403, 'You do not have permission to delete production departments.');
        }

        $query = ProductionDepartment::query();
        $query = $this->applyBranchFilter($query, ProductionDepartment::class);
        $productionDepartment = $query->findOrFail($id);
        $productionDepartment->delete();

        return redirect()->route('production-departments.index')->with('success', 'Production Department deleted successfully.');
    }
}
