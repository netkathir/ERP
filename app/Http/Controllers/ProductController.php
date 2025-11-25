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
    public function index()
    {
        $query = \App\Models\Product::with('unit');
        $query = $this->applyBranchFilter($query, \App\Models\Product::class);
        $products = $query->latest()->paginate(15);
        return view('masters.products.index', compact('products'));
    }

    public function create()
    {
        $units = \App\Models\Unit::all();
        return view('masters.products.create', compact('units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_id' => 'required|exists:units,id',
            'price' => 'required|numeric|min:0',
            'gst_rate' => 'required|numeric|min:0',
            'category' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        \App\Models\Product::create($data);

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
        return view('masters.products.edit', compact('product', 'units'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_id' => 'required|exists:units,id',
            'price' => 'required|numeric|min:0',
            'gst_rate' => 'required|numeric|min:0',
            'category' => 'nullable|string',
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
