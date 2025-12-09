<?php

namespace App\Http\Controllers;

use App\Models\BillingAddress;
use Illuminate\Http\Request;

class BillingAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('billing-addresses', 'view')) {
            abort(403, 'You do not have permission to view billing addresses.');
        }

        $query = BillingAddress::query();
        $query = $this->applyBranchFilter($query, BillingAddress::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('address_line_1', 'like', "%{$search}%")
                  ->orWhere('pincode', 'like', "%{$search}%");
            });
        }

        // Filter by city
        if ($request->filled('filter_city')) {
            $query->where('city', $request->get('filter_city'));
        }

        // Filter by state
        if ($request->filled('filter_state')) {
            $query->where('state', $request->get('filter_state'));
        }

        // Filter by company name
        if ($request->filled('filter_company')) {
            $query->where('company_name', 'like', "%{$request->get('filter_company')}%");
        }

        // Sorting
        $sortColumn = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort column to prevent SQL injection
        $allowedColumns = ['id', 'company_name', 'address_line_1', 'city', 'state', 'pincode', 'email', 'contact_no', 'gst_no', 'created_at'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }
        
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }
        
        $query->orderBy($sortColumn, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $perPageOptions = [10, 15, 20, 50, 100];
        if (!in_array($perPage, $perPageOptions)) {
            $perPage = 15;
        }

        $billingAddresses = $query->paginate($perPage)->withQueryString();

        // Get unique cities and states for filter dropdowns
        $cities = BillingAddress::select('city')->distinct()->orderBy('city')->pluck('city');
        $states = $this->getIndianStates();

        return view('masters.billing-addresses.index', compact('billingAddresses', 'cities', 'states'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('billing-addresses', 'create')) {
            abort(403, 'You do not have permission to create billing addresses.');
        }

        $states = $this->getIndianStates();
        return view('masters.billing-addresses.create', compact('states'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('billing-addresses', 'create')) {
            abort(403, 'You do not have permission to create billing addresses.');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'address_line_1' => 'required|string',
            'address_line_2' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10|regex:/^[0-9]{6}$/',
            'email' => 'required|email|max:255',
            'contact_no' => 'required|string|max:20|regex:/^[0-9]{10}$/',
            'gst_no' => 'required|string|max:50|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
        ], [
            'company_name.required' => 'Company Name is required.',
            'address_line_1.required' => 'Address Line 1 is required.',
            'address_line_2.required' => 'Address Line 2 is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'pincode.required' => 'Pincode is required.',
            'pincode.regex' => 'Pincode must be 6 digits.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'contact_no.required' => 'Contact No is required.',
            'contact_no.regex' => 'Contact No must be 10 digits.',
            'gst_no.required' => 'GST No is required.',
            'gst_no.regex' => 'GST No must be in valid format (e.g., 27AAAAA0000A1Z5).',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        BillingAddress::create($data);

        return redirect()->route('billing-addresses.index')->with('success', 'Billing Address created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('billing-addresses', 'view')) {
            abort(403, 'You do not have permission to view billing addresses.');
        }

        $query = BillingAddress::query();
        $query = $this->applyBranchFilter($query, BillingAddress::class);
        $billingAddress = $query->findOrFail($id);

        return view('masters.billing-addresses.show', compact('billingAddress'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('billing-addresses', 'edit')) {
            abort(403, 'You do not have permission to edit billing addresses.');
        }

        $query = BillingAddress::query();
        $query = $this->applyBranchFilter($query, BillingAddress::class);
        $billingAddress = $query->findOrFail($id);

        $states = $this->getIndianStates();
        return view('masters.billing-addresses.edit', compact('billingAddress', 'states'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('billing-addresses', 'edit')) {
            abort(403, 'You do not have permission to edit billing addresses.');
        }

        $query = BillingAddress::query();
        $query = $this->applyBranchFilter($query, BillingAddress::class);
        $billingAddress = $query->findOrFail($id);

        $request->validate([
            'company_name' => 'required|string|max:255',
            'address_line_1' => 'required|string',
            'address_line_2' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10|regex:/^[0-9]{6}$/',
            'email' => 'required|email|max:255',
            'contact_no' => 'required|string|max:20|regex:/^[0-9]{10}$/',
            'gst_no' => 'required|string|max:50|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
        ], [
            'company_name.required' => 'Company Name is required.',
            'address_line_1.required' => 'Address Line 1 is required.',
            'address_line_2.required' => 'Address Line 2 is required.',
            'city.required' => 'City is required.',
            'state.required' => 'State is required.',
            'pincode.required' => 'Pincode is required.',
            'pincode.regex' => 'Pincode must be 6 digits.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'contact_no.required' => 'Contact No is required.',
            'contact_no.regex' => 'Contact No must be 10 digits.',
            'gst_no.required' => 'GST No is required.',
            'gst_no.regex' => 'GST No must be in valid format (e.g., 27AAAAA0000A1Z5).',
        ]);

        $billingAddress->update($request->all());

        return redirect()->route('billing-addresses.index')->with('success', 'Billing Address updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('billing-addresses', 'delete')) {
            abort(403, 'You do not have permission to delete billing addresses.');
        }

        $query = BillingAddress::query();
        $query = $this->applyBranchFilter($query, BillingAddress::class);
        $billingAddress = $query->findOrFail($id);
        $billingAddress->delete();

        return redirect()->route('billing-addresses.index')->with('success', 'Billing Address deleted successfully.');
    }

    /**
     * Bulk delete billing addresses
     */
    public function bulkDelete(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('billing-addresses', 'delete')) {
            abort(403, 'You do not have permission to delete billing addresses.');
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:billing_addresses,id',
        ]);

        $query = BillingAddress::query();
        $query = $this->applyBranchFilter($query, BillingAddress::class);
        $billingAddresses = $query->whereIn('id', $request->ids)->get();

        foreach ($billingAddresses as $billingAddress) {
            $billingAddress->delete();
        }

        return redirect()->route('billing-addresses.index')->with('success', count($request->ids) . ' billing address(es) deleted successfully.');
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
