<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\CustomerOrderAmendment;
use App\Models\CustomerOrderItem;
use App\Models\CustomerOrderSchedule;
use App\Models\Tender;
use App\Models\TenderItem;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerOrderController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'view')) {
            abort(403, 'You do not have permission to view customer orders.');
        }

        $query = CustomerOrder::with('tender');
        $query = $this->applyBranchFilter($query, CustomerOrder::class);
        $orders = $query->latest()->paginate(15);

        return view('customer_orders.index', compact('orders'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'create')) {
            abort(403, 'You do not have permission to create customer orders.');
        }

        $tenderQuery = Tender::with('items.unit');
        $tenderQuery = $this->applyBranchFilter($tenderQuery, Tender::class);
        $tenders = $tenderQuery->orderByDesc('created_at')->get();

        $tendersData = $tenders->mapWithKeys(function ($t) {
            return [
                $t->id => $t->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'description' => $item->description,
                        'pl_code' => $item->pl_code,
                        'qty' => $item->qty,
                        'unit' => optional($item->unit)->symbol,
                        // Use quoted price from tender as default price per qty, if available
                        'price' => $item->price_quoted,
                    ];
                })->values(),
            ];
        });

        // Generate Customer Order No (CO0001 style)
        $lastOrder = CustomerOrder::latest()->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        $orderNo = 'CO' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $units = Unit::all();
        $unitsData = $units->map(function ($u) {
            return ['id' => $u->id, 'symbol' => $u->symbol];
        });

        return view('customer_orders.create', compact('tenders', 'orderNo', 'units', 'tendersData', 'unitsData'));
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
            'items' => 'required|array',
            'items.*.tender_item_id' => 'required|exists:tender_items,id',
            'items.*.po_sr_no' => 'nullable|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.pl_code' => 'nullable|string|max:255',
            'items.*.ordered_qty' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.installation_charges' => 'nullable|numeric|min:0',
            'items.*.line_amount' => 'required|numeric|min:0',
            'schedules' => 'nullable|array',
            'amendments' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $order = CustomerOrder::create([
                'order_no' => $request->order_no,
                'order_date' => $request->order_date,
                'tender_id' => $request->tender_id,
                'branch_id' => $this->getActiveBranchId(),
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

            $itemMap = [];
            foreach ($request->items as $key => $itemData) {
                $item = CustomerOrderItem::create([
                    'customer_order_id' => $order->id,
                    'tender_item_id' => $itemData['tender_item_id'],
                    'po_sr_no' => $itemData['po_sr_no'] ?? null,
                    'ordered_qty' => $itemData['ordered_qty'],
                    'description' => $itemData['description'] ?? null,
                    'pl_code' => $itemData['pl_code'] ?? null,
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'installation_charges' => $itemData['installation_charges'] ?? 0,
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
                        $filePath = $amendment['file']->store('customer_order_amendments', 'public');
                    }

                    CustomerOrderAmendment::create([
                        'customer_order_id' => $order->id,
                        'customer_order_item_id' => $itemMap[$itemIndex]->id,
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
            'items.tenderItem.unit',
            'schedules.customerOrderItem.tenderItem',
            'amendments.customerOrderItem.tenderItem',
        ]);
        $query = $this->applyBranchFilter($query, CustomerOrder::class);
        $order = $query->findOrFail($id);

        $tenderQuery = Tender::with('items.unit');
        $tenderQuery = $this->applyBranchFilter($tenderQuery, Tender::class);
        $tenders = $tenderQuery->orderByDesc('created_at')->get();

        $tendersData = $tenders->mapWithKeys(function ($t) {
            return [
                $t->id => $t->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'description' => $item->description,
                        'pl_code' => $item->pl_code,
                        'qty' => $item->qty,
                        'unit' => optional($item->unit)->symbol,
                        'price' => $item->price_quoted,
                    ];
                })->values(),
            ];
        });

        $units = Unit::all();
        $unitsData = $units->map(function ($u) {
            return ['id' => $u->id, 'symbol' => $u->symbol];
        });

        return view('customer_orders.edit', compact('order', 'tenders', 'tendersData', 'units', 'unitsData'));
    }

    public function show($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'view')) {
            abort(403, 'You do not have permission to view customer orders.');
        }

        $query = CustomerOrder::with([
            'tender',
            'items.tenderItem.unit',
            'schedules.customerOrderItem.tenderItem.unit',
            'amendments.customerOrderItem.tenderItem',
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
            'items' => 'required|array',
            'items.*.tender_item_id' => 'required|exists:tender_items,id',
            'items.*.po_sr_no' => 'nullable|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.pl_code' => 'nullable|string|max:255',
            'items.*.ordered_qty' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.installation_charges' => 'nullable|numeric|min:0',
            'items.*.line_amount' => 'required|numeric|min:0',
            'schedules' => 'nullable|array',
            'amendments' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            $order->update([
                'order_date' => $request->order_date,
                'tender_id' => $request->tender_id,
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
                    'tender_item_id' => $itemData['tender_item_id'],
                    'po_sr_no' => $itemData['po_sr_no'] ?? null,
                    'ordered_qty' => $itemData['ordered_qty'],
                    'description' => $itemData['description'] ?? null,
                    'pl_code' => $itemData['pl_code'] ?? null,
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'installation_charges' => $itemData['installation_charges'] ?? 0,
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
                        $filePath = $amendment['file']->store('customer_order_amendments', 'public');
                    }

                    CustomerOrderAmendment::create([
                        'customer_order_id' => $order->id,
                        'customer_order_item_id' => $itemMap[$itemIndex]->id,
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
}


