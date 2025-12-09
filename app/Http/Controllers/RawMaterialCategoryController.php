<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RawMaterialCategory;

class RawMaterialCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-material-categories', 'view')) {
            abort(403, 'You do not have permission to view raw material categories.');
        }

        $query = RawMaterialCategory::query();
        $query = $this->applyBranchFilter($query, RawMaterialCategory::class);

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

        $categories = $query->latest()->paginate(15)->withQueryString();
        return view('masters.raw-material-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-material-categories', 'create')) {
            abort(403, 'You do not have permission to create raw material categories.');
        }

        return view('masters.raw-material-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-material-categories', 'create')) {
            abort(403, 'You do not have permission to create raw material categories.');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:raw_material_categories,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Raw Material Category is required.',
            'name.unique' => 'This Raw Material Category already exists.',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        RawMaterialCategory::create($data);

        return redirect()->route('raw-material-categories.index')->with('success', 'Raw Material Category created successfully.');
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
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-material-categories', 'edit')) {
            abort(403, 'You do not have permission to edit raw material categories.');
        }

        $query = RawMaterialCategory::query();
        $query = $this->applyBranchFilter($query, RawMaterialCategory::class);
        $category = $query->findOrFail($id);
        return view('masters.raw-material-categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-material-categories', 'edit')) {
            abort(403, 'You do not have permission to edit raw material categories.');
        }

        $query = RawMaterialCategory::query();
        $query = $this->applyBranchFilter($query, RawMaterialCategory::class);
        $category = $query->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:raw_material_categories,name,' . $id,
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Raw Material Category is required.',
            'name.unique' => 'This Raw Material Category already exists.',
        ]);

        $category->update($request->all());

        return redirect()->route('raw-material-categories.index')->with('success', 'Raw Material Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-material-categories', 'delete')) {
            abort(403, 'You do not have permission to delete raw material categories.');
        }

        $query = RawMaterialCategory::query();
        $query = $this->applyBranchFilter($query, RawMaterialCategory::class);
        $category = $query->findOrFail($id);
        $category->delete();

        return redirect()->route('raw-material-categories.index')->with('success', 'Raw Material Category deleted successfully.');
    }
}
