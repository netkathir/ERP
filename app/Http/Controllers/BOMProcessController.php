<?php

namespace App\Http\Controllers;

use App\Models\BOMProcess;
use App\Models\BOMProcessItem;
use App\Models\Product;
use App\Models\Process;
use App\Models\RawMaterial;
use App\Models\Unit;
use Illuminate\Http\Request;

class BOMProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('bom-processes', 'view')) {
            abort(403, 'You do not have permission to view BOM processes.');
        }

        $query = BOMProcess::with(['product', 'items.rawMaterial', 'items.unit', 'items.process']);
        $query = $this->applyBranchFilter($query, BOMProcess::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%");
                })
                // Search in dates
                ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        $bomProcesses = $query->latest()->paginate(15)->withQueryString();
        
        return view('bom-processes.index', compact('bomProcesses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('bom-processes', 'create')) {
            abort(403, 'You do not have permission to create BOM processes.');
        }

        $query = Process::query();
        $query = $this->applyBranchFilter($query, Process::class);
        $processes = $query->orderBy('name')->get();
        
        $query = Product::query();
        $query = $this->applyBranchFilter($query, Product::class);
        $products = $query->orderBy('name')->get();
        
        $query = RawMaterial::query();
        $query = $this->applyBranchFilter($query, RawMaterial::class);
        $rawMaterials = $query->orderBy('name')->get();
        
        $query = Unit::query();
        $query = $this->applyBranchFilter($query, Unit::class);
        $units = $query->orderBy('name')->get();
        
        return view('bom-processes.create', compact('processes', 'products', 'rawMaterials', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('bom-processes', 'create')) {
            abort(403, 'You do not have permission to create BOM processes.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'items' => 'required|array|min:1',
            'items.*.process_id' => 'required|exists:processes,id',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_id' => 'required|exists:units,id',
        ], [
            'product_id.required' => 'Product Name is required.',
            'items.required' => 'At least one raw material item is required.',
            'items.min' => 'At least one raw material item is required.',
            'items.*.process_id.required' => 'Process is required for all items.',
            'items.*.raw_material_id.required' => 'Raw Material is required for all items.',
            'items.*.quantity.required' => 'Quantity is required for all items.',
            'items.*.quantity.numeric' => 'Quantity must be a valid number.',
            'items.*.quantity.min' => 'Quantity must be greater than 0.',
            'items.*.unit_id.required' => 'UOM is required for all items.',
        ]);

        try {
            $data = [
                'product_id' => $request->product_id,
                'branch_id' => $this->getActiveBranchId(),
                'process_id' => null, // Explicitly set to null since column still exists but is nullable
            ];

            // Allow multiple BOMs for the same product (duplicates allowed)
            $bomProcess = BOMProcess::create($data);

            // Create BOM items (duplicates already validated)
            foreach ($request->items as $item) {
                BOMProcessItem::create([
                    'bom_process_id' => $bomProcess->id,
                    'process_id' => $item['process_id'],
                    'raw_material_id' => $item['raw_material_id'],
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                ]);
            }

            return redirect()->route('bom-processes.index')->with('success', 'BOM Process created successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create BOM Process: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('bom-processes', 'view')) {
            abort(403, 'You do not have permission to view BOM processes.');
        }

        $query = BOMProcess::with(['product', 'items.rawMaterial', 'items.unit', 'items.process']);
        $query = $this->applyBranchFilter($query, BOMProcess::class);
        $bomProcess = $query->findOrFail($id);
        
        return view('bom-processes.show', compact('bomProcess'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('bom-processes', 'edit')) {
            abort(403, 'You do not have permission to edit BOM processes.');
        }

        $query = BOMProcess::with(['items.process']);
        $query = $this->applyBranchFilter($query, BOMProcess::class);
        $bomProcess = $query->findOrFail($id);
        
        $query = Process::query();
        $query = $this->applyBranchFilter($query, Process::class);
        $processes = $query->orderBy('name')->get();
        
        $query = Product::query();
        $query = $this->applyBranchFilter($query, Product::class);
        $products = $query->orderBy('name')->get();
        
        $query = RawMaterial::query();
        $query = $this->applyBranchFilter($query, RawMaterial::class);
        $rawMaterials = $query->orderBy('name')->get();
        
        $query = Unit::query();
        $query = $this->applyBranchFilter($query, Unit::class);
        $units = $query->orderBy('name')->get();
        
        return view('bom-processes.edit', compact('bomProcess', 'processes', 'products', 'rawMaterials', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('bom-processes', 'edit')) {
            abort(403, 'You do not have permission to edit BOM processes.');
        }

        $query = BOMProcess::query();
        $query = $this->applyBranchFilter($query, BOMProcess::class);
        $bomProcess = $query->findOrFail($id);

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'items' => 'required|array|min:1',
            'items.*.process_id' => 'required|exists:processes,id',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.quantity' => 'required|numeric|min:0.0001',
            'items.*.unit_id' => 'required|exists:units,id',
        ], [
            'product_id.required' => 'Product Name is required.',
            'items.required' => 'At least one raw material item is required.',
            'items.min' => 'At least one raw material item is required.',
            'items.*.process_id.required' => 'Process is required for all items.',
            'items.*.raw_material_id.required' => 'Raw Material is required for all items.',
            'items.*.quantity.required' => 'Quantity is required for all items.',
            'items.*.quantity.numeric' => 'Quantity must be a valid number.',
            'items.*.quantity.min' => 'Quantity must be greater than 0.',
            'items.*.unit_id.required' => 'UOM is required for all items.',
        ]);

        try {
            // Allow multiple BOMs for the same product (duplicates allowed)
            $bomProcess->update([
                'product_id' => $request->product_id,
            ]);

            // Delete existing items
            $bomProcess->items()->delete();

            // Create new BOM items
            foreach ($request->items as $item) {
                BOMProcessItem::create([
                    'bom_process_id' => $bomProcess->id,
                    'process_id' => $item['process_id'],
                    'raw_material_id' => $item['raw_material_id'],
                    'quantity' => $item['quantity'],
                    'unit_id' => $item['unit_id'],
                ]);
            }

            return redirect()->route('bom-processes.index')->with('success', 'BOM Process updated successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update BOM Process: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('bom-processes', 'delete')) {
            abort(403, 'You do not have permission to delete BOM processes.');
        }

        $query = BOMProcess::query();
        $query = $this->applyBranchFilter($query, BOMProcess::class);
        $bomProcess = $query->findOrFail($id);
        
        // Items will be deleted automatically due to cascade
        $bomProcess->delete();

        return redirect()->route('bom-processes.index')->with('success', 'BOM Process deleted successfully.');
    }
}
