<?php

namespace App\Http\Controllers;

use App\Models\QcMaterialInward;
use App\Models\QcMaterialInwardItem;
use App\Models\MaterialInward;
use App\Models\MaterialInwardItem;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QcMaterialInwardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('qc-material-inwards', 'view')) {
            abort(403, 'You do not have permission to view QC material inwards.');
        }

        $query = QcMaterialInward::with(['creator', 'purchaseOrder', 'materialInward', 'supplier']);
        $query = $this->applyBranchFilter($query, QcMaterialInward::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('qc_material_no', 'like', "%{$search}%")
                  ->orWhereHas('materialInward', function($miQuery) use ($search) {
                      $miQuery->where('material_inward_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('purchaseOrder', function($poQuery) use ($search) {
                      $poQuery->where('po_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplier', function($supplierQuery) use ($search) {
                      $supplierQuery->where('supplier_name', 'like', "%{$search}%");
                  })
                  ->orWhereRaw("DATE_FORMAT(qc_material_inwards.created_at, '%d-%m-%Y %H:%i:%s') LIKE ?", ["%{$search}%"]);
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        switch ($sortBy) {
            case 'qc_material_no':
                $query->orderBy('qc_material_inwards.qc_material_no', $sortOrder);
                break;
            case 'mrn_no':
                $query->leftJoin('material_inwards', 'qc_material_inwards.material_inward_id', '=', 'material_inwards.id')
                      ->orderBy('material_inwards.material_inward_no', $sortOrder)
                      ->select('qc_material_inwards.*')
                      ->distinct();
                break;
            case 'po_no':
                $query->leftJoin('purchase_orders', 'qc_material_inwards.purchase_order_id', '=', 'purchase_orders.id')
                      ->orderBy('purchase_orders.po_no', $sortOrder)
                      ->select('qc_material_inwards.*')
                      ->distinct();
                break;
            case 'supplier_name':
                $query->leftJoin('suppliers', 'qc_material_inwards.supplier_id', '=', 'suppliers.id')
                      ->orderBy('suppliers.supplier_name', $sortOrder)
                      ->select('qc_material_inwards.*')
                      ->distinct();
                break;
            case 'created_at':
                $query->orderBy('qc_material_inwards.created_at', $sortOrder);
                break;
            default:
                $query->orderBy('qc_material_inwards.created_at', $sortOrder);
                break;
        }

        $qcMaterialInwards = $query->paginate(15)->withQueryString();

        return view('store.qc_material_inwards.index', compact('qcMaterialInwards'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('qc-material-inwards', 'create')) {
            abort(403, 'You do not have permission to create QC material inwards.');
        }

        $qcMaterialNo = $this->generateQcMaterialNo();

        // Get Purchase Orders that have Material Inward entries but are not fully QC-processed
        // Purchase Orders that have Material Inwards with items that don't have QC records yet
        $poQuery = PurchaseOrder::whereHas('materialInwards.items', function($itemQuery) {
                $itemQuery->whereDoesntHave('qcMaterialInwardItems');
            })
            ->with(['supplier']);
        $poQuery = $this->applyBranchFilter($poQuery, PurchaseOrder::class);
        $purchaseOrders = $poQuery->orderByDesc('id')->orderByDesc('created_at')->get();

        return view('store.qc_material_inwards.create', compact(
            'qcMaterialNo',
            'purchaseOrders'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('qc-material-inwards', 'create')) {
            abort(403, 'You do not have permission to create QC material inwards.');
        }

        $validated = $this->validateRequest($request);

        try {
            DB::beginTransaction();

            $qcMaterialInward = new QcMaterialInward();
            $qcMaterialInward->qc_material_no = $this->generateQcMaterialNo();
            $qcMaterialInward->material_inward_id = $validated['material_inward_id'];
            $qcMaterialInward->purchase_order_id = $validated['purchase_order_id'];
            
            // Get supplier from Material Inward or Purchase Order
            $materialInward = MaterialInward::findOrFail($validated['material_inward_id']);
            $qcMaterialInward->supplier_id = $materialInward->supplier_id ?? $materialInward->purchaseOrder->supplier_id;
            
            $qcMaterialInward->branch_id = $this->getActiveBranchId();
            $qcMaterialInward->created_by_id = $user->id;
            $qcMaterialInward->save();

            foreach ($validated['items'] as $itemData) {
                $itemData['qc_material_inward_id'] = $qcMaterialInward->id;
                $itemData['qc_completed'] = true;
                QcMaterialInwardItem::create($itemData);
            }

            DB::commit();

            return redirect()->route('qc-material-inwards.index')->with('success', 'QC Material Inward saved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating QC Material Inward: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('qc-material-inwards', 'view')) {
            abort(403, 'You do not have permission to view QC material inwards.');
        }

        $query = QcMaterialInward::with([
            'creator', 
            'purchaseOrder', 
            'materialInward', 
            'supplier',
            'items.materialInwardItem',
            'items.purchaseOrderItem',
            'items.rawMaterial',
            'items.unit'
        ]);
        $query = $this->applyBranchFilter($query, QcMaterialInward::class);
        $qcMaterialInward = $query->findOrFail($id);

        return view('store.qc_material_inwards.show', compact('qcMaterialInward'));
    }

    public function getMaterialInwardsByPO($poId)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check branch access
        $poQuery = PurchaseOrder::query();
        $poQuery = $this->applyBranchFilter($poQuery, PurchaseOrder::class);
        $purchaseOrder = $poQuery->findOrFail($poId);

        // Get Material Inwards for this PO that have items not fully QC'd
        $materialInwards = MaterialInward::where('purchase_order_id', $poId)
            ->whereHas('items', function($query) {
                $query->whereDoesntHave('qcMaterialInwardItems');
            })
            ->orderByDesc('id')
            ->orderByDesc('created_at')
            ->get();

        $mrnList = [];
        foreach ($materialInwards as $mi) {
            $mrnList[] = [
                'id' => $mi->id,
                'material_inward_no' => $mi->material_inward_no,
            ];
        }

        $supplierName = $purchaseOrder->supplier ? $purchaseOrder->supplier->supplier_name : '';

        return response()->json([
            'material_inwards' => $mrnList,
            'supplier_name' => $supplierName,
        ]);
    }

    public function getMaterialInwardItems($mrnId)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check branch access
        $miQuery = MaterialInward::query();
        $miQuery = $this->applyBranchFilter($miQuery, MaterialInward::class);
        $materialInward = $miQuery->findOrFail($mrnId);

        // Get items that don't have QC records yet
        $items = MaterialInwardItem::where('material_inward_id', $mrnId)
            ->whereDoesntHave('qcMaterialInwardItems')
            ->with(['rawMaterial', 'unit', 'purchaseOrderItem'])
            ->get();

        $itemList = [];
        foreach ($items as $item) {
            $itemList[] = [
                'id' => $item->id,
                'material_inward_item_id' => $item->id,
                'purchase_order_item_id' => $item->purchase_order_item_id,
                'raw_material_id' => $item->raw_material_id,
                'item_description' => $item->item_description ?? ($item->rawMaterial ? $item->rawMaterial->name : ''),
                'received_qty' => (float) $item->received_qty,
                'received_qty_in_kg' => $item->received_qty_in_kg ? (float) $item->received_qty_in_kg : null,
                'unit_id' => $item->unit_id,
                'unit_symbol' => $item->unit ? $item->unit->symbol : '',
                'batch_no' => $item->batch_no,
                'supplier_invoice_no' => $item->supplier_invoice_no,
                'invoice_date' => $item->invoice_date ? $item->invoice_date->format('d-m-Y') : '',
                'given_qty' => (float) $item->received_qty, // Given qty same as received qty by default
            ];
        }

        $supplierName = $materialInward->supplier 
            ? $materialInward->supplier->supplier_name 
            : ($materialInward->purchaseOrder && $materialInward->purchaseOrder->supplier 
                ? $materialInward->purchaseOrder->supplier->supplier_name 
                : '');

        return response()->json([
            'items' => $itemList,
            'supplier_name' => $supplierName,
        ]);
    }

    protected function validateRequest(Request $request): array
    {
        $rules = [
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'material_inward_id' => 'required|exists:material_inwards,id',
            'items' => 'required|array|min:1',
            'items.*.material_inward_item_id' => 'required|exists:material_inward_items,id',
            'items.*.accepted_qty' => 'required|integer|min:0',
            'items.*.rejected_qty' => 'required|integer|min:0',
            'items.*.rejection_reason' => 'nullable|string',
        ];

        $customMessages = [
            'purchase_order_id.required' => 'Please select a Purchase Order.',
            'material_inward_id.required' => 'Please select an MRN No.',
            'items.*.accepted_qty.required' => 'Accepted Qty is required for all items.',
            'items.*.accepted_qty.integer' => 'Accepted Qty must be a whole number.',
            'items.*.accepted_qty.min' => 'Accepted Qty must be 0 or greater.',
            'items.*.rejected_qty.required' => 'Rejected Qty is required for all items.',
            'items.*.rejected_qty.integer' => 'Rejected Qty must be a whole number.',
            'items.*.rejected_qty.min' => 'Rejected Qty must be 0 or greater.',
        ];

        $validated = $request->validate($rules, $customMessages);

        // Additional validation: Accepted Qty + Rejected Qty = Received Qty
        // And if Rejected Qty > 0, Rejection Reason is required
        foreach ($validated['items'] as $index => $item) {
            $materialInwardItem = MaterialInwardItem::findOrFail($item['material_inward_item_id']);
            $receivedQty = (int) round((float) $materialInwardItem->received_qty);
            $acceptedQty = (int) $item['accepted_qty'];
            $rejectedQty = (int) $item['rejected_qty'];

            // Check if Accepted + Rejected = Received
            if ($acceptedQty + $rejectedQty !== $receivedQty) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "items.{$index}.accepted_qty" => "Accepted Qty + Rejected Qty must equal Received Qty ({$receivedQty})."
                ]);
            }

            // If Rejected Qty > 0, Rejection Reason is required
            if ($rejectedQty > 0 && empty($item['rejection_reason'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "items.{$index}.rejection_reason" => "Rejection Reason is required when Rejected Qty is greater than 0."
                ]);
            }

            // Populate additional fields from material_inward_item
            $validated['items'][$index]['purchase_order_item_id'] = $materialInwardItem->purchase_order_item_id;
            $validated['items'][$index]['raw_material_id'] = $materialInwardItem->raw_material_id;
            $validated['items'][$index]['item_description'] = $materialInwardItem->item_description;
            $validated['items'][$index]['received_qty'] = (int) round((float) $materialInwardItem->received_qty);
            $validated['items'][$index]['received_qty_in_kg'] = $materialInwardItem->received_qty_in_kg;
            $validated['items'][$index]['unit_id'] = $materialInwardItem->unit_id;
            $validated['items'][$index]['batch_no'] = $materialInwardItem->batch_no;
            $validated['items'][$index]['supplier_invoice_no'] = $materialInwardItem->supplier_invoice_no;
            $validated['items'][$index]['invoice_date'] = $materialInwardItem->invoice_date;
            $validated['items'][$index]['given_qty'] = (int) round((float) $materialInwardItem->received_qty); // Given qty = received qty
            // Ensure accepted_qty and rejected_qty are stored as integers
            $validated['items'][$index]['accepted_qty'] = (int) $acceptedQty;
            $validated['items'][$index]['rejected_qty'] = (int) $rejectedQty;
        }

        return $validated;
    }

    protected function generateQcMaterialNo(): string
    {
        $last = QcMaterialInward::latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;
        return 'QCM' . str_pad($nextId, 2, '0', STR_PAD_LEFT);
    }
}
