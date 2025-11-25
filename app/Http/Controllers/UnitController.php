<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        
        // Check permission
        if (!$user->hasPermission('units', 'view')) {
            abort(403, 'You do not have permission to view units.');
        }

        $query = \App\Models\Unit::query();
        $query = $this->applyBranchFilter($query, \App\Models\Unit::class);
        $units = $query->latest()->paginate(15);
        return view('masters.units.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();
        
        // Check permission
        if (!$user->hasPermission('units', 'create')) {
            abort(403, 'You do not have permission to create units.');
        }

        return view('masters.units.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Check permission
        if (!$user->hasPermission('units', 'create')) {
            abort(403, 'You do not have permission to create units.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:50',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        \App\Models\Unit::create($data);

        return redirect()->route('units.index')->with('success', 'Unit created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        
        // Check permission
        if (!$user->hasPermission('units', 'edit')) {
            abort(403, 'You do not have permission to edit units.');
        }

        $query = \App\Models\Unit::query();
        $query = $this->applyBranchFilter($query, \App\Models\Unit::class);
        $unit = $query->findOrFail($id);
        return view('masters.units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Check permission
        if (!$user->hasPermission('units', 'edit')) {
            abort(403, 'You do not have permission to edit units.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:50',
        ]);

        $query = \App\Models\Unit::query();
        $query = $this->applyBranchFilter($query, \App\Models\Unit::class);
        $unit = $query->findOrFail($id);
        $unit->update($request->all());

        return redirect()->route('units.index')->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Check permission
        if (!$user->hasPermission('units', 'delete')) {
            abort(403, 'You do not have permission to delete units.');
        }

        $query = \App\Models\Unit::query();
        $query = $this->applyBranchFilter($query, \App\Models\Unit::class);
        $unit = $query->findOrFail($id);
        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Unit deleted successfully.');
    }
}
