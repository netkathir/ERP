<?php

namespace App\Http\Controllers;

use App\Models\MaterialInward;
use App\Models\MaterialInwardItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MaterialInwardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('material-inwards', 'view')) {
            abort(403, 'You do not have permission to view material inwards.');
        }

        $query = MaterialInward::with(['creator', 'purchaseOrder', 'supplier']);
        $query = $this->applyBranchFilter($query, MaterialInward::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('material_inward_no', 'like', "%{$search}%")
                  ->orWhereHas('purchaseOrder', function($poQuery) use ($search) {
                      $poQuery->where('po_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplier', function($supplierQuery) use ($search) {
                      $supplierQuery->where('supplier_name', 'like', "%{$search}%");
                  })
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        switch ($sortBy) {
            case 'material_inward_no':
                $query->orderBy('material_inwards.material_inward_no', $sortOrder);
                break;
            case 'po_no':
                $query->leftJoin('purchase_orders', 'material_inwards.purchase_order_id', '=', 'purchase_orders.id')
                      ->orderBy('purchase_orders.po_no', $sortOrder)
                      ->select('material_inwards.*')
                      ->distinct();
                break;
            case 'supplier_name':
                $query->leftJoin('suppliers', 'material_inwards.supplier_id', '=', 'suppliers.id')
                      ->orderBy('suppliers.supplier_name', $sortOrder)
                      ->select('material_inwards.*')
                      ->distinct();
                break;
            case 'created_at':
                $query->orderBy('material_inwards.created_at', $sortOrder);
                break;
            default:
                $query->orderBy('material_inwards.id', $sortOrder);
                break;
        }

        $materialInwards = $query->paginate(15)->withQueryString();

        return view('store.material_inwards.index', compact('materialInwards'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('material-inwards', 'create')) {
            abort(403, 'You do not have permission to create material inwards.');
        }

        $materialInwardNo = $this->generateMaterialInwardNo();

        // Get Purchase Orders that are usable for material inward
        $poQuery = PurchaseOrder::whereIn('status', ['Draft', 'Submitted', 'Approved'])
            ->with(['supplier', 'items.rawMaterial', 'items.unit']);
        $poQuery = $this->applyBranchFilter($poQuery, PurchaseOrder::class);
        // Sort newest purchase orders first for easier selection
        $purchaseOrders = $poQuery->orderByDesc('created_at')->orderByDesc('id')->get();

        return view('store.material_inwards.create', compact(
            'materialInwardNo',
            'purchaseOrders'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('material-inwards', 'create')) {
            abort(403, 'You do not have permission to create material inwards.');
        }

        $validated = $this->validateRequest($request);

        try {
            DB::beginTransaction();

            $materialInward = new MaterialInward();
            $materialInward->material_inward_no = $this->generateMaterialInwardNo();
            $materialInward->purchase_order_id = $validated['purchase_order_id'];
            
            // Get supplier from Purchase Order
            $purchaseOrder = PurchaseOrder::findOrFail($validated['purchase_order_id']);
            $materialInward->supplier_id = $purchaseOrder->supplier_id;
            
            $materialInward->branch_id = $this->getActiveBranchId();
            $materialInward->created_by_id = $user->id;
            $materialInward->remarks = $validated['remarks'] ?? null;
            $materialInward->save();

            foreach ($validated['items'] as $itemData) {
                $itemData['material_inward_id'] = $materialInward->id;
                MaterialInwardItem::create($itemData);
            }

            DB::commit();

            return redirect()->route('material-inwards.index')->with('success', 'Material Inward created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating Material Inward: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('material-inwards', 'view')) {
            abort(403, 'You do not have permission to view material inwards.');
        }

        $query = MaterialInward::with(['creator', 'purchaseOrder', 'supplier', 'items.purchaseOrderItem.rawMaterial', 'items.rawMaterial', 'items.unit']);
        $query = $this->applyBranchFilter($query, MaterialInward::class);
        $materialInward = $query->findOrFail($id);

        return view('store.material_inwards.show', compact('materialInward'));
    }

    public function edit($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('material-inwards', 'edit')) {
            abort(403, 'You do not have permission to edit material inwards.');
        }

        $query = MaterialInward::with(['items.purchaseOrderItem', 'items.rawMaterial', 'items.unit']);
        $query = $this->applyBranchFilter($query, MaterialInward::class);
        $materialInward = $query->findOrFail($id);

        // Get Purchase Orders that are usable for material inward
        $poQuery = PurchaseOrder::whereIn('status', ['Draft', 'Submitted', 'Approved'])
            ->with(['supplier', 'items.rawMaterial', 'items.unit']);
        $poQuery = $this->applyBranchFilter($poQuery, PurchaseOrder::class);
        // Sort newest purchase orders first for easier selection
        $purchaseOrders = $poQuery->orderByDesc('created_at')->orderByDesc('id')->get();

        return view('store.material_inwards.edit', compact(
            'materialInward',
            'purchaseOrders'
        ));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('material-inwards', 'edit')) {
            abort(403, 'You do not have permission to edit material inwards.');
        }

        $query = MaterialInward::with('items');
        $query = $this->applyBranchFilter($query, MaterialInward::class);
        $materialInward = $query->findOrFail($id);

        $validated = $this->validateRequest($request, $materialInward->id);

        try {
            DB::beginTransaction();

            $materialInward->purchase_order_id = $validated['purchase_order_id'];
            
            // Get supplier from Purchase Order
            $purchaseOrder = PurchaseOrder::findOrFail($validated['purchase_order_id']);
            $materialInward->supplier_id = $purchaseOrder->supplier_id;
            
            $materialInward->updated_by_id = $user->id;
            $materialInward->remarks = $validated['remarks'] ?? null;
            $materialInward->save();

            // Replace items
            $materialInward->items()->delete();
            foreach ($validated['items'] as $itemData) {
                $itemData['material_inward_id'] = $materialInward->id;
                MaterialInwardItem::create($itemData);
            }

            DB::commit();

            return redirect()->route('material-inwards.index')->with('success', 'Material Inward updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating Material Inward: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('material-inwards', 'delete')) {
            abort(403, 'You do not have permission to delete material inwards.');
        }

        $query = MaterialInward::query();
        $query = $this->applyBranchFilter($query, MaterialInward::class);
        $materialInward = $query->findOrFail($id);

        $materialInward->delete();

        return redirect()->route('material-inwards.index')->with('success', 'Material Inward deleted successfully.');
    }

    public function getPurchaseOrderItems($id)
    {
        $purchaseOrder = PurchaseOrder::with(['items.rawMaterial', 'items.unit', 'supplier'])
            ->findOrFail($id);

        // Check branch access
        $query = PurchaseOrder::query();
        $query = $this->applyBranchFilter($query, PurchaseOrder::class);
        if (!$query->where('purchase_orders.id', $id)->exists()) {
            abort(403, 'You do not have access to this Purchase Order.');
        }

        $excludeMaterialInwardId = request('exclude_material_inward_id');

        $items = [];
        foreach ($purchaseOrder->items as $poItem) {
            // Calculate total received qty for this PO item across all Material Inwards
            $totalReceivedQty = MaterialInwardItem::where('purchase_order_item_id', $poItem->id)
                ->when($excludeMaterialInwardId, function($query) use ($excludeMaterialInwardId) {
                    $query->where('material_inward_id', '!=', $excludeMaterialInwardId);
                })
                ->sum('received_qty');
            
            $pendingQty = max(0, $poItem->po_quantity - $totalReceivedQty);
            $itemName = $poItem->item_name ?? ($poItem->rawMaterial ? $poItem->rawMaterial->name : '');

            $items[] = [
                'id' => $poItem->id,
                'raw_material_id' => $poItem->raw_material_id,
                'item_name' => $itemName,
                // Keep item_description for backward compatibility / storage column
                'item_description' => $poItem->item_description ?? $itemName,
                'po_qty' => (float) $poItem->po_quantity,
                'pending_qty' => (float) $pendingQty,
                'unit_id' => $poItem->unit_id,
                'unit_symbol' => $poItem->unit ? $poItem->unit->symbol : '',
                'cost_per_unit' => (float) $poItem->price,
            ];
        }

        return response()->json([
            'supplier_name' => $purchaseOrder->supplier ? $purchaseOrder->supplier->supplier_name : '',
            'items' => $items,
        ]);
    }

    protected function validateRequest(Request $request, ?int $id = null): array
    {
        $request->merge([
            'items' => $this->normalizeItemDates($request->input('items', [])),
        ]);

        $rules = [
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.purchase_order_item_id' => 'required|exists:purchase_order_items,id',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.item_description' => 'nullable|string',
            'items.*.received_qty' => 'required|numeric|min:0.001',
            'items.*.received_qty_in_kg' => 'nullable|numeric|min:0',
            'items.*.batch_no' => 'nullable|string|max:255',
            'items.*.cost_per_unit' => 'required|numeric|min:0',
            'items.*.supplier_invoice_no' => 'required|string|max:255',
            'items.*.invoice_date' => 'required|date',
        ];

        // Validate pending qty for each item
        $request->validate($rules, [], [
            'purchase_order_id' => 'Purchase Order No',
            'items.*.received_qty' => 'Received Qty',
            'items.*.received_qty_in_kg' => 'Received Qty in Kg',
            'items.*.cost_per_unit' => 'Cost/unit',
            'items.*.supplier_invoice_no' => 'Supplier Invoice No',
            'items.*.invoice_date' => 'Invoice date',
        ]);

        $validated = $request->validate($rules);

        // Additional validation: Check received qty doesn't exceed pending qty
        foreach ($validated['items'] as $index => $item) {
            $poItem = PurchaseOrderItem::findOrFail($item['purchase_order_item_id']);
            
            // Calculate total received qty excluding current material inward
            $totalReceivedQty = MaterialInwardItem::where('purchase_order_item_id', $poItem->id)
                ->when($id, function($query) use ($id) {
                    $query->whereHas('materialInward', function($q) use ($id) {
                        $q->where('id', '!=', $id);
                    });
                })
                ->sum('received_qty');
            
            $pendingQty = max(0, $poItem->po_quantity - $totalReceivedQty);

            if ($item['received_qty'] > $pendingQty) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "items.{$index}.received_qty" => "Received Qty ({$item['received_qty']}) cannot exceed Pending Qty ({$pendingQty})."
                ]);
            }

            // Calculate total
            $validated['items'][$index]['po_qty'] = (float) $poItem->po_quantity;
            $validated['items'][$index]['pending_qty'] = (float) $pendingQty;
            $validated['items'][$index]['unit_id'] = $poItem->unit_id;
            $validated['items'][$index]['total'] = (float) ($item['received_qty'] * $item['cost_per_unit']);
        }

        return $validated;
    }

    protected function normalizeItemDates(array $items): array
    {
        foreach ($items as $index => $item) {
            if (isset($item['invoice_date'])) {
                $items[$index]['invoice_date'] = $this->normalizeDate($item['invoice_date']);
            }
        }

        return $items;
    }

    protected function normalizeDate(?string $value): ?string
    {
        if (empty($value)) {
            return $value;
        }

        try {
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
                return Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return $value;
        }
    }

    protected function generateMaterialInwardNo(): string
    {
        $last = MaterialInward::latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;
        return 'MNO' . str_pad($nextId, 2, '0', STR_PAD_LEFT);
    }
}
