<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = \App\Models\Customer::query();
        $query = $this->applyBranchFilter($query, \App\Models\Customer::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('contact_name', 'like', "%{$search}%")
                  ->orWhere('gst_no', 'like', "%{$search}%")
                  ->orWhere('billing_city', 'like', "%{$search}%")
                  ->orWhere('billing_state', 'like', "%{$search}%")
                  ->orWhere('billing_pincode', 'like', "%{$search}%")
                  ->orWhere('contact_info', 'like', "%{$search}%")
                  // Search in dates
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        // Sorting functionality
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';
        switch ($sortBy) {
            case 'company_name': $query->orderBy('customers.company_name', $sortOrder); break;
            case 'contact_name': $query->orderBy('customers.contact_name', $sortOrder); break;
            case 'gst_no': $query->orderBy('customers.gst_no', $sortOrder); break;
            case 'billing_city': $query->orderBy('customers.billing_city', $sortOrder); break;
            default: $query->orderBy('customers.id', $sortOrder); break;
        }
        $customers = $query->paginate(15)->withQueryString();
        return view('masters.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('masters.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'gst_no' => 'nullable|string|max:20',
            'billing_address_line_1' => 'required|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_pincode' => 'nullable|string|max:10|regex:/^[0-9]{6}$/',
            'shipping_address_line_1' => 'required|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_pincode' => 'nullable|string|max:10|regex:/^[0-9]{6}$/',
            'contact_info' => 'nullable|string',
        ], [
            'billing_pincode.regex' => 'The billing pincode must be 6 digits.',
            'shipping_pincode.regex' => 'The shipping pincode must be 6 digits.',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        $customer = \App\Models\Customer::create($data);

        // If the request expects JSON (e.g., from Tender form modal), return the created customer
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'company_name' => $customer->company_name,
                ],
            ]);
        }

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $query = \App\Models\Customer::query();
        $query = $this->applyBranchFilter($query, \App\Models\Customer::class);
        $customer = $query->findOrFail($id);
        return view('masters.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'gst_no' => 'nullable|string|max:20',
            'billing_address_line_1' => 'required|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'required|string|max:100',
            'billing_pincode' => 'nullable|string|max:10|regex:/^[0-9]{6}$/',
            'shipping_address_line_1' => 'required|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_state' => 'required|string|max:100',
            'shipping_pincode' => 'nullable|string|max:10|regex:/^[0-9]{6}$/',
            'contact_info' => 'nullable|string',
        ], [
            'billing_pincode.regex' => 'The billing pincode must be 6 digits.',
            'shipping_pincode.regex' => 'The shipping pincode must be 6 digits.',
        ]);

        $query = \App\Models\Customer::query();
        $query = $this->applyBranchFilter($query, \App\Models\Customer::class);
        $customer = $query->findOrFail($id);
        $customer->update($request->all());

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        $query = \App\Models\Customer::query();
        $query = $this->applyBranchFilter($query, \App\Models\Customer::class);
        $customer = $query->findOrFail($id);

        // Check if customer has related quotations
        $quotationsCount = \App\Models\Quotation::where('customer_id', $customer->id)->count();
        if ($quotationsCount > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete customer. This customer has ' . $quotationsCount . ' quotation(s) associated with it.');
        }

        // Check if customer has related proforma invoices
        $proformaInvoicesCount = \App\Models\ProformaInvoice::where('customer_id', $customer->id)->count();
        if ($proformaInvoicesCount > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete customer. This customer has ' . $proformaInvoicesCount . ' proforma invoice(s) associated with it.');
        }

        // Check if customer is used in tenders
        $tendersCount = \App\Models\Tender::where('company_id', $customer->id)->count();
        if ($tendersCount > 0) {
            return redirect()->route('customers.index')
                ->with('error', 'Cannot delete customer. This customer has ' . $tendersCount . ' tender(s) associated with it.');
        }

        try {
            $customer->delete();
            return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('customers.index')
                ->with('error', 'Error deleting customer: ' . $e->getMessage());
        }
    }
}
