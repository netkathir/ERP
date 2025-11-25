<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $taxes = \App\Models\Tax::latest()->paginate(15);
        return view('masters.taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('masters.taxes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:CGST,SGST,IGST',
            'rate' => 'required|numeric|min:0',
            'effective_date' => 'nullable|date',
        ]);

        \App\Models\Tax::create($request->all());

        return redirect()->route('taxes.index')->with('success', 'Tax created successfully.');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $tax = \App\Models\Tax::findOrFail($id);
        return view('masters.taxes.edit', compact('tax'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:CGST,SGST,IGST',
            'rate' => 'required|numeric|min:0',
            'effective_date' => 'nullable|date',
        ]);

        $tax = \App\Models\Tax::findOrFail($id);
        $tax->update($request->all());

        return redirect()->route('taxes.index')->with('success', 'Tax updated successfully.');
    }

    public function destroy($id)
    {
        $tax = \App\Models\Tax::findOrFail($id);
        $tax->delete();

        return redirect()->route('taxes.index')->with('success', 'Tax deleted successfully.');
    }
}
