@extends('layouts.dashboard')

@section('title', 'View Tender - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Tender #{{ $tender->tender_no }}</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('tenders.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('tenders.edit', $tender->id) }}" style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <!-- General Information -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">General Information</h3>
        </div>
        <div style="padding: 20px;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div>
                    <strong style="color: #333;">Tender No:</strong>
                    <p style="color: #666; margin: 5px 0;">{{ $tender->tender_no }}</p>
                </div>
                <div>
                    <strong style="color: #333;">Customer Tender No:</strong>
                    <p style="color: #666; margin: 5px 0;">{{ $tender->customer_tender_no ?? '-' }}</p>
                </div>
                <div>
                    <strong style="color: #333;">Attended By:</strong>
                    <p style="color: #666; margin: 5px 0;">{{ $tender->attendedBy->name ?? '-' }}</p>
                </div>
                <div>
                    <strong style="color: #333;">Production Dept:</strong>
                    <p style="color: #666; margin: 5px 0;">{{ $tender->production_dept ?? '-' }}</p>
                </div>
                <div>
                    <strong style="color: #333;">Company:</strong>
                    <p style="color: #666; margin: 5px 0;">{{ $tender->company->company_name ?? '-' }}</p>
                </div>
                <div>
                    <strong style="color: #333;">Contact Person:</strong>
                    <p style="color: #666; margin: 5px 0;">{{ $tender->contact_person ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Billing Address -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Billing Address</h3>
        </div>
        <div style="padding: 20px;">
            <p style="color: #666; margin: 5px 0;">
                {{ $tender->billing_address_line_1 ?? '' }}<br>
                @if($tender->billing_address_line_2){{ $tender->billing_address_line_2 }}<br>@endif
                {{ $tender->billing_city ?? '' }}, {{ $tender->billing_state ?? '' }} - {{ $tender->billing_pincode ?? '' }}<br>
                {{ $tender->billing_country ?? '' }}
            </p>
        </div>
    </div>

    <!-- Tender Details -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Tender Details</h3>
        </div>
        <div style="padding: 20px;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div><strong>Publishing Date:</strong> {{ $tender->publishing_date ? $tender->publishing_date->format('d/m/Y') : '-' }}</div>
                <div><strong>Closing Date/Time:</strong> {{ $tender->closing_date_time ? $tender->closing_date_time->format('d/m/Y H:i') : '-' }}</div>
                <div><strong>Bidding Type:</strong> {{ $tender->bidding_type ?? '-' }}</div>
                <div><strong>Tender Type:</strong> {{ $tender->tender_type ?? '-' }}</div>
                <div><strong>Contract Type:</strong> {{ $tender->contract_type ?? '-' }}</div>
                <div><strong>Bidding System:</strong> {{ $tender->bidding_system ?? '-' }}</div>
                <div><strong>Procure from Approved Source:</strong> {{ $tender->procure_from_approved_source ?? '-' }}</div>
                <div><strong>Regular / Developmental:</strong> {{ $tender->regular_developmental ?? 'Regular' }}</div>
                <div><strong>Validity of Offer (Days):</strong> {{ $tender->validity_of_offer_days ?? '-' }}</div>
                <div><strong>Tender Document Cost:</strong> ₹{{ number_format($tender->tender_document_cost ?? 0, 2) }}</div>
                <div><strong>EMD:</strong> ₹{{ number_format($tender->emd ?? 0, 2) }}</div>
                <div><strong>RA Enabled:</strong> {{ $tender->ra_enabled ?? '-' }}</div>
                <div><strong>RA Date &amp; Time:</strong> {{ $tender->ra_date_time ? $tender->ra_date_time->format('d/m/Y H:i') : '-' }}</div>
                <div><strong>Pre-Bid Conference Required:</strong> {{ $tender->pre_bid_conference_required ?? '-' }}</div>
                <div><strong>Pre-Bid Conference Date:</strong> {{ $tender->pre_bid_conference_date ? $tender->pre_bid_conference_date->format('d/m/Y') : '-' }}</div>
                <div><strong>Inspection Agency:</strong> {{ $tender->inspection_agency ?? '-' }}</div>
                <div><strong>Tender Status:</strong>
                    @if($tender->tender_status === 'Bid Coated')
                        Bid Quoted
                    @elseif($tender->tender_status === 'Bid not coated')
                        Bid Not Quoted
                    @else
                        {{ $tender->tender_status }}
                    @endif
                </div>
                <div><strong>Bid Result:</strong> {{ $tender->bid_result ?? '-' }}</div>
            </div>
            @if($tender->tender_document_attachment)
                <div style="margin-top: 15px;">
                    <strong>Document:</strong> 
                    <a href="{{ asset('storage/' . $tender->tender_document_attachment) }}" target="_blank" style="color: #667eea;">View Document</a>
                </div>
            @endif
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
                        <th style="padding: 12px; text-align: left;">PL Code</th>
                        <th style="padding: 12px; text-align: left;">Title</th>
                        <th style="padding: 12px; text-align: left;">Description</th>
                        <th style="padding: 12px; text-align: left;">Qty</th>
                        <th style="padding: 12px; text-align: left;">Unit</th>
                        <th style="padding: 12px; text-align: left;">Price Quoted</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tender->items as $item)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px;">{{ $item->pl_code ?? '-' }}</td>
                            <td style="padding: 12px;">{{ $item->title }}</td>
                            <td style="padding: 12px;">{{ Str::limit($item->description, 50) }}</td>
                            <td style="padding: 12px;">{{ $item->qty ?? '-' }}</td>
                            <td style="padding: 12px;">{{ $item->unit->symbol ?? '-' }}</td>
                            <td style="padding: 12px;">₹{{ number_format($item->price_quoted ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Remarks -->
    @if($tender->remarks->count() > 0)
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Remarks</h3>
        </div>
        <div style="padding: 20px;">
            @foreach($tender->remarks as $remark)
                <div style="margin-bottom: 15px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                    <strong>{{ $remark->date->format('d/m/Y') }}</strong>
                    <p style="margin: 10px 0; color: #666;">{{ $remark->remarks }}</p>
                    @if($remark->corrigendum_file)
                        <a href="{{ asset('storage/' . $remark->corrigendum_file) }}" target="_blank" style="color: #667eea;">View Corrigendum</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

