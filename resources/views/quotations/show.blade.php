@extends('layouts.dashboard')

@section('title', 'View Quotation - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Quotation #{{ $quotation->quotation_no }}</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('quotations.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('quotations.edit', $quotation->id) }}" style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('quotations.pdf', $quotation->id) }}" style="padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            <button onclick="window.print()" style="padding: 10px 20px; background: #17a2b8; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    @if($companyInfo)
        <!-- Company Information Header -->
        <div style="background: white; border: 2px solid #667eea; border-radius: 5px; margin-bottom: 20px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: start;">
                <div style="flex: 1;">
                    @if($companyInfo->logo_path)
                        @php
                            $logoUrl = asset('storage/' . $companyInfo->logo_path);
                        @endphp
                        <img src="{{ $logoUrl }}" alt="Company Logo" style="max-width: 150px; max-height: 80px; margin-bottom: 15px;" onerror="this.style.display='none';">
                    @endif
                    <h3 style="color: #333; font-size: 20px; margin: 0 0 10px 0; font-weight: 600;">{{ $companyInfo->company_name }}</h3>
                    <p style="color: #666; font-size: 14px; margin: 5px 0; line-height: 1.6;">
                        {{ $companyInfo->address_line_1 }}<br>
                        @if($companyInfo->address_line_2)
                            {{ $companyInfo->address_line_2 }}<br>
                        @endif
                        {{ $companyInfo->city }}, {{ $companyInfo->state }} - {{ $companyInfo->pincode }}
                    </p>
                    <p style="color: #666; font-size: 14px; margin: 5px 0;">
                        <strong>GSTIN:</strong> {{ $companyInfo->gstin }}
                    </p>
                    @if($companyInfo->email || $companyInfo->phone)
                        <p style="color: #666; font-size: 14px; margin: 5px 0;">
                            @if($companyInfo->email)Email: {{ $companyInfo->email }}@endif
                            @if($companyInfo->email && $companyInfo->phone) | @endif
                            @if($companyInfo->phone)Phone: {{ $companyInfo->phone }}@endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="padding: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;">
                <div>
                    <h3 style="color: #667eea; font-size: 18px; font-weight: 600; margin-bottom: 15px;">Customer Details</h3>
                    <p style="margin: 8px 0; color: #666;">
                        <strong style="color: #333;">Company Name:</strong> {{ $quotation->customer->company_name }}
                    </p>
                    <p style="margin: 8px 0; color: #666;">
                        <strong style="color: #333;">GST No:</strong> {{ $quotation->customer->gst_no ?? 'N/A' }}
                    </p>
                    <p style="margin: 8px 0; color: #666;">
                        <strong style="color: #333;">Address:</strong><br>
                        {{ $quotation->billing_address_line_1 }}<br>
                        @if($quotation->billing_address_line_2)
                            {{ $quotation->billing_address_line_2 }}<br>
                        @endif
                        {{ $quotation->billing_city }}, {{ $quotation->billing_state }} - {{ $quotation->billing_pincode }}
                    </p>
                </div>
                <div style="text-align: right;">
                    <h3 style="color: #667eea; font-size: 18px; font-weight: 600; margin-bottom: 15px;">Quotation Info</h3>
                    <p style="margin: 8px 0; color: #666;">
                        <strong style="color: #333;">Date:</strong> {{ $quotation->date }}
                    </p>
                    <p style="margin: 8px 0; color: #666;">
                        <strong style="color: #333;">GST Type:</strong> {{ strtoupper($quotation->gst_type) }}
                    </p>
                    <p style="margin: 8px 0; color: #666;">
                        <strong style="color: #333;">Status:</strong> 
                        <span style="background: #6c757d; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; text-transform: capitalize;">
                            {{ $quotation->status }}
                        </span>
                    </p>
                </div>
            </div>

            <div style="overflow-x: auto; margin-bottom: 30px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Product</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Unit</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Price</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Qty</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Disc %</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Tax Amt</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotation->items as $item)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $item->product->name }}</td>
                            <td style="padding: 12px; color: #666;">{{ $item->unit->symbol }}</td>
                            <td style="padding: 12px; text-align: right; color: #666;">₹{{ number_format($item->price, 2) }}</td>
                            <td style="padding: 12px; text-align: right; color: #666;">{{ $item->quantity }}</td>
                            <td style="padding: 12px; text-align: right; color: #666;">{{ $item->discount_percent }}%</td>
                            <td style="padding: 12px; text-align: right; color: #666;">₹{{ number_format($item->tax_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <div style="width: 400px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; color: #333; font-weight: 500;">Freight Charges:</td>
                            <td style="padding: 8px 0; text-align: right; color: #666;">₹{{ number_format($quotation->freight_charges, 2) }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #333; font-weight: 500;">Total Amount:</td>
                            <td style="padding: 8px 0; text-align: right; color: #666;">₹{{ number_format($quotation->total_amount, 2) }}</td>
                        </tr>
                        <tr style="border-top: 2px solid #dee2e6;">
                            <td style="padding: 12px 0; color: #333; font-weight: 600; font-size: 18px;">Net Amount:</td>
                            <td style="padding: 12px 0; text-align: right; color: #667eea; font-weight: 600; font-size: 18px;">₹{{ number_format($quotation->net_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
