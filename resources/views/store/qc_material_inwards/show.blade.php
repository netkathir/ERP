@extends('layouts.dashboard')

@section('title', 'QC Material Inward - View')

@push('styles')
<style>
    .qc-view-container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    .qc-view-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .qc-view-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
    }
    
    .qc-view-section {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        width: 100%;
        box-sizing: border-box;
    }
    
    .qc-view-table-wrapper {
        padding: 20px;
        overflow-x: auto;
        width: 100%;
        box-sizing: border-box;
    }
    
    .qc-view-table {
        width: 100%;
        min-width: 1200px;
        border-collapse: collapse;
    }
    
    .qc-view-table th,
    .qc-view-table td {
        padding: 8px;
        white-space: nowrap;
        font-size: 13px;
    }
    
    @media screen and (max-width: 1200px) {
        .qc-view-grid {
            grid-template-columns: 1fr 1fr;
        }
        .qc-view-table {
            min-width: 1000px;
        }
    }
    
    @media screen and (max-width: 992px) {
        .qc-view-container {
            padding: 15px;
        }
        
        .qc-view-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .qc-view-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .qc-view-table-wrapper {
            padding: 15px;
        }
        
        .qc-view-table {
            min-width: 900px;
            font-size: 12px;
        }
        
        .qc-view-table th,
        .qc-view-table td {
            padding: 6px;
            font-size: 12px;
        }
    }
    
    @media screen and (max-width: 768px) {
        .qc-view-container {
            padding: 10px;
        }
        
        .qc-view-table {
            min-width: 800px;
        }
        
        .qc-view-table th,
        .qc-view-table td {
            padding: 5px;
            font-size: 11px;
        }
    }
</style>
@endpush

@section('content')
<div class="qc-view-container">
    <div class="qc-view-header">
        <h2 style="color:#333; font-size:24px; margin:0;">QC Material Inward - View</h2>
        <a href="{{ route('qc-material-inwards.index') }}" style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px; white-space:nowrap;">
            <i class="fas fa-list"></i> List
        </a>
    </div>

    {{-- Header Information --}}
    <div class="qc-view-section">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Header Information</h3>
        </div>
        <div style="padding:20px;" class="qc-view-grid">
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">QC Material No</label>
                <div style="padding:10px; background:#f8f9fa; border:1px solid #ddd; border-radius:5px; font-size:14px; color:#333;">
                    {{ $qcMaterialInward->qc_material_no }}
                </div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">Purchase Order No</label>
                <div style="padding:10px; background:#f8f9fa; border:1px solid #ddd; border-radius:5px; font-size:14px; color:#333;">
                    {{ optional($qcMaterialInward->purchaseOrder)->po_no ?? 'N/A' }}
                </div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">MRN No</label>
                <div style="padding:10px; background:#f8f9fa; border:1px solid #ddd; border-radius:5px; font-size:14px; color:#333;">
                    {{ optional($qcMaterialInward->materialInward)->material_inward_no ?? 'N/A' }}
                </div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">Supplier Name</label>
                <div style="padding:10px; background:#f8f9fa; border:1px solid #ddd; border-radius:5px; font-size:14px; color:#333;">
                    {{ optional($qcMaterialInward->supplier)->supplier_name ?? 'N/A' }}
                </div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">Created By</label>
                <div style="padding:10px; background:#f8f9fa; border:1px solid #ddd; border-radius:5px; font-size:14px; color:#333;">
                    {{ optional($qcMaterialInward->creator)->name ?? 'N/A' }}
                </div>
            </div>
            <div>
                <label style="display:block; margin-bottom:6px; color:#666; font-weight:500; font-size:12px;">Created On</label>
                <div style="padding:10px; background:#f8f9fa; border:1px solid #ddd; border-radius:5px; font-size:14px; color:#333;">
                    {{ optional($qcMaterialInward->created_at)->setTimezone('Asia/Kolkata')->format('d-m-Y h:i A') ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    {{-- QC Details Table --}}
    <div class="qc-view-section">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">QC Details</h3>
        </div>
        <div class="qc-view-table-wrapper">
            @if($qcMaterialInward->items->count() > 0)
                <table class="qc-view-table">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:10px; text-align:left; color:#333; font-weight:600;">Item Description</th>
                            <th style="padding:10px; text-align:right; color:#333; font-weight:600;">Received Qty</th>
                            <th style="padding:10px; text-align:left; color:#333; font-weight:600;">Unit</th>
                            <th style="padding:10px; text-align:right; color:#333; font-weight:600;">Received Qty in Kg</th>
                            <th style="padding:10px; text-align:left; color:#333; font-weight:600;">Batch No</th>
                            <th style="padding:10px; text-align:left; color:#333; font-weight:600;">Supplier Invoice No</th>
                            <th style="padding:10px; text-align:left; color:#333; font-weight:600;">Invoice Date</th>
                            <th style="padding:10px; text-align:right; color:#333; font-weight:600;">Given Qty</th>
                            <th style="padding:10px; text-align:right; color:#333; font-weight:600;">Accepted Qty</th>
                            <th style="padding:10px; text-align:right; color:#333; font-weight:600;">Rejected Qty</th>
                            <th style="padding:10px; text-align:left; color:#333; font-weight:600;">Rejection Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($qcMaterialInward->items as $item)
                            <tr style="border-bottom:1px solid #dee2e6;">
                                <td style="padding:10px; color:#333;">{{ $item->item_description ?? 'N/A' }}</td>
                                <td style="padding:10px; text-align:right; color:#333;">{{ (int)round((float)$item->received_qty) }}</td>
                                <td style="padding:10px; color:#333;">{{ optional($item->unit)->symbol ?? 'N/A' }}</td>
                                <td style="padding:10px; text-align:right; color:#333;">{{ $item->received_qty_in_kg ? number_format((float)$item->received_qty_in_kg, 3) : 'N/A' }}</td>
                                <td style="padding:10px; color:#333;">{{ $item->batch_no ?? 'N/A' }}</td>
                                <td style="padding:10px; color:#333;">{{ $item->supplier_invoice_no ?? 'N/A' }}</td>
                                <td style="padding:10px; color:#333;">{{ optional($item->invoice_date)->format('d-m-Y') ?? 'N/A' }}</td>
                                <td style="padding:10px; text-align:right; color:#333;">{{ (int)round((float)$item->given_qty) }}</td>
                                <td style="padding:10px; text-align:right; color:#333; font-weight:500;">{{ (int)round((float)$item->accepted_qty) }}</td>
                                <td style="padding:10px; text-align:right; color:#333;">{{ (int)round((float)$item->rejected_qty) }}</td>
                                <td style="padding:10px; color:#333;">
                                    @if($item->rejection_reason)
                                        <div style="min-width:150px; max-width:250px; word-wrap:break-word;">{{ $item->rejection_reason }}</div>
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="margin:0; color:#666; text-align:center; padding:20px;">No QC items found.</p>
            @endif
        </div>
    </div>
</div>
@endsection

