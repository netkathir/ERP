<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\WorkOrderRawMaterial;
use App\Models\CustomerOrder;
use App\Models\ProformaInvoice;
use App\Models\Employee;
use App\Models\Supplier;
use App\Models\RawMaterial;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileUploadHelper;

class WorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkOrder::with(['customerOrder', 'proformaInvoice', 'creator']);
        $query = $this->applyBranchFilter($query, WorkOrder::class);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('work_orders.production_order_no', 'like', "%{$search}%")
                    ->orWhere('work_orders.customer_po_no', 'like', "%{$search}%")
                    ->orWhere('work_orders.sales_type', 'like', "%{$search}%")
                    ->orWhere('work_orders.work_order_no', 'like', "%{$search}%")
                    ->orWhere('work_orders.title', 'like', "%{$search}%")
                    ->orWhereRaw("DATE_FORMAT(work_orders.created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("DATE_FORMAT(work_orders.created_at, '%Y-%m-%d') LIKE ?", ["%{$search}%"]);
            });
        }

        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $allowedSort = ['id', 'production_order_no', 'customer_po_no', 'sales_type', 'work_order_no', 'title', 'created_at'];
        if (in_array($sortBy, $allowedSort)) {
            $query->orderBy('work_orders.' . $sortBy, $sortOrder);
        } else {
            $query->orderBy('work_orders.id', $sortOrder);
        }

        $workOrders = $query->paginate(15)->withQueryString();

        return view('production.work_orders.index', compact('workOrders'));
    }

    public function create(Request $request)
    {
        $existingWoId = $request->get('from');
        $existingWo = null;
        if ($existingWoId) {
            $q = WorkOrder::with('rawMaterials.rawMaterial.unit');
            $q = $this->applyBranchFilter($q, WorkOrder::class);
            $existingWo = $q->find($existingWoId);
        }

        return $this->createEditView(null, $existingWo);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_type' => 'required|in:Tender,Enquiry',
            'po_type' => 'required|in:customer_order,proforma_invoice',
            'po_id' => 'required|integer|min:1',
            'title' => 'nullable|string|max:500',
            'worker_type' => 'required|in:Employee,Sub-Contractor',
            'worker_id' => 'nullable|integer',
            'product_name' => 'nullable|string|max:500',
            'quantity_type' => 'required|in:Sets,Sub Sets,Nos,Others',
            'no_of_sets' => 'nullable|numeric|min:0',
            'starting_set_no' => 'nullable|integer|min:0',
            'no_of_sub_sets_per_set' => 'nullable|numeric|min:0',
            'no_of_quantity' => 'nullable|numeric|min:0',
            'starting_quantity_no' => 'nullable|integer|min:0',
            'quantity_per_set' => 'nullable|numeric|min:0',
            'thickness' => 'nullable|string|max:255',
            'drawing_no' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'completion_date' => 'nullable|date',
            'nature_of_work' => 'nullable|string|max:500',
            'layup_sequence' => 'nullable|string|max:255',
            'batch_no' => 'nullable|string|max:255',
            'work_order_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'reference_table_data' => 'nullable|string',
            'qty_blocks' => 'nullable|array',
            'qty_blocks.*.no_of_sets' => 'nullable|numeric|min:0',
            'qty_blocks.*.starting_set_no' => 'nullable|integer|min:0',
            'qty_blocks.*.no_of_sub_sets_per_set' => 'nullable|numeric|min:0',
            'qty_blocks.*.quantity_per_set' => 'nullable|numeric|min:0',
            'qty_blocks.*.no_of_quantity' => 'nullable|numeric|min:0',
            'qty_blocks.*.starting_quantity_no' => 'nullable|integer|min:0',
        ]);

        $productionOrderNo = '';
        $customerPoNo = '';
        $customerOrderId = null;
        $proformaInvoiceId = null;
        $title = $validated['title'] ?? '';

        if ($validated['po_type'] === 'customer_order') {
            $coQuery = CustomerOrder::query();
            $coQuery = $this->applyBranchFilter($coQuery, CustomerOrder::class);
            $co = $coQuery->findOrFail($validated['po_id']);
            $customerOrderId = $co->id;
            $productionOrderNo = $co->production_order_no ?? $co->order_no ?? '';
            $customerPoNo = $co->customer_po_no ?? '';
            if (empty($title) && $co->items->isNotEmpty()) {
                $titles = $co->items->pluck('product.name')->filter()->unique()->implode(', ');
                if ($titles) {
                    $title = $titles;
                }
            }
        } else {
            $piQuery = ProformaInvoice::query();
            $piQuery = $this->applyBranchFilter($piQuery, ProformaInvoice::class);
            $pi = $piQuery->findOrFail($validated['po_id']);
            $proformaInvoiceId = $pi->id;
            $productionOrderNo = $pi->invoice_no ?? '';
            $customerPoNo = '';
        }

        $poRef = $validated['sales_type'] === 'Tender'
            ? ($customerPoNo ?: $productionOrderNo ?: (string) $validated['po_id'])
            : ($productionOrderNo ?: (string) $validated['po_id']);
        $workOrderNo = WorkOrder::generateWorkOrderNo($validated['sales_type'], $poRef);

        $branchId = $this->getActiveBranchId();

        $workOrder = WorkOrder::create([
            'branch_id' => $branchId,
            'sales_type' => $validated['sales_type'],
            'customer_order_id' => $customerOrderId,
            'proforma_invoice_id' => $proformaInvoiceId,
            'production_order_no' => $productionOrderNo,
            'customer_po_no' => $customerPoNo,
            'work_order_no' => $workOrderNo,
            'title' => $title,
            'worker_type' => $validated['worker_type'],
            'worker_id' => $validated['worker_id'] ?? null,
            'product_name' => $validated['product_name'] ?? null,
            'quantity_type' => $validated['quantity_type'],
            'no_of_sets' => $validated['no_of_sets'] ?? null,
            'starting_set_no' => $validated['starting_set_no'] ?? null,
            'ending_set_no' => $this->calcEndingSetNo($validated),
            'no_of_sub_sets_per_set' => $validated['no_of_sub_sets_per_set'] ?? null,
            'total_sub_sets' => $this->calcTotalSubSets($validated),
            'quantity_per_set' => $validated['quantity_per_set'] ?? null,
            'no_of_quantity' => $validated['no_of_quantity'] ?? null,
            'starting_quantity_no' => $validated['starting_quantity_no'] ?? null,
            'ending_quantity_no' => $this->calcEndingQuantityNo($validated),
            'thickness' => $validated['thickness'] ?? null,
            'drawing_no' => $validated['drawing_no'] ?? null,
            'color' => $validated['color'] ?? null,
            'completion_date' => $validated['completion_date'] ?? null,
            'nature_of_work' => $validated['nature_of_work'] ?? null,
            'layup_sequence' => $validated['layup_sequence'] ?? null,
            'batch_no' => $validated['batch_no'] ?? null,
            'work_order_date' => $validated['work_order_date'] ?? now(),
            'remarks' => $validated['remarks'] ?? null,
            'reference_table_data' => !empty($validated['reference_table_data']) ? json_decode($validated['reference_table_data'], true) : null,
            'quantity_blocks' => $this->buildQuantityBlocks($validated),
            'created_by_id' => auth()->id(),
        ]);

        if ($request->hasFile('document')) {
            $path = FileUploadHelper::storeWithOriginalName($request->file('document'), 'work_orders', 'wo');
            $workOrder->update(['document_path' => $path]);
        }

        $this->storeRawMaterials($workOrder, $request);

        return redirect()->route('work-orders.index')->with('success', 'Work order created successfully.');
    }

    public function show($id)
    {
        return $this->createEditView($id, null, true);
    }

    public function edit($id)
    {
        return $this->createEditView($id, null);
    }

    public function update(Request $request, $id)
    {
        $query = WorkOrder::query();
        $query = $this->applyBranchFilter($query, WorkOrder::class);
        $workOrder = $query->findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string|max:500',
            'worker_type' => 'required|in:Employee,Sub-Contractor',
            'worker_id' => 'nullable|integer',
            'product_name' => 'nullable|string|max:500',
            'quantity_type' => 'required|in:Sets,Sub Sets,Nos,Others',
            'no_of_sets' => 'nullable|numeric|min:0',
            'starting_set_no' => 'nullable|integer|min:0',
            'no_of_sub_sets_per_set' => 'nullable|numeric|min:0',
            'no_of_quantity' => 'nullable|numeric|min:0',
            'starting_quantity_no' => 'nullable|integer|min:0',
            'quantity_per_set' => 'nullable|numeric|min:0',
            'thickness' => 'nullable|string|max:255',
            'drawing_no' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'completion_date' => 'nullable|date',
            'nature_of_work' => 'nullable|string|max:500',
            'layup_sequence' => 'nullable|string|max:255',
            'batch_no' => 'nullable|string|max:255',
            'work_order_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'reference_table_data' => 'nullable|string',
            'qty_blocks' => 'nullable|array',
            'qty_blocks.*.no_of_sets' => 'nullable|numeric|min:0',
            'qty_blocks.*.starting_set_no' => 'nullable|integer|min:0',
            'qty_blocks.*.no_of_sub_sets_per_set' => 'nullable|numeric|min:0',
            'qty_blocks.*.quantity_per_set' => 'nullable|numeric|min:0',
            'qty_blocks.*.no_of_quantity' => 'nullable|numeric|min:0',
            'qty_blocks.*.starting_quantity_no' => 'nullable|integer|min:0',
        ]);

        $workOrder->update([
            'title' => $validated['title'] ?? null,
            'worker_type' => $validated['worker_type'],
            'worker_id' => $validated['worker_id'] ?? null,
            'product_name' => $validated['product_name'] ?? null,
            'quantity_type' => $validated['quantity_type'],
            'no_of_sets' => $validated['no_of_sets'] ?? null,
            'starting_set_no' => $validated['starting_set_no'] ?? null,
            'ending_set_no' => $this->calcEndingSetNo($validated),
            'no_of_sub_sets_per_set' => $validated['no_of_sub_sets_per_set'] ?? null,
            'total_sub_sets' => $this->calcTotalSubSets($validated),
            'quantity_per_set' => $validated['quantity_per_set'] ?? null,
            'no_of_quantity' => $validated['no_of_quantity'] ?? null,
            'starting_quantity_no' => $validated['starting_quantity_no'] ?? null,
            'ending_quantity_no' => $this->calcEndingQuantityNo($validated),
            'thickness' => $validated['thickness'] ?? null,
            'drawing_no' => $validated['drawing_no'] ?? null,
            'color' => $validated['color'] ?? null,
            'completion_date' => $validated['completion_date'] ?? null,
            'nature_of_work' => $validated['nature_of_work'] ?? null,
            'layup_sequence' => $validated['layup_sequence'] ?? null,
            'batch_no' => $validated['batch_no'] ?? null,
            'work_order_date' => $validated['work_order_date'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'reference_table_data' => !empty($validated['reference_table_data']) ? json_decode($validated['reference_table_data'], true) : null,
            'quantity_blocks' => $this->buildQuantityBlocks($validated),
        ]);

        if ($request->hasFile('document')) {
            if ($workOrder->document_path) {
                Storage::disk('public')->delete($workOrder->document_path);
            }
            $path = FileUploadHelper::storeWithOriginalName($request->file('document'), 'work_orders', 'wo');
            $workOrder->update(['document_path' => $path]);
        }

        $workOrder->rawMaterials()->delete();
        $this->storeRawMaterials($workOrder, $request);

        return redirect()->route('work-orders.index')->with('success', 'Work order updated successfully.');
    }

    public function destroy($id)
    {
        $query = WorkOrder::query();
        $query = $this->applyBranchFilter($query, WorkOrder::class);
        $workOrder = $query->findOrFail($id);

        if ($workOrder->document_path) {
            Storage::disk('public')->delete($workOrder->document_path);
        }
        $workOrder->delete();

        return redirect()->route('work-orders.index')->with('success', 'Work order deleted successfully.');
    }

    public function getPoOptions(Request $request)
    {
        $salesType = $request->get('sales_type', 'Tender');
        $options = [];

        if ($salesType === 'Tender') {
            $query = CustomerOrder::with('tender')->orderByDesc('created_at');
            $query = $this->applyBranchFilter($query, CustomerOrder::class);
            $orders = $query->limit(200)->get();
            foreach ($orders as $o) {
                $poNo = $o->production_order_no ?? $o->order_no ?? 'CO-' . $o->id;
                $options[] = [
                    'id' => $o->id,
                    'type' => 'customer_order',
                    'po_no' => $poNo,
                    'customer_po_no' => $o->customer_po_no ?? '',
                    'tender_no' => $o->tender ? $o->tender->tender_no : '',
                ];
            }
        } else {
            $query = ProformaInvoice::orderByDesc('created_at');
            $query = $this->applyBranchFilter($query, ProformaInvoice::class);
            $invoices = $query->limit(200)->get();
            foreach ($invoices as $pi) {
                $options[] = [
                    'id' => $pi->id,
                    'type' => 'proforma_invoice',
                    'po_no' => $pi->invoice_no ?? 'PI-' . $pi->id,
                    'customer_po_no' => '',
                ];
            }
        }

        return response()->json($options);
    }

    public function getTitleFromPo(Request $request)
    {
        $type = $request->get('po_type');
        $id = (int) $request->get('po_id');
        $title = '';

        if ($type === 'customer_order' && $id) {
            $co = CustomerOrder::with('items.product')->find($id);
            if ($co && $co->items->isNotEmpty()) {
                $titles = $co->items->pluck('product.name')->filter()->unique()->implode(', ');
                $title = $titles;
            }
        }

        return response()->json(['title' => $title]);
    }

    public function getRawMaterialUnit(Request $request)
    {
        $id = (int) $request->get('raw_material_id');
        $rm = RawMaterial::with('unit')->find($id);
        return response()->json([
            'unit_id' => $rm ? $rm->unit_id : null,
            'unit_symbol' => ($rm && $rm->unit && $rm->unit->symbol) ? $rm->unit->symbol : 'N/A',
        ]);
    }

    public function getNextWorkOrderNo(Request $request)
    {
        $salesType = $request->get('sales_type', 'Tender');
        $poType = $request->get('po_type', 'customer_order');
        $poId = (int) $request->get('po_id');
        $poRef = '';

        if ($poId) {
            if ($poType === 'customer_order') {
                $co = CustomerOrder::find($poId);
                if ($co) {
                    $poRef = $salesType === 'Tender'
                        ? ($co->customer_po_no ?: $co->production_order_no ?? $co->order_no ?? (string) $poId)
                        : ($co->production_order_no ?? $co->order_no ?? (string) $poId);
                }
            } else {
                $pi = ProformaInvoice::find($poId);
                if ($pi) {
                    $poRef = $pi->invoice_no ?? (string) $poId;
                }
            }
        }

        $workOrderNo = WorkOrder::generateWorkOrderNo($salesType, $poRef ?: '0');
        return response()->json(['work_order_no' => $workOrderNo]);
    }

    protected function createEditView($id, $existingWo)
    {
        if ($id) {
            $q = WorkOrder::with(['rawMaterials.rawMaterial.unit', 'existingWorkOrder']);
            $q = $this->applyBranchFilter($q, WorkOrder::class);
            $workOrder = $q->findOrFail($id);
        } else {
            $workOrder = new WorkOrder();
            if ($existingWo) {
                $workOrder->fill($existingWo->only([
                    'sales_type', 'customer_order_id', 'proforma_invoice_id', 'production_order_no', 'customer_po_no',
                    'title', 'worker_type', 'worker_id', 'product_name', 'quantity_type', 'thickness', 'drawing_no',
                    'color', 'nature_of_work', 'layup_sequence', 'batch_no', 'remarks',
                    'no_of_sets', 'starting_set_no', 'ending_set_no', 'no_of_sub_sets_per_set', 'total_sub_sets',
                    'quantity_per_set', 'no_of_quantity', 'starting_quantity_no', 'ending_quantity_no',
                    'completion_date', 'work_order_date',
                ]));
                $workOrder->existing_work_order_id = $existingWo->id;
                $workOrder->setRelation('rawMaterials', $existingWo->rawMaterials);
            }
        }

        $coQuery = CustomerOrder::with('tender')->orderByDesc('created_at');
        $coQuery = $this->applyBranchFilter($coQuery, CustomerOrder::class);
        $customerOrders = $coQuery->limit(200)->get();

        $piQuery = ProformaInvoice::orderByDesc('created_at');
        $piQuery = $this->applyBranchFilter($piQuery, ProformaInvoice::class);
        $proformaInvoices = $piQuery->limit(200)->get();

        $empQuery = Employee::orderBy('name');
        $empQuery = $this->applyBranchFilter($empQuery, Employee::class);
        $employees = $empQuery->get();

        $subQuery = Supplier::where('supplier_type', 'Sub Contractor')->orderBy('supplier_name');
        $subQuery = $this->applyBranchFilter($subQuery, Supplier::class);
        $subcontractors = $subQuery->get();

        $rmQuery = RawMaterial::with('unit')->orderBy('name');
        $rmQuery = $this->applyBranchFilter($rmQuery, RawMaterial::class);
        $rawMaterials = $rmQuery->get();

        $woQuery = WorkOrder::orderByDesc('created_at');
        $woQuery = $this->applyBranchFilter($woQuery, WorkOrder::class);
        $existingWorkOrders = $woQuery->limit(100)->get();

        $rawMaterialsData = $rawMaterials->mapWithKeys(function ($r) {
            return [
                $r->id => [
                    'name' => trim(($r->name ?? '') . ($r->grade ? ' - ' . $r->grade : '')),
                    'unit_symbol' => ($r->unit && $r->unit->symbol) ? $r->unit->symbol : 'N/A',
                    'unit_id' => $r->unit_id,
                ],
            ];
        });

        $viewOnly = func_num_args() >= 3 && func_get_arg(2) === true;

        return view('production.work_orders.create', compact(
            'workOrder',
            'customerOrders',
            'proformaInvoices',
            'employees',
            'subcontractors',
            'rawMaterials',
            'rawMaterialsData',
            'existingWorkOrders',
            'existingWo',
            'viewOnly'
        ));
    }

    protected function storeRawMaterials(WorkOrder $workOrder, Request $request)
    {
        $rows = $request->input('raw_materials', []);
        if (!is_array($rows)) {
            return;
        }
        $sr = 0;
        foreach ($rows as $r) {
            if (empty($r['raw_material_id'])) {
                continue;
            }
            $rm = RawMaterial::find($r['raw_material_id']);
            if (!$rm) {
                continue;
            }
            $qty = (float) ($r['work_order_quantity'] ?? 0);
            if ($qty <= 0) {
                continue;
            }
            $sr++;
            WorkOrderRawMaterial::create([
                'work_order_id' => $workOrder->id,
                'raw_material_id' => $rm->id,
                'work_order_quantity' => $qty,
                'unit_id' => $rm->unit_id,
                'sr_no' => $sr,
            ]);
        }
    }

    protected function calcEndingSetNo(array $v): ?int
    {
        $no = $v['no_of_sets'] ?? null;
        $start = $v['starting_set_no'] ?? null;
        if ($no !== null && $start !== null && is_numeric($no) && is_numeric($start)) {
            return (int) $start + (int) $no - 1;
        }
        return null;
    }

    protected function calcTotalSubSets(array $v): ?float
    {
        $sets = $v['no_of_sets'] ?? null;
        $subPerSet = $v['no_of_sub_sets_per_set'] ?? null;
        if ($sets !== null && $subPerSet !== null && is_numeric($sets) && is_numeric($subPerSet)) {
            return (float) $sets * (float) $subPerSet;
        }
        return null;
    }

    protected function calcEndingQuantityNo(array $v): ?int
    {
        $qty = $v['no_of_quantity'] ?? $v['no_of_sets'] ?? null;
        $start = $v['starting_quantity_no'] ?? $v['starting_set_no'] ?? null;
        if ($qty !== null && $start !== null && is_numeric($qty) && is_numeric($start)) {
            return (int) $start + (int) $qty - 1;
        }
        return null;
    }

    protected function buildQuantityBlocks(array $v): ?array
    {
        $qtyType = $v['quantity_type'] ?? '';
        if (!in_array($qtyType, ['Sub Sets', 'Nos', 'Others'], true)) {
            return null;
        }
        $first = [
            'no_of_sets' => $v['no_of_sets'] ?? null,
            'starting_set_no' => $v['starting_set_no'] ?? null,
            'no_of_sub_sets_per_set' => $v['no_of_sub_sets_per_set'] ?? null,
            'quantity_per_set' => $v['quantity_per_set'] ?? null,
            'no_of_quantity' => $v['no_of_quantity'] ?? null,
            'starting_quantity_no' => $v['starting_quantity_no'] ?? null,
        ];
        $blocks = [$first];
        $extra = $v['qty_blocks'] ?? [];
        foreach ($extra as $k => $b) {
            if (!is_array($b) || (int) $k === 0) {
                continue;
            }
            $blocks[] = [
                'no_of_sets' => $b['no_of_sets'] ?? null,
                'starting_set_no' => $b['starting_set_no'] ?? null,
                'no_of_sub_sets_per_set' => $b['no_of_sub_sets_per_set'] ?? null,
                'quantity_per_set' => $b['quantity_per_set'] ?? null,
                'no_of_quantity' => $b['no_of_quantity'] ?? null,
                'starting_quantity_no' => $b['starting_quantity_no'] ?? null,
            ];
        }
        return $blocks;
    }
}
