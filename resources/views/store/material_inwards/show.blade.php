@extends('layouts.dashboard')

@section('title', 'Material Inward - View')

@section('content')
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Material Inward Details</h2>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('material-inwards.edit', $materialInward->id) }}" style="padding:10px 20px; background:#ffc107; color:#212529; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('material-inwards.index') }}" style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-list"></i> List
            </a>
        </div>
    </div>

    {{-- Header Information --}}
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Header Information</h3>
        </div>
        <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500;">Material Inward No</label>
                <div style="padding:10px; background:#f8f9fa; border:1px solid #ddd; border-radius:5px; font-size:14px;">{{ $materialInward->material_inward_no }}</div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500;">Purchase Order No</label>
                <div style="padding:10px; background:#f8f9fa; border:1px solid #ddd; border-radius:5px; font-size:14px;">{{ optional($materialInward->purchaseOrder)->po_no ?? 'N/A' }}</div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500;">Supplier Name</label>
                <div style="padding:10px; background:#f8f9fa; border:1px solid #ddd; border-radius:5px; font-size:14px;">{{ optional($materialInward->supplier)->supplier_name ?? 'N/A' }}</div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500;">Created By</label>
                <div style="padding:10px; background:#f8f9fa; border:1px solid #ddd; border-radius:5px; font-size:14px;">{{ optional($materialInward->creator)->name ?? 'N/A' }}</div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500;">Created At</label>
                <div style="padding:10px; background:#f8f9fa; border:1px solid #ddd; border-radius:5px; font-size:14px;">{{ optional($materialInward->created_at)->format('d-m-Y H:i') ?? 'N/A' }}</div>
            </div>
        </div>
    </div>

    {{-- Item Details --}}
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Item Details</h3>
        </div>
        <div style="padding:20px; overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                        <th style="padding:10px; text-align:left; color:#333;">Item Description</th>
                        <th style="padding:10px; text-align:right; color:#333;">PO Qty</th>
                        <th style="padding:10px; text-align:right; color:#333;">Pending Qty</th>
                        <th style="padding:10px; text-align:right; color:#333;">Received Qty</th>
                        <th style="padding:10px; text-align:left; color:#333;">Unit</th>
                        <th style="padding:10px; text-align:right; color:#333;">Received Qty in Kg</th>
                        <th style="padding:10px; text-align:left; color:#333;">Batch No</th>
                        <th style="padding:10px; text-align:right; color:#333;">Cost/unit</th>
                        <th style="padding:10px; text-align:right; color:#333;">Total</th>
                        <th style="padding:10px; text-align:left; color:#333;">Supplier Invoice No</th>
                        <th style="padding:10px; text-align:left; color:#333;">Invoice date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($materialInward->items as $item)
                        <tr style="border-bottom:1px solid #dee2e6;">
                            <td style="padding:10px; color:#333;">{{ $item->item_description ?? optional($item->rawMaterial)->name ?? 'N/A' }}</td>
                            <td style="padding:10px; text-align:right; color:#333;">{{ number_format($item->po_qty, 3) }}</td>
                            <td style="padding:10px; text-align:right; color:#333;">{{ number_format($item->pending_qty, 3) }}</td>
                            <td style="padding:10px; text-align:right; color:#333;">{{ number_format($item->received_qty, 3) }}</td>
                            <td style="padding:10px; color:#333;">{{ optional($item->unit)->symbol ?? 'N/A' }}</td>
                            <td style="padding:10px; text-align:right; color:#333;">{{ $item->received_qty_in_kg ? number_format($item->received_qty_in_kg, 3) : 'N/A' }}</td>
                            <td style="padding:10px; color:#333;">{{ $item->batch_no ?? 'N/A' }}</td>
                            <td style="padding:10px; text-align:right; color:#333;">{{ number_format($item->cost_per_unit, 2) }}</td>
                            <td style="padding:10px; text-align:right; color:#333;">{{ number_format($item->total, 2) }}</td>
                            <td style="padding:10px; color:#333;">{{ $item->supplier_invoice_no }}</td>
                            <td style="padding:10px; color:#333;">{{ optional($item->invoice_date)->format('d-m-Y') ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="padding:20px; text-align:center; color:#666;">No items found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($materialInward->remarks)
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Remarks</h3>
            </div>
            <div style="padding:20px;">
                <div style="padding:10px; background:#f8f9fa; border:1px solid #ddd; border-radius:5px; font-size:14px; white-space:pre-wrap;">{{ $materialInward->remarks }}</div>
            </div>
        </div>
    @endif
</div>
@endsection

