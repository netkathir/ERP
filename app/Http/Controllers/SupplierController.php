<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'view')) {
            abort(403, 'You do not have permission to view suppliers.');
        }

        $query = Supplier::query();
        $query = $this->applyBranchFilter($query, Supplier::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('state', 'like', "%{$search}%")
                  ->orWhere('supplier_type', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('audit_frequency', 'like', "%{$search}%")
                  ->orWhere('qms_status', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('gst', 'like', "%{$search}%")
                  // Search in dates
                  ->orWhereRaw("DATE_FORMAT(approved_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(approved_date, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(approved_date, '%Y-%m-%d') LIKE ?", ["%{$search}%"])
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
            case 'supplier_name':
                $query->orderBy('suppliers.supplier_name', $sortOrder);
                break;
            case 'code':
                $query->orderBy('suppliers.code', $sortOrder);
                break;
            case 'supplier_type':
                $query->orderBy('suppliers.supplier_type', $sortOrder);
                break;
            case 'city':
                $query->orderBy('suppliers.city', $sortOrder);
                break;
            case 'state':
                $query->orderBy('suppliers.state', $sortOrder);
                break;
            default:
                $query->orderBy('suppliers.id', $sortOrder);
                break;
        }

        $suppliers = $query->paginate(15)->withQueryString();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'create')) {
            abort(403, 'You do not have permission to create suppliers.');
        }

        $typeOfControlOptions = [
            'Selection and Evaluation of Supplier',
            'Subsequent Inspection of work at our end',
            'Test Certificate from Supplier',
            'Providing our formats to collect necessary information of process control',
            'supplying material to supplier',
            'System audit',
        ];

        $supplierTypeOptions = ['Supplier', 'Sub Contractor'];

        $auditFrequencyOptions = [
            'Qtry-3 months',
            'Half-6 months',
            'Annual- 12 months',
        ];

        $codeOptions = ['Trader', 'Manufracturer', 'Dealer', 'calibration'];

        $qmsStatusOptions = ['Yes', 'No'];

        // Indian states list
        $states = $this->getIndianStates();

        return view('suppliers.create', compact(
            'typeOfControlOptions',
            'supplierTypeOptions',
            'auditFrequencyOptions',
            'codeOptions',
            'qmsStatusOptions',
            'states'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'create')) {
            abort(403, 'You do not have permission to create suppliers.');
        }

        $validated = $request->validate([
            'nature' => 'nullable|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'gst' => 'nullable|string|max:50',
            'tan' => 'nullable|string|max:50',
            'pan' => 'nullable|string|max:50',
            'nature_of_work' => 'nullable|string',
            'type_of_control' => 'nullable|string|max:255',
            'customer_approved' => 'nullable|string|max:255',
            'supplier_iso_certified' => 'nullable|string|max:255',
            'audit_frequency' => 'nullable|string|max:255',
            'revaluation_period' => 'nullable|date',
            'remarks' => 'nullable|string',
            'supplier_type' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'items' => 'nullable|string',
            'material_grade' => 'nullable|string|max:255',
            'applicable_requirements' => 'nullable|string',
            'certificate_validity' => 'nullable|date',
            'approved_date' => 'nullable|date',
            'supplier_development' => 'nullable|string',
            'qms_status' => 'nullable|string|max:10',
        ]);

        $validated['branch_id'] = $this->getActiveBranchId();
        $validated['created_by_id'] = $user->id;

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function show($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'view')) {
            abort(403, 'You do not have permission to view suppliers.');
        }

        $query = Supplier::query();
        $query = $this->applyBranchFilter($query, Supplier::class);
        $supplier = $query->findOrFail($id);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'edit')) {
            abort(403, 'You do not have permission to edit suppliers.');
        }

        $query = Supplier::query();
        $query = $this->applyBranchFilter($query, Supplier::class);
        $supplier = $query->findOrFail($id);

        $typeOfControlOptions = [
            'Selection and Evaluation of Supplier',
            'Subsequent Inspection of work at our end',
            'Test Certificate from Supplier',
            'Providing our formats to collect necessary information of process control',
            'supplying material to supplier',
            'System audit',
        ];

        $supplierTypeOptions = ['Supplier', 'Sub Contractor'];

        $auditFrequencyOptions = [
            'Qtry-3 months',
            'Half-6 months',
            'Annual- 12 months',
        ];

        $codeOptions = ['Trader', 'Manufracturer', 'Dealer', 'calibration'];

        $qmsStatusOptions = ['Yes', 'No'];

        // Indian states list
        $states = $this->getIndianStates();

        return view('suppliers.edit', compact(
            'supplier',
            'typeOfControlOptions',
            'supplierTypeOptions',
            'auditFrequencyOptions',
            'codeOptions',
            'qmsStatusOptions',
            'states'
        ));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'edit')) {
            abort(403, 'You do not have permission to edit suppliers.');
        }

        $query = Supplier::query();
        $query = $this->applyBranchFilter($query, Supplier::class);
        $supplier = $query->findOrFail($id);

        $validated = $request->validate([
            'nature' => 'nullable|string|max:255',
            'supplier_name' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'gst' => 'nullable|string|max:50',
            'tan' => 'nullable|string|max:50',
            'pan' => 'nullable|string|max:50',
            'nature_of_work' => 'nullable|string',
            'type_of_control' => 'nullable|string|max:255',
            'customer_approved' => 'nullable|string|max:255',
            'supplier_iso_certified' => 'nullable|string|max:255',
            'audit_frequency' => 'nullable|string|max:255',
            'revaluation_period' => 'nullable|date',
            'remarks' => 'nullable|string',
            'supplier_type' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'items' => 'nullable|string',
            'material_grade' => 'nullable|string|max:255',
            'applicable_requirements' => 'nullable|string',
            'certificate_validity' => 'nullable|date',
            'approved_date' => 'nullable|date',
            'supplier_development' => 'nullable|string',
            'qms_status' => 'nullable|string|max:10',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'delete')) {
            abort(403, 'You do not have permission to delete suppliers.');
        }

        $query = Supplier::query();
        $query = $this->applyBranchFilter($query, Supplier::class);
        $supplier = $query->findOrFail($id);

        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }

    /**
     * Get Indian states list
     */
    private function getIndianStates()
    {
        return [
            'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
            'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand',
            'Karnataka', 'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur',
            'Meghalaya', 'Mizoram', 'Nagaland', 'Odisha', 'Punjab',
            'Rajasthan', 'Sikkim', 'Tamil Nadu', 'Telangana', 'Tripura',
            'Uttar Pradesh', 'Uttarakhand', 'West Bengal',
            'Andaman and Nicobar Islands', 'Chandigarh', 'Dadra and Nagar Haveli and Daman and Diu',
            'Delhi', 'Jammu and Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry'
        ];
    }
}


