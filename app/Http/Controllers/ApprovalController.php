<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    /**
     * Show pending approvals for the current user
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Only Super Admins can approve
        if (!$user->isSuperAdmin()) {
            abort(403, 'You do not have permission to approve records.');
        }

        $formName = $request->get('form', 'customer_orders');

        // Get pending records based on form type
        $pendingRecords = $this->getPendingRecords($formName);

        return view('approvals.index', compact('formName', 'pendingRecords'));
    }

    /**
     * Approve a record
     */
    public function approve(Request $request, $formName, $id)
    {
        $user = auth()->user();

        // Only Super Admins can approve
        if (!$user->isSuperAdmin()) {
            abort(403, 'You do not have permission to approve this record.');
        }

        $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $record = $this->getRecord($formName, $id);

            if (!$record) {
                return back()->with('error', 'Record not found.');
            }

            if ($record->approval_status !== 'pending') {
                return back()->with('error', 'This record has already been processed.');
            }

            $record->update([
                'approval_status' => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'approval_remarks' => $request->remarks,
            ]);

            DB::commit();

            return redirect()->route('approvals.index', ['form' => $formName])
                ->with('success', 'Record approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error approving record: ' . $e->getMessage());
        }
    }

    /**
     * Reject a record
     */
    public function reject(Request $request, $formName, $id)
    {
        $user = auth()->user();

        // Only Super Admins can reject
        if (!$user->isSuperAdmin()) {
            abort(403, 'You do not have permission to reject this record.');
        }

        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $record = $this->getRecord($formName, $id);

            if (!$record) {
                return back()->with('error', 'Record not found.');
            }

            if ($record->approval_status !== 'pending') {
                return back()->with('error', 'This record has already been processed.');
            }

            $record->update([
                'approval_status' => 'rejected',
                'rejected_by' => $user->id,
                'rejected_at' => now(),
                'rejection_remarks' => $request->remarks,
            ]);

            DB::commit();

            return redirect()->route('approvals.index', ['form' => $formName])
                ->with('success', 'Record rejected successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error rejecting record: ' . $e->getMessage());
        }
    }

    /**
     * Get pending records based on form name
     */
    private function getPendingRecords($formName)
    {
        switch ($formName) {
            case 'customer_orders':
                return CustomerOrder::where('approval_status', 'pending')
                    ->with(['tender', 'branch'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            // Add more cases as needed for other forms
            default:
                return collect();
        }
    }

    /**
     * Get a specific record by form name and ID
     */
    private function getRecord($formName, $id)
    {
        switch ($formName) {
            case 'customer_orders':
                return CustomerOrder::find($id);
            // Add more cases as needed for other forms
            default:
                return null;
        }
    }
}

