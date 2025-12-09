<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use App\Models\TenderEvaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileUploadHelper;

class TenderEvaluationController extends Controller
{
    /**
     * Display a listing of Tender Evaluations.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'view')) {
            abort(403, 'You do not have permission to view tender evaluations.');
        }

        // Limit evaluations to tenders visible for the active branch
        $tenderQuery = Tender::query();
        $tenderQuery = $this->applyBranchFilter($tenderQuery, Tender::class);
        $tenderIds = $tenderQuery->pluck('id');

        $query = TenderEvaluation::with('tender')
            ->whereIn('tender_id', $tenderIds);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                // Search in tender number
                $q->whereHas('tender', function($tenderQuery) use ($search) {
                    $tenderQuery->where('tender_no', 'like', "%{$search}%")
                        ->orWhere('customer_tender_no', 'like', "%{$search}%");
                })
                // Search in dates (format: d-m-Y or d/m/Y or Y-m-d)
                ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y') LIKE ?", ["%{$search}%"])
                ->orWhereRaw("DATE_FORMAT(created_at, '%d/%m/%Y') LIKE ?", ["%{$search}%"])
                ->orWhereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') LIKE ?", ["%{$search}%"])
                ->orWhereRaw("DATE_FORMAT(created_at, '%d-%m-%Y %H:%i') LIKE ?", ["%{$search}%"])
                ->orWhereRaw("DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') LIKE ?", ["%{$search}%"]);
            });
        }

        $evaluations = $query->latest()->paginate(15)->withQueryString();

        return view('tenders.evaluations.index', compact('evaluations'));
    }

    /**
     * Show the Tender Evaluation form.
     */
    public function create()
    {
        $user = auth()->user();

        // Reuse tender view permission
        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'view')) {
            abort(403, 'You do not have permission to evaluate tenders.');
        }

        // Load tenders (with items & units) for the active branch
        $query = Tender::with(['items.unit']);
        $query = $this->applyBranchFilter($query, Tender::class);
        $tenders = $query->orderByDesc('created_at')->get();

        // Prepare lightweight data structure for JS
        $evaluationsData = $tenders->mapWithKeys(function ($t) {
            return [
                $t->id => [
                    'closing_date_time' => $t->closing_date_time ? $t->closing_date_time->format('Y-m-d\TH:i') : null,
                    'items' => $t->items->map(function ($item) {
                        return [
                            'title' => $item->title,
                            'description' => $item->description,
                            'qty' => $item->qty,
                            'unit' => optional($item->unit)->symbol,
                        ];
                    })->values(),
                ],
            ];
        });

        return view('tenders.evaluations.create', compact('tenders', 'evaluationsData'));
    }

    /**
     * Display a single Tender Evaluation.
     */
    public function show(TenderEvaluation $tender_evaluation)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'view')) {
            abort(403, 'You do not have permission to view tender evaluations.');
        }

        // Ensure this evaluation belongs to an allowed branch
        $tenderQuery = Tender::query();
        $tenderQuery = $this->applyBranchFilter($tenderQuery, Tender::class);
        $allowedTenderIds = $tenderQuery->pluck('id')->toArray();

        if (!in_array($tender_evaluation->tender_id, $allowedTenderIds, true)) {
            abort(404);
        }

        $tender_evaluation->load(['tender.items.unit']);

        return view('tenders.evaluations.show', [
            'evaluation' => $tender_evaluation,
        ]);
    }

    /**
     * Store a Tender Evaluation.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'view')) {
            abort(403, 'You do not have permission to evaluate tenders.');
        }

        $request->validate([
            'tender_id' => 'required|exists:tenders,id',
            'evaluation_document' => 'required|file|mimes:pdf,xls,xlsx,doc,docx|max:10240',
        ]);

        try {
            DB::beginTransaction();

            // Ensure evaluation belongs to an allowed branch
            $tenderQuery = Tender::query();
            $tenderQuery = $this->applyBranchFilter($tenderQuery, Tender::class);
            $tender = $tenderQuery->findOrFail($request->tender_id);

            $path = FileUploadHelper::storeWithOriginalName(
                $request->file('evaluation_document'),
                'tender_evaluations'
            );

            TenderEvaluation::create([
                'tender_id' => $tender->id,
                'evaluation_document' => $path,
            ]);

            DB::commit();

            return redirect()->route('tender-evaluations.index')
                ->with('success', 'Tender evaluation uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving tender evaluation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing a Tender Evaluation.
     */
    public function edit(TenderEvaluation $tender_evaluation)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'view')) {
            abort(403, 'You do not have permission to edit tender evaluations.');
        }

        // Ensure this evaluation belongs to an allowed branch
        $tenderQuery = Tender::query();
        $tenderQuery = $this->applyBranchFilter($tenderQuery, Tender::class);
        $allowedTenderIds = $tenderQuery->pluck('id')->toArray();

        if (!in_array($tender_evaluation->tender_id, $allowedTenderIds, true)) {
            abort(404);
        }

        $tender_evaluation->load('tender.items.unit');

        return view('tenders.evaluations.edit', [
            'evaluation' => $tender_evaluation,
        ]);
    }

    /**
     * Update a Tender Evaluation (replace evaluation document).
     */
    public function update(Request $request, TenderEvaluation $tender_evaluation)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'view')) {
            abort(403, 'You do not have permission to edit tender evaluations.');
        }

        $request->validate([
            'evaluation_document' => 'nullable|file|mimes:pdf,xls,xlsx,doc,docx|max:10240',
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('evaluation_document')) {
                if ($tender_evaluation->evaluation_document) {
                    Storage::disk('public')->delete($tender_evaluation->evaluation_document);
                }

                $path = FileUploadHelper::storeWithOriginalName(
                    $request->file('evaluation_document'),
                    'tender_evaluations'
                );
                $tender_evaluation->evaluation_document = $path;
            }

            $tender_evaluation->save();

            DB::commit();

            return redirect()->route('tender-evaluations.index')
                ->with('success', 'Tender evaluation updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating tender evaluation: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete a Tender Evaluation.
     */
    public function destroy(TenderEvaluation $tender_evaluation)
    {
        $user = auth()->user();

        if (!$user->isSuperAdmin() && !$user->hasPermission('tenders', 'view')) {
            abort(403, 'You do not have permission to delete tender evaluations.');
        }

        try {
            DB::beginTransaction();

            if ($tender_evaluation->evaluation_document) {
                Storage::disk('public')->delete($tender_evaluation->evaluation_document);
            }

            $tender_evaluation->delete();

            DB::commit();

            return redirect()->route('tender-evaluations.index')
                ->with('success', 'Tender evaluation deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting tender evaluation: ' . $e->getMessage());
        }
    }
}