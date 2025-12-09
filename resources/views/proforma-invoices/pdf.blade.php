<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Proforma Invoice - {{ $invoice->invoice_no }}</title>
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
        .invoice-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .invoice-details {
            width: 48%;
        }
        .invoice-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-details td {
            padding: 5px;
            font-size: 11px;
        }
        .invoice-details td:first-child {
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
            justify-content: space-between;
            margin-top: 40px;
        }
        .signature-box {
            width: 45%;
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

    <!-- Invoice Title -->
    <div class="invoice-title">PROFORMA INVOICE</div>

    <!-- Invoice Details -->
    <div class="invoice-info">
        <div class="invoice-details">
            <table>
                <tr>
                    <td>Invoice Number:</td>
                    <td>{{ $invoice->invoice_no }}</td>
                </tr>
                <tr>
                    <td>Date:</td>
                    <td>{{ date('M-d-Y', strtotime($invoice->date)) }}</td>
                </tr>
                <tr>
                    <td>Place of Supply:</td>
                    <td>{{ $invoice->billing_state ?? 'N/A' }} ({{ substr($invoice->billing_state ?? '00', 0, 2) }})</td>
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
                    <div style="font-weight: bold; margin-bottom: 8px;">{{ $invoice->customer->company_name }}</div>
                    @if(!empty($invoice->billing_address_line_1))
                        <div class="address-line">{{ $invoice->billing_address_line_1 }}</div>
                    @endif
                    @if(!empty($invoice->billing_address_line_2))
                        <div class="address-line">{{ $invoice->billing_address_line_2 }}</div>
                    @endif
                    @php
                        $billedCityParts = [];
                        if (!empty($invoice->billing_city)) {
                            $billedCityParts[] = $invoice->billing_city;
                        }
                        if (!empty($invoice->billing_state)) {
                            $billedCityParts[] = $invoice->billing_state;
                        }
                        $billedCityLine = implode(', ', $billedCityParts);
                    @endphp
                    @if($billedCityLine || !empty($invoice->billing_pincode))
                        <div class="address-line">
                            {{ $billedCityLine }}@if($billedCityLine && $invoice->billing_pincode) - @endif{{ $invoice->billing_pincode }}
                        </div>
                    @endif
                    @php
                        $billedMobile = $invoice->customer->contact_info ?? null;
                        $billedState = $invoice->billing_state ?? null;
                        $billedGst   = $invoice->customer->gst_no ?? null;
                    @endphp
                    @if($billedMobile || $billedState || $billedGst)
                        <div class="address-line" style="margin-top: 8px;">
                            @if($billedMobile)
                                <strong>Party Mobile No:</strong> {{ $billedMobile }}<br>
                            @endif
                            @if($billedState)
                                <strong>State:</strong> {{ $billedState }}<br>
                            @endif
                            @if($billedGst)
                                <strong>GST No:</strong> {{ $billedGst }}
                            @endif
                        </div>
                    @endif
                </td>
                <td class="shipped-to">
                    <div class="section-title">Shipped To</div>
                    <div style="font-weight: bold; margin-bottom: 8px;">{{ $invoice->customer->company_name }}</div>
                    @php
                        $useShipping = !empty($invoice->customer->shipping_address_line_1) ||
                                       !empty($invoice->customer->shipping_address_line_2) ||
                                       !empty($invoice->customer->shipping_city) ||
                                       !empty($invoice->customer->shipping_state) ||
                                       !empty($invoice->customer->shipping_pincode);
                    @endphp
                    @if($useShipping)
                        @if(!empty($invoice->customer->shipping_address_line_1))
                            <div class="address-line">{{ $invoice->customer->shipping_address_line_1 }}</div>
                        @endif
                        @if(!empty($invoice->customer->shipping_address_line_2))
                            <div class="address-line">{{ $invoice->customer->shipping_address_line_2 }}</div>
                        @endif
                        @php
                            $shipCityParts = [];
                            if (!empty($invoice->customer->shipping_city)) {
                                $shipCityParts[] = $invoice->customer->shipping_city;
                            }
                            if (!empty($invoice->customer->shipping_state)) {
                                $shipCityParts[] = $invoice->customer->shipping_state;
                            }
                            $shipCityLine = implode(', ', $shipCityParts);
                        @endphp
                        @if($shipCityLine || !empty($invoice->customer->shipping_pincode))
                            <div class="address-line">
                                {{ $shipCityLine }}@if($shipCityLine && $invoice->customer->shipping_pincode) - @endif{{ $invoice->customer->shipping_pincode }}
                            </div>
                        @endif
                    @else
                        @if(!empty($invoice->billing_address_line_1))
                            <div class="address-line">{{ $invoice->billing_address_line_1 }}</div>
                        @endif
                        @if(!empty($invoice->billing_address_line_2))
                            <div class="address-line">{{ $invoice->billing_address_line_2 }}</div>
                        @endif
                        @php
                            $shipFallbackParts = [];
                            if (!empty($invoice->billing_city)) {
                                $shipFallbackParts[] = $invoice->billing_city;
                            }
                            if (!empty($invoice->billing_state)) {
                                $shipFallbackParts[] = $invoice->billing_state;
                            }
                            $shipFallbackLine = implode(', ', $shipFallbackParts);
                        @endphp
                        @if($shipFallbackLine || !empty($invoice->billing_pincode))
                            <div class="address-line">
                                {{ $shipFallbackLine }}@if($shipFallbackLine && $invoice->billing_pincode) - @endif{{ $invoice->billing_pincode }}
                            </div>
                        @endif
                    @endif
                    @php
                        $shipMobile = $invoice->customer->contact_info ?? null;
                        $shipState = $invoice->customer->shipping_state ?? $invoice->billing_state ?? null;
                        $shipGst   = $invoice->customer->gst_no ?? null;
                    @endphp
                    @if($shipMobile || $shipState || $shipGst)
                        <div class="address-line" style="margin-top: 8px;">
                            @if($shipMobile)
                                <strong>Party Mobile No:</strong> {{ $shipMobile }}<br>
                            @endif
                            @if($shipState)
                                <strong>State:</strong> {{ $shipState }}<br>
                            @endif
                            @if($shipGst)
                                <strong>GST No:</strong> {{ $shipGst }}
                            @endif
                        </div>
                    @endif
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
                    @foreach($invoice->items as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->product->name ?? 'N/A' }} @if($item->product && $item->product->description)({{ $item->product->description }})@endif</td>
                        <td class="text-center">{{ $item->product->hsn_code ?? ($item->product->hsn ?? 'N/A') }}</td>
                        <td class="text-center">{{ $item->unit->symbol ?? 'N/A' }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">
                            @if($invoice->overall_discount_percent > 0)
                                0.00%
                            @else
                                {{ number_format($item->discount_percent, 2) }}%
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($item->price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary Section -->
        <div class="summary-wrapper">
            <table class="summary-table">
            <tr>
                <td>Gross Amount:</td>
                <td class="text-right">{{ number_format($invoice->gross_amount, 2) }}</td>
            </tr>
            @if($invoice->overall_discount_percent > 0)
            <tr>
                <td>Overall Discount ({{ number_format($invoice->overall_discount_percent, 2) }}%):</td>
                <td class="text-right">{{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
            @endif
            <tr>
                <td>Taxable Amount:</td>
                <td class="text-right">{{ number_format($invoice->taxable_amount, 2) }}</td>
            </tr>
            @if($invoice->gst_type == 'intra')
                <tr>
                    <td>CGST ({{ number_format($cgstRate, 2) }}%):</td>
                    <td class="text-right">{{ number_format($invoice->cgst_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>SGST ({{ number_format($sgstRate, 2) }}%):</td>
                    <td class="text-right">{{ number_format($invoice->sgst_amount, 2) }}</td>
                </tr>
                <tr>
                    <td>IGST (0.00%):</td>
                    <td class="text-right">0.00</td>
                </tr>
            @else
                <tr>
                    <td>CGST (0.00%):</td>
                    <td class="text-right">0.00</td>
                </tr>
                <tr>
                    <td>SGST (0.00%):</td>
                    <td class="text-right">0.00</td>
                </tr>
                <tr>
                    <td>IGST ({{ number_format($igstRate, 2) }}%):</td>
                    <td class="text-right">{{ number_format($invoice->igst_amount, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td>Freight & Forwarding Charges:</td>
                <td class="text-right">{{ number_format($invoice->freight_charges, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Net Amount:</td>
                <td class="text-right">{{ number_format($invoice->net_amount, 2) }}</td>
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

