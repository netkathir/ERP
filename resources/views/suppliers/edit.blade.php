@extends('layouts.dashboard')

@section('title', 'Edit Supplier - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Supplier</h2>
        <a href="{{ route('suppliers.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
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

    <form action="{{ route('suppliers.update', $supplier->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display:flex; gap:30px; flex-wrap:wrap;">
            {{-- Left column --}}
            <div style="flex:1; min-width:380px;">
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Nature</label>
                    <input type="text" name="nature" value="{{ old('nature', $supplier->nature) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Supplier Name <span style="color:red;">*</span></label>
                    <input type="text" name="supplier_name" value="{{ old('supplier_name', $supplier->supplier_name) }}" required
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    @error('supplier_name')
                        <p style="color:#dc3545; font-size:12px; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 1 <span style="color:red;">*</span></label>
                    <input type="text" name="address_line_1" value="{{ old('address_line_1', $supplier->address_line_1) }}" required
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    @error('address_line_1')
                        <p style="color:#dc3545; font-size:12px; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">City <span style="color:red;">*</span></label>
                    <input type="text" name="city" value="{{ old('city', $supplier->city) }}" required
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    @error('city')
                        <p style="color:#dc3545; font-size:12px; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Contact Person <span style="color:red;">*</span></label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" required
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    @error('contact_person')
                        <p style="color:#dc3545; font-size:12px; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Email</label>
                    <input type="email" name="email" value="{{ old('email', $supplier->email) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">GST</label>
                    <input type="text" name="gst" value="{{ old('gst', $supplier->gst) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">TAN</label>
                    <input type="text" name="tan" value="{{ old('tan', $supplier->tan) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Nature Of Work</label>
                    <textarea name="nature_of_work" rows="2"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('nature_of_work', $supplier->nature_of_work) }}</textarea>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Type Of Control</label>
                    <select name="type_of_control"
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        <option value="">Select</option>
                        @foreach($typeOfControlOptions as $opt)
                            <option value="{{ $opt }}" {{ old('type_of_control', $supplier->type_of_control) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Customer Approved</label>
                    <input type="text" name="customer_approved" value="{{ old('customer_approved', $supplier->customer_approved) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Supplier ISO Certified</label>
                    <input type="text" name="supplier_iso_certified" value="{{ old('supplier_iso_certified', $supplier->supplier_iso_certified) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Audit Frequency</label>
                    <select name="audit_frequency"
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        <option value="">Select</option>
                        @foreach($auditFrequencyOptions as $opt)
                            <option value="{{ $opt }}" {{ old('audit_frequency', $supplier->audit_frequency) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Revaluation Period</label>
                    <input type="date" name="revaluation_period" value="{{ old('revaluation_period', optional($supplier->revaluation_period)->format('Y-m-d')) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Remarks</label>
                    <textarea name="remarks" rows="2"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('remarks', $supplier->remarks) }}</textarea>
                </div>
            </div>

            {{-- Right column --}}
            <div style="flex:1; min-width:380px;">
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Supplier type</label>
                    <select name="supplier_type"
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        @foreach($supplierTypeOptions as $opt)
                            <option value="{{ $opt }}" {{ old('supplier_type', $supplier->supplier_type) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 2</label>
                    <input type="text" name="address_line_2" value="{{ old('address_line_2', $supplier->address_line_2) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">State <span style="color:red;">*</span></label>
                    <select name="state" id="state" required
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:white;">
                        <option value="">Select State</option>
                        @foreach($states as $state)
                            <option value="{{ $state }}" {{ old('state', $supplier->state) == $state ? 'selected' : '' }}>{{ $state }}</option>
                        @endforeach
                    </select>
                    @error('state')
                        <p style="color:#dc3545; font-size:12px; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Contact Number</label>
                    <input type="text" name="contact_number" value="{{ old('contact_number', $supplier->contact_number) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Code</label>
                    <select name="code"
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        <option value="">Select</option>
                        @foreach($codeOptions as $opt)
                            <option value="{{ $opt }}" {{ old('code', $supplier->code) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">PAN</label>
                    <input type="text" name="pan" value="{{ old('pan', $supplier->pan) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Items</label>
                    <textarea name="items" rows="2"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('items', $supplier->items) }}</textarea>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Material Grade</label>
                    <input type="text" name="material_grade" value="{{ old('material_grade', $supplier->material_grade) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Applicable statutory &amp; regulatory requirements</label>
                    <textarea name="applicable_requirements" rows="2"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('applicable_requirements', $supplier->applicable_requirements) }}</textarea>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Certificate Validity</label>
                    <input type="date" name="certificate_validity" value="{{ old('certificate_validity', optional($supplier->certificate_validity)->format('Y-m-d')) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Approved Date</label>
                    <input type="date" name="approved_date" value="{{ old('approved_date', optional($supplier->approved_date)->format('Y-m-d')) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Supplier Development</label>
                    <textarea name="supplier_development" rows="2"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('supplier_development', $supplier->supplier_development) }}</textarea>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Qms Status</label>
                    <select name="qms_status"
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        <option value="">Select</option>
                        @foreach($qmsStatusOptions as $opt)
                            <option value="{{ $opt }}" {{ old('qms_status', $supplier->qms_status) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div style="display:flex; gap:15px; margin-top:20px;">
            <a href="{{ route('suppliers.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Update Supplier
            </button>
        </div>
    </form>
</div>
@endsection


