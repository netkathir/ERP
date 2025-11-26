<?php

namespace App\Http\Controllers;

use App\Mail\UserWelcomeMail;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $user = auth()->user();
        
        if ($user->isSuperAdmin()) {
            // Super Admin can see all users
            $users = User::with(['role', 'branches'])->latest()->paginate(15);
        } elseif ($user->isBranchUser()) {
            // Branch User can only see themselves
            $users = User::where('id', $user->id)
                ->with(['role', 'branches'])
                ->paginate(15);
        } else {
            $users = User::where('id', $user->id)
                ->with(['role', 'branches'])
                ->paginate(15);
        }
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $user = auth()->user();
        
        // Only Super Admin can create users
        if (!$user->isSuperAdmin()) {
            abort(403, 'Only Super Admin can create users.');
        }
        
        // Super Admin can assign any role
        $roles = \App\Models\Role::where('is_active', true)->get();
        $branches = \App\Models\Branch::where('is_active', true)->get();
        
        return view('users.create', compact('roles', 'branches', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $currentUser = auth()->user();
        
        // Only Super Admin can create users
        if (!$currentUser->isSuperAdmin()) {
            abort(403, 'Only Super Admin can create users.');
        }
        
        // Validate request
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'mobile' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'branches' => 'nullable|array',
            'branches.*' => 'exists:branches,id',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed'
            ],
        ];
        
        $request->validate($rules, [
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);
        
        // Get selected role
        $role = \App\Models\Role::findOrFail($request->role_id);
        
        // Validate branch assignment (not required for Super Admin)
        if ($role->slug !== 'super-admin' && (!$request->branches || !is_array($request->branches) || count($request->branches) == 0)) {
            return back()->withErrors(['branches' => 'At least one branch is required for non-Super Admin users.'])->withInput();
        }
        
        try {
            // Use password from form
            $password = $request->password;
            
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'mobile' => $request->mobile ?? null,
                'role_id' => $role->id,
                'status' => 'active',
                'created_by' => $currentUser->id,
                'organization_id' => null,
                'branch_id' => null,
                'entity_id' => null,
            ];

            $user = User::create($data);
            
            // Sync branches (many-to-many) - only if not Super Admin
            if ($role->slug !== 'super-admin' && $request->branches) {
                $user->branches()->sync($request->branches);
            }
            
            // Email is not sent - Admin will share credentials externally
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create user: ' . $e->getMessage()])->withInput();
        }

        $message = 'User created successfully';
        if ($request->branches && count($request->branches) > 0) {
            $message .= ' and assigned to ' . count($request->branches) . ' branch(es)';
        }
        $message .= '.';
        
        // Store password in session to display to admin
        session()->flash('user_password', $password);
        session()->flash('user_email', $user->email);
        session()->flash('user_name', $user->name);

        return redirect()->route('users.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        $user = User::with(['role', 'entity'])->findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $currentUser = auth()->user();
        $user = User::findOrFail($id);
        
        // Only Super Admin can edit users
        if (!$currentUser->isSuperAdmin()) {
            abort(403, 'Only Super Admin can edit users.');
        }
        
        $roles = \App\Models\Role::where('is_active', true)->get();
        $branches = \App\Models\Branch::where('is_active', true)->get();
        
        return view('users.edit', compact('user', 'roles', 'branches', 'currentUser'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $currentUser = auth()->user();
        $user = User::findOrFail($id);
        
        // Only Super Admin can update users
        if (!$currentUser->isSuperAdmin()) {
            abort(403, 'Only Super Admin can update users.');
        }
        
        $role = \App\Models\Role::findOrFail($request->role_id);
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'mobile' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive,locked',
        ];
        
        // Validate branches only if not Super Admin
        if ($role->slug !== 'super-admin') {
            $rules['branches'] = 'required|array|min:1';
            $rules['branches.*'] = 'exists:branches,id';
        }
        
        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed'
            ];
        }
        
        $request->validate($rules);
        
        $data = $request->only(['name', 'email', 'mobile', 'status', 'role_id']);
        
        // Update password only if provided and not empty
        $password = $request->input('password');
        if (!empty($password) && is_string($password) && strlen(trim($password)) > 0) {
            $data['password'] = Hash::make(trim($password));
        }

        $user->update($data);
        
        // Sync branches (many-to-many) - only if not Super Admin
        if ($role->slug !== 'super-admin' && $request->branches) {
            $user->branches()->sync($request->branches);
        } elseif ($role->slug === 'super-admin') {
            // Remove all branch assignments for Super Admin
            $user->branches()->detach();
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully and assigned to ' . count($request->branches) . ' branch(es).');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $currentUser = auth()->user();
        
        // Only Super Admin can delete users
        if (!$currentUser->isSuperAdmin()) {
            abort(403, 'Only Super Admin can delete users.');
        }
        
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === $currentUser->id) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }
        
        // Instead of deleting, deactivate the user
        $user->update(['status' => 'inactive']);
        
        // Remove branch assignments
        $user->branches()->detach();

        return redirect()->route('users.index')
            ->with('success', 'User deactivated successfully.');
    }

    /**
     * Show the form for changing user's own password.
     *
     * @return View
     */
    public function showChangePasswordForm(): View
    {
        return view('account.change-password');
    }

    /**
     * Change the authenticated user's password.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function changePassword(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed'
            ],
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.regex' => 'New password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'new_password.confirmed' => 'New password confirmation does not match.',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        // Check if new password is different from current password
        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors(['new_password' => 'New password must be different from your current password.'])->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Logout user after password change
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Your password has been successfully changed. Please log in with your new password.');
    }

    /**
     * Admin change user password (without requiring current password).
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function adminChangePassword(Request $request, int $id): RedirectResponse
    {
        $currentUser = auth()->user();
        
        // Only Super Admin can change user passwords
        if (!$currentUser->isSuperAdmin()) {
            abort(403, 'Only Super Admin can change user passwords.');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'new_password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'confirmed'
            ],
        ], [
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.regex' => 'New password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'new_password.confirmed' => 'New password confirmation does not match.',
        ]);

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('users.edit', $user->id)
            ->with('success', 'User password has been successfully changed.');
    }
}
