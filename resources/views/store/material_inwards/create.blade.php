@extends('layouts.dashboard')

@section('title', 'Material Inward - Create')

@section('content')
@php
    $displayDate = function ($value) {
        if (empty($value)) {
            return '';
        }
        try {
            return \Carbon\Carbon::parse($value)->format('d-m-Y');
        } catch (\Exception $e) {
            return $value;
        }
    };
@endphp
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Material Inward</h2>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('material-inwards.index') }}" style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-list"></i> List
            </a>
        </div>
    </div>  

    @if($errors->any())
        <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:5px; margin-bottom:20px; border:1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin:10px 0 0 20px; padding:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('material-inwards.store') }}" method="POST" id="materialInwardForm">
        @csrf

        {{-- Header Fields --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Header Information</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; align-items:flex-end;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Material Inward</label>
                    <input type="text" value="{{ $materialInwardNo }}" readonly
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Purchase Order No <span style="color:red;">*</span></label>
                    <select name="purchase_order_id" id="purchase_order_id" required
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        <option value="">Select Purchase Order</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}" {{ old('purchase_order_id') == $po->id ? 'selected' : '' }}>
                                {{ $po->po_no }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Supplier Name</label>
                    <input type="text" id="supplier_name" value="" readonly
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;">
                </div>
            </div>
        </div>

        {{-- Item Details Table --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Item Details</h3>
            </div>
            <div style="padding:20px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;" id="itemsTable">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:10px; text-align:left; color:#333;">Item Name <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:right; color:#333;">PO Qty</th>
                            <th style="padding:10px; text-align:right; color:#333;">Pending Qty</th>
                            <th style="padding:10px; text-align:right; color:#333;">Received Qty <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333;">Unit</th>
                            <th style="padding:10px; text-align:right; color:#333;">Received Qty in Kg <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333;">Batch No</th>
                            <th style="padding:10px; text-align:right; color:#333;">Cost/unit</th>
                            <th style="padding:10px; text-align:right; color:#333;">Total</th>
                            <th style="padding:10px; text-align:left; color:#333;">Supplier Invoice No <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333;">Invoice date <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:center; color:#333; width:80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <tr>
                            <td colspan="12" style="padding:20px; text-align:center; color:#666;">
                                Please select a Purchase Order to load items
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Remarks --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Remarks</h3>
            </div>
            <div style="padding:20px;">
                <textarea name="remarks" rows="3"
                          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div style="display:flex; gap:15px; margin-top:10px;">
            <button type="submit" style="padding:12px 24px; background:#667eea; color:white; border:none; border-radius:5px; font-weight:500; cursor:pointer;">
                Submit
            </button>
            <a href="{{ route('material-inwards.index') }}" style="padding:12px 24px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500;">
                Cancel
            </a>
        </div>
    </form>
</div>

@include('store.material_inwards.partials.scripts')
@endsection

