@extends('layouts.dashboard')

@section('title', 'View Tender Evaluation - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Tender Evaluation Details</h2>
        <a href="{{ route('tender-evaluations.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    @php
        $tender = $evaluation->tender;
    @endphp

    <!-- General Information -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">General Information</h3>
        </div>
        <div style="padding: 20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div>
                <div style="color: #777; font-size: 13px; margin-bottom: 4px;">Tender No</div>
                <div style="color: #333; font-weight: 600;">{{ optional($tender)->tender_no ?? '-' }}</div>
            </div>
            <div>
                <div style="color: #777; font-size: 13px; margin-bottom: 4px;">Closing Date &amp; Time</div>
                <div style="color: #333; font-weight: 600;">
                    @if(optional($tender)->closing_date_time)
                        {{ optional($tender->closing_date_time)->format('d-m-Y H:i') }}
                    @else
                        -
                    @endif
                </div>
            </div>
            <div>
                <div style="color: #777; font-size: 13px; margin-bottom: 4px;">Evaluation Document</div>
                @if($evaluation->evaluation_document)
                    <a href="{{ asset('storage/' . $evaluation->evaluation_document) }}" target="_blank" style="color: #667eea; text-decoration: none; font-weight: 500;">
                        <i class="fas fa-file-download"></i> View Document
                    </a>
                @else
                    <span style="color: #777;">-</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Tender Items -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Tender Items</h3>
        </div>
        <div style="padding: 20px; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Title</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Description</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Quantity</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @if($tender && $tender->items->count())
                        @foreach($tender->items as $item)
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px; color: #333;">{{ $item->title }}</td>
                                <td style="padding: 10px; color: #555; white-space: pre-wrap;">{{ $item->description }}</td>
                                <td style="padding: 10px; text-align: right; color: #333;">{{ $item->qty }}</td>
                                <td style="padding: 10px; color: #333;">{{ optional($item->unit)->symbol }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" style="padding: 12px; text-align: center; color: #777;">
                                No items found for this tender.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


