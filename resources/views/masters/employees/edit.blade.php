@extends('layouts.dashboard')

@section('title', 'Edit Employee - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1200px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Employee</h2>
        <a href="{{ route('employees.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

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

    <form action="{{ route('employees.update', $employee->id) }}" method="POST" id="employeeForm">
        @csrf
        @method('PUT')
        
        <h3 style="color: #333; font-size: 18px; margin: 20px 0 15px 0; border-bottom: 2px solid #dee2e6; padding-bottom: 10px;">Employee Information</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="employee_code" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Employee Code</label>
                <input type="text" name="employee_code" id="employee_code" value="{{ old('employee_code', $employee->employee_code) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Employee Code">
                @error('employee_code')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Employee Name <span style="color: red;">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $employee->name) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Employee Name">
                @error('name')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="department_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Department <span style="color: red;">*</span></label>
                <select name="department_id" id="department_id" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <option value="">-- Select Department --</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="designation_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Designation <span style="color: red;">*</span></label>
                <select name="designation_id" id="designation_id" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <option value="">-- Select Designation --</option>
                    @foreach($designations as $designation)
                        <option value="{{ $designation->id }}" {{ old('designation_id', $employee->designation_id) == $designation->id ? 'selected' : '' }}>
                            {{ $designation->name }}
                        </option>
                    @endforeach
                </select>
                @error('designation_id')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="date_of_birth" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Date of Birth (DD-MM-YYYY) <span style="color: red;">*</span></label>
                <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('Y-m-d') : '') }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                @error('date_of_birth')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Email <span style="color: red;">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="employee@example.com">
                @error('email')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="mobile_no" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Mobile No <span style="color: red;">*</span></label>
                <input type="text" name="mobile_no" id="mobile_no" value="{{ old('mobile_no', $employee->mobile_no) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Mobile Number" maxlength="20">
                @error('mobile_no')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="active" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Active <span style="color: red;">*</span></label>
                <select name="active" id="active" required
                    style="width: 100%; max-width: 200px; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <option value="Yes" {{ old('active', $employee->active) == 'Yes' ? 'selected' : '' }}>Yes</option>
                    <option value="No" {{ old('active', $employee->active) == 'No' ? 'selected' : '' }}>No</option>
                </select>
                @error('active')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <h3 style="color: #333; font-size: 18px; margin: 30px 0 15px 0; border-bottom: 2px solid #dee2e6; padding-bottom: 10px;">Address Information</h3>

        <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="address_line_1" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 1</label>
                <input type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1', $employee->address_line_1) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Address Line 1">
                @error('address_line_1')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="address_line_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2</label>
                <input type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2', $employee->address_line_2) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Address Line 2">
                @error('address_line_2')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="city" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">City <span style="color: red;">*</span></label>
                <input type="text" name="city" id="city" value="{{ old('city', $employee->city) }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="City">
                @error('city')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="state" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">State <span style="color: red;">*</span></label>
                <select name="state" id="state" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <option value="">-- Select State --</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}" {{ old('state', $employee->state) == $state ? 'selected' : '' }}>
                            {{ $state }}
                        </option>
                    @endforeach
                </select>
                @error('state')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="country" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Country <span style="color: red;">*</span></label>
                <select name="country" id="country" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    @foreach($countries as $country)
                        <option value="{{ $country }}" {{ old('country', $employee->country) == $country ? 'selected' : '' }}>
                            {{ $country }}
                        </option>
                    @endforeach
                </select>
                @error('country')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="pincode" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">PinCode</label>
                <input type="text" name="pincode" id="pincode" value="{{ old('pincode', $employee->pincode) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="6 digits" maxlength="6" pattern="[0-9]{6}">
                @error('pincode')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="emergency_contact_no" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Emergency Contact No</label>
                <input type="text" name="emergency_contact_no" id="emergency_contact_no" value="{{ old('emergency_contact_no', $employee->emergency_contact_no) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Emergency Contact Number" maxlength="20">
                @error('emergency_contact_no')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="button" onclick="clearForm()" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Clear
            </button>
            <button type="submit" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Update
            </button>
            <a href="{{ route('employees.index') }}" style="padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                List
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Date input handles format automatically

    // Pincode: only numbers, max 6 digits
    document.getElementById('pincode').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '').substring(0, 6);
    });

    // Mobile number: only numbers and allowed characters
    document.getElementById('mobile_no').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9+\-() ]/g, '');
    });

    // Emergency contact: only numbers and allowed characters
    document.getElementById('emergency_contact_no').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9+\-() ]/g, '');
    });

    function clearForm() {
        document.getElementById('employeeForm').reset();
        document.getElementById('country').value = '{{ $employee->country }}';
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(msg => msg.remove());
    }
</script>
@endpush
@endsection

