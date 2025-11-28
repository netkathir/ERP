@extends('layouts.dashboard')

@section('title', 'Customer Complaint Details')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Customer Complaint Details</h2>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('customer-complaints.edit', $complaint->id) }}"
               style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('customer-complaints.index') }}"
               style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    {{-- Basic Information --}}
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Basic Info</h3>
        </div>
        <div style="padding:20px;">
            <div style="display:grid; grid-template-columns: repeat(2,1fr); gap:20px; margin-bottom:10px;">
                <div>
                    <strong style="color:#333;">Customer Name:</strong>
                    <p style="color:#666; margin:5px 0;">{{ $complaint->customer_name }}</p>
                </div>
                <div>
                    <strong style="color:#333;">Customer Address:</strong>
                    <p style="color:#666; margin:5px 0;">{{ $complaint->customer_address }}</p>
                </div>
                <div>
                    <strong style="color:#333;">Site Address:</strong>
                    <p style="color:#666; margin:5px 0;">{{ $complaint->site_address }}</p>
                </div>
                <div>
                    <strong style="color:#333;">Product:</strong>
                    <p style="color:#666; margin:5px 0;">{{ optional($complaint->product)->name }}</p>
                </div>
                <div>
                    <strong style="color:#333;">Attended By:</strong>
                    <p style="color:#666; margin:5px 0;">{{ optional($complaint->attendedBy)->name }}</p>
                </div>
                <div>
                    <strong style="color:#333;">Complaint:</strong>
                    <p style="color:#666; margin:5px 0;">{{ $complaint->complaint_type }}</p>
                </div>
                <div>
                    <strong style="color:#333;">Complaint Reg No:</strong>
                    <p style="color:#666; margin:5px 0;">{{ $complaint->complaint_reg_no }}</p>
                </div>
                <div>
                    <strong style="color:#333;">Complaint Date:</strong>
                    <p style="color:#666; margin:5px 0;">{{ optional($complaint->complaint_date)->format('d-m-Y') }}</p>
                </div>
                <div>
                    <strong style="color:#333;">Quantity:</strong>
                    <p style="color:#666; margin:5px 0;">{{ $complaint->quantity }}</p>
                </div>
                <div>
                    <strong style="color:#333;">Status:</strong>
                    <p style="color:#666; margin:5px 0;">{{ $complaint->status }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Complaint Details & Actions --}}
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Details & Actions</h3>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <div>
                <strong style="color:#333; display:block; margin-bottom:5px;">Complaint Details:</strong>
                <p style="color:#666; margin:0 0 15px 0;">{{ $complaint->complaint_details }}</p>

                <strong style="color:#333; display:block; margin-bottom:5px;">Remarks From Customer:</strong>
                <p style="color:#666; margin:0 0 15px 0;">{{ $complaint->remarks_from_customer }}</p>

                <strong style="color:#333; display:block; margin-bottom:5px;">Root Cause Analysis:</strong>
                <p style="color:#666; margin:0 0 15px 0;">{{ $complaint->root_cause_analysis }}</p>

                <strong style="color:#333; display:block; margin-bottom:5px;">Internal Remarks:</strong>
                <p style="color:#666; margin:0 0 15px 0;">{{ $complaint->remarks }}</p>
            </div>
            <div>
                <strong style="color:#333; display:block; margin-bottom:5px;">Correction:</strong>
                <p style="color:#666; margin:0 0 15px 0;">{{ $complaint->correction }}</p>

                <strong style="color:#333; display:block; margin-bottom:5px;">Preventive Action:</strong>
                <p style="color:#666; margin:0 0 15px 0;">{{ $complaint->preventive_action }}</p>

                <strong style="color:#333; display:block; margin-bottom:5px;">Corrective Action:</strong>
                <p style="color:#666; margin:0 0 15px 0;">{{ $complaint->corrective_action }}</p>

                <strong style="color:#333; display:block; margin-bottom:5px;">Location:</strong>
                <p style="color:#666; margin:0 0 15px 0;">{{ $complaint->location }}</p>

                <strong style="color:#333; display:block; margin-bottom:5px;">Closed On:</strong>
                <p style="color:#666; margin:0 0 15px 0;">{{ optional($complaint->closed_on)->format('d-m-Y') }}</p>

                @if($complaint->attachment_path)
                    <strong style="color:#333; display:block; margin-bottom:5px;">Attachment:</strong>
                    <a href="{{ asset('storage/'.$complaint->attachment_path) }}" target="_blank" style="color:#667eea;">
                        View File
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


