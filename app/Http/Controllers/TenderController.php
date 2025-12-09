<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tender;
use App\Models\TenderItem;
use App\Models\TenderTechnicalSpecification;
use App\Models\TenderFinancialTabulation;
use App\Models\TenderRemark;
use App\Models\Customer;
use App\Models\Unit;
use App\Models\ProductionDepartment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileUploadHelper;

class TenderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'view')) {
            abort(403, 'You do not have permission to view tenders.');
        }

        // Load related company, attended user, and items (for list view columns like Title)
        $query = Tender::with(['company', 'attendedBy', 'items']);
        $query = $this->applyBranchFilter($query, Tender::class);

        // Text search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('tender_no', 'like', "%{$search}%")
                  ->orWhere('customer_tender_no', 'like', "%{$search}%")
                  ->orWhereHas('items', function($itemsQuery) use ($search) {
                      $itemsQuery->where('title', 'like', "%{$search}%");
                  })
                  ->orWhereHas('company', function($companyQuery) use ($search) {
                      $companyQuery->where('company_name', 'like', "%{$search}%")
                                    ->orWhere('contact_name', 'like', "%{$search}%");
                  })
                  ->orWhere('tender_type', 'like', "%{$search}%")
                  ->orWhere('bidding_system', 'like', "%{$search}%")
                  ->orWhere('tender_status', 'like', "%{$search}%")
                  ->orWhere('technical_spec_rank', 'like', "%{$search}%")
                  ->orWhere('bid_result', 'like', "%{$search}%")
                  // Search in dates (string-based)
                  ->orWhereRaw("DATE_FORMAT(closing_date_time, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(closing_date_time, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(closing_date_time, '%Y-%m-%d') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(closing_date_time, '%d-%m-%Y %H:%i') LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"]);
            });
        }

        // Date range filter on Closing Date & Time
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $query->where(function ($q) use ($startDate, $endDate) {
                if ($startDate) {
                    // from start date 00:00
                    $q->whereDate('closing_date_time', '>=', $startDate);
                }
                if ($endDate) {
                    // up to end date 23:59:59
                    $q->whereDate('closing_date_time', '<=', $endDate);
                }
            });
        }

        // Sorting functionality
        $sortBy = $request->get('sort_by', 'id');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        switch ($sortBy) {
            case 'tender_no':
                $query->orderBy('tenders.tender_no', $sortOrder);
                break;
            case 'customer_tender_no':
                $query->orderBy('tenders.customer_tender_no', $sortOrder);
                break;
            case 'company_name':
                $query->leftJoin('customers', 'tenders.company_id', '=', 'customers.id')
                      ->orderBy('customers.company_name', $sortOrder)
                      ->select('tenders.*')
                      ->distinct();
                break;
            case 'closing_date':
                $query->orderBy('tenders.closing_date_time', $sortOrder);
                break;
            case 'tender_status':
                $query->orderBy('tenders.tender_status', $sortOrder);
                break;
            default:
                $query->orderBy('tenders.id', $sortOrder);
                break;
        }

        $tenders = $query->paginate(15)->withQueryString();
        return view('tenders.index', compact('tenders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'create')) {
            abort(403, 'You do not have permission to create tenders.');
        }
        // Filter customers by active branch
        $customerQuery = Customer::query();
        $customerQuery = $this->applyBranchFilter($customerQuery, Customer::class);
        $customers = $customerQuery->get();
        
        // Get units (shared across branches)
        $units = Unit::all();

        // Get production departments (by active branch)
        $prodDeptQuery = ProductionDepartment::query();
        $prodDeptQuery = $this->applyBranchFilter($prodDeptQuery, ProductionDepartment::class);
        $productionDepartments = $prodDeptQuery->get();
        
        // Generate Tender Number
        $lastTender = Tender::latest()->first();
        $nextId = $lastTender ? $lastTender->id + 1 : 1;
        $tenderNo = 'TND' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        
        // Get logged in user
        $user = auth()->user();

        return view('tenders.create', compact('customers', 'units', 'tenderNo', 'user', 'productionDepartments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'create')) {
            abort(403, 'You do not have permission to create tenders.');
        }

        $request->validate([
            'tender_no' => 'required|unique:tenders,tender_no',
            'customer_tender_no' => 'nullable|string|max:255',
            // Must match an existing production department name
            'production_dept' => 'nullable|exists:production_departments,name',
            'company_id' => 'nullable|exists:customers,id',
            'contact_person' => 'nullable|string|max:255',
            'billing_address_line_1' => 'nullable|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_country' => 'nullable|string|max:100',
            'billing_pincode' => 'nullable|string|max:20',
            'publishing_date' => 'nullable|date',
            'closing_date_time' => 'nullable|date',
            'bidding_type' => 'nullable|in:Online,Offline',
            'tender_type' => 'nullable|in:Open,Limited,Special Limited,Single',
            'contract_type' => 'nullable|in:Goods,Service',
            'bidding_system' => 'nullable|in:Single Packet,Two Packet',
            'procure_from_approved_source' => 'nullable|in:Yes,No',
            'validity_of_offer_days' => 'nullable|integer|min:0',
            'regular_developmental' => 'nullable|in:Regular,Developmental',
            'validity_of_offer_days' => 'nullable|integer|min:0',
            'regular_developmental' => 'nullable|in:Regular,Developmental',
            'tender_document_cost' => 'nullable|numeric|min:0',
            'emd' => 'nullable|numeric|min:0',
            'ra_enabled' => 'nullable|in:Yes,No',
            'ra_date_time' => 'nullable|date',
            'pre_bid_conference_required' => 'nullable|in:Yes,No',
            'pre_bid_conference_date' => 'nullable|date',
            'inspection_agency' => 'nullable|string|max:255',
            'tender_document_attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'financial_tabulation_attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'technical_spec_attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'delete_tender_document_attachment' => 'nullable|boolean',
            'delete_financial_tabulation_attachment' => 'nullable|boolean',
            'delete_technical_spec_attachment' => 'nullable|boolean',
            'technical_spec_rank' => 'nullable|string|max:255',
            'tender_status' => 'nullable|in:Bid not coated,Bid Coated',
            'bid_result' => 'nullable|in:Bid Awarded,Bid not Awarded',
            'items' => 'required|array|min:1',
            'items.*.title' => 'required|string|max:255',
            'items.*.qty' => 'nullable|numeric|min:0',
            'financial_tabulations' => 'nullable|array',
            'remarks' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Handle file uploads
            $tenderDocumentPath = null;
            $financialTabulationPath = null;
            $technicalSpecPath = null;

            if ($request->hasFile('tender_document_attachment')) {
                $tenderDocumentPath = FileUploadHelper::storeWithOriginalName(
                    $request->file('tender_document_attachment'),
                    'tender_documents'
                );
            }
            if ($request->hasFile('financial_tabulation_attachment')) {
                $financialTabulationPath = FileUploadHelper::storeWithOriginalName(
                    $request->file('financial_tabulation_attachment'),
                    'financial_tabulations'
                );
            }
            if ($request->hasFile('technical_spec_attachment')) {
                $technicalSpecPath = FileUploadHelper::storeWithOriginalName(
                    $request->file('technical_spec_attachment'),
                    'technical_specs'
                );
            }

            $tender = Tender::create([
                'tender_no' => $request->tender_no,
                'customer_tender_no' => $request->customer_tender_no,
                'attended_by' => auth()->id(),
                'production_dept' => $request->production_dept,
                'company_id' => $request->company_id,
                'contact_person' => $request->contact_person,
                'billing_address_line_1' => $request->billing_address_line_1,
                'billing_address_line_2' => $request->billing_address_line_2,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_country' => $request->billing_country,
                'billing_pincode' => $request->billing_pincode,
                'publishing_date' => $request->publishing_date,
                'closing_date_time' => $request->closing_date_time,
                'bidding_type' => $request->bidding_type,
                'tender_type' => $request->tender_type,
                'contract_type' => $request->contract_type,
                'bidding_system' => $request->bidding_system,
                'procure_from_approved_source' => $request->procure_from_approved_source,
                'validity_of_offer_days' => $request->validity_of_offer_days,
                'regular_developmental' => $request->regular_developmental ?: 'Regular',
                'tender_document_cost' => $request->tender_document_cost,
                'emd' => $request->emd,
                'ra_enabled' => $request->ra_enabled,
                'ra_date_time' => $request->ra_date_time,
                'pre_bid_conference_required' => $request->pre_bid_conference_required,
                'pre_bid_conference_date' => $request->pre_bid_conference_date,
                'inspection_agency' => $request->inspection_agency,
                'tender_document_attachment' => $tenderDocumentPath,
                'financial_tabulation_attachment' => $financialTabulationPath,
                'technical_spec_attachment' => $technicalSpecPath,
                'technical_spec_rank' => $request->technical_spec_rank,
                'tender_status' => $request->tender_status ?? 'Bid not coated',
                'bid_result' => $request->bid_result,
                'branch_id' => $this->getActiveBranchId(),
            ]);

            // Create tender items
            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    $item = TenderItem::create([
                        'tender_id' => $tender->id,
                        'pl_code' => $itemData['pl_code'] ?? null,
                        'title' => $itemData['title'],
                        'description' => $itemData['description'] ?? null,
                        'delivery_location' => $itemData['delivery_location'] ?? null,
                        'qty' => $itemData['qty'] ?? null,
                        'unit_id' => $itemData['unit_id'] ?? null,
                        'request_for_price' => $itemData['request_for_price'] ?? 'No',
                        'price_received' => $itemData['price_received'] ?? null,
                        'price_quoted' => $itemData['price_quoted'] ?? null,
                        'tender_status' => $itemData['tender_status'] ?? null,
                        'bid_result' => $itemData['bid_result'] ?? null,
                    ]);

                    // Create technical specifications for this item
                    if (isset($itemData['technical_specifications']) && is_array($itemData['technical_specifications'])) {
                        foreach ($itemData['technical_specifications'] as $specData) {
                            if (!empty($specData['specification'])) {
                                TenderTechnicalSpecification::create([
                                    'tender_item_id' => $item->id,
                                    'specification' => $specData['specification'],
                                    'rank' => $specData['rank'] ?? null,
                                ]);
                            }
                        }
                    }
                }
            }

            // Create financial tabulations
            if ($request->has('financial_tabulations') && is_array($request->financial_tabulations)) {
                foreach ($request->financial_tabulations as $tabData) {
                    if (!empty($tabData['pl_number'])) {
                        TenderFinancialTabulation::create([
                            'tender_id' => $tender->id,
                            'pl_number' => $tabData['pl_number'],
                            'bid_closed_date' => $tabData['bid_closed_date'] ?? null,
                        ]);
                    }
                }
            }

            // Create remarks
            if ($request->has('remarks') && is_array($request->remarks)) {
                foreach ($request->remarks as $remarkData) {
                    if (!empty($remarkData['remarks'])) {
                        $corrigendumPath = null;
                        if (isset($remarkData['corrigendum_file']) && $remarkData['corrigendum_file'] instanceof \Illuminate\Http\UploadedFile) {
                            $corrigendumPath = FileUploadHelper::storeWithOriginalName(
                                $remarkData['corrigendum_file'],
                                'tender_corrigendums'
                            );
                        }

                        TenderRemark::create([
                            'tender_id' => $tender->id,
                            'date' => $remarkData['date'] ?? now(),
                            'remarks' => $remarkData['remarks'],
                            'corrigendum_file' => $corrigendumPath,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('tenders.index')->with('success', 'Tender created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating tender: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'view')) {
            abort(403, 'You do not have permission to view tenders.');
        }

        $query = Tender::with(['company', 'attendedBy', 'items.unit', 'items.technicalSpecifications', 'financialTabulations', 'remarks']);
        $query = $this->applyBranchFilter($query, Tender::class);
        $tender = $query->findOrFail($id);
        
        return view('tenders.show', compact('tender'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'edit')) {
            abort(403, 'You do not have permission to edit tenders.');
        }
        $query = Tender::with(['items.unit', 'items.technicalSpecifications', 'financialTabulations', 'remarks']);
        $query = $this->applyBranchFilter($query, Tender::class);
        $tender = $query->findOrFail($id);
        
        // Filter customers by active branch
        $customerQuery = Customer::query();
        $customerQuery = $this->applyBranchFilter($customerQuery, Customer::class);
        $customers = $customerQuery->get();
        
        // Get units
        $units = Unit::all();

        // Get production departments (by active branch)
        $prodDeptQuery = ProductionDepartment::query();
        $prodDeptQuery = $this->applyBranchFilter($prodDeptQuery, ProductionDepartment::class);
        $productionDepartments = $prodDeptQuery->get();
        
        // Get logged in user
        $user = auth()->user();
        
        return view('tenders.edit', compact('tender', 'customers', 'units', 'user', 'productionDepartments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'edit')) {
            abort(403, 'You do not have permission to edit tenders.');
        }

        $query = Tender::query();
        $query = $this->applyBranchFilter($query, Tender::class);
        $tender = $query->findOrFail($id);
        
        $request->validate([
            'tender_no' => 'required|unique:tenders,tender_no,' . $id,
            'customer_tender_no' => 'nullable|string|max:255',
            // Must match an existing production department name
            'production_dept' => 'nullable|exists:production_departments,name',
            'company_id' => 'nullable|exists:customers,id',
            'contact_person' => 'nullable|string|max:255',
            'billing_address_line_1' => 'nullable|string|max:255',
            'billing_address_line_2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_country' => 'nullable|string|max:100',
            'billing_pincode' => 'nullable|string|max:20',
            'publishing_date' => 'nullable|date',
            'closing_date_time' => 'nullable|date',
            'bidding_type' => 'nullable|in:Online,Offline',
            'tender_type' => 'nullable|in:Open,Limited,Special Limited,Single',
            'contract_type' => 'nullable|in:Goods,Service',
            'bidding_system' => 'nullable|in:Single Packet,Two Packet',
            'procure_from_approved_source' => 'nullable|in:Yes,No',
            'tender_document_cost' => 'nullable|numeric|min:0',
            'emd' => 'nullable|numeric|min:0',
            'ra_enabled' => 'nullable|in:Yes,No',
            'ra_date_time' => 'nullable|date',
            'pre_bid_conference_required' => 'nullable|in:Yes,No',
            'pre_bid_conference_date' => 'nullable|date',
            'inspection_agency' => 'nullable|string|max:255',
            'tender_document_attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'financial_tabulation_attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'technical_spec_attachment' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'technical_spec_rank' => 'nullable|string|max:255',
            'tender_status' => 'nullable|in:Bid not coated,Bid Coated',
            'bid_result' => 'nullable|in:Bid Awarded,Bid not Awarded',
            'items' => 'required|array|min:1',
            'items.*.title' => 'required|string|max:255',
            'items.*.qty' => 'nullable|numeric|min:0',
            'financial_tabulations' => 'nullable|array',
            'remarks' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Handle file uploads and optional deletions
            $tenderDocumentPath = $tender->tender_document_attachment;
            $financialTabulationPath = $tender->financial_tabulation_attachment ?? null;
            $technicalSpecPath = $tender->technical_spec_attachment ?? null;

            // Explicit delete for Tender Document attachment
            if ($request->boolean('delete_tender_document_attachment')) {
                if ($tenderDocumentPath) {
                    Storage::disk('public')->delete($tenderDocumentPath);
                }
                $tenderDocumentPath = null;
            } elseif ($request->hasFile('tender_document_attachment')) {
                if ($tenderDocumentPath) {
                    Storage::disk('public')->delete($tenderDocumentPath);
                }
                $tenderDocumentPath = FileUploadHelper::storeWithOriginalName(
                    $request->file('tender_document_attachment'),
                    'tender_documents'
                );
            }

            // Explicit delete for Financial Tabulation attachment
            if ($request->boolean('delete_financial_tabulation_attachment')) {
                if ($financialTabulationPath) {
                    Storage::disk('public')->delete($financialTabulationPath);
                }
                $financialTabulationPath = null;
            } elseif ($request->hasFile('financial_tabulation_attachment')) {
                if ($financialTabulationPath) {
                    Storage::disk('public')->delete($financialTabulationPath);
                }
                $financialTabulationPath = FileUploadHelper::storeWithOriginalName(
                    $request->file('financial_tabulation_attachment'),
                    'financial_tabulations'
                );
            }

            // Explicit delete for Technical Specification attachment
            if ($request->boolean('delete_technical_spec_attachment')) {
                if ($technicalSpecPath) {
                    Storage::disk('public')->delete($technicalSpecPath);
                }
                $technicalSpecPath = null;
            } elseif ($request->hasFile('technical_spec_attachment')) {
                if ($technicalSpecPath) {
                    Storage::disk('public')->delete($technicalSpecPath);
                }
                $technicalSpecPath = FileUploadHelper::storeWithOriginalName(
                    $request->file('technical_spec_attachment'),
                    'technical_specs'
                );
            }

            $tender->update([
                'tender_no' => $request->tender_no,
                'customer_tender_no' => $request->customer_tender_no,
                'production_dept' => $request->production_dept,
                'company_id' => $request->company_id,
                'contact_person' => $request->contact_person,
                'billing_address_line_1' => $request->billing_address_line_1,
                'billing_address_line_2' => $request->billing_address_line_2,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_country' => $request->billing_country,
                'billing_pincode' => $request->billing_pincode,
                'publishing_date' => $request->publishing_date,
                'closing_date_time' => $request->closing_date_time,
                'bidding_type' => $request->bidding_type,
                'tender_type' => $request->tender_type,
                'contract_type' => $request->contract_type,
                'bidding_system' => $request->bidding_system,
                'procure_from_approved_source' => $request->procure_from_approved_source,
                'validity_of_offer_days' => $request->validity_of_offer_days,
                'regular_developmental' => $request->regular_developmental ?: 'Regular',
                'tender_document_cost' => $request->tender_document_cost,
                'emd' => $request->emd,
                'ra_enabled' => $request->ra_enabled,
                'ra_date_time' => $request->ra_date_time,
                'pre_bid_conference_required' => $request->pre_bid_conference_required,
                'pre_bid_conference_date' => $request->pre_bid_conference_date,
                'inspection_agency' => $request->inspection_agency,
                'tender_document_attachment' => $tenderDocumentPath,
                'financial_tabulation_attachment' => $financialTabulationPath,
                'technical_spec_attachment' => $technicalSpecPath,
                'technical_spec_rank' => $request->technical_spec_rank,
                'tender_status' => $request->tender_status ?? 'Bid not coated',
                'bid_result' => $request->bid_result,
            ]);

            // Delete existing items and related data
            $tender->items()->each(function ($item) {
                $item->technicalSpecifications()->delete();
            });
            $tender->items()->delete();
            $tender->financialTabulations()->delete();
            $tender->remarks()->delete();

            // Recreate items
            if ($request->has('items')) {
                foreach ($request->items as $itemData) {
                    $item = TenderItem::create([
                        'tender_id' => $tender->id,
                        'pl_code' => $itemData['pl_code'] ?? null,
                        'title' => $itemData['title'],
                        'description' => $itemData['description'] ?? null,
                        'delivery_location' => $itemData['delivery_location'] ?? null,
                        'qty' => $itemData['qty'] ?? null,
                        'unit_id' => $itemData['unit_id'] ?? null,
                        'request_for_price' => $itemData['request_for_price'] ?? 'No',
                        'price_received' => $itemData['price_received'] ?? null,
                        'price_quoted' => $itemData['price_quoted'] ?? null,
                        'tender_status' => $itemData['tender_status'] ?? null,
                        'bid_result' => $itemData['bid_result'] ?? null,
                    ]);

                    // Create technical specifications
                    if (isset($itemData['technical_specifications']) && is_array($itemData['technical_specifications'])) {
                        foreach ($itemData['technical_specifications'] as $specData) {
                            if (!empty($specData['specification'])) {
                                TenderTechnicalSpecification::create([
                                    'tender_item_id' => $item->id,
                                    'specification' => $specData['specification'],
                                    'rank' => $specData['rank'] ?? null,
                                ]);
                            }
                        }
                    }
                }
            }

            // Recreate financial tabulations
            if ($request->has('financial_tabulations') && is_array($request->financial_tabulations)) {
                foreach ($request->financial_tabulations as $tabData) {
                    if (!empty($tabData['pl_number'])) {
                        TenderFinancialTabulation::create([
                            'tender_id' => $tender->id,
                            'pl_number' => $tabData['pl_number'],
                            'bid_closed_date' => $tabData['bid_closed_date'] ?? null,
                        ]);
                    }
                }
            }

            // Recreate remarks
            if ($request->has('remarks') && is_array($request->remarks)) {
                foreach ($request->remarks as $remarkData) {
                    if (!empty($remarkData['remarks'])) {
                        $corrigendumPath = null;
                        if (isset($remarkData['corrigendum_file']) && $remarkData['corrigendum_file'] instanceof \Illuminate\Http\UploadedFile) {
                            $corrigendumPath = FileUploadHelper::storeWithOriginalName(
                                $remarkData['corrigendum_file'],
                                'tender_corrigendums'
                            );
                        }

                        TenderRemark::create([
                            'tender_id' => $tender->id,
                            'date' => $remarkData['date'] ?? now(),
                            'remarks' => $remarkData['remarks'],
                            'corrigendum_file' => $corrigendumPath,
                        ]);
                    }
                }
            }

            DB::commit();
            // After editing, go back to the Tender list view
            return redirect()->route('tenders.index')->with('success', 'Tender updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating tender: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        // Super Admin has access by default, but check permission for other users
        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'delete')) {
            abort(403, 'You do not have permission to delete tenders.');
        }

        $query = Tender::query();
        $query = $this->applyBranchFilter($query, Tender::class);
        $tender = $query->findOrFail($id);
        
        try {
            // Delete files if exist
            if ($tender->tender_document_attachment) {
                Storage::disk('public')->delete($tender->tender_document_attachment);
            }
            if ($tender->financial_tabulation_attachment) {
                Storage::disk('public')->delete($tender->financial_tabulation_attachment);
            }
            if ($tender->technical_spec_attachment) {
                Storage::disk('public')->delete($tender->technical_spec_attachment);
            }
            
            // Delete related files in remarks
            foreach ($tender->remarks as $remark) {
                if ($remark->corrigendum_file) {
                    Storage::disk('public')->delete($remark->corrigendum_file);
                }
            }
            
            $tender->delete();
            return redirect()->route('tenders.index')->with('success', 'Tender deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting tender: ' . $e->getMessage());
        }
    }

    /**
     * Get customer details via AJAX
     */
    public function getCustomerDetails($id)
    {
        $query = Customer::query();
        $query = $this->applyBranchFilter($query, Customer::class);
        $customer = $query->findOrFail($id);
        
        return response()->json([
            'billing_address_line_1' => $customer->billing_address_line_1,
            'billing_address_line_2' => $customer->billing_address_line_2,
            'billing_city' => $customer->billing_city,
            'billing_state' => $customer->billing_state,
            'billing_country' => $customer->billing_country ?? null,
            'billing_pincode' => $customer->billing_pincode,
            'gst_no' => $customer->gst_no,
            'contact_name' => $customer->contact_name,
        ]);
    }
}
