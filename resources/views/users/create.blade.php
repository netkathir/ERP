@extends('layouts.dashboard')

@section('title', 'Create User - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <h2 style="color: #333; margin-bottom: 25px; font-size: 24px;">Create New User</h2>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.store') }}" method="POST" id="userForm">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Full Name <span style="color: red;">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    class="@error('name') border-red-500 @enderror">
                @error('name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Email <span style="color: red;">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    class="@error('email') border-red-500 @enderror">
                @error('email')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="mobile" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Mobile Number</label>
            <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                class="@error('mobile') border-red-500 @enderror"
                placeholder="e.g., +1234567890">
            @error('mobile')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="password" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Password <span style="color: red;">*</span></label>
                <input type="password" name="password" id="password" value="{{ old('password') }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    class="@error('password') border-red-500 @enderror"
                    autocomplete="new-password">
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Minimum 8 characters, 1 uppercase, 1 lowercase, 1 number</small>
                @error('password')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Confirm Password <span style="color: red;">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" value="{{ old('password_confirmation') }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    class="@error('password_confirmation') border-red-500 @enderror"
                    autocomplete="new-password">
                @error('password_confirmation')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="role_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Role <span style="color: red;">*</span></label>
            <select name="role_id" id="role_id" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                onchange="toggleBranchSelection()">
                <option value="">-- Select Role --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role_id')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Branches multi-select - Only required for non-Super Admin roles --}}
        <div style="margin-bottom: 20px;" id="branches-section">
            <label for="branches" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Branches <span style="color: red;">*</span> <span id="branches-required-note" style="color: #666; font-size: 12px;">(Required for non-Super Admin roles)</span></label>
            @if($branches->count() > 0)
                <select name="branches[]" id="branches" multiple
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; min-height: 120px;">
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ in_array($branch->id, old('branches', [])) ? 'selected' : '' }}>
                            {{ $branch->name }} ({{ $branch->code }})
                        </option>
                    @endforeach
                </select>
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Hold Ctrl (Windows) or Cmd (Mac) to select multiple branches</small>
            @else
                <div style="background: #fff3cd; color: #856404; padding: 12px; border-radius: 5px; border: 1px solid #ffeaa7;">
                    <strong>No branches available!</strong> Please create at least one branch before creating users.
                    <a href="{{ route('branches.create') }}" style="color: #667eea; text-decoration: underline; margin-left: 10px;">Create Branch</a>
                </div>
            @endif
            @error('branches')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; cursor: pointer;">
                <input type="checkbox" name="send_email" id="send_email" value="1" {{ old('send_email') ? 'checked' : '' }}
                    style="margin-right: 8px; width: 18px; height: 18px;">
                <span style="color: #333; font-weight: 500;">Send welcome email with login credentials</span>
            </label>
            <small style="color: #666; font-size: 12px; display: block; margin-top: 5px; margin-left: 26px;">
                User will receive an email with their login credentials. User can change their password after first login.
            </small>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('users.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Save
            </button>
        </div>
    </form>
</div>

<script>
function toggleBranchSelection() {
    const roleSelect = document.getElementById('role_id');
    const branchesSection = document.getElementById('branches-section');
    const branchesSelect = document.getElementById('branches');
    const selectedRole = roleSelect.options[roleSelect.selectedIndex];
    const roleName = selectedRole ? selectedRole.text.toLowerCase() : '';
    
    if (roleName.includes('super admin')) {
        branchesSection.style.display = 'none';
        branchesSelect.removeAttribute('required');
        branchesSelect.value = null;
    } else {
        branchesSection.style.display = 'block';
        branchesSelect.setAttribute('required', 'required');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleBranchSelection();
});
</script>

@endsection
