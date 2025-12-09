<?php

namespace App\Http\Controllers;

use App\Models\CustomerComplaint;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileUploadHelper;

class CustomerComplaintController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('customer-complaints', 'view')) {
            abort(403, 'You do not have permission to view customer complaints.');
        }

        $query = CustomerComplaint::with(['product', 'attendedBy']);
        $query = $this->applyBranchFilter($query, CustomerComplaint::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('complaint_type', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhereHas('product', function($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  })
                  // Search in dates
                  ->orWhereRaw("DATE_FORMAT(complaint_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(complaint_date, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(complaint_date, '%Y-%m-%d') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        $complaints = $query->latest()->paginate(15)->withQueryString();

        return view('tenders.customer_complaints.index', compact('complaints'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('customer-complaints', 'create')) {
            abort(403, 'You do not have permission to create customer complaints.');
        }

        $productQuery = Product::query();
        $productQuery = $this->applyBranchFilter($productQuery, Product::class);
        $products = $productQuery->orderBy('name')->get();

        $attendedByUsers = User::where('status', 'active')->orderBy('name')->get();

        $defaultComplaintTypes = ['Product defect', 'Delivery issue'];

        return view('tenders.customer_complaints.create', compact('products', 'attendedByUsers', 'defaultComplaintTypes'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('customer-complaints', 'create')) {
            abort(403, 'You do not have permission to create customer complaints.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'site_address' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'attended_by_id' => 'required|exists:users,id',
            'complaint_type' => 'required|string|max:255',
            'complaint_reg_no' => 'nullable|string|max:255',
            'quantity' => 'nullable|numeric|min:0',
            'complaint_details' => 'nullable|string',
            'correction' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'remarks_from_customer' => 'nullable|string',
            'complaint_date' => 'required|date',
            'root_cause_analysis' => 'required|string',
            'preventive_action' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'closed_on' => 'nullable|date',
            'status' => 'required|string|max:50',
            'attachment' => 'nullable|file|max:10240',
            'remarks' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = FileUploadHelper::storeWithOriginalName(
                    $request->file('attachment'),
                    'customer_complaints'
                );
            }

            CustomerComplaint::create([
                'customer_name' => $validated['customer_name'],
                'customer_address' => $validated['customer_address'] ?? null,
                'site_address' => $validated['site_address'] ?? null,
                'product_id' => $validated['product_id'],
                'attended_by_id' => $validated['attended_by_id'],
                'complaint_type' => $validated['complaint_type'],
                'complaint_reg_no' => $validated['complaint_reg_no'] ?? null,
                'quantity' => $validated['quantity'] ?? null,
                'complaint_details' => $validated['complaint_details'] ?? null,
                'correction' => $validated['correction'] ?? null,
                'location' => $validated['location'] ?? null,
                'remarks_from_customer' => $validated['remarks_from_customer'] ?? null,
                'remarks' => $validated['remarks'],
                'complaint_date' => $validated['complaint_date'],
                'root_cause_analysis' => $validated['root_cause_analysis'],
                'preventive_action' => $validated['preventive_action'] ?? null,
                'corrective_action' => $validated['corrective_action'] ?? null,
                'closed_on' => $validated['closed_on'] ?? null,
                'status' => $validated['status'],
                'attachment_path' => $attachmentPath,
                'branch_id' => $this->getActiveBranchId(),
                'created_by_id' => $user->id,
            ]);

            DB::commit();

            return redirect()->route('customer-complaints.index')->with('success', 'Customer complaint registered successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating customer complaint: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('customer-complaints', 'view')) {
            abort(403, 'You do not have permission to view customer complaints.');
        }

        $query = CustomerComplaint::with(['product', 'attendedBy']);
        $query = $this->applyBranchFilter($query, CustomerComplaint::class);
        $complaint = $query->findOrFail($id);

        return view('tenders.customer_complaints.show', compact('complaint'));
    }

    public function edit($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('customer-complaints', 'edit')) {
            abort(403, 'You do not have permission to edit customer complaints.');
        }

        $query = CustomerComplaint::query();
        $query = $this->applyBranchFilter($query, CustomerComplaint::class);
        $complaint = $query->findOrFail($id);

        $productQuery = Product::query();
        $productQuery = $this->applyBranchFilter($productQuery, Product::class);
        $products = $productQuery->orderBy('name')->get();

        $attendedByUsers = User::where('status', 'active')->orderBy('name')->get();

        $defaultComplaintTypes = ['Product defect', 'Delivery issue'];

        return view('tenders.customer_complaints.edit', compact('complaint', 'products', 'attendedByUsers', 'defaultComplaintTypes'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('customer-complaints', 'edit')) {
            abort(403, 'You do not have permission to edit customer complaints.');
        }

        $query = CustomerComplaint::query();
        $query = $this->applyBranchFilter($query, CustomerComplaint::class);
        $complaint = $query->findOrFail($id);

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_address' => 'nullable|string',
            'site_address' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'attended_by_id' => 'required|exists:users,id',
            'complaint_type' => 'required|string|max:255',
            'complaint_reg_no' => 'nullable|string|max:255',
            'quantity' => 'nullable|numeric|min:0',
            'complaint_details' => 'nullable|string',
            'correction' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'remarks_from_customer' => 'nullable|string',
            'complaint_date' => 'required|date',
            'root_cause_analysis' => 'required|string',
            'preventive_action' => 'nullable|string',
            'corrective_action' => 'nullable|string',
            'closed_on' => 'nullable|date',
            'status' => 'required|string|max:50',
            'attachment' => 'nullable|file|max:10240',
            'remarks' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $attachmentPath = $complaint->attachment_path;

            if ($request->hasFile('attachment')) {
                if ($attachmentPath) {
                    Storage::disk('public')->delete($attachmentPath);
                }

                $attachmentPath = FileUploadHelper::storeWithOriginalName(
                    $request->file('attachment'),
                    'customer_complaints'
                );
            }

            $complaint->update([
                'customer_name' => $validated['customer_name'],
                'customer_address' => $validated['customer_address'] ?? null,
                'site_address' => $validated['site_address'] ?? null,
                'product_id' => $validated['product_id'],
                'attended_by_id' => $validated['attended_by_id'],
                'complaint_type' => $validated['complaint_type'],
                'complaint_reg_no' => $validated['complaint_reg_no'] ?? null,
                'quantity' => $validated['quantity'] ?? null,
                'complaint_details' => $validated['complaint_details'] ?? null,
                'correction' => $validated['correction'] ?? null,
                'location' => $validated['location'] ?? null,
                'remarks_from_customer' => $validated['remarks_from_customer'] ?? null,
                'remarks' => $validated['remarks'],
                'complaint_date' => $validated['complaint_date'],
                'root_cause_analysis' => $validated['root_cause_analysis'],
                'preventive_action' => $validated['preventive_action'] ?? null,
                'corrective_action' => $validated['corrective_action'] ?? null,
                'closed_on' => $validated['closed_on'] ?? null,
                'status' => $validated['status'],
                'attachment_path' => $attachmentPath,
            ]);

            DB::commit();

            return redirect()->route('customer-complaints.index')->with('success', 'Customer complaint updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating customer complaint: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('customer-complaints', 'delete')) {
            abort(403, 'You do not have permission to delete customer complaints.');
        }

        $query = CustomerComplaint::query();
        $query = $this->applyBranchFilter($query, CustomerComplaint::class);
        $complaint = $query->findOrFail($id);

        try {
            DB::beginTransaction();

            if ($complaint->attachment_path) {
                Storage::disk('public')->delete($complaint->attachment_path);
            }

            $complaint->delete();

            DB::commit();

            return redirect()->route('customer-complaints.index')->with('success', 'Customer complaint deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting customer complaint: ' . $e->getMessage());
        }
    }
}


