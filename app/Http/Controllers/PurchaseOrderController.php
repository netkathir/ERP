<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseIndent;
use App\Models\PurchaseIndentItem;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\BillingAddress;
use App\Models\RawMaterial;
use App\Models\Unit;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileUploadHelper;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-orders', 'view')) {
            abort(403, 'You do not have permission to view purchase orders.');
        }

        $query = PurchaseOrder::with(['creator', 'items', 'purchaseIndent.creator', 'supplier']);
        $query = $this->applyBranchFilter($query, PurchaseOrder::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('po_no', 'like', "%{$search}%")
                  ->orWhereHas('purchaseIndent', function($indentQuery) use ($search) {
                      $indentQuery->where('indent_no', 'like', "%{$search}%");
                  })
                  ->orWhereHas('supplier', function($supplierQuery) use ($search) {
                      $supplierQuery->where('supplier_name', 'like', "%{$search}%");
                  })
                  // Search in dates
                  ->orWhereRaw("DATE_FORMAT(po_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(po_date, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(po_date, '%Y-%m-%d') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        // Sorting functionality
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validate sort order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        // Handle sorting for different columns
        switch ($sortBy) {
            case 'po_no':
                $query->orderBy('purchase_orders.po_no', $sortOrder);
                break;
            case 'supplier_name':
                $query->leftJoin('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
                      ->orderBy('suppliers.supplier_name', $sortOrder)
                      ->select('purchase_orders.*')
                      ->distinct();
                break;
            case 'purchase_indent_no':
                $query->leftJoin('purchase_indents', 'purchase_orders.purchase_indent_id', '=', 'purchase_indents.id')
                      ->orderBy('purchase_indents.indent_no', $sortOrder)
                      ->select('purchase_orders.*')
                      ->distinct();
                break;
            case 'material_inward_status':
                // Material Inward Status is based on PO Qty vs Received Qty from Material Inward Items
                // Sorting by status string alphabetically: "Fully Received", "N/A", "Partially Received", "Yet to Receive"
                // The actual status calculation happens in the view, but we sort by a simplified check here
                // to maintain sorting functionality
                $query->orderByRaw("(
                    SELECT 
                        CASE 
                            WHEN NOT EXISTS (SELECT 1 FROM purchase_order_items WHERE purchase_order_id = purchase_orders.id) THEN 'N/A'
                            WHEN NOT EXISTS (
                                SELECT 1 FROM material_inward_items mii
                                INNER JOIN purchase_order_items poi ON mii.purchase_order_item_id = poi.id
                                WHERE poi.purchase_order_id = purchase_orders.id AND mii.received_qty > 0
                            ) THEN 'Yet to Receive'
                            WHEN (
                                SELECT COUNT(*) FROM purchase_order_items poi2
                                WHERE poi2.purchase_order_id = purchase_orders.id
                                AND (
                                    SELECT COALESCE(SUM(mii2.received_qty), 0) 
                                    FROM material_inward_items mii2 
                                    WHERE mii2.purchase_order_item_id = poi2.id
                                ) = poi2.po_quantity
                            ) = (
                                SELECT COUNT(*) FROM purchase_order_items poi3
                                WHERE poi3.purchase_order_id = purchase_orders.id
                            ) THEN 'Fully Received'
                            ELSE 'Partially Received'
                        END
                ) {$sortOrder}");
                break;
            case 'purchase_indent_raised_user':
                $query->leftJoin('purchase_indents', 'purchase_orders.purchase_indent_id', '=', 'purchase_indents.id')
                      ->leftJoin('users', 'purchase_indents.created_by_id', '=', 'users.id')
                      ->orderBy('users.name', $sortOrder)
                      ->select('purchase_orders.*')
                      ->distinct();
                break;
            default:
                $query->orderBy('purchase_orders.id', $sortOrder);
                break;
        }

        $purchaseOrders = $query->paginate(15)->withQueryString();

        return view('purchase.purchase_orders.index', compact('purchaseOrders'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-orders', 'create')) {
            abort(403, 'You do not have permission to create purchase orders.');
        }

        $poNo = $this->generatePONo();
        
        // Get purchase_indent_id from query parameter if provided
        $selectedPurchaseIndentId = $request->get('purchase_indent_id');

        // Get only approved purchase indents that still have available quantity
        $purchaseIndentQuery = PurchaseIndent::where('status', 'Approved')
            ->with('items');
        $purchaseIndentQuery = $this->applyBranchFilter($purchaseIndentQuery, PurchaseIndent::class);
        $allApprovedIndents = $purchaseIndentQuery->orderBy('id', 'desc')->get();

        // Filter out purchase indents where all items are fully used
        $purchaseIndents = $allApprovedIndents->filter(function ($indent) {
            foreach ($indent->items as $item) {
                // Calculate already raised PO qty for this item
                $alreadyRaisedQty = PurchaseOrderItem::where('purchase_indent_item_id', $item->id)
                    ->sum('po_quantity');
                
                // If any item has remaining quantity, include this indent
                if ($item->quantity > $alreadyRaisedQty) {
                    return true;
                }
            }
            return false; // All items are fully used
        });

        // Get customers
        $customerQuery = Customer::query();
        $customerQuery = $this->applyBranchFilter($customerQuery, Customer::class);
        $customers = $customerQuery->orderBy('company_name')->get();

        // Get suppliers (for subcontractor)
        $supplierQuery = Supplier::query();
        $supplierQuery = $this->applyBranchFilter($supplierQuery, Supplier::class);
        $suppliers = $supplierQuery->orderBy('supplier_name')->get();

        // Get billing addresses (for company)
        $billingAddressQuery = BillingAddress::query();
        $billingAddressQuery = $this->applyBranchFilter($billingAddressQuery, BillingAddress::class);
        $billingAddresses = $billingAddressQuery->orderBy('company_name')->get();

        // Get raw materials
        $rawMaterialQuery = RawMaterial::with('unit');
        $rawMaterialQuery = $this->applyBranchFilter($rawMaterialQuery, RawMaterial::class);
        $rawMaterials = $rawMaterialQuery->orderBy('name')->get();

        $rawMaterialsData = $rawMaterials->mapWithKeys(function ($rm) {
            return [
                $rm->id => [
                    'name' => $rm->name,
                    'grade' => $rm->grade,
                    'thickness' => $rm->thickness,
                    'description' => trim(($rm->name ?? '') . ' ' . ($rm->grade ?? '') . ' ' . ($rm->thickness ?? '')),
                    'unit_id' => $rm->unit_id,
                    'unit_symbol' => optional($rm->unit)->symbol,
                ],
            ];
        });

        $units = Unit::orderBy('name')->get();

        // Branches for branch selection (Super Admin sees all, others see their assigned active branches)
        $branches = collect();
        $selectedBranchId = $this->getActiveBranchId();
        if ($user->isSuperAdmin()) {
            $branches = Branch::where('is_active', true)->orderBy('name')->get();
        } else {
            $branches = $user->branches()->where('is_active', true)->orderBy('name')->get();
        }

        return view('purchase.purchase_orders.create', compact(
            'poNo',
            'purchaseIndents',
            'customers',
            'suppliers',
            'billingAddresses',
            'rawMaterials',
            'rawMaterialsData',
            'units',
            'branches',
            'selectedBranchId',
            'selectedPurchaseIndentId'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-orders', 'create')) {
            abort(403, 'You do not have permission to create purchase orders.');
        }

        $validated = $this->validateRequest($request);

        try {
            DB::beginTransaction();

            $po = new PurchaseOrder();
            $po->po_no = $this->generatePONo();
            $po->purchase_indent_id = $validated['purchase_indent_id'] ?? null;
            $po->ship_to = $validated['ship_to'] ?? null;
            $po->customer_id = $validated['customer_id'] ?? null;
            $po->subcontractor_id = $validated['subcontractor_id'] ?? null;
            $po->company_id = $validated['company_id'] ?? null;
            
            // Ship To Address
            $po->ship_to_address_line_1 = $validated['ship_to_address_line_1'] ?? null;
            $po->ship_to_address_line_2 = $validated['ship_to_address_line_2'] ?? null;
            $po->ship_to_city = $validated['ship_to_city'] ?? null;
            $po->ship_to_state = $validated['ship_to_state'] ?? null;
            $po->ship_to_pincode = $validated['ship_to_pincode'] ?? null;
            $po->ship_to_email = $validated['ship_to_email'] ?? null;
            $po->ship_to_contact_no = $validated['ship_to_contact_no'] ?? null;
            $po->ship_to_gst_no = $validated['ship_to_gst_no'] ?? null;
            
            // Supplier Address
            $po->supplier_id = $validated['supplier_id'] ?? null;
            $po->supplier_address_line_1 = $validated['supplier_address_line_1'] ?? null;
            $po->supplier_address_line_2 = $validated['supplier_address_line_2'] ?? null;
            $po->supplier_city = $validated['supplier_city'] ?? null;
            $po->supplier_state = $validated['supplier_state'] ?? null;
            $po->supplier_email = $validated['supplier_email'] ?? null;
            $po->supplier_gst_no = $validated['supplier_gst_no'] ?? null;
            
            // Billing Address
            $po->billing_address_id = $validated['billing_address_id'] ?? null;
            $po->billing_address_line_1 = $validated['billing_address_line_1'] ?? null;
            $po->billing_address_line_2 = $validated['billing_address_line_2'] ?? null;
            $po->billing_city = $validated['billing_city'] ?? null;
            $po->billing_state = $validated['billing_state'] ?? null;
            $po->billing_email = $validated['billing_email'] ?? null;
            $po->billing_gst_no = $validated['billing_gst_no'] ?? null;
            
            // Amount Calculation
            $po->tax_type = $validated['tax_type'] ?? 'cgst_sgst';
            $po->gst = $validated['gst'] ?? 0;
            $po->gst_percent = $validated['gst_percent'] ?? 0;
            $po->sgst = $validated['sgst'] ?? 0;
            $po->cgst_percent = $validated['cgst_percent'] ?? 0;
            $po->cgst_amount = $validated['cgst_amount'] ?? 0;
            $po->igst_percent = $validated['igst_percent'] ?? 0;
            $po->igst_amount = $validated['igst_amount'] ?? 0;
            $po->total = $validated['total'] ?? 0;
            $po->discount = $validated['discount'] ?? 0;
            $po->discount_percent = $validated['discount_percent'] ?? 0;
            $po->net_amount = $validated['net_amount'] ?? 0;
            
            // Terms and Conditions
            $po->freight_charges = $validated['freight_charges'] ?? null;
            $po->terms_of_payment = $validated['terms_of_payment'] ?? null;
            $po->special_conditions = $validated['special_conditions'] ?? null;
            $po->inspection = $validated['inspection'] ?? null;
            
            // Transport and Warranty
            $po->name_of_transport = $validated['name_of_transport'] ?? null;
            $po->transport_certificate = $validated['transport_certificate'] ?? null;
            $po->insurance_of_goods_damages = $validated['insurance_of_goods_damages'] ?? null;
            $po->warranty_expiry = $validated['warranty_expiry'] ?? null;
            
            $po->status = 'Draft';
            $po->remarks = $validated['remarks'] ?? null;

            // Branch handling: allow any user with access to select branch, otherwise use active branch
            if (!empty($validated['branch_id']) && $user->hasAccessToBranch((int)$validated['branch_id'])) {
                $po->branch_id = (int)$validated['branch_id'];
            } else {
                $po->branch_id = $this->getActiveBranchId();
            }
            $po->created_by_id = $user->id;

            if ($request->hasFile('upload')) {
                $path = FileUploadHelper::storeWithOriginalName(
                    $request->file('upload'),
                    'purchase-orders'
                );
                $po->upload_path = $path;
                $po->upload_original_name = $request->file('upload')->getClientOriginalName();
            }

            $po->save();

            // Create items
            foreach ($validated['items'] as $itemData) {
                $itemData['purchase_order_id'] = $po->id;
                PurchaseOrderItem::create($itemData);
            }

            // Update already_raised_po_qty in purchase indent items
            $this->updatePurchaseIndentRaisedQty($po);

            DB::commit();

            return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating Purchase Order: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-orders', 'view')) {
            abort(403, 'You do not have permission to view purchase orders.');
        }

        $query = PurchaseOrder::with([
            'creator',
            'items.rawMaterial.unit',
            'items.purchaseIndentItem',
            'purchaseIndent',
            'customer',
            'subcontractor',
            'company',
            'supplier',
            'billingAddress'
        ]);
        $query = $this->applyBranchFilter($query, PurchaseOrder::class);
        $purchaseOrder = $query->findOrFail($id);

        return view('purchase.purchase_orders.show', compact('purchaseOrder'));
    }

    public function edit($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-orders', 'edit')) {
            abort(403, 'You do not have permission to edit purchase orders.');
        }

        $query = PurchaseOrder::with('items');
        $query = $this->applyBranchFilter($query, PurchaseOrder::class);
        $purchaseOrder = $query->findOrFail($id);

        // Get only approved purchase indents that still have available quantity
        $purchaseIndentQuery = PurchaseIndent::where('status', 'Approved')
            ->with('items');
        $purchaseIndentQuery = $this->applyBranchFilter($purchaseIndentQuery, PurchaseIndent::class);
        $allApprovedIndents = $purchaseIndentQuery->orderBy('indent_no')->get();

        // Filter out purchase indents where all items are fully used
        $purchaseIndents = $allApprovedIndents->filter(function ($indent) {
            foreach ($indent->items as $item) {
                // Calculate already raised PO qty for this item
                $alreadyRaisedQty = PurchaseOrderItem::where('purchase_indent_item_id', $item->id)
                    ->sum('po_quantity');
                
                // If any item has remaining quantity, include this indent
                if ($item->quantity > $alreadyRaisedQty) {
                    return true;
                }
            }
            return false; // All items are fully used
        });

        // Get customers
        $customerQuery = Customer::query();
        $customerQuery = $this->applyBranchFilter($customerQuery, Customer::class);
        $customers = $customerQuery->orderBy('company_name')->get();

        // Get suppliers
        $supplierQuery = Supplier::query();
        $supplierQuery = $this->applyBranchFilter($supplierQuery, Supplier::class);
        $suppliers = $supplierQuery->orderBy('supplier_name')->get();

        // Get billing addresses
        $billingAddressQuery = BillingAddress::query();
        $billingAddressQuery = $this->applyBranchFilter($billingAddressQuery, BillingAddress::class);
        $billingAddresses = $billingAddressQuery->orderBy('company_name')->get();

        // Get raw materials
        $rawMaterialQuery = RawMaterial::with('unit');
        $rawMaterialQuery = $this->applyBranchFilter($rawMaterialQuery, RawMaterial::class);
        $rawMaterials = $rawMaterialQuery->orderBy('name')->get();

        $rawMaterialsData = $rawMaterials->mapWithKeys(function ($rm) {
            return [
                $rm->id => [
                    'name' => $rm->name,
                    'grade' => $rm->grade,
                    'thickness' => $rm->thickness,
                    'description' => trim(($rm->name ?? '') . ' ' . ($rm->grade ?? '') . ' ' . ($rm->thickness ?? '')),
                    'unit_id' => $rm->unit_id,
                    'unit_symbol' => optional($rm->unit)->symbol,
                ],
            ];
        });

        $units = Unit::orderBy('name')->get();

        // Branches for branch selection (Super Admin sees all, others see their assigned active branches)
        $branches = collect();
        $selectedBranchId = $purchaseOrder->branch_id ?? $this->getActiveBranchId();
        if ($user->isSuperAdmin()) {
            $branches = Branch::where('is_active', true)->orderBy('name')->get();
        } else {
            $branches = $user->branches()->where('is_active', true)->orderBy('name')->get();
        }

        return view('purchase.purchase_orders.edit', compact(
            'purchaseOrder',
            'purchaseIndents',
            'customers',
            'suppliers',
            'billingAddresses',
            'rawMaterials',
            'rawMaterialsData',
            'units',
            'branches',
            'selectedBranchId'
        ));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-orders', 'edit')) {
            abort(403, 'You do not have permission to edit purchase orders.');
        }

        $query = PurchaseOrder::with('items');
        $query = $this->applyBranchFilter($query, PurchaseOrder::class);
        $po = $query->findOrFail($id);

        $validated = $this->validateRequest($request, $po->id);

        try {
            DB::beginTransaction();

            // Update main PO fields
            $po->purchase_indent_id = $validated['purchase_indent_id'] ?? null;
            $po->ship_to = $validated['ship_to'] ?? null;
            $po->customer_id = $validated['customer_id'] ?? null;
            $po->subcontractor_id = $validated['subcontractor_id'] ?? null;
            $po->company_id = $validated['company_id'] ?? null;
            
            // Ship To Address
            $po->ship_to_address_line_1 = $validated['ship_to_address_line_1'] ?? null;
            $po->ship_to_address_line_2 = $validated['ship_to_address_line_2'] ?? null;
            $po->ship_to_city = $validated['ship_to_city'] ?? null;
            $po->ship_to_state = $validated['ship_to_state'] ?? null;
            $po->ship_to_pincode = $validated['ship_to_pincode'] ?? null;
            $po->ship_to_email = $validated['ship_to_email'] ?? null;
            $po->ship_to_contact_no = $validated['ship_to_contact_no'] ?? null;
            $po->ship_to_gst_no = $validated['ship_to_gst_no'] ?? null;
            
            // Supplier Address
            $po->supplier_id = $validated['supplier_id'] ?? null;
            $po->supplier_address_line_1 = $validated['supplier_address_line_1'] ?? null;
            $po->supplier_address_line_2 = $validated['supplier_address_line_2'] ?? null;
            $po->supplier_city = $validated['supplier_city'] ?? null;
            $po->supplier_state = $validated['supplier_state'] ?? null;
            $po->supplier_email = $validated['supplier_email'] ?? null;
            $po->supplier_gst_no = $validated['supplier_gst_no'] ?? null;
            
            // Billing Address
            $po->billing_address_id = $validated['billing_address_id'] ?? null;
            $po->billing_address_line_1 = $validated['billing_address_line_1'] ?? null;
            $po->billing_address_line_2 = $validated['billing_address_line_2'] ?? null;
            $po->billing_city = $validated['billing_city'] ?? null;
            $po->billing_state = $validated['billing_state'] ?? null;
            $po->billing_email = $validated['billing_email'] ?? null;
            $po->billing_gst_no = $validated['billing_gst_no'] ?? null;
            
            // Amount Calculation
            $po->tax_type = $validated['tax_type'] ?? 'cgst_sgst';
            $po->gst = $validated['gst'] ?? 0;
            $po->gst_percent = $validated['gst_percent'] ?? 0;
            $po->sgst = $validated['sgst'] ?? 0;
            $po->cgst_percent = $validated['cgst_percent'] ?? 0;
            $po->cgst_amount = $validated['cgst_amount'] ?? 0;
            $po->igst_percent = $validated['igst_percent'] ?? 0;
            $po->igst_amount = $validated['igst_amount'] ?? 0;
            $po->total = $validated['total'] ?? 0;
            $po->discount = $validated['discount'] ?? 0;
            $po->discount_percent = $validated['discount_percent'] ?? 0;
            $po->net_amount = $validated['net_amount'] ?? 0;
            
            // Terms and Conditions
            $po->freight_charges = $validated['freight_charges'] ?? null;
            $po->terms_of_payment = $validated['terms_of_payment'] ?? null;
            $po->special_conditions = $validated['special_conditions'] ?? null;
            $po->inspection = $validated['inspection'] ?? null;
            
            // Transport and Warranty
            $po->name_of_transport = $validated['name_of_transport'] ?? null;
            $po->transport_certificate = $validated['transport_certificate'] ?? null;
            $po->insurance_of_goods_damages = $validated['insurance_of_goods_damages'] ?? null;
            $po->warranty_expiry = $validated['warranty_expiry'] ?? null;
            
            $po->remarks = $validated['remarks'] ?? null;

            // Branch handling on update: allow any user with access to change branch, otherwise keep existing or fallback to active
            if (!empty($validated['branch_id']) && $user->hasAccessToBranch((int)$validated['branch_id'])) {
                $po->branch_id = (int)$validated['branch_id'];
            } elseif (!$po->branch_id) {
                $po->branch_id = $this->getActiveBranchId();
            }

            $po->updated_by_id = $user->id;

            if ($request->boolean('remove_upload')) {
                if ($po->upload_path) {
                    Storage::disk('public')->delete($po->upload_path);
                }
                $po->upload_path = null;
                $po->upload_original_name = null;
            }

            if ($request->hasFile('upload')) {
                if ($po->upload_path) {
                    Storage::disk('public')->delete($po->upload_path);
                }
                $path = FileUploadHelper::storeWithOriginalName(
                    $request->file('upload'),
                    'purchase-orders'
                );
                $po->upload_path = $path;
                $po->upload_original_name = $request->file('upload')->getClientOriginalName();
            }

            $po->save();

            // Update items - only allow editing certain fields in edit mode
            $po->items()->delete();
            foreach ($validated['items'] as $itemData) {
                $itemData['purchase_order_id'] = $po->id;
                PurchaseOrderItem::create($itemData);
            }

            // Update purchase indent items with PO data
            $this->updatePurchaseIndentFromPO($po);

            // Update already_raised_po_qty in purchase indent items
            $this->updatePurchaseIndentRaisedQty($po);

            DB::commit();

            return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating Purchase Order: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-orders', 'delete')) {
            abort(403, 'You do not have permission to delete purchase orders.');
        }

        $query = PurchaseOrder::query();
        $query = $this->applyBranchFilter($query, PurchaseOrder::class);
        $po = $query->findOrFail($id);

        if ($po->upload_path) {
            Storage::disk('public')->delete($po->upload_path);
        }

        $po->delete();

        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order deleted successfully.');
    }

    /**
     * Get purchase indent items for a given purchase indent ID
     */
    public function getPurchaseIndentItems($purchaseIndentId)
    {
        try {
            // Don't apply branch filter here - we want to load items from the selected purchase indent
            $purchaseIndent = PurchaseIndent::with(['items.rawMaterial.unit', 'items.supplier'])
                ->findOrFail($purchaseIndentId);

            // Check if purchase indent has items
            if (!$purchaseIndent->items || $purchaseIndent->items->isEmpty()) {
                return response()->json([
                    'error' => 'No items found in this Purchase Indent.',
                    'items' => []
                ], 200);
            }

            // Calculate already raised PO qty for each item and return ONLY items with remaining quantity
            $items = $purchaseIndent->items
                ->map(function ($item) {
                    // Calculate already raised PO qty from all purchase orders
                    $alreadyRaisedQty = PurchaseOrderItem::where('purchase_indent_item_id', $item->id)
                        ->sum('po_quantity');

                    $approvedQty = (float)($item->quantity ?? 0);

                    // Skip items where Approved Qty is fully raised
                    if ($approvedQty <= $alreadyRaisedQty) {
                        return null;
                    }

                    return [
                        'id' => $item->id,
                        'purchase_indent_item_id' => $item->id,
                        'raw_material_id' => $item->raw_material_id,
                        'item_name' => optional($item->rawMaterial)->name ?? '',
                        'item_description' => $item->item_description ?? '',
                        'approved_quantity' => $approvedQty,
                        'already_raised_po_qty' => (float)$alreadyRaisedQty,
                        'unit_id' => $item->unit_id,
                        'unit_symbol' => optional($item->unit)->symbol ?? '',
                        'supplier_id' => $item->supplier_id,
                    ];
                })
                ->filter() // remove nulls (fully used items)
                ->values();

            // If all items are fully used, return a friendly message
            if ($items->isEmpty()) {
                return response()->json([
                    'error' => 'All items in this Purchase Indent are fully used (Already Raised PO Qty equals Approved Qty).',
                    'items' => [],
                ], 200);
            }

            return response()->json($items->all());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Purchase Indent not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error loading purchase indent items: ' . $e->getMessage(), [
                'purchase_indent_id' => $purchaseIndentId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error loading purchase indent items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all purchase indent items for display (view-only table)
     * Returns ALL items regardless of remaining quantity
     */
    public function getPurchaseIndentItemsForDisplay($purchaseIndentId)
    {
        try {
            // Don't apply branch filter here - we want to load items from the selected purchase indent
            $purchaseIndent = PurchaseIndent::with(['items.rawMaterial.unit', 'items.supplier'])
                ->findOrFail($purchaseIndentId);

            // Check if purchase indent has items
            if (!$purchaseIndent->items || $purchaseIndent->items->isEmpty()) {
                return response()->json([
                    'items' => []
                ], 200);
            }

            // Return ALL items for display purposes
            $items = $purchaseIndent->items
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'item_name' => optional($item->rawMaterial)->name ?? '',
                        'item_description' => $item->item_description ?? '',
                        'quantity' => (float)($item->quantity ?? 0),
                        'unit_symbol' => optional($item->unit)->symbol ?? optional($item->unit)->name ?? '',
                        'schedule_date' => optional($item->schedule_date)->format('d-m-Y') ?? '',
                        'supplier_name' => optional($item->supplier)->supplier_name ?? '',
                        'special_instructions' => $item->special_instructions ?? '',
                        'po_status' => $item->po_status ?? '',
                        'lr_details' => $item->lr_details ?? '',
                        'booking_agency' => $item->booking_agency ?? '',
                        'delivered_qty' => $item->delivered_qty ?? '',
                        'delivery_status' => $item->delivery_status ?? '',
                        'po_remarks' => $item->po_remarks ?? '',
                    ];
                })
                ->values();

            return response()->json($items->all());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Purchase Indent not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error loading purchase indent items for display: ' . $e->getMessage(), [
                'purchase_indent_id' => $purchaseIndentId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error loading purchase indent items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get customer details
     */
    public function getCustomerDetails($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        
        return response()->json([
            'shipping_address_line_1' => $customer->shipping_address_line_1,
            'shipping_address_line_2' => $customer->shipping_address_line_2,
            'shipping_city' => $customer->shipping_city,
            'shipping_state' => $customer->shipping_state,
            'shipping_pincode' => $customer->shipping_pincode,
            'contact_no' => $customer->contact_info, // Using contact_info as contact_no
            'email' => null, // Customer model doesn't have email field
            'gst_no' => $customer->gst_no,
        ]);
    }

    /**
     * Get supplier details
     */
    public function getSupplierDetails($supplierId)
    {
        $supplier = Supplier::findOrFail($supplierId);
        
        return response()->json([
            'address_line_1' => $supplier->address_line_1,
            'address_line_2' => $supplier->address_line_2,
            'city' => $supplier->city,
            'state' => $supplier->state,
            'email' => $supplier->email,
            'gst' => $supplier->gst,
        ]);
    }

    /**
     * Get billing address details
     */
    public function getBillingAddressDetails($billingAddressId)
    {
        $billingAddress = BillingAddress::findOrFail($billingAddressId);
        
        return response()->json([
            'address_line_1' => $billingAddress->address_line_1,
            'address_line_2' => $billingAddress->address_line_2,
            'city' => $billingAddress->city,
            'state' => $billingAddress->state,
            'email' => $billingAddress->email,
            'gst_no' => $billingAddress->gst_no,
        ]);
    }

    protected function validateRequest(Request $request, ?int $id = null): array
    {
        $today = now()->toDateString();

        $rules = [
            'purchase_indent_id' => 'nullable|exists:purchase_indents,id',
            'ship_to' => 'required|in:Customer,Subcontractor,Company',
            'customer_id' => 'nullable|required_if:ship_to,Customer|exists:customers,id',
            'subcontractor_id' => 'nullable|required_if:ship_to,Subcontractor|exists:suppliers,id',
            'company_id' => 'nullable|required_if:ship_to,Company|exists:billing_addresses,id',
            'ship_to_address_line_1' => 'nullable|string|max:255',
            'ship_to_address_line_2' => 'nullable|string|max:255',
            'ship_to_city' => 'nullable|string|max:255',
            'ship_to_state' => 'nullable|string|max:255',
            'ship_to_pincode' => 'nullable|string|max:20',
            'ship_to_email' => 'nullable|email|max:255',
            'ship_to_contact_no' => 'nullable|string|max:20',
            'ship_to_gst_no' => 'nullable|string|max:50',
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_address_line_1' => 'nullable|string|max:255',
            'supplier_address_line_2' => 'nullable|string|max:255',
            'supplier_city' => 'nullable|string|max:255',
            'supplier_state' => 'nullable|string|max:255',
            'supplier_email' => 'nullable|email|max:255',
            'supplier_gst_no' => 'nullable|string|max:50',
            'billing_address_id' => 'required|exists:billing_addresses,id',
            'billing_address_line_1' => 'nullable|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:255',
            'billing_state' => 'nullable|string|max:255',
            'billing_email' => 'nullable|email|max:255',
            'billing_gst_no' => 'nullable|string|max:50',
            'branch_id' => 'nullable|exists:branches,id',
            'tax_type' => 'required|in:cgst_sgst,igst',
            'gst' => 'nullable|numeric|min:0',
            'gst_percent' => 'required|numeric|min:0|max:100',
            'sgst' => 'nullable|numeric|min:0',
            'cgst_percent' => 'nullable|numeric|min:0|max:100',
            'cgst_amount' => 'nullable|numeric|min:0',
            'igst_percent' => 'nullable|numeric|min:0|max:100',
            'igst_amount' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'net_amount' => 'nullable|numeric|min:0',
            'freight_charges' => 'nullable|string',
            'terms_of_payment' => 'nullable|string',
            'special_conditions' => 'nullable|string',
            'inspection' => 'nullable|string',
            'name_of_transport' => 'nullable|string|max:255',
            'transport_certificate' => 'nullable|string|max:255',
            'insurance_of_goods_damages' => 'nullable|string',
            'warranty_expiry' => 'nullable|date',
            'upload' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'remove_upload' => 'nullable|boolean',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.purchase_indent_item_id' => 'nullable|exists:purchase_indent_items,id',
            'items.*.raw_material_id' => 'nullable|exists:raw_materials,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.pack_details' => 'nullable|string',
            'items.*.approved_quantity' => 'nullable|numeric|min:0',
            'items.*.already_raised_po_qty' => 'nullable|numeric|min:0',
            'items.*.po_quantity' => 'required|integer|min:1',
            'items.*.expected_delivery_date' => 'nullable|date|after_or_equal:' . $today,
            'items.*.unit_id' => 'nullable|exists:units,id',
            'items.*.qty_in_kg' => 'nullable|numeric|min:0',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.amount' => 'nullable|numeric|min:0',
            'items.*.po_status' => 'nullable|string|max:255',
            'items.*.lr_details' => 'nullable|string|max:255',
            'items.*.booking_agency' => 'nullable|string|max:255',
            'items.*.delivered_on' => 'nullable|date',
            'items.*.delivered_qty' => 'nullable|numeric|min:0',
            'items.*.delivery_status' => 'nullable|in:Completed,Incomplete,Partially Completed',
            'items.*.remarks' => 'nullable|string',
        ];

        $messages = [
            'ship_to.required' => 'Please select where to ship the order.',
            'ship_to.in' => 'Invalid ship to option selected.',
            'customer_id.required_if' => 'Please select a customer when Ship To is set to Customer.',
            'subcontractor_id.required_if' => 'Please select a subcontractor when Ship To is set to Subcontractor.',
            'company_id.required_if' => 'Please select a company when Ship To is set to Company.',
            'supplier_id.required' => 'Please select a supplier.',
            'supplier_id.exists' => 'The selected supplier is invalid.',
            'billing_address_id.required' => 'Please select a billing address.',
            'billing_address_id.exists' => 'The selected billing address is invalid.',
            'items.required' => 'Please add at least one product item.',
            'items.min' => 'Please add at least one product item.',
            'items.*.item_name.required' => 'Item name is required for all product rows.',
            'items.*.po_quantity.required' => 'PO Quantity is required for all product rows.',
            'items.*.po_quantity.integer' => 'PO Quantity must be a whole number (no decimals allowed).',
            'items.*.po_quantity.min' => 'PO Quantity must be at least 1 for all product rows.',
            'items.*.price.required' => 'Price is required for all product rows.',
            'items.*.price.min' => 'Price must be 0 or greater for all product rows.',
            'items.*.expected_delivery_date.after_or_equal' => 'Expected delivery date cannot be in the past.',
            'tax_type.required' => 'Please select a GST Type.',
            'tax_type.in' => 'Invalid GST Type selected.',
            'gst_percent.required' => 'GST percentage is required.',
            'gst_percent.numeric' => 'GST percentage must be a valid number.',
            'gst_percent.min' => 'GST percentage must be 0 or greater.',
            'gst_percent.max' => 'GST percentage cannot exceed 100.',
        ];

        return $request->validate($rules, $messages);
    }

    protected function generatePONo(): string
    {
        $last = PurchaseOrder::latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;
        return 'PO-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Update already_raised_po_qty in purchase indent items
     */
    protected function updatePurchaseIndentRaisedQty(PurchaseOrder $po)
    {
        if (!$po->purchase_indent_id) {
            return;
        }

        $purchaseIndent = PurchaseIndent::with('items')->find($po->purchase_indent_id);
        if (!$purchaseIndent) {
            return;
        }

        foreach ($purchaseIndent->items as $indentItem) {
            $alreadyRaisedQty = PurchaseOrderItem::where('purchase_indent_item_id', $indentItem->id)
                ->sum('po_quantity');
            
            // Note: We don't store already_raised_po_qty in purchase_indent_items table
            // It's calculated dynamically when needed
        }
    }

    /**
     * Update purchase indent items with PO data (for edit mode)
     */
    protected function updatePurchaseIndentFromPO(PurchaseOrder $po)
    {
        if (!$po->purchase_indent_id) {
            return;
        }

        foreach ($po->items as $poItem) {
            if ($poItem->purchase_indent_item_id) {
                $indentItem = PurchaseIndentItem::find($poItem->purchase_indent_item_id);
                if ($indentItem) {
                    // Update fields that can be edited in PO edit mode
                    $indentItem->po_status = $poItem->po_status;
                    $indentItem->lr_details = $poItem->lr_details;
                    $indentItem->booking_agency = $poItem->booking_agency;
                    $indentItem->delivered_qty = $poItem->delivered_qty;
                    $indentItem->delivery_status = $poItem->delivery_status;
                    $indentItem->po_remarks = $poItem->remarks;
                    $indentItem->save();
                }
            }
        }
    }
}

