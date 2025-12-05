<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = \App\Models\Quotation::with('customer');
        $query = $this->applyBranchFilter($query, \App\Models\Quotation::class);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('quotation_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('company_name', 'like', "%{$search}%")
                                    ->orWhere('contact_name', 'like', "%{$search}%");
                  })
                  // Search in dates
                  ->orWhereRaw("DATE_FORMAT(quotation_date, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(quotation_date, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(quotation_date, '%Y-%m-%d') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        // Sorting functionality
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        if (!in_array($sortOrder, ['asc', 'desc'])) $sortOrder = 'desc';
        switch ($sortBy) {
            case 'quotation_no': $query->orderBy('quotations.quotation_no', $sortOrder); break;
            case 'quotation_date': $query->orderBy('quotations.quotation_date', $sortOrder); break;
            case 'customer_name':
                $query->leftJoin('customers', 'quotations.customer_id', '=', 'customers.id')
                      ->orderBy('customers.company_name', $sortOrder)
                      ->select('quotations.*')
                      ->distinct();
                break;
            default: $query->orderBy('quotations.id', $sortOrder); break;
        }
        $quotations = $query->paginate(15)->withQueryString();
        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        // Filter customers and products by active branch
        $customerQuery = \App\Models\Customer::query();
        $customerQuery = $this->applyBranchFilter($customerQuery, \App\Models\Customer::class);
        $customers = $customerQuery->get();
        
        $productQuery = \App\Models\Product::query();
        $productQuery = $this->applyBranchFilter($productQuery, \App\Models\Product::class);
        $products = $productQuery->get();
        
        // Generate Quotation Number
        $lastQuotation = \App\Models\Quotation::latest()->first();
        $nextId = $lastQuotation ? $lastQuotation->id + 1 : 1;
        $quotationNo = 'ENQ' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        // Get company information for the branch (if user has selected a branch)
        $companyInfo = null;
        $user = auth()->user();
        if ($user->isBranchUser() && $user->branches->count() > 0) {
            $selectedBranchId = session('active_branch_id') ?? $user->branches->first()->id;
            $companyInfo = \App\Models\CompanyInformation::where('branch_id', $selectedBranchId)->first();
        } elseif ($user->isSuperAdmin()) {
            // Super Admin - get first available company info or allow selection
            $companyInfo = \App\Models\CompanyInformation::first();
        }

        return view('quotations.create', compact('customers', 'products', 'quotationNo', 'companyInfo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quotation_no' => 'required|unique:quotations,quotation_no',
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
            'products.*.price' => 'required|numeric|min:0',
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
            return back()->withErrors(['overall_discount_percent' => 'You cannot apply both Item-wise Discount and Overall Discount. Please select only one type of discount.'])->withInput();
        }

        try {
            \DB::beginTransaction();

            $quotation = \App\Models\Quotation::create([
                'quotation_no' => $request->quotation_no,
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'billing_address_line_1' => $request->billing_address_line_1,
                'billing_address_line_2' => $request->billing_address_line_2,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_pincode' => $request->billing_pincode,
                'gst_type' => $request->gst_type,
                'overall_discount_percent' => $request->overall_discount_percent ?? 0,
                'gst_percent' => $request->gst_percent ?? 0,
                'freight_charges' => $request->freight_charges ?? 0,
                'total_amount' => $request->total_amount,
                'net_amount' => $request->net_amount,
                'status' => 'draft',
                'branch_id' => $this->getActiveBranchId(),
            ]);

            foreach ($request->products as $item) {
                \App\Models\QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'total' => $item['total'],
                ]);
            }

            \DB::commit();
            return redirect()->route('quotations.index')->with('success', 'Quotation created successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Error creating quotation: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $query = \App\Models\Quotation::with(['customer', 'items.product', 'items.unit']);
        $query = $this->applyBranchFilter($query, \App\Models\Quotation::class);
        $quotation = $query->findOrFail($id);
        
        // Get company information for the branch (if user has selected a branch)
        $companyInfo = null;
        $user = auth()->user();
        if ($user->isBranchUser() && $user->branches->count() > 0) {
            $selectedBranchId = session('active_branch_id') ?? $user->branches->first()->id;
            $companyInfo = \App\Models\CompanyInformation::where('branch_id', $selectedBranchId)->first();
        } elseif ($user->isSuperAdmin()) {
            // Super Admin - get first available company info or allow selection
            $companyInfo = \App\Models\CompanyInformation::first();
        }
        
        return view('quotations.show', compact('quotation', 'companyInfo'));
    }

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

    public function edit($id)
    {
        $query = \App\Models\Quotation::with(['customer', 'items.product', 'items.unit']);
        $query = $this->applyBranchFilter($query, \App\Models\Quotation::class);
        $quotation = $query->findOrFail($id);
        
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

        return view('quotations.edit', compact('quotation', 'customers', 'products', 'companyInfo'));
    }

    public function update(Request $request, $id)
    {
        $query = \App\Models\Quotation::query();
        $query = $this->applyBranchFilter($query, \App\Models\Quotation::class);
        $quotation = $query->findOrFail($id);
        
        $request->validate([
            'quotation_no' => 'required|unique:quotations,quotation_no,' . $id,
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
            'products.*.price' => 'required|numeric|min:0',
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
            return back()->withErrors(['overall_discount_percent' => 'You cannot apply both Item-wise Discount and Overall Discount. Please select only one type of discount.'])->withInput();
        }

        try {
            \DB::beginTransaction();

            $quotation->update([
                'quotation_no' => $request->quotation_no,
                'date' => $request->date,
                'customer_id' => $request->customer_id,
                'billing_address_line_1' => $request->billing_address_line_1,
                'billing_address_line_2' => $request->billing_address_line_2,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_pincode' => $request->billing_pincode,
                'gst_type' => $request->gst_type,
                'overall_discount_percent' => $request->overall_discount_percent ?? 0,
                'gst_percent' => $request->gst_percent ?? 0,
                'freight_charges' => $request->freight_charges ?? 0,
                'total_amount' => $request->total_amount,
                'net_amount' => $request->net_amount,
            ]);

            // Delete existing items
            $quotation->items()->delete();

            // Create new items
            foreach ($request->products as $item) {
                \App\Models\QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'total' => $item['total'],
                ]);
            }

            \DB::commit();
            return redirect()->route('quotations.index')->with('success', 'Quotation updated successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Error updating quotation: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $query = \App\Models\Quotation::query();
        $query = $this->applyBranchFilter($query, \App\Models\Quotation::class);
        $quotation = $query->findOrFail($id);

        try {
            \DB::beginTransaction();

            // Delete related items first
            $quotation->items()->delete();

            // Delete the quotation
            $quotation->delete();

            \DB::commit();
            return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully.');

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->route('quotations.index')->with('error', 'Error deleting quotation: ' . $e->getMessage());
        }
    }

    public function getProductDetails($id)
    {
        $query = \App\Models\Product::with('unit');
        $query = $this->applyBranchFilter($query, \App\Models\Product::class);
        $product = $query->findOrFail($id);
        return response()->json($product);
    }

    /**
     * Generate PDF for quotation
     */
    public function pdf($id)
    {
        $query = \App\Models\Quotation::with(['customer', 'items.product', 'items.unit']);
        $query = $this->applyBranchFilter($query, \App\Models\Quotation::class);
        $quotation = $query->findOrFail($id);
        
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
            return redirect()->route('quotations.show', $id)
                ->with('error', 'Company information not found. Please configure company information first.');
        }

        // Calculate CGST, SGST, IGST based on GST type and gst_percent
        $cgstRate = 0;
        $sgstRate = 0;
        $igstRate = 0;
        $cgstAmount = 0;
        $sgstAmount = 0;
        $igstAmount = 0;

        // Get GST percent from quotation (default to 0 if not set)
        $gstPercent = $quotation->gst_percent ?? 0;
        
        // Calculate gross amount (sum of all item totals before any discount)
        $grossAmount = 0;
        foreach ($quotation->items as $item) {
            $grossAmount += ($item->price * $item->quantity);
        }
        
        // Calculate discount amount
        $discountAmount = 0;
        if ($quotation->overall_discount_percent > 0) {
            $discountAmount = ($grossAmount * $quotation->overall_discount_percent) / 100;
        }
        
        // Subtotal after discount
        $subtotalAfterDiscount = $grossAmount - $discountAmount;
        
        // Calculate GST on subtotal
        $gstAmount = ($subtotalAfterDiscount * $gstPercent) / 100;
        
        // Calculate freight tax
        $freight = $quotation->freight_charges ?? 0;
        $freightTax = ($freight * $gstPercent) / 100;
        
        // Total tax (GST + freight tax)
        $totalTax = $gstAmount + $freightTax;

        if ($quotation->gst_type == 'intra') {
            // Intra-state: Split GST rate equally between CGST and SGST
            $cgstRate = $sgstRate = $gstPercent / 2;
            $cgstAmount = $sgstAmount = $totalTax / 2;
            $igstRate = 0;
            $igstAmount = 0;
        } else {
            // Inter-state: Full IGST
            $igstRate = $gstPercent;
            $igstAmount = $totalTax;
            $cgstRate = $sgstRate = 0;
            $cgstAmount = $sgstAmount = 0;
        }

        // Generate filename: Quotation number + date + time
        $dateTime = now()->format('YmdHis');
        $filename = $quotation->quotation_no . '_' . $dateTime . '.pdf';

        // Number to words helper
        $amountInWords = $this->numberToWords($quotation->net_amount);

        $pdf = PDF::loadView('quotations.pdf', compact('quotation', 'companyInfo', 'cgstRate', 'sgstRate', 'igstRate', 'cgstAmount', 'sgstAmount', 'igstAmount', 'amountInWords'));
        
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
