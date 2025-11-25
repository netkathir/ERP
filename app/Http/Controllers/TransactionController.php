<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            // Super Admin can see all transactions
            $transactions = Transaction::with(['user', 'branch'])->latest()->paginate(15);
        } elseif ($user->isBranchUser()) {
            // Get user's assigned branch IDs
            $userBranchIds = $user->branches->pluck('id')->toArray();
            
            if (empty($userBranchIds)) {
                $transactions = Transaction::whereRaw('1 = 0')->paginate(15);
            } else {
                // Filter by selected branch if set, otherwise show all user's branches
                $selectedBranchId = session('selected_branch_id');
                
                if ($selectedBranchId && in_array($selectedBranchId, $userBranchIds)) {
                    // Show transactions for selected branch only
                    $transactions = Transaction::where('user_id', $user->id)
                        ->where('branch_id', $selectedBranchId)
                        ->with(['user', 'branch'])
                        ->latest()
                        ->paginate(15);
                } else {
                    // Show transactions for all user's branches
                    $transactions = Transaction::where('user_id', $user->id)
                        ->whereIn('branch_id', $userBranchIds)
                        ->with(['user', 'branch'])
                        ->latest()
                        ->paginate(15);
                }
            }
        } else {
            $transactions = Transaction::whereRaw('1 = 0')->paginate(15);
        }

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = auth()->user();
        
        // Only Branch Users can create transactions
        if (!$user->isBranchUser()) {
            abort(403, 'Only Branch Users can create transactions.');
        }

        // Get user's assigned branches
        $branches = $user->branches;
        
        if ($branches->isEmpty()) {
            return redirect()->route('transactions.index')
                ->with('error', 'You are not assigned to any branches. Please contact Super Admin.');
        }

        return view('transactions.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();
        
        // Only Branch Users can create transactions
        if (!$user->isBranchUser()) {
            abort(403, 'Only Branch Users can create transactions.');
        }

        $request->validate([
            'transaction_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,completed,cancelled',
            'branch_id' => 'required|exists:branches,id',
        ]);

        // Verify user has access to the selected branch
        $selectedBranchId = $request->branch_id;
        if (!$user->hasAccessToBranch($selectedBranchId)) {
            return back()->withErrors(['branch_id' => 'You do not have access to this branch.'])->withInput();
        }

        // Generate unique reference number
        $referenceNumber = 'TXN-' . strtoupper(uniqid());

        Transaction::create([
            'user_id' => $user->id,
            'branch_id' => $selectedBranchId,
            'transaction_type' => $request->transaction_type,
            'reference_number' => $referenceNumber,
            'description' => $request->description,
            'amount' => $request->amount,
            'status' => $request->status ?? 'pending',
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): View
    {
        $user = auth()->user();
        $transaction = Transaction::with(['user', 'branch'])->findOrFail($id);
        
        // Check access
        if ($user->isBranchUser()) {
            if ($transaction->user_id != $user->id) {
                abort(403, 'Unauthorized to view this transaction.');
            }
            // Verify user has access to this transaction's branch
            if (!$user->hasAccessToBranch($transaction->branch_id)) {
                abort(403, 'You do not have access to this transaction.');
            }
        }

        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): View
    {
        $user = auth()->user();
        $transaction = Transaction::findOrFail($id);
        
        // Only Branch Users can edit their own transactions
        if (!$user->isBranchUser() || $transaction->user_id != $user->id) {
            abort(403, 'Unauthorized to edit this transaction.');
        }

        // Verify user has access to this transaction's branch
        if (!$user->hasAccessToBranch($transaction->branch_id)) {
            abort(403, 'You do not have access to this transaction.');
        }

        // Get user's assigned branches
        $branches = $user->branches;

        return view('transactions.edit', compact('transaction', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $user = auth()->user();
        $transaction = Transaction::findOrFail($id);
        
        // Only Branch Users can update their own transactions
        if (!$user->isBranchUser() || $transaction->user_id != $user->id) {
            abort(403, 'Unauthorized to update this transaction.');
        }

        // Verify user has access to this transaction's branch
        if (!$user->hasAccessToBranch($transaction->branch_id)) {
            abort(403, 'You do not have access to this transaction.');
        }

        $request->validate([
            'transaction_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'amount' => 'required|numeric|min:0',
            'status' => 'nullable|in:pending,completed,cancelled',
        ]);

        $transaction->update($request->only(['transaction_type', 'description', 'amount', 'status']));

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $user = auth()->user();
        $transaction = Transaction::findOrFail($id);
        
        // Only Branch Users can delete their own transactions
        if (!$user->isBranchUser() || $transaction->user_id != $user->id) {
            abort(403, 'Unauthorized to delete this transaction.');
        }

        // Verify user has access to this transaction's branch
        if (!$user->hasAccessToBranch($transaction->branch_id)) {
            abort(403, 'You do not have access to this transaction.');
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction deleted successfully.');
    }
}
