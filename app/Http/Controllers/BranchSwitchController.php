<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class BranchSwitchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Switch to a specific branch context.
     */
    public function switch(Request $request, Branch $branch): RedirectResponse
    {
        $user = Auth::user();

        // Only Branch Users can switch branches
        if (!$user->isBranchUser()) {
            abort(403, 'Only Branch Users can switch branches.');
        }

        // Verify user has access to this branch
        if (!$user->hasAccessToBranch($branch->id)) {
            abort(403, 'You do not have access to this branch.');
        }

        Session::put('selected_branch_id', $branch->id);
        Session::put('selected_branch_name', $branch->name);

        return redirect()->back()->with('success', 'Switched to branch: ' . $branch->name);
    }

    /**
     * Clear the branch context.
     */
    public function clear(): RedirectResponse
    {
        $user = Auth::user();

        // Only Branch Users can clear branch context
        if (!$user->isBranchUser()) {
            abort(403, 'Only Branch Users can clear branch context.');
        }

        Session::forget('selected_branch_id');
        Session::forget('selected_branch_name');

        return redirect()->back()->with('success', 'Branch context cleared.');
    }
}
