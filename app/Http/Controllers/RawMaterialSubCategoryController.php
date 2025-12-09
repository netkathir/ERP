<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RawMaterialSubCategory;
use App\Models\RawMaterialCategory;

class RawMaterialSubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-material-sub-categories', 'view')) {
            abort(403, 'You do not have permission to view raw material sub categories.');
        }

        $query = RawMaterialSubCategory::with('rawMaterialCategory');
        $query = $this->applyBranchFilter($query, RawMaterialSubCategory::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('rawMaterialCategory', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  })
                  // Search in dates
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        $subCategories = $query->latest()->paginate(15)->withQueryString();
        return view('masters.raw-material-sub-categories.index', compact('subCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-material-sub-categories', 'create')) {
            abort(403, 'You do not have permission to create raw material sub categories.');
        }

        // Get raw material categories filtered by branch
        $categoryQuery = RawMaterialCategory::query();
        $categoryQuery = $this->applyBranchFilter($categoryQuery, RawMaterialCategory::class);
        $categories = $categoryQuery->get();

        return view('masters.raw-material-sub-categories.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-material-sub-categories', 'create')) {
            abort(403, 'You do not have permission to create raw material sub categories.');
        }

        $request->validate([
            'raw_material_category_id' => 'required|exists:raw_material_categories,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ], [
            'raw_material_category_id.required' => 'Raw Material Type is required.',
            'raw_material_category_id.exists' => 'Selected Raw Material Type does not exist.',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        RawMaterialSubCategory::create($data);

        return redirect()->route('raw-material-sub-categories.index')->with('success', 'Raw Material SubCategory created successfully.');
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
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-material-sub-categories', 'edit')) {
            abort(403, 'You do not have permission to edit raw material sub categories.');
        }

        $query = RawMaterialSubCategory::query();
        $query = $this->applyBranchFilter($query, RawMaterialSubCategory::class);
        $subCategory = $query->findOrFail($id);

        // Get raw material categories filtered by branch
        $categoryQuery = RawMaterialCategory::query();
        $categoryQuery = $this->applyBranchFilter($categoryQuery, RawMaterialCategory::class);
        $categories = $categoryQuery->get();

        return view('masters.raw-material-sub-categories.edit', compact('subCategory', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-material-sub-categories', 'edit')) {
            abort(403, 'You do not have permission to edit raw material sub categories.');
        }

        $query = RawMaterialSubCategory::query();
        $query = $this->applyBranchFilter($query, RawMaterialSubCategory::class);
        $subCategory = $query->findOrFail($id);

        $request->validate([
            'raw_material_category_id' => 'required|exists:raw_material_categories,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ], [
            'raw_material_category_id.required' => 'Raw Material Type is required.',
            'raw_material_category_id.exists' => 'Selected Raw Material Type does not exist.',
        ]);

        $subCategory->update($request->all());

        return redirect()->route('raw-material-sub-categories.index')->with('success', 'Raw Material SubCategory updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-material-sub-categories', 'delete')) {
            abort(403, 'You do not have permission to delete raw material sub categories.');
        }

        $query = RawMaterialSubCategory::query();
        $query = $this->applyBranchFilter($query, RawMaterialSubCategory::class);
        $subCategory = $query->findOrFail($id);
        $subCategory->delete();

        return redirect()->route('raw-material-sub-categories.index')->with('success', 'Raw Material SubCategory deleted successfully.');
    }
}
