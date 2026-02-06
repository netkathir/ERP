@extends('layouts.dashboard')

@section('title', 'View Work Order - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Work Order Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('work-orders.edit', $workOrder->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('work-orders.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 25px; border-radius: 5px;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 15px;">
            <div style="color: #666; font-weight: 500;">Work Order No:</div>
            <div style="color: #333; font-weight: 600;">{{ $workOrder->work_order_no }}</div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 15px;">
            <div style="color: #666; font-weight: 500;">Production Order No:</div>
            <div style="color: #333;">{{ $workOrder->production_order_no ?? 'N/A' }}</div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 15px;">
            <div style="color: #666; font-weight: 500;">Customer PO No:</div>
            <div style="color: #333;">{{ $workOrder->customer_po_no ?? 'N/A' }}</div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 15px;">
            <div style="color: #666; font-weight: 500;">Sales Type:</div>
            <div style="color: #333;">{{ $workOrder->sales_type ?? 'N/A' }}</div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 15px;">
            <div style="color: #666; font-weight: 500;">Date:</div>
            <div style="color: #333;">{{ $workOrder->created_at->format('d-m-Y') }}</div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 15px;">
            <div style="color: #666; font-weight: 500;">Title:</div>
            <div style="color: #333;">{{ $workOrder->title ?? 'N/A' }}</div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 15px;">
            <div style="color: #666; font-weight: 500;">Worker Type:</div>
            <div style="color: #333;">{{ $workOrder->worker_type ?? 'N/A' }}</div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 15px;">
            <div style="color: #666; font-weight: 500;">Work Name:</div>
            <div style="color: #333;">{{ $workOrder->worker_name ?? 'N/A' }}</div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 15px;">
            <div style="color: #666; font-weight: 500;">Product Name:</div>
            <div style="color: #333;">{{ $workOrder->product_name ?? 'N/A' }}</div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 15px;">
            <div style="color: #666; font-weight: 500;">Quantity Type:</div>
            <div style="color: #333;">{{ $workOrder->quantity_type ?? 'N/A' }}</div>
        </div>
        @if($workOrder->thickness || $workOrder->drawing_no || $workOrder->color || $workOrder->completion_date)
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6;">
            <h4 style="margin-bottom: 15px; color: #333;">Additional Fields</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                @if($workOrder->thickness)
                <div><span style="color: #666;">Thickness:</span> {{ $workOrder->thickness }}</div>
                @endif
                @if($workOrder->drawing_no)
                <div><span style="color: #666;">Drawing No:</span> {{ $workOrder->drawing_no }}</div>
                @endif
                @if($workOrder->color)
                <div><span style="color: #666;">Color:</span> {{ $workOrder->color }}</div>
                @endif
                @if($workOrder->completion_date)
                <div><span style="color: #666;">Completion Date:</span> {{ $workOrder->completion_date->format('d-m-Y') }}</div>
                @endif
                @if($workOrder->nature_of_work)
                <div><span style="color: #666;">Nature of Work:</span> {{ $workOrder->nature_of_work }}</div>
                @endif
                @if($workOrder->layup_sequence)
                <div><span style="color: #666;">Layup Sequence:</span> {{ $workOrder->layup_sequence }}</div>
                @endif
                @if($workOrder->batch_no)
                <div><span style="color: #666;">Batch No:</span> {{ $workOrder->batch_no }}</div>
                @endif
                @if($workOrder->work_order_date)
                <div><span style="color: #666;">Date:</span> {{ $workOrder->work_order_date->format('d-m-Y') }}</div>
                @endif
            </div>
        </div>
        @endif
        @if($workOrder->rawMaterials->isNotEmpty())
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6;">
            <h4 style="margin-bottom: 15px; color: #333;">Raw Materials</h4>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f1f5f9; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 10px; text-align: left;">Sl.No</th>
                            <th style="padding: 10px; text-align: left;">Raw Material</th>
                            <th style="padding: 10px; text-align: right;">Quantity</th>
                            <th style="padding: 10px; text-align: left;">UOM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workOrder->rawMaterials as $rm)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 10px;">{{ $loop->iteration }}</td>
                            <td style="padding: 10px;">{{ $rm->rawMaterial->name ?? 'N/A' }} @if(optional($rm->rawMaterial)->grade) - {{ $rm->rawMaterial->grade }} @endif</td>
                            <td style="padding: 10px; text-align: right;">{{ $rm->work_order_quantity }}</td>
                            @php
                                $symbol = 'N/A';
                                if ($rm->rawMaterial && $rm->rawMaterial->unit && $rm->rawMaterial->unit->symbol) {
                                    $symbol = $rm->rawMaterial->unit->symbol;
                                } elseif ($rm->unit && $rm->unit->symbol) {
                                    $symbol = $rm->unit->symbol;
                                }
                            @endphp
                            <td style="padding: 10px;">{{ $symbol }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        @if($workOrder->remarks)
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6;">
            <div style="color: #666; font-weight: 500; margin-bottom: 8px;">Remarks:</div>
            <div style="color: #333;">{{ $workOrder->remarks }}</div>
        </div>
        @endif
        @if($workOrder->document_path)
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6;">
            <div style="color: #666; font-weight: 500; margin-bottom: 8px;">Document:</div>
            <a href="{{ asset('storage/' . $workOrder->document_path) }}" target="_blank" style="color: #667eea;">{{ basename($workOrder->document_path) }}</a>
        </div>
        @endif
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #dee2e6; color: #666; font-size: 13px;">
            Created by: {{ optional($workOrder->creator)->name ?? 'N/A' }} on {{ $workOrder->created_at->format('d-m-Y H:i') }}
        </div>
    </div>
</div>
@endsection
