<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DiscountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $discounts = \App\Models\Discount::latest()->paginate(15);
        return view('masters.discounts.index', compact('discounts'));
    }

    public function create()
    {
        return view('masters.discounts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Item,Overall',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        \App\Models\Discount::create($request->all());

        return redirect()->route('discounts.index')->with('success', 'Discount created successfully.');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $discount = \App\Models\Discount::findOrFail($id);
        return view('masters.discounts.edit', compact('discount'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Item,Overall',
            'percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $discount = \App\Models\Discount::findOrFail($id);
        $discount->update($request->all());

        return redirect()->route('discounts.index')->with('success', 'Discount updated successfully.');
    }

    public function destroy($id)
    {
        $discount = \App\Models\Discount::findOrFail($id);
        $discount->delete();

        return redirect()->route('discounts.index')->with('success', 'Discount deleted successfully.');
    }
}
