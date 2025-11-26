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
    public function index()
    {
        $query = \App\Models\Customer::query();
        $query = $this->applyBranchFilter($query, \App\Models\Customer::class);
        $customers = $query->latest()->paginate(15);
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
            'billing_address_line_1' => 'nullable|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_pincode' => 'nullable|string|max:10|regex:/^[0-9]{6}$/',
            'shipping_address_line_1' => 'nullable|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_pincode' => 'nullable|string|max:10|regex:/^[0-9]{6}$/',
            'contact_info' => 'nullable|string',
        ], [
            'billing_pincode.regex' => 'The billing pincode must be 6 digits.',
            'shipping_pincode.regex' => 'The shipping pincode must be 6 digits.',
        ]);

        $data = $request->all();
        $data['branch_id'] = $this->getActiveBranchId();
        \App\Models\Customer::create($data);

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
            'billing_address_line_1' => 'nullable|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_pincode' => 'nullable|string|max:10|regex:/^[0-9]{6}$/',
            'shipping_address_line_1' => 'nullable|string|max:255',
            'shipping_address_line_2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
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
