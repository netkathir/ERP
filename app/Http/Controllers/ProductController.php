<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = \App\Models\Product::with(['unit', 'productCategory']);
        $query = $this->applyBranchFilter($query, \App\Models\Product::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('productCategory', function($categoryQuery) use ($search) {
                      $categoryQuery->where('name', 'like', "%{$search}%");
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
        
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        switch ($sortBy) {
            case 'name':
                $query->orderBy('products.name', $sortOrder);
                break;
            case 'code':
                $query->orderBy('products.code', $sortOrder);
                break;
            case 'category':
                $query->leftJoin('product_categories', 'products.product_category_id', '=', 'product_categories.id')
                      ->orderBy('product_categories.name', $sortOrder)
                      ->select('products.*')
                      ->distinct();
                break;
            case 'unit':
                $query->leftJoin('units', 'products.unit_id', '=', 'units.id')
                      ->orderBy('units.name', $sortOrder)
                      ->select('products.*')
                      ->distinct();
                break;
            default:
                $query->orderBy('products.id', $sortOrder);
                break;
        }

        $products = $query->paginate(15)->withQueryString();
        return view('masters.products.index', compact('products'));
    }

    public function create()
    {
        $unitQuery = \App\Models\Unit::query();
        $unitQuery = $this->applyBranchFilter($unitQuery, \App\Models\Unit::class);
        $units = $unitQuery->get();

        // Load Product Categories with branch filtering
        $categoryQuery = \App\Models\ProductCategory::query();
        $categoryQuery = $this->applyBranchFilter($categoryQuery, \App\Models\ProductCategory::class);
        $categories = $categoryQuery->orderBy('name')->get();

        return view('masters.products.create', compact('units', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_id' => 'required|exists:units,id',
            'category' => 'nullable|string',
            'product_category_id' => 'nullable|exists:product_categories,id',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        $product = \App\Models\Product::create($data);

        // If this is an AJAX request, return JSON response
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'unit_id' => $product->unit_id,
                    'unit_symbol' => optional($product->unit)->symbol ?? '',
                ]
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $query = \App\Models\Product::query();
        $query = $this->applyBranchFilter($query, \App\Models\Product::class);
        $product = $query->findOrFail($id);
        
        $unitQuery = \App\Models\Unit::query();
        $unitQuery = $this->applyBranchFilter($unitQuery, \App\Models\Unit::class);
        $units = $unitQuery->get();

        // Load Product Categories with branch filtering
        $categoryQuery = \App\Models\ProductCategory::query();
        $categoryQuery = $this->applyBranchFilter($categoryQuery, \App\Models\ProductCategory::class);
        $categories = $categoryQuery->orderBy('name')->get();

        return view('masters.products.edit', compact('product', 'units', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_id' => 'required|exists:units,id',
            'category' => 'nullable|string',
            'product_category_id' => 'nullable|exists:product_categories,id',
        ]);

        $query = \App\Models\Product::query();
        $query = $this->applyBranchFilter($query, \App\Models\Product::class);
        $product = $query->findOrFail($id);
        $product->update($request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $query = \App\Models\Product::query();
        $query = $this->applyBranchFilter($query, \App\Models\Product::class);
        $product = $query->findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
