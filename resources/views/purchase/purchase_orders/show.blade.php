@extends('layouts.dashboard')

@section('title', 'Purchase Order - View')

@section('content')
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Purchase Order Details</h2>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('purchase-orders.edit', $purchaseOrder->id) }}" style="padding:10px 20px; background:#ffc107; color:#212529; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('purchase-orders.index') }}" style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-list"></i> List
            </a>
        </div>
    </div>

    {{-- General Information --}}
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">General Information</h3>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">Purchase Order No</label>
                <div style="padding:10px; background:#f8f9fa; border-radius:5px; color:#333;">{{ $purchaseOrder->po_no }}</div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">Purchase Indent No</label>
                <div style="padding:10px; background:#f8f9fa; border-radius:5px; color:#333;">{{ $purchaseOrder->purchaseIndent->indent_no ?? 'N/A' }}</div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">Status</label>
                <div style="padding:10px; background:#f8f9fa; border-radius:5px; color:#333;">{{ $purchaseOrder->status }}</div>
            </div>
        </div>
    </div>

    {{-- Ship To Section --}}
    @if($purchaseOrder->ship_to)
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Ship To</h3>
        </div>
        <div style="padding:20px;">
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">Ship To</label>
                <div style="padding:10px; background:#f8f9fa; border-radius:5px; color:#333;">{{ $purchaseOrder->ship_to }}</div>
            </div>
            @if($purchaseOrder->ship_to_address_line_1)
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div><strong>Address Line 1:</strong> {{ $purchaseOrder->ship_to_address_line_1 }}</div>
                <div><strong>Address Line 2:</strong> {{ $purchaseOrder->ship_to_address_line_2 }}</div>
                <div><strong>City:</strong> {{ $purchaseOrder->ship_to_city }}</div>
                <div><strong>State:</strong> {{ $purchaseOrder->ship_to_state }}</div>
                <div><strong>Pincode:</strong> {{ $purchaseOrder->ship_to_pincode }}</div>
                <div><strong>Email:</strong> {{ $purchaseOrder->ship_to_email }}</div>
                <div><strong>Contact No:</strong> {{ $purchaseOrder->ship_to_contact_no }}</div>
                <div><strong>GST No:</strong> {{ $purchaseOrder->ship_to_gst_no }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Supplier Address --}}
    @if($purchaseOrder->supplier)
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Supplier Address</h3>
        </div>
        <div style="padding:20px;">
            <div style="margin-bottom:15px;">
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">Supplier</label>
                <div style="padding:10px; background:#f8f9fa; border-radius:5px; color:#333;">{{ $purchaseOrder->supplier->supplier_name }}</div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div><strong>Address Line 1:</strong> {{ $purchaseOrder->supplier_address_line_1 }}</div>
                <div><strong>Address Line 2:</strong> {{ $purchaseOrder->supplier_address_line_2 }}</div>
                <div><strong>City:</strong> {{ $purchaseOrder->supplier_city }}</div>
                <div><strong>State:</strong> {{ $purchaseOrder->supplier_state }}</div>
                <div><strong>Email:</strong> {{ $purchaseOrder->supplier_email }}</div>
                <div><strong>GST No:</strong> {{ $purchaseOrder->supplier_gst_no }}</div>
            </div>
        </div>
    </div>
    @endif

    {{-- Product Information --}}
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Product Information</h3>
        </div>
        <div style="padding:20px; overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                        <th style="padding:10px; text-align:left; color:#333;">Item Name</th>
                        <th style="padding:10px; text-align:left; color:#333;">Description</th>
                        <th style="padding:10px; text-align:right; color:#333;">PO Quantity</th>
                        <th style="padding:10px; text-align:left; color:#333;">Unit</th>
                        <th style="padding:10px; text-align:right; color:#333;">Price</th>
                        <th style="padding:10px; text-align:right; color:#333;">Amount</th>
                        <th style="padding:10px; text-align:left; color:#333;">PO Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->items as $item)
                        <tr style="border-bottom:1px solid #dee2e6;">
                            <td style="padding:10px;">{{ $item->item_name }}</td>
                            <td style="padding:10px;">{{ $item->item_description }}</td>
                            <td style="padding:10px; text-align:right;">{{ number_format((int)$item->po_quantity, 0) }}</td>
                            <td style="padding:10px;">{{ optional($item->unit)->symbol ?? 'N/A' }}</td>
                            <td style="padding:10px; text-align:right;">₹{{ number_format($item->price, 2) }}</td>
                            <td style="padding:10px; text-align:right;">₹{{ number_format($item->amount, 2) }}</td>
                            <td style="padding:10px;">{{ $item->po_status ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Amount Calculation --}}
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Amount Calculation</h3>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr 1fr 1fr 1fr; gap:20px;">
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">GST</label>
                <div style="padding:10px; background:#f8f9fa; border-radius:5px; color:#333;">₹{{ number_format($purchaseOrder->gst, 2) }}</div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">SGST</label>
                <div style="padding:10px; background:#f8f9fa; border-radius:5px; color:#333;">₹{{ number_format($purchaseOrder->sgst, 2) }}</div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">Total</label>
                <div style="padding:10px; background:#f8f9fa; border-radius:5px; color:#333;">₹{{ number_format($purchaseOrder->total, 2) }}</div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">Discount</label>
                <div style="padding:10px; background:#f8f9fa; border-radius:5px; color:#333;">₹{{ number_format($purchaseOrder->discount, 2) }}</div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">Net Amount</label>
                <div style="padding:10px; background:#f8f9fa; border-radius:5px; color:#333; font-weight:600;">₹{{ number_format($purchaseOrder->net_amount, 2) }}</div>
            </div>
        </div>
    </div>

    @if($purchaseOrder->remarks)
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Remarks</h3>
        </div>
        <div style="padding:20px;">
            <p style="margin:0; color:#333;">{{ $purchaseOrder->remarks }}</p>
        </div>
    </div>
    @endif
</div>
@endsection

