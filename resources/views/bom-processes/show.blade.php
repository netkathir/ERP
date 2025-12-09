@extends('layouts.dashboard')

@section('title', 'BOM Process Details - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">BOM Process Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('bom-processes.edit', $bomProcess->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('bom-processes.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div style="margin-bottom: 30px;">
        <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Product Name</label>
        <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $bomProcess->product->name ?? 'N/A' }}</p>
    </div>

    <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 20px;">
        <h3 style="margin: 0 0 20px 0; color: #667eea; font-size: 18px; font-weight: 600;">Raw Materials</h3>
        
        @if($bomProcess->items->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #e9ecef; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Process</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Raw Material</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Quantity</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">UOM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bomProcess->items as $item)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px; color: #666;">{{ $loop->iteration }}</td>
                                <td style="padding: 12px; color: #333; font-weight: 500;">{{ $item->process->name ?? 'N/A' }}</td>
                                <td style="padding: 12px; color: #333; font-weight: 500;">{{ $item->rawMaterial->name ?? 'N/A' }}</td>
                                <td style="padding: 12px; color: #666; text-align: right;">{{ number_format($item->quantity, 4) }}</td>
                                <td style="padding: 12px; color: #666;">{{ $item->unit->name ?? 'N/A' }} ({{ $item->unit->symbol ?? '' }})</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="color: #666; text-align: center; padding: 20px;">No raw materials added to this BOM.</p>
        @endif
    </div>
</div>
@endsection

