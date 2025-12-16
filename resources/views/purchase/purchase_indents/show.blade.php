@extends('layouts.dashboard')

@section('title', 'Purchase Indent Details')

@section('content')
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Purchase Indent Details</h2>
        <div style="display:flex; gap:10px;">
            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('purchase-indents', 'approve'))
                @if($indent->status !== 'Approved')
                    <form action="{{ route('purchase-indents.approve', $indent->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" 
                                style="padding:10px 20px; background:#28a745; color:white; border:none; border-radius:5px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:8px;"
                                onclick="return confirm('Are you sure you want to approve this Purchase Indent?');">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    </form>
                @endif
            @endif
            <a href="{{ route('purchase-indents.edit', $indent->id) }}"
               style="padding:10px 20px; background:#ffc107; color:#212529; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('purchase-indents.index') }}"
               style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    {{-- General Info --}}
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">General Information</h3>
        </div>
        <div style="padding:20px;">
            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:20px;">
                <div style="margin-bottom:10px;">
                    <strong>Indent No:</strong> {{ $indent->indent_no }}
                </div>
                <div style="margin-bottom:10px;">
                    <strong>Indent Date:</strong> {{ optional($indent->indent_date)->format('d-m-Y') }}
                </div>
                <div style="margin-bottom:10px;">
                    <strong>Created By:</strong> {{ optional($indent->creator)->name }}
                </div>
                <div style="margin-bottom:10px;">
                    <strong>Status:</strong> {{ $indent->status }}
                </div>
                <div style="grid-column:1 / span 2; margin-bottom:10px;">
                    <strong>Remarks:</strong> {{ $indent->remarks }}
                </div>
                @if($indent->upload_path)
                <div style="grid-column:1 / span 2; margin-bottom:10px;">
                    <strong>Attachment:</strong> 
                    <a href="{{ asset('storage/'.$indent->upload_path) }}" target="_blank">
                        {{ $indent->upload_original_name }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Items --}}
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Indent Items</h3>
        </div>
        <div style="padding:20px; overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                        <th style="padding:10px; text-align:left; color:#333;">Item</th>
                        <th style="padding:10px; text-align:left; color:#333;">Description</th>
                        <th style="padding:10px; text-align:right; color:#333;">Qty</th>
                        <th style="padding:10px; text-align:left; color:#333;">Unit</th>
                        <th style="padding:10px; text-align:left; color:#333;">Schedule Date</th>
                        <th style="padding:10px; text-align:left; color:#333;">Supplier</th>
                        <th style="padding:10px; text-align:left; color:#333;">Special Instructions</th>
                        <th style="padding:10px; text-align:left; color:#333;">PO Status</th>
                        <th style="padding:10px; text-align:left; color:#333;">LR Details / Agency</th>
                        <th style="padding:10px; text-align:right; color:#333;">Delivered Qty</th>
                        <th style="padding:10px; text-align:left; color:#333;">Delivery Status</th>
                        <th style="padding:10px; text-align:left; color:#333;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($indent->items as $item)
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">{{ optional($item->rawMaterial)->name }}</td>
                            <td style="padding:8px 10px;">{{ $item->item_description }}</td>
                            <td style="padding:8px 10px; text-align:right;">{{ (int)round((float)$item->quantity) }}</td>
                            <td style="padding:8px 10px;">{{ optional($item->unit)->symbol ?? optional($item->unit)->name }}</td>
                            <td style="padding:8px 10px;">{{ optional($item->schedule_date)->format('d-m-Y') }}</td>
                            <td style="padding:8px 10px;">{{ optional($item->supplier)->supplier_name }}</td>
                            <td style="padding:8px 10px;">{{ $item->special_instructions }}</td>
                            <td style="padding:8px 10px;">{{ $item->po_status }}</td>
                            <td style="padding:8px 10px;">{{ $item->lr_details }} {{ $item->booking_agency ? ' / '.$item->booking_agency : '' }}</td>
                            <td style="padding:8px 10px; text-align:right;">{{ $item->delivered_qty }}</td>
                            <td style="padding:8px 10px;">{{ $item->delivery_status }}</td>
                            <td style="padding:8px 10px;">{{ $item->po_remarks }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:15px; padding-top:15px; border-top:1px solid #dee2e6; text-align:left;">
                <strong style="color:#333; font-size:14px;">Total Items: {{ $indent->items->count() }}</strong>
            </div>
        </div>
    </div>
</div>
@endsection


