<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('product-categories', 'view')) {
            abort(403, 'You do not have permission to view product categories.');
        }

        $query = ProductCategory::query();
        $query = $this->applyBranchFilter($query, ProductCategory::class);

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
        return view('masters.product-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('product-categories', 'create')) {
            abort(403, 'You do not have permission to create product categories.');
        }

        return view('masters.product-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('product-categories', 'create')) {
            abort(403, 'You do not have permission to create product categories.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Product Category is required.',
            'name.unique' => 'This Product Category already exists.',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        ProductCategory::create($data);

        return redirect()->route('product-categories.index')->with('success', 'Product Category created successfully.');
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
        if (!$user->isSuperAdmin() && !$user->hasPermission('product-categories', 'edit')) {
            abort(403, 'You do not have permission to edit product categories.');
        }

        $query = ProductCategory::query();
        $query = $this->applyBranchFilter($query, ProductCategory::class);
        $category = $query->findOrFail($id);
        return view('masters.product-categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('product-categories', 'edit')) {
            abort(403, 'You do not have permission to edit product categories.');
        }

        $query = ProductCategory::query();
        $query = $this->applyBranchFilter($query, ProductCategory::class);
        $category = $query->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:product_categories,name,' . $id,
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Product Category is required.',
            'name.unique' => 'This Product Category already exists.',
        ]);

        $category->update($request->all());

        return redirect()->route('product-categories.index')->with('success', 'Product Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('product-categories', 'delete')) {
            abort(403, 'You do not have permission to delete product categories.');
        }

        $query = ProductCategory::query();
        $query = $this->applyBranchFilter($query, ProductCategory::class);
        $category = $query->findOrFail($id);
        $category->delete();

        return redirect()->route('product-categories.index')->with('success', 'Product Category deleted successfully.');
    }
}
