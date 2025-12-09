@extends('layouts.dashboard')

@section('title', 'Supplier Details - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Supplier Details</h2>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('suppliers.edit', $supplier->id) }}"
               style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('suppliers.index') }}"
               style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    {{-- Basic Info --}}
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Basic Info</h3>
        </div>
        <div style="padding:20px;">
            <div style="display:grid; grid-template-columns: repeat(2,1fr); gap:20px;">
                <div>
                    <strong>Nature:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->nature }}</p>
                </div>
                <div>
                    <strong>Supplier Name:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->supplier_name }}</p>
                </div>
                <div>
                    <strong>Supplier Type:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->supplier_type }}</p>
                </div>
                <div>
                    <strong>Code:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->code }}</p>
                </div>
                <div>
                    <strong>GST:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->gst }}</p>
                </div>
                <div>
                    <strong>TAN:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->tan }}</p>
                </div>
                <div>
                    <strong>PAN:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->pan }}</p>
                </div>
                <div>
                    <strong>Contact Person:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->contact_person }}</p>
                </div>
                <div>
                    <strong>Contact Number:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->contact_number }}</p>
                </div>
                <div>
                    <strong>Email:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->email }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Address & Work --}}
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Address & Work</h3>
        </div>
        <div style="padding:20px;">
            <div style="display:grid; grid-template-columns: repeat(2,1fr); gap:20px;">
                <div>
                    <strong>Address Line 1:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->address_line_1 }}</p>
                </div>
                <div>
                    <strong>Address Line 2:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->address_line_2 }}</p>
                </div>
                <div>
                    <strong>City:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->city }}</p>
                </div>
                <div>
                    <strong>State:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->state }}</p>
                </div>
                <div style="grid-column:1 / span 2;">
                    <strong>Nature Of Work:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->nature_of_work }}</p>
                </div>
                <div style="grid-column:1 / span 2;">
                    <strong>Items:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->items }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quality & Approvals --}}
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Quality & Approvals</h3>
        </div>
        <div style="padding:20px;">
            <div style="display:grid; grid-template-columns: repeat(2,1fr); gap:20px;">
                <div>
                    <strong>Type Of Control:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->type_of_control }}</p>
                </div>
                <div>
                    <strong>Audit Frequency:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->audit_frequency }}</p>
                </div>
                <div>
                    <strong>Customer Approved:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->customer_approved }}</p>
                </div>
                <div>
                    <strong>Supplier ISO Certified:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->supplier_iso_certified }}</p>
                </div>
                <div>
                    <strong>Material Grade:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->material_grade }}</p>
                </div>
                <div>
                    <strong>Qms Status:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->qms_status }}</p>
                </div>
                <div>
                    <strong>Revaluation Period:</strong>
                    <p style="margin:5px 0; color:#555;">{{ optional($supplier->revaluation_period)->format('d-m-Y') }}</p>
                </div>
                <div>
                    <strong>Certificate Validity:</strong>
                    <p style="margin:5px 0; color:#555;">{{ optional($supplier->certificate_validity)->format('d-m-Y') }}</p>
                </div>
                <div>
                    <strong>Approved Date:</strong>
                    <p style="margin:5px 0; color:#555;">{{ optional($supplier->approved_date)->format('d-m-Y') }}</p>
                </div>
                <div style="grid-column:1 / span 2;">
                    <strong>Applicable statutory &amp; regulatory requirements:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->applicable_requirements }}</p>
                </div>
                <div style="grid-column:1 / span 2;">
                    <strong>Supplier Development:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->supplier_development }}</p>
                </div>
                <div style="grid-column:1 / span 2;">
                    <strong>Remarks:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $supplier->remarks }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


