<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierEvaluation;
use Illuminate\Http\Request;

class SupplierEvaluationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'view')) {
            abort(403, 'You do not have permission to view supplier evaluations.');
        }

        $query = SupplierEvaluation::with('supplier');
        $query = $this->applyBranchFilter($query, SupplierEvaluation::class);
        $evaluations = $query->latest()->paginate(15);

        return view('suppliers.evaluations.index', compact('evaluations'));
    }

    public function create()
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'create')) {
            abort(403, 'You do not have permission to create supplier evaluations.');
        }

        // Only suppliers with supplier_type = 'Supplier' (exclude Sub Contractor)
        $supplierQuery = Supplier::where('supplier_type', 'Supplier');
        $supplierQuery = $this->applyBranchFilter($supplierQuery, Supplier::class);
        $suppliers = $supplierQuery->orderBy('supplier_name')->get();

        // Lightweight data for auto‑populate
        $suppliersData = $suppliers->mapWithKeys(function ($s) {
            return [
                $s->id => [
                    'contact_person' => $s->contact_person,
                    'address_line_1' => $s->address_line_1,
                    'address_line_2' => $s->address_line_2,
                    'city' => $s->city,
                    'state' => $s->state,
                    'pincode' => $s->pincode ?? '',
                    'remarks' => $s->remarks ?? '',
                ],
            ];
        });

        $assessmentResults = ['Approved', 'Needs Improvement', 'Not Approved'];
        $statusOptions = ['Pending', 'Approved', 'Rejected'];

        return view('suppliers.evaluations.create', compact('suppliers', 'assessmentResults', 'statusOptions', 'suppliersData'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'create')) {
            abort(403, 'You do not have permission to create supplier evaluations.');
        }

        $validated = $this->validateRequest($request);

        // Pincode is no longer on the form; default to empty string if missing
        if (!array_key_exists('pincode', $validated) || $validated['pincode'] === null) {
            $validated['pincode'] = '';
        }

        [$totalScore, $grandTotal] = $this->calculateTotals($validated);

        $validated['total_score'] = $totalScore;
        $validated['grand_total'] = $grandTotal;
        $validated['branch_id'] = $this->getActiveBranchId();
        $validated['created_by_id'] = $user->id;

        SupplierEvaluation::create($validated);

        return redirect()->route('supplier-evaluations.index')->with('success', 'Supplier evaluation created successfully.');
    }

    public function show($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'view')) {
            abort(403, 'You do not have permission to view supplier evaluations.');
        }

        $query = SupplierEvaluation::with('supplier');
        $query = $this->applyBranchFilter($query, SupplierEvaluation::class);
        $evaluation = $query->findOrFail($id);

        return view('suppliers.evaluations.show', compact('evaluation'));
    }

    public function edit($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'edit')) {
            abort(403, 'You do not have permission to edit supplier evaluations.');
        }

        $query = SupplierEvaluation::query();
        $query = $this->applyBranchFilter($query, SupplierEvaluation::class);
        $evaluation = $query->findOrFail($id);

        $supplierQuery = Supplier::where('supplier_type', 'Supplier');
        $supplierQuery = $this->applyBranchFilter($supplierQuery, Supplier::class);
        $suppliers = $supplierQuery->orderBy('supplier_name')->get();

        $suppliersData = $suppliers->mapWithKeys(function ($s) {
            return [
                $s->id => [
                    'contact_person' => $s->contact_person ?? '',
                    'address_line_1' => $s->address_line_1 ?? '',
                    'address_line_2' => $s->address_line_2 ?? '',
                    'city' => $s->city ?? '',
                    'state' => $s->state ?? '',
                    'pincode' => $s->pincode ?? '',
                    'remarks' => $s->remarks ?? '',
                ],
            ];
        });

        $assessmentResults = ['Approved', 'Needs Improvement', 'Not Approved'];
        $statusOptions = ['Pending', 'Approved', 'Rejected'];

        return view('suppliers.evaluations.edit', compact('evaluation', 'suppliers', 'assessmentResults', 'statusOptions', 'suppliersData'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'edit')) {
            abort(403, 'You do not have permission to edit supplier evaluations.');
        }

        $query = SupplierEvaluation::query();
        $query = $this->applyBranchFilter($query, SupplierEvaluation::class);
        $evaluation = $query->findOrFail($id);

        $validated = $this->validateRequest($request);

        // Pincode is no longer on the form; keep old value or default to empty string
        if (!array_key_exists('pincode', $validated) || $validated['pincode'] === null) {
            $validated['pincode'] = $evaluation->pincode ?? '';
        }

        [$totalScore, $grandTotal] = $this->calculateTotals($validated);

        $validated['total_score'] = $totalScore;
        $validated['grand_total'] = $grandTotal;

        $evaluation->update($validated);

        return redirect()->route('supplier-evaluations.index')->with('success', 'Supplier evaluation updated successfully.');
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('suppliers', 'delete')) {
            abort(403, 'You do not have permission to delete supplier evaluations.');
        }

        $query = SupplierEvaluation::query();
        $query = $this->applyBranchFilter($query, SupplierEvaluation::class);
        $evaluation = $query->findOrFail($id);

        $evaluation->delete();

        return redirect()->route('supplier-evaluations.index')->with('success', 'Supplier evaluation deleted successfully.');
    }

    protected function validateRequest(Request $request): array
    {
        $assessmentResults = ['Approved', 'Needs Improvement', 'Not Approved'];
        $statusOptions = ['Pending', 'Approved', 'Rejected'];

        return $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'contact_person' => 'required|string|max:255',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            // Pincode removed from form; keep optional for backward compatibility
            'pincode' => 'nullable|string|max:20',
            'supplier_remarks' => 'nullable|string',

            'facilities_capacity_rating' => 'required|integer|min:0|max:25',
            'facilities_capacity_remarks' => 'nullable|string',

            'manufacturing_facility_rating' => 'required|integer|min:0|max:5',
            'manufacturing_facility_remarks' => 'nullable|string',

            'business_volume_rating' => 'required|integer|min:0|max:5',
            'business_volume_remarks' => 'nullable|string',

            'financial_stability_rating' => 'required|integer|min:0|max:5',
            'financial_stability_remarks' => 'nullable|string',

            'decision_making_rating' => 'required|integer|min:0|max:5',
            'decision_making_remarks' => 'nullable|string',

            'complexity_rating' => 'required|integer|min:0|max:5',
            'complexity_remarks' => 'nullable|string',

            'tech_competency_rating' => 'required|integer|min:0|max:35',
            'tech_competency_remarks' => 'nullable|string',

            'supplier_technology_rating' => 'required|integer|min:0|max:5',
            'supplier_technology_remarks' => 'nullable|string',

            'resources_availability_rating' => 'required|integer|min:0|max:5',
            'resources_availability_remarks' => 'nullable|string',

            'process_design_rating' => 'required|integer|min:0|max:5',
            'process_design_remarks' => 'nullable|string',

            'manufacturing_capability_rating' => 'required|integer|min:0|max:5',
            'manufacturing_capability_remarks' => 'nullable|string',

            'change_management_rating' => 'required|integer|min:0|max:5',
            'change_management_remarks' => 'nullable|string',

            'disaster_preparedness_rating' => 'required|integer|min:0|max:5',
            'disaster_preparedness_remarks' => 'nullable|string',

            'equipment_monitoring_rating' => 'required|integer|min:0|max:5',
            'equipment_monitoring_remarks' => 'nullable|string',

            'working_environment_rating' => 'required|integer|min:0|max:5',
            'working_environment_remarks' => 'nullable|string',

            'product_testing_rating' => 'required|integer|min:0|max:5',
            'product_testing_remarks' => 'nullable|string',

            'storage_handling_rating' => 'required|integer|min:0|max:5',
            'storage_handling_remarks' => 'nullable|string',

            'transportation_rating' => 'required|integer|min:0|max:5',
            'transportation_remarks' => 'nullable|string',

            'statutory_regulatory_rating' => 'required|integer|min:0|max:10',
            'statutory_regulatory_remarks' => 'nullable|string',

            'qms_risk_rating' => 'required|integer|min:0|max:10',
            'qms_risk_remarks' => 'nullable|string',

            'assessment_result' => 'required|string|in:' . implode(',', $assessmentResults),
            'status' => 'required|string|in:' . implode(',', $statusOptions),
            'final_remarks' => 'nullable|string',
        ]);
    }

    protected function calculateTotals(array $data): array
    {
        $ratingFields = [
            'facilities_capacity_rating',
            'manufacturing_facility_rating',
            'business_volume_rating',
            'financial_stability_rating',
            'decision_making_rating',
            'complexity_rating',
            'tech_competency_rating',
            'supplier_technology_rating',
            'resources_availability_rating',
            'process_design_rating',
            'manufacturing_capability_rating',
            'change_management_rating',
            'disaster_preparedness_rating',
            'equipment_monitoring_rating',
            'working_environment_rating',
            'product_testing_rating',
            'storage_handling_rating',
            'transportation_rating',
            'statutory_regulatory_rating',
            'qms_risk_rating',
        ];

        $total = 0;
        foreach ($ratingFields as $field) {
            $total += (int)($data[$field] ?? 0);
        }

        // Grand total is same as total for now (kept separate for future weighting)
        return [$total, $total];
    }
}


