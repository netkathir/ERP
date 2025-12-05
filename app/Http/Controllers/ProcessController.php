<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Process;

class ProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('processes', 'view')) {
            abort(403, 'You do not have permission to view processes.');
        }

        $query = Process::query();
        $query = $this->applyBranchFilter($query, Process::class);

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

        // Sorting functionality
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';
        switch ($sortBy) {
            case 'name': $query->orderBy('processes.name', $sortOrder); break;
            default: $query->orderBy('processes.id', $sortOrder); break;
        }
        $processes = $query->paginate(15)->withQueryString();
        return view('masters.processes.index', compact('processes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('processes', 'create')) {
            abort(403, 'You do not have permission to create processes.');
        }

        return view('masters.processes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('processes', 'create')) {
            abort(403, 'You do not have permission to create processes.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:processes,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Process Name is required.',
            'name.unique' => 'This Process Name already exists.',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        Process::create($data);

        return redirect()->route('processes.index')->with('success', 'Process created successfully.');
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
        if (!$user->isSuperAdmin() && !$user->hasPermission('processes', 'edit')) {
            abort(403, 'You do not have permission to edit processes.');
        }

        $query = Process::query();
        $query = $this->applyBranchFilter($query, Process::class);
        $process = $query->findOrFail($id);
        return view('masters.processes.edit', compact('process'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('processes', 'edit')) {
            abort(403, 'You do not have permission to edit processes.');
        }

        $query = Process::query();
        $query = $this->applyBranchFilter($query, Process::class);
        $process = $query->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:processes,name,' . $id,
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Process Name is required.',
            'name.unique' => 'This Process Name already exists.',
        ]);

        $process->update($request->all());

        return redirect()->route('processes.index')->with('success', 'Process updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('processes', 'delete')) {
            abort(403, 'You do not have permission to delete processes.');
        }

        $query = Process::query();
        $query = $this->applyBranchFilter($query, Process::class);
        $process = $query->findOrFail($id);
        $process->delete();

        return redirect()->route('processes.index')->with('success', 'Process deleted successfully.');
    }
}
