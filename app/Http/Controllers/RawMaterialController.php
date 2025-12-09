<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RawMaterial;
use App\Models\RawMaterialCategory;
use App\Models\RawMaterialSubCategory;
use App\Models\Unit;

class RawMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-materials', 'view')) {
            abort(403, 'You do not have permission to view raw materials.');
        }

        $query = RawMaterial::with(['rawMaterialCategory', 'rawMaterialSubCategory', 'unit']);
        $query = $this->applyBranchFilter($query, RawMaterial::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('rawMaterialCategory', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('rawMaterialSubCategory', function($subCategoryQuery) use ($search) {
                      $subCategoryQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('unit', function($unitQuery) use ($search) {
                      $unitQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('symbol', 'like', "%{$search}%");
                  })
                  // Search in dates
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        // Sorting functionality
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';
        switch ($sortBy) {
            case 'name': $query->orderBy('raw_materials.name', $sortOrder); break;
            case 'code': $query->orderBy('raw_materials.code', $sortOrder); break;
            case 'category':
                $query->leftJoin('raw_material_categories', 'raw_materials.raw_material_category_id', '=', 'raw_material_categories.id')
                      ->orderBy('raw_material_categories.name', $sortOrder)
                      ->select('raw_materials.*')
                      ->distinct();
                break;
            case 'sub_category':
                $query->leftJoin('raw_material_sub_categories', 'raw_materials.raw_material_sub_category_id', '=', 'raw_material_sub_categories.id')
                      ->orderBy('raw_material_sub_categories.name', $sortOrder)
                      ->select('raw_materials.*')
                      ->distinct();
                break;
            case 'unit':
                $query->leftJoin('units', 'raw_materials.unit_id', '=', 'units.id')
                      ->orderBy('units.name', $sortOrder)
                      ->select('raw_materials.*')
                      ->distinct();
                break;
            default: $query->orderBy('raw_materials.id', $sortOrder); break;
        }
        $rawMaterials = $query->paginate(15)->withQueryString();
        return view('masters.raw-materials.index', compact('rawMaterials'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-materials', 'create')) {
            abort(403, 'You do not have permission to create raw materials.');
        }

        // Get raw material categories filtered by branch
        $categoryQuery = RawMaterialCategory::query();
        $categoryQuery = $this->applyBranchFilter($categoryQuery, RawMaterialCategory::class);
        $categories = $categoryQuery->get();

        // Get units filtered by branch
        $unitQuery = Unit::query();
        $unitQuery = $this->applyBranchFilter($unitQuery, Unit::class);
        $units = $unitQuery->get();

        return view('masters.raw-materials.create', compact('categories', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-materials', 'create')) {
            abort(403, 'You do not have permission to create raw materials.');
        }

        $request->validate([
            'raw_material_category_id' => 'required|exists:raw_material_categories,id',
            'raw_material_sub_category_id' => 'nullable|exists:raw_material_sub_categories,id',
            'name' => 'required|string|max:255',
            'grade' => 'nullable|string|max:255',
            'thickness' => 'nullable|string|max:255',
            'batch_required' => 'required|in:Yes,No',
            'qc_applicable' => 'required|in:Yes,No',
            'test_certificate_applicable' => 'required|in:Yes,No',
            'unit_id' => 'required|exists:units,id',
            'sop' => 'nullable|string',
        ], [
            'raw_material_category_id.required' => 'Raw Material Category is required.',
            'raw_material_category_id.exists' => 'Selected Raw Material Category does not exist.',
            'raw_material_sub_category_id.exists' => 'Selected Raw Material SubCategory does not exist.',
            'name.required' => 'Raw Material is required.',
            'unit_id.required' => 'UOM is required.',
            'unit_id.exists' => 'Selected UOM does not exist.',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        RawMaterial::create($data);

        return redirect()->route('raw-materials.index')->with('success', 'Raw Material created successfully.');
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
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-materials', 'edit')) {
            abort(403, 'You do not have permission to edit raw materials.');
        }

        $query = RawMaterial::query();
        $query = $this->applyBranchFilter($query, RawMaterial::class);
        $rawMaterial = $query->findOrFail($id);

        // Get raw material categories filtered by branch
        $categoryQuery = RawMaterialCategory::query();
        $categoryQuery = $this->applyBranchFilter($categoryQuery, RawMaterialCategory::class);
        $categories = $categoryQuery->get();

        // Get subcategories for the selected category
        $subCategoryQuery = RawMaterialSubCategory::where('raw_material_category_id', $rawMaterial->raw_material_category_id);
        $subCategoryQuery = $this->applyBranchFilter($subCategoryQuery, RawMaterialSubCategory::class);
        $subCategories = $subCategoryQuery->get();

        // Get units filtered by branch
        $unitQuery = Unit::query();
        $unitQuery = $this->applyBranchFilter($unitQuery, Unit::class);
        $units = $unitQuery->get();

        return view('masters.raw-materials.edit', compact('rawMaterial', 'categories', 'subCategories', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-materials', 'edit')) {
            abort(403, 'You do not have permission to edit raw materials.');
        }

        $query = RawMaterial::query();
        $query = $this->applyBranchFilter($query, RawMaterial::class);
        $rawMaterial = $query->findOrFail($id);

        $request->validate([
            'raw_material_category_id' => 'required|exists:raw_material_categories,id',
            'raw_material_sub_category_id' => 'nullable|exists:raw_material_sub_categories,id',
            'name' => 'required|string|max:255',
            'grade' => 'nullable|string|max:255',
            'thickness' => 'nullable|string|max:255',
            'batch_required' => 'required|in:Yes,No',
            'qc_applicable' => 'required|in:Yes,No',
            'test_certificate_applicable' => 'required|in:Yes,No',
            'unit_id' => 'required|exists:units,id',
            'sop' => 'nullable|string',
        ], [
            'raw_material_category_id.required' => 'Raw Material Category is required.',
            'raw_material_category_id.exists' => 'Selected Raw Material Category does not exist.',
            'raw_material_sub_category_id.exists' => 'Selected Raw Material SubCategory does not exist.',
            'name.required' => 'Raw Material is required.',
            'unit_id.required' => 'UOM is required.',
            'unit_id.exists' => 'Selected UOM does not exist.',
        ]);

        $rawMaterial->update($request->all());

        return redirect()->route('raw-materials.index')->with('success', 'Raw Material updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('raw-materials', 'delete')) {
            abort(403, 'You do not have permission to delete raw materials.');
        }

        $query = RawMaterial::query();
        $query = $this->applyBranchFilter($query, RawMaterial::class);
        $rawMaterial = $query->findOrFail($id);
        $rawMaterial->delete();

        return redirect()->route('raw-materials.index')->with('success', 'Raw Material deleted successfully.');
    }

    /**
     * Get subcategories for a given category (AJAX endpoint)
     */
    public function getSubCategories(Request $request)
    {
        try {
            $categoryId = $request->get('category_id');
            
            if (!$categoryId) {
                return response()->json([]);
            }

            $query = RawMaterialSubCategory::where('raw_material_category_id', $categoryId);
            $query = $this->applyBranchFilter($query, RawMaterialSubCategory::class);
            $subCategories = $query->get(['id', 'name']);

            return response()->json($subCategories);
        } catch (\Exception $e) {
            \Log::error('Error fetching subcategories: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load subcategories'], 500);
        }
    }
}
