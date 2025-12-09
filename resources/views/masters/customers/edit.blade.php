@extends('layouts.dashboard')

@section('title', 'Edit Customer - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Customer</h2>
        <a href="{{ route('customers.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
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

    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div style="margin-bottom: 20px;">
            <label for="company_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Company Name <span style="color: red;">*</span></label>
            <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $customer->company_name) }}" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('company_name')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="contact_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contact Name</label>
            <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name', $customer->contact_name) }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('contact_name')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="gst_no" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">GST Number</label>
            <input type="text" name="gst_no" id="gst_no" value="{{ old('gst_no', $customer->gst_no) }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="e.g., 27AABCU9603R1ZX">
            @error('gst_no')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        @php
            $countries = ['India','United States','United Kingdom','Australia','Canada','Germany','France','Singapore','United Arab Emirates','Other'];
        @endphp

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <h3 style="color: #667eea; font-size: 16px; margin-bottom: 15px;">Billing Address</h3>
            <div style="margin-bottom: 15px;">
                <label for="billing_address_line_1" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">
                    Address Line 1 <span style="color: red;">*</span>
                </label>
                <input type="text" name="billing_address_line_1" id="billing_address_line_1" value="{{ old('billing_address_line_1', $customer->billing_address_line_1) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;" required>
                @error('billing_address_line_1')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
            <div style="margin-bottom: 15px;">
                <label for="billing_address_line_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2 (Optional)</label>
                <input type="text" name="billing_address_line_2" id="billing_address_line_2" value="{{ old('billing_address_line_2', $customer->billing_address_line_2) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                @error('billing_address_line_2')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 15px;">
                <div>
                    <label for="billing_city" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">
                        City <span style="color: red;">*</span>
                    </label>
                    <input type="text" name="billing_city" id="billing_city" value="{{ old('billing_city', $customer->billing_city) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;" required>
                    @error('billing_city')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="billing_state" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">
                        State <span style="color: red;">*</span>
                    </label>
                    @php
                        $indianStates = [
                            'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh',
                            'Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland',
                            'Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal',
                            'Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu','Delhi','Jammu and Kashmir',
                            'Ladakh','Lakshadweep','Puducherry'
                        ];
                        $selectedState = old('billing_state', $customer->billing_state);
                    @endphp
                    <select name="billing_state" id="billing_state"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;" required>
                        <option value="">Select State</option>
                        @foreach($indianStates as $state)
                            <option value="{{ $state }}" {{ $selectedState === $state ? 'selected' : '' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                    @error('billing_state')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="billing_country" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Country</label>
                    @php $selectedBillingCountry = old('billing_country', $customer->billing_country ?? 'India'); @endphp
                    <select name="billing_country" id="billing_country"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        @foreach($countries as $country)
                            <option value="{{ $country }}" {{ $selectedBillingCountry === $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                    @error('billing_country')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="billing_pincode" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Pincode</label>
                    <input type="text" name="billing_pincode" id="billing_pincode" value="{{ old('billing_pincode', $customer->billing_pincode) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        pattern="[0-9]{6}" maxlength="6" placeholder="123456">
                    @error('billing_pincode')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="color: #667eea; font-size: 16px; margin: 0;">Shipping Address</h3>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #667eea; font-weight: 500;">
                    <input type="checkbox" id="copy_billing_address" onchange="copyBillingToShipping()"
                        style="width: 18px; height: 18px; cursor: pointer;">
                    <span>Copy from Billing Address</span>
                </label>
            </div>
            <div style="margin-bottom: 15px;">
                <label for="shipping_address_line_1" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">
                    Address Line 1 <span style="color: red;">*</span>
                </label>
                <input type="text" name="shipping_address_line_1" id="shipping_address_line_1" value="{{ old('shipping_address_line_1', $customer->shipping_address_line_1) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;" required>
                @error('shipping_address_line_1')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
            <div style="margin-bottom: 15px;">
                <label for="shipping_address_line_2" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2 (Optional)</label>
                <input type="text" name="shipping_address_line_2" id="shipping_address_line_2" value="{{ old('shipping_address_line_2', $customer->shipping_address_line_2) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                @error('shipping_address_line_2')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 15px;">
                <div>
                    <label for="shipping_city" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">
                        City <span style="color: red;">*</span>
                    </label>
                    <input type="text" name="shipping_city" id="shipping_city" value="{{ old('shipping_city', $customer->shipping_city) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;" required>
                    @error('shipping_city')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="shipping_state" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">
                        State <span style="color: red;">*</span>
                    </label>
                    @php $selectedShippingState = old('shipping_state', $customer->shipping_state); @endphp
                    <select name="shipping_state" id="shipping_state"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;" required>
                        <option value="">Select State</option>
                        @foreach($indianStates as $state)
                            <option value="{{ $state }}" {{ $selectedShippingState === $state ? 'selected' : '' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                    @error('shipping_state')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="shipping_country" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Country</label>
                    @php $selectedShippingCountry = old('shipping_country', $customer->shipping_country ?? 'India'); @endphp
                    <select name="shipping_country" id="shipping_country"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #fff;">
                        @foreach($countries as $country)
                            <option value="{{ $country }}" {{ $selectedShippingCountry === $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                    @error('shipping_country')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="shipping_pincode" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Pincode</label>
                    <input type="text" name="shipping_pincode" id="shipping_pincode" value="{{ old('shipping_pincode', $customer->shipping_pincode) }}"
                        style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                        pattern="[0-9]{6}" maxlength="6" placeholder="123456">
                    @error('shipping_pincode')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label for="contact_info" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contact Information</label>
            <input type="text" name="contact_info" id="contact_info" value="{{ old('contact_info', $customer->contact_info) }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="Phone, Email, etc.">
            @error('contact_info')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('customers.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Update Customer
            </button>
        </div>
    </form>
</div>

<script>
    function copyBillingToShipping() {
        const checkbox = document.getElementById('copy_billing_address');
        if (checkbox.checked) {
            document.getElementById('shipping_address_line_1').value = document.getElementById('billing_address_line_1').value;
            document.getElementById('shipping_address_line_2').value = document.getElementById('billing_address_line_2').value;
            document.getElementById('shipping_city').value = document.getElementById('billing_city').value;
            document.getElementById('shipping_state').value = document.getElementById('billing_state').value;
            document.getElementById('shipping_pincode').value = document.getElementById('billing_pincode').value;
            const billingCountrySelect = document.getElementById('billing_country');
            const shippingCountrySelect = document.getElementById('shipping_country');
            if (billingCountrySelect && shippingCountrySelect) {
                shippingCountrySelect.value = billingCountrySelect.value;
            }
        }
    }
</script>
@endsection
