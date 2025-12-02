<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation - {{ $quotation->quotation_no }}</title>
    <style>
        @page {
            margin: 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #000;
        }
        .header {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .logo-section {
            width: 25%;
            vertical-align: top;
        }
        .logo-section img {
            max-width: 150px;
            max-height: 80px;
        }
        .company-info {
            width: 75%;
            vertical-align: top;
            padding-left: 20px;
        }
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .company-address {
            font-size: 11px;
            line-height: 1.5;
            margin-bottom: 5px;
        }
        .company-contact {
            font-size: 11px;
            margin-top: 5px;
        }
        .quotation-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }
        .quotation-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .quotation-details {
            width: 48%;
        }
        .quotation-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .quotation-details td {
            padding: 5px;
            font-size: 11px;
        }
        .quotation-details td:first-child {
            font-weight: bold;
            width: 40%;
        }
        .customer-section {
            width: 100%;
            margin-bottom: 20px;
        }
        .customer-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px 0;
        }
        .billed-to, .shipped-to {
            width: 48%;
            border: 1px solid #000;
            padding: 10px;
            min-height: 150px;
            vertical-align: top;
        }
        .section-title {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        .address-line {
            font-size: 11px;
            line-height: 1.5;
            margin-bottom: 3px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }
        .items-table th {
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
        }
        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 11px;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .items-summary-wrapper {
            width: 100%;
            margin-bottom: 20px;
        }
        .items-section {
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-wrapper {
            width: 350px;
            margin-left: auto;
            margin-right: 0;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 8px;
            font-size: 11px;
            border: 1px solid #000;
        }
        .summary-table td:first-child {
            font-weight: bold;
            width: 60%;
        }
        .summary-table td:last-child {
            text-align: right;
        }
        .summary-table .total-row {
            font-weight: bold;
            font-size: 13px;
            background-color: #f0f0f0;
        }
        .amount-in-words {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #000;
            font-size: 11px;
        }
        .amount-in-words strong {
            font-size: 12px;
        }
        .terms-conditions {
            margin-top: 20px;
            font-size: 11px;
        }
        .terms-conditions h4 {
            font-size: 12px;
            margin-bottom: 10px;
        }
        .terms-conditions ol {
            margin-left: 20px;
            line-height: 1.8;
        }
        .signature-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 40px;
            gap: 30px;
        }
        .signature-box {
            width: 200px;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 11px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Header with Logo and Company Info -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-section">
                    @if($companyInfo && $companyInfo->logo_path)
                        @php
                            $logoPath = storage_path('app/public/' . $companyInfo->logo_path);
                        @endphp
                        @if(file_exists($logoPath))
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="Company Logo">
                        @endif
                    @endif
                </td>
                <td class="company-info">
                    <div class="company-name">{{ $companyInfo->company_name ?? 'Company Name' }}</div>
                    <div class="company-address">
                        {{ $companyInfo->address_line_1 ?? '' }}<br>
                        @if($companyInfo && $companyInfo->address_line_2)
                            {{ $companyInfo->address_line_2 }}<br>
                        @endif
                        {{ $companyInfo->city ?? '' }}, {{ $companyInfo->state ?? '' }} - {{ $companyInfo->pincode ?? '' }}
                    </div>
                    @if($companyInfo)
                        <div class="company-contact">
                            @if($companyInfo->phone)Mobile: {{ $companyInfo->phone }}@endif
                            @if($companyInfo->phone && $companyInfo->email) | @endif
                            @if($companyInfo->email)Email: {{ $companyInfo->email }}@endif<br>
                            @if($companyInfo->gstin)GSTIN: {{ $companyInfo->gstin }}@endif
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Quotation Title -->
    <div class="quotation-title">QUOTATION</div>

    <!-- Quotation Details -->
    <div class="quotation-info">
        <div class="quotation-details">
            <table>
                <tr>
                    <td>Quotation Number:</td>
                    <td>{{ $quotation->quotation_no }}</td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td>{{ date('M-d-Y', strtotime($quotation->date)) }}</td>
                </tr>
                <tr>
                    <td>Place of Supply:</td>
                    <td>{{ $quotation->customer->billing_state ?? 'N/A' }} ({{ substr($quotation->customer->billing_state ?? '00', 0, 2) }})</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Customer Details -->
    <div class="customer-section">
        <table class="customer-table">
            <tr>
                <td class="billed-to">
                    <div class="section-title">Billed To</div>
                    <div style="font-weight: bold; margin-bottom: 8px;">{{ $quotation->customer->company_name }}</div>
                    <div class="address-line">{{ $quotation->billing_address_line_1 ?? '' }}</div>
                    @if($quotation->billing_address_line_2)
                        <div class="address-line">{{ $quotation->billing_address_line_2 }}</div>
                    @endif
                    <div class="address-line">{{ $quotation->billing_city ?? '' }}, {{ $quotation->billing_state ?? '' }} - {{ $quotation->billing_pincode ?? '' }}</div>
                    <div class="address-line" style="margin-top: 8px;">
                        <strong>Party Mobile No:</strong> {{ $quotation->customer->contact_info ?? 'N/A' }}<br>
                        <strong>State:</strong> {{ $quotation->billing_state ?? 'N/A' }}<br>
                        <strong>GST No:</strong> {{ $quotation->customer->gst_no ?? 'N/A' }}
                    </div>
                </td>
                <td class="shipped-to">
                    <div class="section-title">Shipped To</div>
                    <div style="font-weight: bold; margin-bottom: 8px;">{{ $quotation->customer->company_name }}</div>
                    @if($quotation->customer->shipping_address_line_1)
                        <div class="address-line">{{ $quotation->customer->shipping_address_line_1 }}</div>
                        @if($quotation->customer->shipping_address_line_2)
                            <div class="address-line">{{ $quotation->customer->shipping_address_line_2 }}</div>
                        @endif
                        <div class="address-line">{{ $quotation->customer->shipping_city ?? '' }}, {{ $quotation->customer->shipping_state ?? '' }} - {{ $quotation->customer->shipping_pincode ?? '' }}</div>
                    @else
                        <div class="address-line">{{ $quotation->billing_address_line_1 ?? '' }}</div>
                        @if($quotation->billing_address_line_2)
                            <div class="address-line">{{ $quotation->billing_address_line_2 }}</div>
                        @endif
                        <div class="address-line">{{ $quotation->billing_city ?? '' }}, {{ $quotation->billing_state ?? '' }} - {{ $quotation->billing_pincode ?? '' }}</div>
                    @endif
                    <div class="address-line" style="margin-top: 8px;">
                        <strong>Party Mobile No:</strong> {{ $quotation->customer->contact_info ?? 'N/A' }}<br>
                        <strong>State:</strong> {{ $quotation->customer->shipping_state ?? $quotation->billing_state ?? 'N/A' }}<br>
                        <strong>GST No:</strong> {{ $quotation->customer->gst_no ?? 'N/A' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Items Table and Summary Section -->
    <div class="items-summary-wrapper">
        <!-- Items Table -->
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">S.No</th>
                        <th style="width: 35%;">Description of goods</th>
                        <th style="width: 10%;">HSN/SAC Code</th>
                        <th style="width: 8%;">Unit</th>
                        <th style="width: 8%;">Quantity</th>
                        <th style="width: 8%;">Discount</th>
                        <th style="width: 12%;">Price/Unit</th>
                        <th style="width: 14%;" class="text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotation->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->product->name ?? 'N/A' }} @if($item->product->description)({{ $item->product->description }})@endif</td>
                        <td class="text-center">{{ $item->product->hsn_code ?? ($item->product->hsn ?? 'N/A') }}</td>
                        <td class="text-center">{{ $item->unit->symbol ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">
                            @if($quotation->overall_discount_percent > 0)
                                0.00%
                            @else
                                {{ number_format($item->discount_percent, 2) }}%
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($item->price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="summary-wrapper">
            <table class="summary-table">
            @php
                // Calculate gross amount (sum of all item totals before any discount)
                $grossAmount = 0;
                foreach($quotation->items as $item) {
                    $grossAmount += ($item->price * $item->quantity);
                }
                
                // Calculate discount amount
                $discountAmount = 0;
                if($quotation->overall_discount_percent > 0) {
                    $discountAmount = ($grossAmount * $quotation->overall_discount_percent) / 100;
                }
                
                // Subtotal after discount
                $subtotalAfterDiscount = $grossAmount - $discountAmount;
            @endphp
            <tr>
                <td>Gross Amount:</td>
                <td class="text-right">{{ number_format($grossAmount, 2) }}</td>
            </tr>
            @if($quotation->overall_discount_percent > 0)
            <tr>
                <td>Overall Discount ({{ number_format($quotation->overall_discount_percent, 2) }}%):</td>
                <td class="text-right">{{ number_format($discountAmount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td>Sub Total:</td>
                <td class="text-right">{{ number_format($subtotalAfterDiscount, 2) }}</td>
            </tr>
            @if($quotation->gst_type == 'intra')
                <tr>
                    <td>CGST ({{ number_format($cgstRate, 2) }}%):</td>
                    <td class="text-right">{{ number_format($cgstAmount, 2) }}</td>
                </tr>
                <tr>
                    <td>SGST ({{ number_format($sgstRate, 2) }}%):</td>
                    <td class="text-right">{{ number_format($sgstAmount, 2) }}</td>
                </tr>
            @else
                <tr>
                    <td>IGST ({{ number_format($igstRate, 2) }}%):</td>
                    <td class="text-right">{{ number_format($igstAmount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td>Forwarding charges:</td>
                <td class="text-right">{{ number_format($quotation->freight_charges, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Net Amount:</td>
                <td class="text-right">{{ number_format($quotation->net_amount, 2) }}</td>
            </tr>
            </table>
        </div>
    </div>

    <!-- Amount in Words -->
    <div class="amount-in-words">
        <strong>Amount in Words:</strong> {{ ucwords($amountInWords) }} rupees
    </div>

    <!-- Terms and Conditions -->
    <div class="terms-conditions">
        <h4>Terms and Conditions:</h4>
        <ol>
            <li>Goods once sold will not be taken back.</li>
            <li>Interest @ 18% p.a. will be charged if the payment is not made within the stipulated time.</li>
            <li>Subject to '{{ $companyInfo->city ?? 'Jurisdiction' }}' Jurisdiction only.</li>
        </ol>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line">Receiver's Signature</div>
        </div>
        <div class="signature-box">
            <div class="signature-line">Authorized Signatory</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>*This is a computer generated document.</p>
        <p>Page 1/1</p>
    </div>
</body>
</html>

