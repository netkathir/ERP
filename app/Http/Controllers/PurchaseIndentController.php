<?php

namespace App\Http\Controllers;

use App\Models\PurchaseIndent;
use App\Models\PurchaseIndentItem;
use App\Models\RawMaterial;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileUploadHelper;
use Carbon\Carbon;

class PurchaseIndentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-indents', 'view')) {
            abort(403, 'You do not have permission to view purchase indents.');
        }

        $query = PurchaseIndent::with(['creator', 'updater', 'items.rawMaterial', 'items']);
        $query = $this->applyBranchFilter($query, PurchaseIndent::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('indent_no', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  // Search in dates
                  ->orWhereRaw("DATE_FORMAT(indent_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(indent_date, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(indent_date, '%Y-%m-%d') LIKE ?", ["%{$search}%"])
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
            case 'indent_no':
                $query->orderBy('purchase_indents.indent_no', $sortOrder);
                break;
            case 'indent_date':
                $query->orderBy('purchase_indents.indent_date', $sortOrder);
                break;
            case 'created_by':
                $query->leftJoin('users', 'purchase_indents.created_by_id', '=', 'users.id')
                      ->orderBy('users.name', $sortOrder)
                      ->select('purchase_indents.*')
                      ->distinct();
                break;
            case 'status':
                $query->orderBy('purchase_indents.status', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('purchase_indents.created_at', $sortOrder);
                break;
            default:
                $query->orderBy('purchase_indents.id', $sortOrder);
                break;
        }

        $indents = $query->paginate(15)->withQueryString();

        return view('purchase.purchase_indents.index', compact('indents'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-indents', 'create')) {
            abort(403, 'You do not have permission to create purchase indents.');
        }

        $indentNo = $this->generateIndentNo();

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

        $supplierQuery = Supplier::query();
        $supplierQuery = $this->applyBranchFilter($supplierQuery, Supplier::class);
        $suppliers = $supplierQuery->orderBy('supplier_name')->get();

        return view('purchase.purchase_indents.create', compact(
            'indentNo',
            'rawMaterials',
            'rawMaterialsData',
            'units',
            'suppliers'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-indents', 'create')) {
            abort(403, 'You do not have permission to create purchase indents.');
        }

        $validated = $this->validateRequest($request);

        try {
            DB::beginTransaction();

            $indent = new PurchaseIndent();
            $indent->indent_no = $this->generateIndentNo();
            $indent->indent_date = $validated['indent_date'];
            $indent->status = 'Pending';
            $indent->remarks = $validated['remarks'] ?? null;
            $indent->branch_id = $this->getActiveBranchId();
            $indent->created_by_id = $user->id;

            if ($request->hasFile('upload')) {
                $path = FileUploadHelper::storeWithOriginalName(
                    $request->file('upload'),
                    'purchase-indents'
                );
                $indent->upload_path = $path;
                $indent->upload_original_name = $request->file('upload')->getClientOriginalName();
            }

            $indent->save();

            foreach ($validated['items'] as $itemData) {
                $itemData['purchase_indent_id'] = $indent->id;
                PurchaseIndentItem::create($itemData);
            }

            DB::commit();

            // Create notification for Admin and Super Admin
            Notification::create([
                'type' => 'purchase_indent',
                'sender' => 'Admin',
                'message' => 'Purchase Indent (' . $indent->indent_no . ') has been generated. Please Check and Confirm',
                'action_type' => 'check',
                'action_url' => route('purchase-indents.show', $indent->id),
                'related_id' => $indent->id,
                'is_read' => false,
                'user_id' => null, // null means visible to all admins
            ]);

            return redirect()->route('purchase-indents.index')->with('success', 'Purchase Indent created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating Purchase Indent: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-indents', 'view')) {
            abort(403, 'You do not have permission to view purchase indents.');
        }

        $query = PurchaseIndent::with(['creator', 'items.rawMaterial.unit', 'items.supplier']);
        $query = $this->applyBranchFilter($query, PurchaseIndent::class);
        $indent = $query->findOrFail($id);

        return view('purchase.purchase_indents.show', compact('indent'));
    }

    public function edit($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-indents', 'edit')) {
            abort(403, 'You do not have permission to edit purchase indents.');
        }

        $query = PurchaseIndent::with('items');
        $query = $this->applyBranchFilter($query, PurchaseIndent::class);
        $indent = $query->findOrFail($id);

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

        $supplierQuery = Supplier::query();
        $supplierQuery = $this->applyBranchFilter($supplierQuery, Supplier::class);
        $suppliers = $supplierQuery->orderBy('supplier_name')->get();

        return view('purchase.purchase_indents.edit', compact(
            'indent',
            'rawMaterials',
            'rawMaterialsData',
            'units',
            'suppliers'
        ));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-indents', 'edit')) {
            abort(403, 'You do not have permission to edit purchase indents.');
        }

        $query = PurchaseIndent::with('items');
        $query = $this->applyBranchFilter($query, PurchaseIndent::class);
        $indent = $query->findOrFail($id);

        $validated = $this->validateRequest($request, $indent->id);

        try {
            DB::beginTransaction();

            $indent->indent_date = $validated['indent_date'];
            $indent->remarks = $validated['remarks'] ?? null;
            $indent->updated_by_id = $user->id;

            if ($request->hasFile('upload')) {
                if ($indent->upload_path) {
                    Storage::disk('public')->delete($indent->upload_path);
                }
                $path = FileUploadHelper::storeWithOriginalName(
                    $request->file('upload'),
                    'purchase-indents'
                );
                $indent->upload_path = $path;
                $indent->upload_original_name = $request->file('upload')->getClientOriginalName();
            }

            $indent->save();

            // Replace items for simplicity
            $indent->items()->delete();
            foreach ($validated['items'] as $itemData) {
                $itemData['purchase_indent_id'] = $indent->id;
                PurchaseIndentItem::create($itemData);
            }

            DB::commit();

            return redirect()->route('purchase-indents.index')->with('success', 'Purchase Indent updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating Purchase Indent: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-indents', 'delete')) {
            abort(403, 'You do not have permission to delete purchase indents.');
        }

        $query = PurchaseIndent::query();
        $query = $this->applyBranchFilter($query, PurchaseIndent::class);
        $indent = $query->findOrFail($id);

        if ($indent->upload_path) {
            Storage::disk('public')->delete($indent->upload_path);
        }

        $indent->delete();

        return redirect()->route('purchase-indents.index')->with('success', 'Purchase Indent deleted successfully.');
    }

    protected function validateRequest(Request $request, ?int $id = null): array
    {
        $today = now()->toDateString();

        $request->merge([
            'indent_date' => $this->normalizeDate($request->input('indent_date')),
            'items' => $this->normalizeItemDates($request->input('items', [])),
        ]);

        return $request->validate([
            'indent_date' => 'required|date',
            'remarks' => 'nullable|string',
            'upload' => 'nullable|file|mimes:xls,xlsx|max:5120',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.schedule_date' => 'required|date|after_or_equal:' . $today,
            'items.*.special_instructions' => 'required|string',
            'items.*.supplier_id' => 'required|exists:suppliers,id',
            'items.*.po_status' => 'nullable|string|max:255',
            'items.*.lr_details' => 'nullable|string|max:255',
            'items.*.booking_agency' => 'nullable|string|max:255',
            'items.*.delivered_qty' => 'nullable|numeric|min:0',
            'items.*.delivery_status' => 'nullable|string|max:255',
            'items.*.po_remarks' => 'nullable|string',
        ]);
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

    protected function normalizeItemDates(array $items): array
    {
        foreach ($items as $index => $item) {
            if (isset($item['created_date'])) {
                $items[$index]['created_date'] = $this->normalizeDate($item['created_date']);
            }

            if (isset($item['schedule_date'])) {
                $items[$index]['schedule_date'] = $this->normalizeDate($item['schedule_date']);
            }
        }

        return $items;
    }

    public function approve($id)
    {
        $user = auth()->user();

        // Only Admin and Super Admin can approve
        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-indents', 'approve')) {
            abort(403, 'You do not have permission to approve purchase indents.');
        }

        $query = PurchaseIndent::query();
        $query = $this->applyBranchFilter($query, PurchaseIndent::class);
        $indent = $query->findOrFail($id);

        if ($indent->status === 'Approved') {
            return back()->with('error', 'Purchase Indent is already approved.');
        }

        try {
            DB::beginTransaction();

            $indent->status = 'Approved';
            $indent->updated_by_id = $user->id;
            $indent->save();

            // Mark related notification as read
            Notification::where('type', 'purchase_indent')
                ->where('related_id', $indent->id)
                ->update(['is_read' => true, 'read_at' => now()]);

            DB::commit();

            return redirect()->route('purchase-indents.show', $indent->id)->with('success', 'Purchase Indent approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error approving Purchase Indent: ' . $e->getMessage());
        }
    }

    protected function generateIndentNo(): string
    {
        $last = PurchaseIndent::latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;
        return 'PI-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
}


