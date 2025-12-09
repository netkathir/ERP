<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ProformaInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = \App\Models\ProformaInvoice::with('customer');
        $query = $this->applyBranchFilter($query, \App\Models\ProformaInvoice::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('company_name', 'like', "%{$search}%")
                                    ->orWhere('contact_name', 'like', "%{$search}%");
                  })
                  // Search in dates
                  ->orWhereRaw("DATE_FORMAT(invoice_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(invoice_date, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(invoice_date, '%Y-%m-%d') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        $invoices = $query->latest()->paginate(15)->withQueryString();
        return view('proforma-invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Filter customers and products by active branch
        $customerQuery = \App\Models\Customer::query();
        $customerQuery = $this->applyBranchFilter($customerQuery, \App\Models\Customer::class);
        $customers = $customerQuery->get();
        
        $productQuery = \App\Models\Product::query();
        $productQuery = $this->applyBranchFilter($productQuery, \App\Models\Product::class);
        $products = $productQuery->get();
        
        // Generate Invoice Number
        $lastInvoice = \App\Models\ProformaInvoice::latest()->first();
        $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
        $invoiceNo = 'INV' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        // Get company information for the branch
        $companyInfo = null;
        $user = auth()->user();
        if ($user->isBranchUser() && $user->branches->count() > 0) {
            $selectedBranchId = session('active_branch_id') ?? $user->branches->first()->id;
            $companyInfo = \App\Models\CompanyInformation::where('branch_id', $selectedBranchId)->first();
        } elseif ($user->isSuperAdmin()) {
            $companyInfo = \App\Models\CompanyInformation::first();
        }

        return view('proforma-invoices.create', compact('customers', 'products', 'invoiceNo', 'companyInfo'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_no' => 'required|unique:proforma_invoices,invoice_no',
            'date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'gst_type' => 'required|in:intra,inter',
            'gst_percent' => 'nullable|numeric|min:0|max:100',
            'billing_address_line_1' => 'nullable|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_pincode' => 'nullable|string|max:10|regex:/^[0-9]{6}$/',
            'overall_discount_percent' => 'nullable|numeric|min:0|max:100',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ], [
            'billing_pincode.regex' => 'The billing pincode must be 6 digits.',
        ]);

        // Validate mutual exclusivity of discounts
        $overallDiscount = $request->overall_discount_percent ?? 0;
        $hasItemDiscount = false;
        if (isset($request->products)) {
            foreach ($request->products as $item) {
                if (isset($item['discount_percent']) && $item['discount_percent'] > 0) {
                    $hasItemDiscount = true;
                    break;
                }
            }
        }

        if ($overallDiscount > 0 && $hasItemDiscount) {
            return back()->withErrors(['overall_discount_percent' => 'You can apply either Item-wise Discount OR Overall Discount, not both.'])->withInput();
        }

        try {
            DB::beginTransaction();

            $invoice = \App\Models\ProformaInvoice::create([
                'invoice_no' => $request->invoice_no,
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'billing_address_line_1' => $request->billing_address_line_1,
                'billing_address_line_2' => $request->billing_address_line_2,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_pincode' => $request->billing_pincode,
                'gst_type' => $request->gst_type,
                'gst_percent' => $request->gst_percent ?? 0,
                'overall_discount_percent' => $request->overall_discount_percent ?? 0,
                'freight_charges' => $request->freight_charges ?? 0,
                'gross_amount' => $request->gross_amount,
                'discount_amount' => $request->discount_amount ?? 0,
                'cgst_amount' => $request->cgst_amount ?? 0,
                'sgst_amount' => $request->sgst_amount ?? 0,
                'igst_amount' => $request->igst_amount ?? 0,
                'total_tax' => $request->total_tax ?? 0,
                'net_amount' => $request->net_amount,
                'status' => 'draft',
                'branch_id' => $this->getActiveBranchId(),
            ]);

            foreach ($request->products as $item) {
                \App\Models\ProformaInvoiceItem::create([
                    'proforma_invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'line_base_amount' => $item['line_base_amount'] ?? 0,
                    'item_discount_amount' => $item['item_discount_amount'] ?? 0,
                    'line_total' => $item['line_total'] ?? 0,
                ]);
            }

            DB::commit();
            return redirect()->route('proforma-invoices.index')->with('success', 'Proforma Invoice created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating proforma invoice: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query = \App\Models\ProformaInvoice::with(['customer', 'items.product', 'items.unit']);
        $query = $this->applyBranchFilter($query, \App\Models\ProformaInvoice::class);
        $invoice = $query->findOrFail($id);
        
        // Get company information for the branch
        $companyInfo = null;
        $user = auth()->user();
        if ($user->isBranchUser() && $user->branches->count() > 0) {
            $selectedBranchId = session('active_branch_id') ?? $user->branches->first()->id;
            $companyInfo = \App\Models\CompanyInformation::where('branch_id', $selectedBranchId)->first();
        } elseif ($user->isSuperAdmin()) {
            $companyInfo = \App\Models\CompanyInformation::first();
        }
        
        return view('proforma-invoices.show', compact('invoice', 'companyInfo'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $query = \App\Models\ProformaInvoice::with(['customer', 'items.product', 'items.unit']);
        $query = $this->applyBranchFilter($query, \App\Models\ProformaInvoice::class);
        $invoice = $query->findOrFail($id);
        
        // Filter customers and products by active branch
        $customerQuery = \App\Models\Customer::query();
        $customerQuery = $this->applyBranchFilter($customerQuery, \App\Models\Customer::class);
        $customers = $customerQuery->get();
        
        $productQuery = \App\Models\Product::query();
        $productQuery = $this->applyBranchFilter($productQuery, \App\Models\Product::class);
        $products = $productQuery->get();
        
        // Get company information for the branch
        $companyInfo = null;
        $user = auth()->user();
        if ($user->isBranchUser() && $user->branches->count() > 0) {
            $selectedBranchId = session('active_branch_id') ?? $user->branches->first()->id;
            $companyInfo = \App\Models\CompanyInformation::where('branch_id', $selectedBranchId)->first();
        } elseif ($user->isSuperAdmin()) {
            $companyInfo = \App\Models\CompanyInformation::first();
        }

        return view('proforma-invoices.edit', compact('invoice', 'customers', 'products', 'companyInfo'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $query = \App\Models\ProformaInvoice::query();
        $query = $this->applyBranchFilter($query, \App\Models\ProformaInvoice::class);
        $invoice = $query->findOrFail($id);
        
        $request->validate([
            'invoice_no' => 'required|unique:proforma_invoices,invoice_no,' . $id,
            'date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'gst_type' => 'required|in:intra,inter',
            'billing_address_line_1' => 'nullable|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_pincode' => 'nullable|string|max:10|regex:/^[0-9]{6}$/',
            'overall_discount_percent' => 'nullable|numeric|min:0|max:100',
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ], [
            'billing_pincode.regex' => 'The billing pincode must be 6 digits.',
        ]);

        // Validate mutual exclusivity of discounts
        $overallDiscount = $request->overall_discount_percent ?? 0;
        $hasItemDiscount = false;
        if (isset($request->products)) {
            foreach ($request->products as $item) {
                if (isset($item['discount_percent']) && $item['discount_percent'] > 0) {
                    $hasItemDiscount = true;
                    break;
                }
            }
        }

        if ($overallDiscount > 0 && $hasItemDiscount) {
            return back()->withErrors(['overall_discount_percent' => 'You can apply either Item-wise Discount OR Overall Discount, not both.'])->withInput();
        }

        try {
            DB::beginTransaction();

            $invoice->update([
                'invoice_no' => $request->invoice_no,
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'billing_address_line_1' => $request->billing_address_line_1,
                'billing_address_line_2' => $request->billing_address_line_2,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_pincode' => $request->billing_pincode,
                'gst_type' => $request->gst_type,
                'gst_percent' => $request->gst_percent ?? 0,
                'overall_discount_percent' => $request->overall_discount_percent ?? 0,
                'freight_charges' => $request->freight_charges ?? 0,
                'gross_amount' => $request->gross_amount,
                'discount_amount' => $request->discount_amount ?? 0,
                'cgst_amount' => $request->cgst_amount ?? 0,
                'sgst_amount' => $request->sgst_amount ?? 0,
                'igst_amount' => $request->igst_amount ?? 0,
                'total_tax' => $request->total_tax ?? 0,
                'net_amount' => $request->net_amount,
            ]);

            // Delete existing items
            $invoice->items()->delete();

            // Create new items
            foreach ($request->products as $item) {
                \App\Models\ProformaInvoiceItem::create([
                    'proforma_invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'line_base_amount' => $item['line_base_amount'] ?? 0,
                    'item_discount_amount' => $item['item_discount_amount'] ?? 0,
                    'line_total' => $item['line_total'] ?? 0,
                ]);
            }

            DB::commit();
            return redirect()->route('proforma-invoices.show', $invoice->id)->with('success', 'Proforma Invoice updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating proforma invoice: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $query = \App\Models\ProformaInvoice::query();
        $query = $this->applyBranchFilter($query, \App\Models\ProformaInvoice::class);
        $invoice = $query->findOrFail($id);

        try {
            DB::beginTransaction();

            // Delete related items first
            $invoice->items()->delete();

            // Delete the invoice
        $invoice->delete();

            DB::commit();
        return redirect()->route('proforma-invoices.index')->with('success', 'Proforma Invoice deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('proforma-invoices.index')->with('error', 'Error deleting proforma invoice: ' . $e->getMessage());
        }
    }

    /**
     * Get customer details for AJAX
     */
    public function getCustomerDetails($id)
    {
        $query = \App\Models\Customer::query();
        $query = $this->applyBranchFilter($query, \App\Models\Customer::class);
        $customer = $query->findOrFail($id);
        return response()->json([
            'billing_address_line_1' => $customer->billing_address_line_1,
            'billing_address_line_2' => $customer->billing_address_line_2,
            'billing_city' => $customer->billing_city,
            'billing_state' => $customer->billing_state,
            'billing_pincode' => $customer->billing_pincode,
            'gst_no' => $customer->gst_no,
        ]);
    }

    /**
     * Get product details for AJAX
     */
    public function getProductDetails($id)
    {
        $query = \App\Models\Product::with('unit');
        $query = $this->applyBranchFilter($query, \App\Models\Product::class);
        $product = $query->findOrFail($id);
        return response()->json($product);
    }

    /**
     * Generate PDF for proforma invoice
     */
    public function pdf($id)
    {
        $query = \App\Models\ProformaInvoice::with(['customer', 'items.product', 'items.unit']);
        $query = $this->applyBranchFilter($query, \App\Models\ProformaInvoice::class);
        $invoice = $query->findOrFail($id);
        
        // Get company information for the branch
        $companyInfo = null;
        $user = auth()->user();
        if ($user->isBranchUser() && $user->branches->count() > 0) {
            $selectedBranchId = session('active_branch_id') ?? $user->branches->first()->id;
            $companyInfo = \App\Models\CompanyInformation::where('branch_id', $selectedBranchId)->first();
        } elseif ($user->isSuperAdmin()) {
            $companyInfo = \App\Models\CompanyInformation::first();
        }

        if (!$companyInfo) {
            return redirect()->route('proforma-invoices.show', $id)
                ->with('error', 'Company information not found. Please configure company information first.');
        }

        // Calculate GST rates and amounts
        $cgstRate = 0;
        $sgstRate = 0;
        $igstRate = 0;
        
        // Calculate average GST rate from items
        $totalGstRate = 0;
        $itemCount = 0;
        foreach ($invoice->items as $item) {
            if ($item->product && $item->product->gst_rate) {
                $totalGstRate += $item->product->gst_rate;
                $itemCount++;
            }
        }
        $avgGstRate = $itemCount > 0 ? $totalGstRate / $itemCount : 18;

        if ($invoice->gst_type == 'intra') {
            $cgstRate = $sgstRate = $avgGstRate / 2;
        } else {
            $igstRate = $avgGstRate;
        }

        // Generate filename: Invoice number + date + time
        $dateTime = now()->format('YmdHis');
        $filename = $invoice->invoice_no . '_' . $dateTime . '.pdf';

        // Number to words helper
        $amountInWords = $this->numberToWords($invoice->net_amount);

        $pdf = PDF::loadView('proforma-invoices.pdf', compact('invoice', 'companyInfo', 'cgstRate', 'sgstRate', 'igstRate', 'amountInWords'));
        
        return $pdf->download($filename);
    }

    /**
     * Convert number to words
     */
    private function numberToWords($number)
    {
        $ones = array(
            0 => '', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four',
            5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
            10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen',
            14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen',
            18 => 'eighteen', 19 => 'nineteen'
        );
        
        $tens = array(
            2 => 'twenty', 3 => 'thirty', 4 => 'forty', 5 => 'fifty',
            6 => 'sixty', 7 => 'seventy', 8 => 'eighty', 9 => 'ninety'
        );
        
        $number = (int)$number;
        
        if ($number == 0) {
            return 'zero';
        }
        
        $result = '';
        
        // Handle thousands
        if ($number >= 1000) {
            $thousands = (int)($number / 1000);
            $result .= $this->convertHundreds($thousands, $ones, $tens) . ' thousand ';
            $number %= 1000;
        }
        
        // Handle hundreds
        if ($number >= 100) {
            $result .= $this->convertHundreds($number, $ones, $tens);
        } else {
            $result .= $this->convertTens($number, $ones, $tens);
        }
        
        return trim($result);
    }
    
    private function convertHundreds($number, $ones, $tens)
    {
        $result = '';
        $hundreds = (int)($number / 100);
        if ($hundreds > 0) {
            $result .= $ones[$hundreds] . ' hundred ';
        }
        $number %= 100;
        $result .= $this->convertTens($number, $ones, $tens);
        return $result;
    }
    
    private function convertTens($number, $ones, $tens)
    {
        if ($number < 20) {
            return $ones[$number];
        }
        $ten = (int)($number / 10);
        $one = $number % 10;
        return $tens[$ten] . ($one > 0 ? ' ' . $ones[$one] : '');
    }
}
