@extends('layouts.dashboard')

@section('title', 'Customer Complaint Register - Edit')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Customer Complaint</h2>
        <a href="{{ route('customer-complaints.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if(session('error'))
        <div style="background:#f8d7da;color:#721c24;padding:10px 15px;border-radius:4px;margin-bottom:15px;">
            {{ session('error') }}
        </div>
    @endif

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

    <form action="{{ route('customer-complaints.update', $complaint->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Complaint & Customer Details card --}}
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Customer & Complaint Details</h3>
            </div>
            <div style="padding: 20px;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                            Customer Name <span style="color:red;">*</span>
                        </label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $complaint->customer_name) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;" required>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">Customer Address</label>
                        <textarea name="customer_address" rows="2"
                                  style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('customer_address', $complaint->customer_address) }}</textarea>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(2,1fr); gap:15px; margin-bottom:15px;">
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                            Site Address <span style="color:red;">*</span>
                        </label>
                        <input type="text" name="site_address" value="{{ old('site_address', $complaint->site_address) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                            Customer Complaint RegNo
                        </label>
                        <input type="text" name="complaint_reg_no" value="{{ old('complaint_reg_no', $complaint->complaint_reg_no) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(3,1fr); gap:15px; margin-bottom:15px;">
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                            Product Name <span style="color:red;">*</span>
                        </label>
                        <select name="product_id"
                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;"
                                required>
                            <option value="">Select</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id', $complaint->product_id) == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                            Attended By <span style="color:red;">*</span>
                        </label>
                        <select name="attended_by_id"
                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;"
                                required>
                            <option value="">Select</option>
                            @foreach($attendedByUsers as $u)
                                @if($u->isActive())
                                    <option value="{{ $u->id }}" {{ old('attended_by_id', $complaint->attended_by_id) == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                            Quantity
                        </label>
                        <input type="number" step="0.001" name="quantity" value="{{ old('quantity', $complaint->quantity) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(3,1fr); gap:15px;">
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                            Complaint <span style="color:red;">*</span>
                        </label>
                        <select name="complaint_type"
                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;"
                                required>
                            @foreach($defaultComplaintTypes as $type)
                                <option value="{{ $type }}" {{ old('complaint_type', $complaint->complaint_type) == $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                            Complaint Date <span style="color:red;">*</span>
                        </label>
                        <input type="date" name="complaint_date"
                               value="{{ old('complaint_date', optional($complaint->complaint_date)->format('Y-m-d')) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;"
                               required>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                            Closed On
                        </label>
                        <input type="date" name="closed_on"
                               value="{{ old('closed_on', optional($complaint->closed_on)->format('Y-m-d')) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                </div>
            </div>
        </div>

        {{-- Analysis & Actions card --}}
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Root Cause & Actions</h3>
            </div>
            <div style="padding: 20px;">
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                        Complaint Details
                    </label>
                    <textarea name="complaint_details" rows="3"
                              style="width:100%; padding:10px; border:1px solid:#ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('complaint_details', $complaint->complaint_details) }}</textarea>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                        Remarks From Customer
                    </label>
                    <textarea name="remarks_from_customer" rows="2"
                              style="width:100%; padding:10px; border:1px solid:#ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('remarks_from_customer', $complaint->remarks_from_customer) }}</textarea>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                        Root Cause Analysis <span style="color:red;">*</span>
                    </label>
                    <textarea name="root_cause_analysis" rows="3"
                              style="width:100%; padding:10px; border:1px solid:#ddd; border-radius:5px; font-size:14px; resize:vertical;"
                              required>{{ old('root_cause_analysis', $complaint->root_cause_analysis) }}</textarea>
                </div>

                <div style="display:grid; grid-template-columns: repeat(2,1fr); gap:15px; margin-bottom:15px;">
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                            Correction <span style="color:red;">*</span>
                        </label>
                        <textarea name="correction" rows="2"
                                  style="width:100%; padding:10px; border:1px solid:#ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('correction', $complaint->correction) }}</textarea>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                            Corrective Action
                        </label>
                        <textarea name="corrective_action" rows="2"
                                  style="width:100%; padding:10px; border:1px solid:#ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('corrective_action', $complaint->corrective_action) }}</textarea>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(2,1fr); gap:15px;">
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                            Preventive Action
                        </label>
                        <textarea name="preventive_action" rows="2"
                                  style="width:100%; padding:10px; border:1px solid:#ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('preventive_action', $complaint->preventive_action) }}</textarea>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                            Location
                        </label>
                        <input type="text" name="location" value="{{ old('location', $complaint->location) }}"
                               style="width:100%; padding:10px; border:1px solid:#ddd; border-radius:5px; font-size:14px;">
                    </div>
                </div>
            </div>
        </div>

        {{-- Status & Attachments card --}}
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Status & Attachments</h3>
            </div>
            <div style="padding: 20px; display:grid; grid-template-columns: repeat(2,1fr); gap:15px;">
                <div>
                    <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                        Status <span style="color:red;">*</span>
                    </label>
                    @php
                        $statuses = ['Open', 'In Progress', 'Closed'];
                    @endphp
                    <select name="status"
                            style="width:100%; padding:10px; border:1px solid:#ddd; border-radius:5px; font-size:14px;"
                            required>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ old('status', $complaint->status) == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                        File Upload
                    </label>
                    <input type="file" name="attachment"
                           style="width:100%; padding:10px; border:1px solid:#ddd; border-radius:5px; font-size:14px;">
                    @if($complaint->attachment_path)
                        <p style="margin-top:5px;font-size:13px;">
                            Current file:
                            <a href="{{ asset('storage/'.$complaint->attachment_path) }}" target="_blank">View</a>
                        </p>
                    @endif
                </div>
                <div style="grid-column: 1 / span 2;">
                    <label style="display:block; margin-bottom:8px; color:#333; font-weight:500;">
                        Remarks <span style="color:red;">*</span>
                    </label>
                    <textarea name="remarks" rows="2"
                              style="width:100%; padding:10px; border:1px solid:#ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('remarks', $complaint->remarks) }}</textarea>
                </div>
            </div>
        </div>

        <div style="display:flex; gap:15px; margin-top:10px;">
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Update Complaint
            </button>
            <a href="{{ route('customer-complaints.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection


