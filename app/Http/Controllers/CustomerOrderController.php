<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\CustomerOrderAmendment;
use App\Models\CustomerOrderItem;
use App\Models\CustomerOrderSchedule;
use App\Models\Tender;
use App\Models\TenderItem;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileUploadHelper;

class CustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'view')) {
            abort(403, 'You do not have permission to view customer orders.');
        }

        $query = CustomerOrder::with('tender');
        $query = $this->applyBranchFilter($query, CustomerOrder::class);

        // Filter by status if requested
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('order_no', 'like', "%{$search}%")
                  ->orWhereHas('tender', function($tenderQuery) use ($search) {
                      $tenderQuery->where('tender_no', 'like', "%{$search}%");
                  })
                  // Search in dates
                  ->orWhereRaw("DATE_FORMAT(order_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(order_date, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(order_date, '%Y-%m-%d') LIKE ?", ["%{$search}%"])
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
            case 'order_no':
                $query->orderBy('customer_orders.order_no', $sortOrder);
                break;
            case 'order_date':
                $query->orderBy('customer_orders.order_date', $sortOrder);
                break;
            case 'tender_no':
                $query->leftJoin('tenders', 'customer_orders.tender_id', '=', 'tenders.id')
                      ->orderBy('tenders.tender_no', $sortOrder)
                      ->select('customer_orders.*')
                      ->distinct();
                break;
            case 'status':
                $query->orderBy('customer_orders.status', $sortOrder);
                break;
            default:
                $query->orderBy('customer_orders.id', $sortOrder);
                break;
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('customer_orders.index', compact('orders'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'create')) {
            abort(403, 'You do not have permission to create customer orders.');
        }

        $tenderQuery = Tender::with(['company']);
        $tenderQuery = $this->applyBranchFilter($tenderQuery, Tender::class);
        $tenders = $tenderQuery->orderByDesc('created_at')->get();

        // Header-level data per tender for auto-population
        $tendersMeta = $tenders->mapWithKeys(function ($t) {
            return [
                $t->id => [
                    'customer_tender_no' => $t->customer_tender_no,
                    'customer_po_no' => $t->customer_po_no ?? null,
                    'customer_po_date' => optional($t->customer_po_date)->format('Y-m-d'),
                    'inspection_agency' => $t->inspection_agency,
                    'customer_name' => optional($t->company)->company_name,
                ],
            ];
        });

        // Generate Customer Order No (CO0001 style)
        $lastOrder = CustomerOrder::latest()->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        $orderNo = 'CO' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        // Load products for the items table
        $productQuery = Product::with('unit');
        $productQuery = $this->applyBranchFilter($productQuery, Product::class);
        $products = $productQuery->orderBy('name')->get();

        $productsData = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'unit_id' => $p->unit_id,
                'unit_symbol' => optional($p->unit)->symbol ?? '',
            ];
        })->values();

        $units = Unit::all();
        $unitsData = $units->map(function ($u) {
            return ['id' => $u->id, 'symbol' => $u->symbol];
        });

        // Load Product Categories for the Add Product modal
        $categoryQuery = \App\Models\ProductCategory::query();
        $categoryQuery = $this->applyBranchFilter($categoryQuery, \App\Models\ProductCategory::class);
        $productCategories = $categoryQuery->orderBy('name')->get();

        $placeholderId = 'PLACEHOLDER_ID';

        return view('customer_orders.create', compact('tenders', 'orderNo', 'units', 'unitsData', 'tendersMeta', 'products', 'productsData', 'productCategories', 'placeholderId'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'create')) {
            abort(403, 'You do not have permission to create customer orders.');
        }

        $request->validate([
            'order_no' => 'required|unique:customer_orders,order_no',
            'order_date' => 'required|date',
            'tender_id' => 'required|exists:tenders,id',
            'customer_tender_no' => 'nullable|string|max:255',
            'customer_po_no' => 'nullable|string|max:255',
            'customer_po_date' => 'nullable|date',
            'inspection_agency' => 'nullable|string|max:255',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.description' => 'nullable|string',
            'items.*.ordered_qty' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.line_amount' => 'required|numeric|min:0',
            'schedules' => 'nullable|array',
            'schedules.*.po_sr_no' => 'nullable|string|max:255',
            'amendments' => 'nullable|array',
            'amendments.*.po_sr_no' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $order = CustomerOrder::create([
                'order_no' => $request->order_no,
                'order_date' => $request->order_date,
                'tender_id' => $request->tender_id,
                'branch_id' => $this->getActiveBranchId(),
                'customer_tender_no' => $request->customer_tender_no,
                'customer_po_no' => $request->customer_po_no,
                'customer_po_date' => $request->customer_po_date,
                'inspection_agency' => $request->inspection_agency,
                'tax_type' => $request->tax_type ?? 'cgst_sgst',
                'total_amount' => $request->total_amount ?? 0,
                'gst_percent' => $request->gst_percent ?? 0,
                'gst_amount' => $request->gst_amount ?? 0,
                'cgst_percent' => $request->cgst_percent ?? 0,
                'cgst_amount' => $request->cgst_amount ?? 0,
                'sgst_percent' => $request->sgst_percent ?? 0,
                'sgst_amount' => $request->sgst_amount ?? 0,
                'igst_percent' => $request->igst_percent ?? 0,
                'igst_amount' => $request->igst_amount ?? 0,
                'freight' => $request->freight ?? 0,
                'inspection_charges' => $request->inspection_charges ?? 0,
                'net_amount' => $request->net_amount ?? 0,
                'amount_note' => $request->amount_note ?? null,
                'status' => 'Pending',
            ]);

            $itemMap = [];
            foreach ($request->items as $key => $itemData) {
                $item = CustomerOrderItem::create([
                    'customer_order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'unit_id' => $itemData['unit_id'],
                    'ordered_qty' => $itemData['ordered_qty'],
                    'description' => $itemData['description'] ?? null,
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'line_amount' => $itemData['line_amount'] ?? 0,
                ]);
                $itemMap[$key] = $item;
            }

            // Validate schedule quantities per item
            if ($request->filled('schedules')) {
                $scheduleSums = [];
                foreach ($request->schedules as $schedule) {
                    $itemIndex = $schedule['item_index'];
                    $qty = (float)($schedule['quantity'] ?? 0);
                    $scheduleSums[$itemIndex] = ($scheduleSums[$itemIndex] ?? 0) + $qty;
                }

                foreach ($scheduleSums as $itemIndex => $totalScheduled) {
                    $ordered = (float)($request->items[$itemIndex]['ordered_qty'] ?? 0);
                    if ($totalScheduled > $ordered) {
                        throw new \Exception("Total scheduled quantity ({$totalScheduled}) cannot exceed ordered quantity ({$ordered}) for the selected product.");
                    }
                }

                foreach ($request->schedules as $schedule) {
                    $itemIndex = $schedule['item_index'];
                    if (!isset($itemMap[$itemIndex])) {
                        continue;
                    }

                    CustomerOrderSchedule::create([
                        'customer_order_id' => $order->id,
                        'customer_order_item_id' => $itemMap[$itemIndex]->id,
                        'po_sr_no' => $schedule['po_sr_no'] ?? null,
                        'quantity' => $schedule['quantity'],
                        'unit_id' => $schedule['unit_id'],
                        'start_date' => $schedule['start_date'],
                        'end_date' => $schedule['end_date'],
                        'inspection_clause' => $schedule['inspection_clause'] ?? null,
                    ]);
                }
            }

            if ($request->filled('amendments')) {
                foreach ($request->amendments as $amendment) {
                    $itemIndex = $amendment['item_index'];
                    if (!isset($itemMap[$itemIndex])) {
                        continue;
                    }

                    $filePath = null;
                    if (isset($amendment['file']) && $amendment['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $filePath = FileUploadHelper::storeWithOriginalName(
                            $amendment['file'],
                            'customer_order_amendments'
                        );
                    }

                    CustomerOrderAmendment::create([
                        'customer_order_id' => $order->id,
                        'customer_order_item_id' => $itemMap[$itemIndex]->id,
                        'po_sr_no' => $amendment['po_sr_no'] ?? null,
                        'amendment_no' => $amendment['amendment_no'] ?? null,
                        'amendment_date' => $amendment['amendment_date'],
                        'existing_quantity' => $amendment['existing_quantity'] ?? null,
                        'new_quantity' => $amendment['new_quantity'],
                        'existing_info' => $amendment['existing_info'] ?? null,
                        'new_info' => $amendment['new_info'] ?? null,
                        'remarks' => $amendment['remarks'] ?? null,
                        'file_path' => $filePath,
                    ]);
                }
            }

            DB::commit();

            // Create notification for Admin and Super Admin
            Notification::create([
                'type' => 'customer_order',
                'sender' => 'Admin',
                'message' => 'Customer Order (' . $order->order_no . ') has been generated. Please Check and Confirm',
                'action_type' => 'check',
                'action_url' => route('customer-orders.show', $order->id),
                'related_id' => $order->id,
                'is_read' => false,
                'user_id' => null, // null means visible to all admins
            ]);

            return redirect()->route('customer-orders.index')->with('success', 'Customer Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating Customer Order: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'edit')) {
            abort(403, 'You do not have permission to edit customer orders.');
        }

        $query = CustomerOrder::with([
            'tender',
            'items.product.unit',
            'items.unit',
            'schedules.customerOrderItem.product',
            'amendments.customerOrderItem.product',
        ]);
        $query = $this->applyBranchFilter($query, CustomerOrder::class);
        $order = $query->findOrFail($id);

        $tenderQuery = Tender::with('company');
        $tenderQuery = $this->applyBranchFilter($tenderQuery, Tender::class);
        $tenders = $tenderQuery->orderByDesc('created_at')->get();

        // Load products for the items table
        $productQuery = Product::with('unit');
        $productQuery = $this->applyBranchFilter($productQuery, Product::class);
        $products = $productQuery->orderBy('name')->get();

        $productsData = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'unit_id' => $p->unit_id,
                'unit_symbol' => optional($p->unit)->symbol ?? '',
            ];
        })->values();

        $units = Unit::all();
        $unitsData = $units->map(function ($u) {
            return ['id' => $u->id, 'symbol' => $u->symbol];
        });

        // Prepare schedules data
        $schedulesData = $order->schedules->map(function ($s) use ($order) {
            $itemIndex = $s->customerOrderItem ? $order->items->search(function($item) use ($s) {
                return $item->id == $s->customer_order_item_id;
            }) : 0;
            
            return [
                'item_index' => $itemIndex !== false ? $itemIndex : 0,
                'product_name' => optional(optional($s->customerOrderItem)->product)->name ?? optional(optional($s->customerOrderItem)->tenderItem)->title ?? '',
                'po_sr_no' => $s->po_sr_no,
                'ordered_qty' => optional($s->customerOrderItem)->ordered_qty ?? 0,
                'quantity' => $s->quantity,
                'unit_id' => $s->unit_id,
                'unit_symbol' => optional($s->unit)->symbol ?? '',
                'start_date' => optional($s->start_date)->format('Y-m-d') ?? '',
                'end_date' => optional($s->end_date)->format('Y-m-d') ?? '',
                'inspection_clause' => $s->inspection_clause ?? '',
            ];
        })->values();

        // Prepare amendments data
        $amendmentsData = $order->amendments->map(function ($a) use ($order) {
            $itemIndex = $a->customerOrderItem ? $order->items->search(function($item) use ($a) {
                return $item->id == $a->customer_order_item_id;
            }) : 0;
            
            return [
                'item_index' => $itemIndex !== false ? $itemIndex : 0,
                'product_name' => optional(optional($a->customerOrderItem)->product)->name ?? optional(optional($a->customerOrderItem)->tenderItem)->title ?? '',
                'po_sr_no' => $a->po_sr_no,
                'ordered_qty' => optional($a->customerOrderItem)->ordered_qty ?? 0,
                'amendment_no' => $a->amendment_no ?? '',
                'amendment_date' => optional($a->amendment_date)->format('Y-m-d') ?? '',
                'existing_quantity' => $a->existing_quantity ?? '',
                'new_quantity' => $a->new_quantity,
                'existing_info' => $a->existing_info ?? '',
                'new_info' => $a->new_info ?? '',
                'remarks' => $a->remarks ?? '',
            ];
        })->values();

        // Load Product Categories for the Add Product modal
        $categoryQuery = \App\Models\ProductCategory::query();
        $categoryQuery = $this->applyBranchFilter($categoryQuery, \App\Models\ProductCategory::class);
        $productCategories = $categoryQuery->orderBy('name')->get();

        $placeholderId = 'PLACEHOLDER_ID';

        return view('customer_orders.edit', compact('order', 'tenders', 'units', 'unitsData', 'products', 'productsData', 'schedulesData', 'amendmentsData', 'productCategories', 'placeholderId'));
    }

    public function show($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'view')) {
            abort(403, 'You do not have permission to view customer orders.');
        }

        $query = CustomerOrder::with([
            'tender',
            'items.product.unit',
            'items.unit',
            'schedules.customerOrderItem.product.unit',
            'amendments.customerOrderItem.product',
        ]);
        $query = $this->applyBranchFilter($query, CustomerOrder::class);
        $order = $query->findOrFail($id);

        return view('customer_orders.show', compact('order'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'edit')) {
            abort(403, 'You do not have permission to edit customer orders.');
        }

        $query = CustomerOrder::query();
        $query = $this->applyBranchFilter($query, CustomerOrder::class);
        $order = $query->findOrFail($id);

        $request->validate([
            'order_date' => 'required|date',
            'tender_id' => 'required|exists:tenders,id',
            'customer_tender_no' => 'nullable|string|max:255',
            'customer_po_no' => 'nullable|string|max:255',
            'customer_po_date' => 'nullable|date',
            'inspection_agency' => 'nullable|string|max:255',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.description' => 'nullable|string',
            'items.*.ordered_qty' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.line_amount' => 'required|numeric|min:0',
            'schedules' => 'nullable|array',
            'schedules.*.po_sr_no' => 'nullable|string|max:255',
            'amendments' => 'nullable|array',
            'amendments.*.po_sr_no' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $order->update([
                'order_date' => $request->order_date,
                'tender_id' => $request->tender_id,
                'customer_tender_no' => $request->customer_tender_no,
                'customer_po_no' => $request->customer_po_no,
                'customer_po_date' => $request->customer_po_date,
                'inspection_agency' => $request->inspection_agency,
                'tax_type' => $request->tax_type ?? 'cgst_sgst',
                'total_amount' => $request->total_amount ?? 0,
                'gst_percent' => $request->gst_percent ?? 0,
                'gst_amount' => $request->gst_amount ?? 0,
                'cgst_percent' => $request->cgst_percent ?? 0,
                'cgst_amount' => $request->cgst_amount ?? 0,
                'sgst_percent' => $request->sgst_percent ?? 0,
                'sgst_amount' => $request->sgst_amount ?? 0,
                'igst_percent' => $request->igst_percent ?? 0,
                'igst_amount' => $request->igst_amount ?? 0,
                'freight' => $request->freight ?? 0,
                'inspection_charges' => $request->inspection_charges ?? 0,
                'net_amount' => $request->net_amount ?? 0,
                'amount_note' => $request->amount_note ?? null,
            ]);

            // Rebuild items, schedules and amendments on update for simplicity
            $order->amendments()->each(function ($amendment) {
                if ($amendment->file_path) {
                    Storage::disk('public')->delete($amendment->file_path);
                }
            });
            $order->amendments()->delete();
            $order->schedules()->delete();
            $order->items()->delete();

            $itemMap = [];
            foreach ($request->items as $key => $itemData) {
                $item = CustomerOrderItem::create([
                    'customer_order_id' => $order->id,
                    'product_id' => $itemData['product_id'],
                    'unit_id' => $itemData['unit_id'],
                    'ordered_qty' => $itemData['ordered_qty'],
                    'description' => $itemData['description'] ?? null,
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'line_amount' => $itemData['line_amount'] ?? 0,
                ]);
                $itemMap[$key] = $item;
            }

            // Validate schedule quantities per item
            if ($request->filled('schedules')) {
                $scheduleSums = [];
                foreach ($request->schedules as $schedule) {
                    $itemIndex = $schedule['item_index'];
                    $qty = (float)($schedule['quantity'] ?? 0);
                    $scheduleSums[$itemIndex] = ($scheduleSums[$itemIndex] ?? 0) + $qty;
                }

                foreach ($scheduleSums as $itemIndex => $totalScheduled) {
                    $ordered = (float)($request->items[$itemIndex]['ordered_qty'] ?? 0);
                    if ($totalScheduled > $ordered) {
                        throw new \Exception("Total scheduled quantity ({$totalScheduled}) cannot exceed ordered quantity ({$ordered}) for the selected product.");
                    }
                }

                foreach ($request->schedules as $schedule) {
                    $itemIndex = $schedule['item_index'];
                    if (!isset($itemMap[$itemIndex])) {
                        continue;
                    }

                    CustomerOrderSchedule::create([
                        'customer_order_id' => $order->id,
                        'customer_order_item_id' => $itemMap[$itemIndex]->id,
                        'po_sr_no' => $schedule['po_sr_no'] ?? null,
                        'quantity' => $schedule['quantity'],
                        'unit_id' => $schedule['unit_id'],
                        'start_date' => $schedule['start_date'],
                        'end_date' => $schedule['end_date'],
                        'inspection_clause' => $schedule['inspection_clause'] ?? null,
                    ]);
                }
            }

            if ($request->filled('amendments')) {
                foreach ($request->amendments as $amendment) {
                    $itemIndex = $amendment['item_index'];
                    if (!isset($itemMap[$itemIndex])) {
                        continue;
                    }

                    $filePath = null;
                    if (isset($amendment['file']) && $amendment['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $filePath = FileUploadHelper::storeWithOriginalName(
                            $amendment['file'],
                            'customer_order_amendments'
                        );
                    }

                    CustomerOrderAmendment::create([
                        'customer_order_id' => $order->id,
                        'customer_order_item_id' => $itemMap[$itemIndex]->id,
                        'po_sr_no' => $amendment['po_sr_no'] ?? null,
                        'amendment_no' => $amendment['amendment_no'] ?? null,
                        'amendment_date' => $amendment['amendment_date'],
                        'existing_quantity' => $amendment['existing_quantity'] ?? null,
                        'new_quantity' => $amendment['new_quantity'],
                        'existing_info' => $amendment['existing_info'] ?? null,
                        'new_info' => $amendment['new_info'] ?? null,
                        'remarks' => $amendment['remarks'] ?? null,
                        'file_path' => $filePath,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('customer-orders.index')->with('success', 'Customer Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating Customer Order: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'delete')) {
            abort(403, 'You do not have permission to delete customer orders.');
        }

        $query = CustomerOrder::with('amendments');
        $query = $this->applyBranchFilter($query, CustomerOrder::class);
        $order = $query->findOrFail($id);

        try {
            DB::beginTransaction();

            foreach ($order->amendments as $amendment) {
                if ($amendment->file_path) {
                    Storage::disk('public')->delete($amendment->file_path);
                }
            }

            $order->delete();

            DB::commit();

            return redirect()->route('customer-orders.index')->with('success', 'Customer Order deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting Customer Order: ' . $e->getMessage());
        }
    }

    /**
     * Approve a Customer Order
     */
    public function approve($id)
    {
        $user = auth()->user();

        // Only Admin and Super Admin can approve
        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'approve')) {
            abort(403, 'You do not have permission to approve customer orders.');
        }

        $query = CustomerOrder::query();
        $query = $this->applyBranchFilter($query, CustomerOrder::class);
        $order = $query->findOrFail($id);

        if ($order->status === 'Approved') {
            return back()->with('error', 'Customer Order is already approved.');
        }

        try {
            DB::beginTransaction();

            $order->status = 'Approved';
            $order->updated_by_id = $user->id;
            $order->save();

            // Mark related notification as read
            Notification::where('type', 'customer_order')
                ->where('related_id', $order->id)
                ->update(['is_read' => true, 'read_at' => now()]);

            DB::commit();

            return redirect()->route('customer-orders.show', $order->id)->with('success', 'Customer Order approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error approving Customer Order: ' . $e->getMessage());
        }
    }

    public function getTenderItemsForDisplay($tenderId)
    {
        try {
            $tender = Tender::with(['items.unit'])
                ->findOrFail($tenderId);

            if (!$tender->items || $tender->items->isEmpty()) {
                return response()->json([
                    'error' => 'No items found in this Tender.',
                    'items' => []
                ], 200);
            }

            $items = $tender->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'pl_code' => $item->pl_code ?? '',
                    'title' => $item->title ?? '',
                    'description' => $item->description ?? '',
                    'delivery_location' => $item->delivery_location ?? '',
                    'qty' => (float)($item->qty ?? 0),
                    'unit_id' => $item->unit_id,
                    'unit_symbol' => optional($item->unit)->symbol ?? '',
                    'request_for_price' => $item->request_for_price ?? 'No',
                    'price_received' => (float)($item->price_received ?? 0),
                    'price_quoted' => (float)($item->price_quoted ?? 0),
                    'tender_status' => $item->tender_status ?? '',
                    'bid_result' => $item->bid_result ?? '',
                ];
            })->values();

            return response()->json($items->all());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Tender not found.'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error loading tender items for display: ' . $e->getMessage(), [
                'tender_id' => $tenderId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error loading tender items for display: ' . $e->getMessage()
            ], 500);
        }
    }
}


