@extends('layouts.dashboard')

@section('title', 'Billing Address Master - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Billing Address Master</h2>
        <a href="{{ route('billing-addresses.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
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

    <form action="{{ route('billing-addresses.store') }}" method="POST" id="billingAddressForm">
        @csrf
        
        <div style="margin-bottom: 20px;">
            <label for="company_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Company Name <span style="color: red;">*</span></label>
            <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="Company Name">
            @error('company_name')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="address_line_1" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 1 <span style="color: red;">*</span></label>
            <textarea name="address_line_1" id="address_line_1" required rows="3"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;"
                placeholder="Address Line 1">{{ old('address_line_1') }}</textarea>
            @error('address_line_1')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="address_line_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2 <span style="color: red;">*</span></label>
            <textarea name="address_line_2" id="address_line_2" required rows="3"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;"
                placeholder="Address Line 2">{{ old('address_line_2') }}</textarea>
            @error('address_line_2')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="city" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">City <span style="color: red;">*</span></label>
                <input type="text" name="city" id="city" value="{{ old('city') }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="City">
                @error('city')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="state" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">State Name <span style="color: red;">*</span></label>
                <select name="state" id="state" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <option value="">-- Select State --</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}" {{ old('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                </select>
                @error('state')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="pincode" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Pincode <span style="color: red;">*</span></label>
                <input type="text" name="pincode" id="pincode" value="{{ old('pincode') }}" required maxlength="6"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Pincode (6 digits)" pattern="[0-9]{6}">
                @error('pincode')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Email <span style="color: red;">*</span></label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Email Address">
                @error('email')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="contact_no" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contact No <span style="color: red;">*</span></label>
                <input type="text" name="contact_no" id="contact_no" value="{{ old('contact_no') }}" required maxlength="10"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Contact No (10 digits)" pattern="[0-9]{10}">
                @error('contact_no')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="gst_no" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">GST No <span style="color: red;">*</span></label>
                <input type="text" name="gst_no" id="gst_no" value="{{ old('gst_no') }}" required maxlength="15"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-transform: uppercase;"
                    placeholder="GST No (e.g., 27AAAAA0000A1Z5)">
                @error('gst_no')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="button" onclick="clearForm()" style="padding: 12px 24px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Clear
            </button>
            <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Submit
            </button>
            <a href="{{ route('billing-addresses.index') }}" style="padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                List
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function clearForm() {
        document.getElementById('billingAddressForm').reset();
    }

    // Restrict pincode to numbers only
    document.getElementById('pincode').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Restrict contact_no to numbers only
    document.getElementById('contact_no').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Convert GST No to uppercase
    document.getElementById('gst_no').addEventListener('input', function(e) {
        this.value = this.value.toUpperCase();
    });
</script>
@endpush
@endsection

