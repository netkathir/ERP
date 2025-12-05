@extends('layouts.dashboard')

@section('title', 'Edit Customer Order - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Customer Order</h2>
        <a href="{{ route('customer-orders.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customer-orders.update', $order->id) }}" method="POST" enctype="multipart/form-data" id="customerOrderForm">
        @csrf
        @method('PUT')

        <!-- Header Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Customer Order Details</h3>
            </div>
            <div style="padding: 20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Tender No <span style="color: red;">*</span></label>
                    <select name="tender_id" id="tender_id" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        @foreach($tenders as $tender)
                            <option value="{{ $tender->id }}" {{ $order->tender_id == $tender->id ? 'selected' : '' }}>
                                {{ $tender->tender_no }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer Order No</label>
                    <input type="text" value="{{ $order->order_no }}" readonly
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Order Date <span style="color: red;">*</span></label>
                    <input type="date" name="order_date" value="{{ old('order_date', optional($order->order_date)->format('Y-m-d')) }}" required
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer Tender No</label>
                    <input type="text" name="customer_tender_no" value="{{ old('customer_tender_no', $order->customer_tender_no) }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer PO Date</label>
                    <input type="date" name="customer_po_date" value="{{ old('customer_po_date', optional($order->customer_po_date)->format('Y-m-d')) }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer Name</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name', optional($order->tender->company)->company_name ?? '') }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Inspection Agency</label>
                    <input type="text" name="inspection_agency" value="{{ old('inspection_agency', $order->inspection_agency) }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer PO No</label>
                    <input type="text" name="customer_po_no" value="{{ old('customer_po_no', $order->customer_po_no) }}"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
            </div>
        </div>

        <!-- Items Section -->
        {{-- Tender Items Details Section (View Only) --}}
        <div id="tender_items_details_section" style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1); {{ $order->tender_id ? '' : 'display:none;' }}">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Tender Items Details</h3>
            </div>
            <div style="padding:20px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">PL Code</th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Title</th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Description</th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Delivery Location</th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">Qty</th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Unit</th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Request for Price</th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">Price Received</th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">Price Quoted</th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Tender Status</th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Bid Result</th>
                        </tr>
                    </thead>
                    <tbody id="tender_items_details_table_body">
                        {{-- Content populated by JavaScript --}}
                    </tbody>
                </table>
            </div>
        </div>

        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Items</h3>
                <button type="button" onclick="addItemRow()" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
            <div style="padding: 20px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 20%;">
                                Product Name <span style="color:red;">*</span>
                                <button type="button" onclick="openAddProductModal()" style="margin-left: 10px; padding: 4px 8px; background: #28a745; color: white; border: none; border-radius: 3px; font-size: 11px; cursor: pointer; font-weight: 500;">
                                    <i class="fas fa-plus"></i> Add Product
                                </button>
                            </th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 25%;">Description</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 10%;">Unit <span style="color:red;">*</span></th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600; width: 12%;">Quantity <span style="color:red;">*</span></th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600; width: 15%;">Price per Qty <span style="color:red;">*</span></th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600; width: 12%;">Amount</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 6%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        @php
                            $items = $order->items;
                        @endphp
                        @foreach($items as $index => $orderItem)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 10px;">
                                    <select name="items[{{ $index }}][product_id]" id="product_{{ $index }}" required
                                            onchange="onProductSelect({{ $index }})"
                                            style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px;">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" {{ $orderItem->product_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="padding: 10px;">
                                    <textarea name="items[{{ $index }}][description]" rows="2" placeholder="Enter description"
                                              style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px;">{{ $orderItem->description }}</textarea>
                                </td>
                                <td style="padding: 10px;">
                                    <input type="text" name="items[{{ $index }}][unit_symbol]" id="unit_symbol_{{ $index }}" 
                                           value="{{ optional($orderItem->unit)->symbol ?? optional($orderItem->product->unit)->symbol ?? '' }}" readonly
                                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; background:#f8f9fa;">
                                    <input type="hidden" name="items[{{ $index }}][unit_id]" id="unit_id_{{ $index }}" 
                                           value="{{ $orderItem->unit_id ?? optional($orderItem->product)->unit_id ?? '' }}">
                                </td>
                                <td style="padding: 10px; text-align: right;">
                                    <input type="number" name="items[{{ $index }}][ordered_qty]" value="{{ $orderItem->ordered_qty }}" step="0.01" min="0.01" required
                                           oninput="recalcItemAmount({{ $index }})"
                                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; text-align: right;">
                                </td>
                                <td style="padding: 10px; text-align: right;">
                                    <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $orderItem->unit_price }}" step="0.01" min="0" required
                                           oninput="recalcItemAmount({{ $index }})"
                                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; text-align: right;">
                                </td>
                                <td style="padding: 10px; text-align: right; color:#333;">
                                    @php $amount = $orderItem->line_amount ?? ($orderItem->ordered_qty * $orderItem->unit_price); @endphp
                                    <span id="item_amount_display_{{ $index }}">{{ number_format($amount, 2) }}</span>
                                    <input type="hidden" name="items[{{ $index }}][line_amount]" id="item_amount_{{ $index }}" value="{{ $amount }}">
                                </td>
                                <td style="padding: 10px; text-align: center;">
                                    <button type="button" onclick="removeItemRow(this)" style="padding: 5px 10px; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        @if($items->isEmpty())
                            <tr>
                                <td colspan="7" style="padding: 12px; text-align: center; color: #777;">
                                    Click "Add Item" to add products.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- GST / Tax Selection and Amount Summary -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <!-- Left: GST/Tax Selection and Additional Charges -->
                <div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: flex; align-items: center; gap: 8px; color: #333; font-weight: 500; margin-bottom: 15px;">
                            @php
                                $taxType = old('tax_type', $order->tax_type ?? 'cgst_sgst');
                                $isCgstSgst = ($taxType == 'cgst_sgst' || $taxType != 'igst');
                            @endphp
                            <input type="radio" name="tax_type" value="cgst_sgst" id="tax_type_cgst_sgst" {{ $isCgstSgst ? 'checked' : '' }} onchange="recalculateTax()"
                                   style="width: 18px; height: 18px; cursor: pointer;">
                            <span>CGST and SGST</span>
                        </label>
                        <div style="margin-bottom: 15px;">
                            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Freight</label>
                            <input type="number" name="freight" id="freight" step="0.01" min="0" value="{{ old('freight', $order->freight ?? 0) }}"
                                   oninput="recalculateTax()"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Inspection Charges</label>
                            <input type="number" name="inspection_charges" id="inspection_charges" step="0.01" min="0" value="{{ old('inspection_charges', $order->inspection_charges ?? 0) }}"
                                   oninput="recalculateTax()"
                                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        </div>
                    </div>
                </div>

                <!-- Right: IGST Radio and Amount Summary -->
                <div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: flex; align-items: center; gap: 8px; color: #333; font-weight: 500; margin-bottom: 15px;">
                            <input type="radio" name="tax_type" value="igst" id="tax_type_igst" {{ old('tax_type', $order->tax_type) == 'igst' ? 'checked' : '' }} onchange="recalculateTax()"
                                   style="width: 18px; height: 18px; cursor: pointer;">
                            <span>IGST</span>
                        </label>
                    </div>
                    
                    <!-- Amount Summary -->
                    <div>
                        <h4 style="margin: 0 0 15px 0; color: #333; font-size: 16px; font-weight: 600;">Amount</h4>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <label style="color: #333; font-weight: 500;">Total:</label>
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <span style="color: #333;">₹</span>
                                    <input type="text" id="total_amount_display" value="{{ number_format(old('total_amount', $order->total_amount ?? 0), 2) }}" readonly
                                           style="width: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right; background: #f8f9fa;">
                                    <input type="hidden" name="total_amount" id="total_amount" value="{{ old('total_amount', $order->total_amount ?? 0) }}">
                                </div>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <label style="color: #333; font-weight: 500;">GST <span style="color: red;">*</span>:</label>
                                <div style="display: flex; gap: 5px;">
                                    <input type="number" name="gst_percent" id="gst_percent" step="0.01" min="0" max="100" value="{{ old('gst_percent', $order->gst_percent ?? 0) }}"
                                           oninput="recalculateTax()"
                                           style="width: 80px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right;">
                                    <span style="color: #333; padding-top: 8px;">%</span>
                                </div>
                            </div>
                            
                            @php
                                $cgstAmt = old('cgst_amount', $order->cgst_amount ?? 0);
                                $sgstAmt = old('sgst_amount', $order->sgst_amount ?? 0);
                                $igstAmt = old('igst_amount', $order->igst_amount ?? 0);
                                $totalGstAmt = $cgstAmt + $sgstAmt + $igstAmt;
                            @endphp
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <label style="color: #333; font-weight: 500;">GST Amount:</label>
                                <input type="text" id="gst_amount_display" value="{{ number_format($totalGstAmt, 2) }}" readonly
                                       style="width: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right; background: #f8f9fa;">
                                <input type="hidden" name="gst_amount" id="gst_amount" value="{{ $totalGstAmt }}">
                            </div>
                            
                            <div id="cgst_sgst_section">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                    <label style="color: #333; font-weight: 500;">CGST:</label>
                                    <input type="text" id="cgst_amount_display" value="{{ number_format(old('cgst_amount', $order->cgst_amount ?? 0), 2) }}" readonly
                                           style="width: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right; background: #f8f9fa;">
                                    <input type="hidden" name="cgst_percent" id="cgst_percent" value="{{ old('cgst_percent', $order->cgst_percent ?? 0) }}">
                                    <input type="hidden" name="cgst_amount" id="cgst_amount" value="{{ old('cgst_amount', $order->cgst_amount ?? 0) }}">
                                </div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <label style="color: #333; font-weight: 500;">SGST:</label>
                                    <input type="text" id="sgst_amount_display" value="{{ number_format(old('sgst_amount', $order->sgst_amount ?? 0), 2) }}" readonly
                                           style="width: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right; background: #f8f9fa;">
                                    <input type="hidden" name="sgst_percent" id="sgst_percent" value="{{ old('sgst_percent', $order->sgst_percent ?? 0) }}">
                                    <input type="hidden" name="sgst_amount" id="sgst_amount" value="{{ old('sgst_amount', $order->sgst_amount ?? 0) }}">
                                </div>
                            </div>
                            
                            @php
                                $showIgst = (old('tax_type', $order->tax_type ?? 'cgst_sgst') == 'igst');
                            @endphp
                            <div id="igst_section" style="display: {{ $showIgst ? 'block' : 'none' }}; margin-bottom: 12px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <label style="color: #333; font-weight: 500;">IGST:</label>
                                    <input type="text" id="igst_amount_display" value="{{ number_format(old('igst_amount', $order->igst_amount ?? 0), 2) }}" readonly
                                           style="width: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right; background: #f8f9fa;">
                                    <input type="hidden" name="igst_percent" id="igst_percent" value="{{ old('igst_percent', $order->igst_percent ?? 0) }}">
                                    <input type="hidden" name="igst_amount" id="igst_amount" value="{{ old('igst_amount', $order->igst_amount ?? 0) }}">
                                </div>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px solid #eee;">
                                <label style="color: #333; font-weight: 600; font-size: 15px;">Net Amount:</label>
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <span style="color: #333;">₹</span>
                                    <input type="text" id="net_amount_display" value="{{ number_format(old('net_amount', $order->net_amount ?? 0), 2) }}" readonly
                                           style="width: 120px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right; background: #f8f9fa; font-weight: 600;">
                                    <input type="hidden" name="net_amount" id="net_amount" value="{{ old('net_amount', $order->net_amount ?? 0) }}">
                                </div>
                            </div>
                            
                            <div style="margin-top: 10px;">
                                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Note:</label>
                                <textarea name="amount_note" id="amount_note" rows="3"
                                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;">{{ old('amount_note', $order->amount_note) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Schedule</h3>
                <button type="button" onclick="openSchedulePopup()" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-plus"></i> Add / Edit Schedule
                </button>
            </div>
            <div style="padding: 20px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Product</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PO SR No</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Quantity</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Unit</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Start Date</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">End Date</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Inspection Clause</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleGrid">
                         @forelse($order->schedules as $s)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 10px; color: #333;">{{ optional(optional($s->customerOrderItem)->product)->name ?? optional(optional($s->customerOrderItem)->tenderItem)->title ?? '' }}</td>
                                 <td style="padding: 10px; color: #333;">{{ $s->po_sr_no }}</td>
                                <td style="padding: 10px; text-align: right; color: #333;">{{ $s->quantity }}</td>
                                <td style="padding: 10px; color: #333;">{{ optional($s->unit)->symbol }}</td>
                                <td style="padding: 10px; color: #333;">{{ optional($s->start_date)->format('Y-m-d') }}</td>
                                <td style="padding: 10px; color: #333;">{{ optional($s->end_date)->format('Y-m-d') }}</td>
                                <td style="padding: 10px; color: #333;">{{ $s->inspection_clause }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 12px; text-align: center; color: #777;">
                                    No schedule lines added.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Amendment Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Amendments</h3>
                <button type="button" onclick="openAmendmentPopup()" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-plus"></i> Add / Edit Amendment
                </button>
            </div>
            <div style="padding: 20px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Product</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PO SR No</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Amendment No</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Amendment Date</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Existing Qty</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">New Qty</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="amendmentGrid">
                         @forelse($order->amendments as $a)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 10px; color: #333;">{{ optional(optional($a->customerOrderItem)->product)->name ?? optional(optional($a->customerOrderItem)->tenderItem)->title ?? '' }}</td>
                                 <td style="padding: 10px; color: #333;">{{ $a->po_sr_no }}</td>
                                <td style="padding: 10px; color: #333;">{{ $a->amendment_no }}</td>
                                <td style="padding: 10px; color: #333;">{{ optional($a->amendment_date)->format('Y-m-d') }}</td>
                                <td style="padding: 10px; text-align: right; color: #333;">{{ $a->existing_quantity }}</td>
                                <td style="padding: 10px; text-align: right; color: #333;">{{ $a->new_quantity }}</td>
                                <td style="padding: 10px; color: #333;">{{ $a->remarks }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="padding: 12px; text-align: center; color: #777;">
                                    No amendments added.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div id="hiddenScheduleInputs"></div>
        <div id="hiddenAmendmentInputs"></div>

        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px;">
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-save"></i> Update
            </button>
        </div>
    </form>
</div>

@include('customer_orders.partials.schedule_modal')
@include('customer_orders.partials.amendment_modal')

<!-- Add Product Modal -->
<div id="addProductModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="color: #333; font-size: 24px; margin: 0;">Add Product</h2>
            <button type="button" onclick="closeAddProductModal()" style="background: none; border: none; font-size: 24px; color: #999; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div id="addProductModalErrors" style="display: none; background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <ul style="margin: 0; padding-left: 20px;" id="addProductModalErrorsList"></ul>
        </div>

        <form id="addProductForm">
            <div style="margin-bottom: 20px;">
                <label for="modal_product_category_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Product Category</label>
                <select name="product_category_id" id="modal_product_category_id"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: white;">
                    <option value="">Select Product Category</option>
                    @if(isset($productCategories) && $productCategories->count() > 0)
                        @foreach($productCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    @else
                        <option value="" disabled>No Product Categories available</option>
                    @endif
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="modal_product_name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Product Name <span style="color: red;">*</span></label>
                <input type="text" name="name" id="modal_product_name" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="modal_product_description" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Description</label>
                <textarea name="description" id="modal_product_description" rows="4"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;"></textarea>
            </div>

            <div style="margin-bottom: 20px;">
                <label for="modal_product_unit_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Unit <span style="color: red;">*</span></label>
                <select name="unit_id" id="modal_product_unit_id" required
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <option value="">Select Unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->symbol }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 30px; justify-content: flex-end;">
                <button type="button" onclick="closeAddProductModal()" style="padding: 12px 24px; background: #6c757d; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                    Save Product
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const units = @json($unitsData);
    const products = @json($productsData);
    const routeIdPlaceholder = '{{ $placeholderId ?? 'PLACEHOLDER_ID' }}';
    const tenderItemsDisplayUrl = @json(route('customer-orders.tender.items.display', ['id' => $placeholderId ?? 'PLACEHOLDER_ID']));

    let itemIndexCounter = {{ $order->items->count() }};
    let selectedItemIndex = null;
    let schedules = @json($schedulesData);
    let amendments = @json($amendmentsData);

    // Function to load and display Tender Items Details table
    function loadTenderItemsDetailsTable(tenderId) {
        const detailsSection = document.getElementById('tender_items_details_section');
        const detailsTableBody = document.getElementById('tender_items_details_table_body');
        
        if (!tenderId) {
            // Hide the section if no tender is selected
            if (detailsSection) {
                detailsSection.style.display = 'none';
            }
            if (detailsTableBody) {
                detailsTableBody.innerHTML = '<tr><td colspan="11" style="padding:10px; text-align:center; color:#777;">Select a Tender to view details.</td></tr>';
            }
            return;
        }
        
        // Show the section
        if (detailsSection) {
            detailsSection.style.display = 'block';
        }
        
        // Clear existing rows
        if (detailsTableBody) {
            detailsTableBody.innerHTML = '<tr><td colspan="11" style="padding:10px; text-align:center; color:#777;">Loading details...</td></tr>';
        }
        
        // Fetch all items for display
        const displayUrl = tenderItemsDisplayUrl.replace(routeIdPlaceholder, tenderId);
        fetch(displayUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server error (${response.status})`);
                }
                return response.json();
            })
            .then(data => {
                const items = Array.isArray(data) ? data : [];
                
                if (items.length === 0) {
                    if (detailsTableBody) {
                        detailsTableBody.innerHTML = '<tr><td colspan="11" style="padding:20px; text-align:center; color:#666;">No items found in this Tender.</td></tr>';
                    }
                    return;
                }
                
                // Populate the table
                detailsTableBody.innerHTML = '';
                items.forEach(item => {
                    const row = document.createElement('tr');
                    row.style.borderBottom = '1px solid #eee';
                    
                    row.innerHTML = `
                        <td style="padding:8px 10px; font-size:12px;">${item.pl_code || ''}</td>
                        <td style="padding:8px 10px; font-size:12px;">${item.title || ''}</td>
                        <td style="padding:8px 10px; font-size:12px;">${item.description || ''}</td>
                        <td style="padding:8px 10px; font-size:12px;">${item.delivery_location || ''}</td>
                        <td style="padding:8px 10px; text-align:right; font-size:12px;">${item.qty || '0'}</td>
                        <td style="padding:8px 10px; font-size:12px;">${item.unit_symbol || ''}</td>
                        <td style="padding:8px 10px; font-size:12px;">${item.request_for_price || 'No'}</td>
                        <td style="padding:8px 10px; text-align:right; font-size:12px;">${item.price_received ? '₹' + parseFloat(item.price_received).toFixed(2) : ''}</td>
                        <td style="padding:8px 10px; text-align:right; font-size:12px;">${item.price_quoted ? '₹' + parseFloat(item.price_quoted).toFixed(2) : ''}</td>
                        <td style="padding:8px 10px; font-size:12px;">${item.tender_status || ''}</td>
                        <td style="padding:8px 10px; font-size:12px;">${item.bid_result || ''}</td>
                    `;
                    
                    if (detailsTableBody) {
                        detailsTableBody.appendChild(row);
                    }
                });
            })
            .catch(error => {
                console.error('Error loading tender items details:', error);
                if (detailsTableBody) {
                    detailsTableBody.innerHTML = '<tr><td colspan="11" style="padding:20px; text-align:center; color:#dc3545;">Error loading tender items details.</td></tr>';
                }
            });
    }

    // Load Tender Items Details table on page load if tender is already selected (for edit mode)
    const tenderSelect = document.getElementById('tender_id');
    if (tenderSelect) {
        const initialTenderId = tenderSelect.value;
        if (initialTenderId) {
            loadTenderItemsDetailsTable(initialTenderId);
        }
        
        // Load table when tender changes
        tenderSelect.addEventListener('change', function() {
            const tenderId = this.value;
            loadTenderItemsDetailsTable(tenderId);
        });
    }

    function addItemRow() {
        const tbody = document.getElementById('itemsBody');
        const index = itemIndexCounter++;
        
        // Remove empty message if present
        if (tbody.querySelector('tr td[colspan]')) {
            tbody.innerHTML = '';
        }

        const row = document.createElement('tr');
        row.style.borderBottom = '1px solid #dee2e6';
        row.innerHTML = `
            <td style="padding: 10px;">
                <select name="items[${index}][product_id]" id="product_${index}" required
                        onchange="onProductSelect(${index})"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px;">
                    <option value="">Select Product</option>
                    ${products.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                </select>
            </td>
            <td style="padding: 10px;">
                <textarea name="items[${index}][description]" rows="2" placeholder="Enter description"
                          style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px;"></textarea>
            </td>
            <td style="padding: 10px;">
                <input type="text" name="items[${index}][unit_symbol]" id="unit_symbol_${index}" readonly
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; background:#f8f9fa;">
                <input type="hidden" name="items[${index}][unit_id]" id="unit_id_${index}" value="">
            </td>
            <td style="padding: 10px; text-align: right;">
                <input type="number" name="items[${index}][ordered_qty]" value="0" step="0.01" min="0.01" required
                       oninput="recalcItemAmount(${index})"
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; text-align: right;">
            </td>
            <td style="padding: 10px; text-align: right;">
                <input type="number" name="items[${index}][unit_price]" value="0" step="0.01" min="0" required
                       oninput="recalcItemAmount(${index})"
                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px; text-align: right;">
            </td>
            <td style="padding: 10px; text-align: right; color:#333;">
                <span id="item_amount_display_${index}">0.00</span>
                <input type="hidden" name="items[${index}][line_amount]" id="item_amount_${index}" value="0">
            </td>
            <td style="padding: 10px; text-align: center;">
                <button type="button" onclick="removeItemRow(this)" style="padding: 5px 10px; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    }

    function onProductSelect(index) {
        const productSelect = document.getElementById(`product_${index}`);
        const productId = productSelect.value;
        const product = products.find(p => p.id == productId);
        
        if (product) {
            document.getElementById(`unit_id_${index}`).value = product.unit_id;
            document.getElementById(`unit_symbol_${index}`).value = product.unit_symbol;
        } else {
            document.getElementById(`unit_id_${index}`).value = '';
            document.getElementById(`unit_symbol_${index}`).value = '';
        }
        recalcItemAmount(index);
    }

    function removeItemRow(button) {
        const row = button.closest('tr');
        row.remove();
        
        const tbody = document.getElementById('itemsBody');
        if (tbody.children.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="padding: 12px; text-align: center; color: #777;">Click "Add Item" to add products.</td></tr>`;
        }
        recalculateTotal();
    }

    function onItemSelected(index) {
        selectedItemIndex = index;
    }

    function getSelectedItem() {
        // Option 1: User selected a row - return that item
        if (selectedItemIndex !== null) {
            const productSelect = document.querySelector(`select[name="items[${selectedItemIndex}][product_id]"]`);
            const qtyInput = document.querySelector(`input[name="items[${selectedItemIndex}][ordered_qty]"]`);
            const unitSymbolInput = document.querySelector(`input[name="items[${selectedItemIndex}][unit_symbol]"]`);
            
            if (!productSelect || !productSelect.value) {
                alert('Please select a product first.');
                return null;
            }
            
            const product = products.find(p => p.id == productSelect.value);
            if (!product) {
                alert('Invalid product selection.');
                return null;
            }
            
            return {
                index: selectedItemIndex,
                product_name: product.name,
                po_sr_no: '',
                ordered_qty: parseFloat(qtyInput ? qtyInput.value : '0') || 0,
                unit_symbol: unitSymbolInput ? unitSymbolInput.value : product.unit_symbol,
            };
        }
        // Option 2: No row selected - return null to show dropdown in modal
        return null;
    }

    function getAvailableItems() {
        const tbody = document.getElementById('itemsBody');
        const rows = tbody.querySelectorAll('tr');
        const availableItems = [];
        
        rows.forEach((row, index) => {
            const productSelect = row.querySelector(`select[name="items[${index}][product_id]"]`);
            const qtyInput = row.querySelector(`input[name="items[${index}][ordered_qty]"]`);
            const unitSymbolInput = row.querySelector(`input[name="items[${index}][unit_symbol]"]`);
            
            if (productSelect && productSelect.value) {
                const product = products.find(p => p.id == productSelect.value);
                if (product) {
                    availableItems.push({
                        index: index,
                        product_name: product.name,
                        po_sr_no: '',
                        ordered_qty: parseFloat(qtyInput ? qtyInput.value : '0') || 0,
                        unit_symbol: unitSymbolInput ? unitSymbolInput.value : product.unit_symbol,
                    });
                }
            }
        });
        
        return availableItems;
    }

    function openSchedulePopup() {
        const item = getSelectedItem(); // May be null if no row selected
        const availableItems = getAvailableItems();
        if (availableItems.length === 0) {
            alert('Please add at least one product item first.');
            return;
        }
        window.CustomerOrderScheduleModal.open(item, availableItems, schedules, units, function (updatedSchedules) {
            schedules = updatedSchedules;
            renderSchedules();
            syncScheduleHiddenInputs();
        });
    }

    function openAmendmentPopup() {
        const item = getSelectedItem(); // May be null if no row selected
        const availableItems = getAvailableItems();
        if (availableItems.length === 0) {
            alert('Please add at least one product item first.');
            return;
        }
        window.CustomerOrderAmendmentModal.open(item, availableItems, amendments, function (updatedAmendments) {
            amendments = updatedAmendments;
            renderAmendments();
            syncAmendmentHiddenInputs();
        });
    }

    function renderSchedules() {
        const tbody = document.getElementById('scheduleGrid');
        tbody.innerHTML = '';
        if (schedules.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="padding: 12px; text-align: center; color: #777;">No schedule lines added.</td></tr>`;
            return;
        }
        schedules.forEach((s) => {
            const row = document.createElement('tr');
            row.style.borderBottom = '1px solid #dee2e6';
            row.innerHTML = `
                <td style="padding: 10px; color: #333;">${s.product_name}</td>
                <td style="padding: 10px; color: #333;">${s.po_sr_no || ''}</td>
                <td style="padding: 10px; text-align: right; color: #333;">${s.quantity}</td>
                <td style="padding: 10px; color: #333;">${s.unit_symbol || ''}</td>
                <td style="padding: 10px; color: #333;">${s.start_date}</td>
                <td style="padding: 10px; color: #333;">${s.end_date}</td>
                <td style="padding: 10px; color: #333;">${s.inspection_clause || ''}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function renderAmendments() {
        const tbody = document.getElementById('amendmentGrid');
        tbody.innerHTML = '';
        if (amendments.length === 0) {
            tbody.innerHTML = `<tr><td colspan="7" style="padding: 12px; text-align: center; color: #777;">No amendments added.</td></tr>`;
            return;
        }
        amendments.forEach((a) => {
            const row = document.createElement('tr');
            row.style.borderBottom = '1px solid #dee2e6';
            row.innerHTML = `
                <td style="padding: 10px; color: #333;">${a.product_name}</td>
                <td style="padding: 10px; color: #333;">${a.po_sr_no || ''}</td>
                <td style="padding: 10px; color: #333;">${a.amendment_no || ''}</td>
                <td style="padding: 10px; color: #333;">${a.amendment_date}</td>
                <td style="padding: 10px; text-align: right; color: #333;">${a.existing_quantity || ''}</td>
                <td style="padding: 10px; text-align: right; color: #333;">${a.new_quantity}</td>
                <td style="padding: 10px; color: #333;">${a.remarks || ''}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function syncScheduleHiddenInputs() {
        const container = document.getElementById('hiddenScheduleInputs');
        container.innerHTML = '';
        schedules.forEach((s, index) => {
            const base = `schedules[${index}]`;
            container.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="${base}[item_index]" value="${s.item_index}">
                <input type="hidden" name="${base}[po_sr_no]" value="${s.po_sr_no || ''}">
                <input type="hidden" name="${base}[quantity]" value="${s.quantity}">
                <input type="hidden" name="${base}[unit_id]" value="${s.unit_id}">
                <input type="hidden" name="${base}[start_date]" value="${s.start_date}">
                <input type="hidden" name="${base}[end_date]" value="${s.end_date}">
                <input type="hidden" name="${base}[inspection_clause]" value="${s.inspection_clause || ''}">
            `);
        });
    }

    function syncAmendmentHiddenInputs() {
        const container = document.getElementById('hiddenAmendmentInputs');
        container.innerHTML = '';
        amendments.forEach((a, index) => {
            const base = `amendments[${index}]`;
            container.insertAdjacentHTML('beforeend', `
                <input type="hidden" name="${base}[item_index]" value="${a.item_index}">
                <input type="hidden" name="${base}[po_sr_no]" value="${a.po_sr_no || ''}">
                <input type="hidden" name="${base}[amendment_no]" value="${a.amendment_no || ''}">
                <input type="hidden" name="${base}[amendment_date]" value="${a.amendment_date}">
                <input type="hidden" name="${base}[existing_quantity]" value="${a.existing_quantity || ''}">
                <input type="hidden" name="${base}[new_quantity]" value="${a.new_quantity}">
                <input type="hidden" name="${base}[existing_info]" value="${a.existing_info || ''}">
                <input type="hidden" name="${base}[new_info]" value="${a.new_info || ''}">
                <input type="hidden" name="${base}[remarks]" value="${a.remarks || ''}">
            `);
        });
    }

    function recalcItemAmount(index) {
        const qtyInput = document.querySelector(`input[name="items[${index}][ordered_qty]"]`);
        const priceInput = document.querySelector(`input[name="items[${index}][unit_price]"]`);
        const qty = parseFloat(qtyInput ? qtyInput.value : '0') || 0;
        const price = parseFloat(priceInput ? priceInput.value : '0') || 0;
        const amount = qty * price;
        const amountField = document.getElementById(`item_amount_${index}`);
        const amountDisplay = document.getElementById(`item_amount_display_${index}`);
        if (amountField) amountField.value = amount.toFixed(2);
        if (amountDisplay) amountDisplay.textContent = amount.toFixed(2);
        recalculateTax();
    }

    function recalculateTotal() {
        recalculateTax();
    }

    function recalculateTax() {
        // Calculate Total from all items
        let total = 0;
        const itemAmountInputs = document.querySelectorAll('input[id^="item_amount_"]');
        itemAmountInputs.forEach(input => {
            total += parseFloat(input.value || '0') || 0;
        });
        
        document.getElementById('total_amount').value = total.toFixed(2);
        document.getElementById('total_amount_display').value = total.toFixed(2);
        
        // Get tax type
        const taxType = document.querySelector('input[name="tax_type"]:checked')?.value || 'cgst_sgst';
        const gstPercent = parseFloat(document.getElementById('gst_percent').value || '0') || 0;
        const freight = parseFloat(document.getElementById('freight').value || '0') || 0;
        const inspectionCharges = parseFloat(document.getElementById('inspection_charges').value || '0') || 0;
        
        let cgstPercent = 0;
        let sgstPercent = 0;
        let cgstAmount = 0;
        let sgstAmount = 0;
        let igstPercent = 0;
        let igstAmount = 0;
        
        if (taxType === 'cgst_sgst') {
            // Show CGST+SGST section, hide IGST
            document.getElementById('cgst_sgst_section').style.display = 'block';
            document.getElementById('igst_section').style.display = 'none';
            
            // Split GST% into two halves
            cgstPercent = gstPercent / 2;
            sgstPercent = gstPercent / 2;
            cgstAmount = (total * cgstPercent) / 100;
            sgstAmount = (total * sgstPercent) / 100;
            
            document.getElementById('cgst_percent').value = cgstPercent.toFixed(2);
            document.getElementById('cgst_amount').value = cgstAmount.toFixed(2);
            document.getElementById('cgst_amount_display').value = cgstAmount.toFixed(2);
            
            document.getElementById('sgst_percent').value = sgstPercent.toFixed(2);
            document.getElementById('sgst_amount').value = sgstAmount.toFixed(2);
            document.getElementById('sgst_amount_display').value = sgstAmount.toFixed(2);
            
            // Clear IGST
            document.getElementById('igst_percent').value = '0';
            document.getElementById('igst_amount').value = '0';
            document.getElementById('igst_amount_display').value = '0';
        } else {
            // Show IGST section, hide CGST+SGST
            document.getElementById('cgst_sgst_section').style.display = 'none';
            document.getElementById('igst_section').style.display = 'block';
            
            // IGST% = GST%
            igstPercent = gstPercent;
            igstAmount = (total * igstPercent) / 100;
            
            document.getElementById('igst_percent').value = igstPercent.toFixed(2);
            document.getElementById('igst_amount').value = igstAmount.toFixed(2);
            document.getElementById('igst_amount_display').value = igstAmount.toFixed(2);
            
            // Clear CGST+SGST
            document.getElementById('cgst_percent').value = '0';
            document.getElementById('cgst_amount').value = '0';
            document.getElementById('cgst_amount_display').value = '0';
            document.getElementById('sgst_percent').value = '0';
            document.getElementById('sgst_amount').value = '0';
            document.getElementById('sgst_amount_display').value = '0';
        }
        
        // Calculate total GST amount (CGST+SGST or IGST)
        const totalGstAmount = cgstAmount + sgstAmount + igstAmount;
        document.getElementById('gst_amount').value = totalGstAmount.toFixed(2);
        document.getElementById('gst_amount_display').value = totalGstAmount.toFixed(2);
        
        // Calculate Net Amount = Total + All tax amounts + Freight + Inspection Charges
        const netAmount = total + cgstAmount + sgstAmount + igstAmount + freight + inspectionCharges;
        document.getElementById('net_amount').value = netAmount.toFixed(2);
        document.getElementById('net_amount_display').value = netAmount.toFixed(2);
    }

    function toggleShowMore(id) {
        const fullDiv = document.getElementById(id + '_full');
        if (fullDiv) {
            fullDiv.style.display = fullDiv.style.display === 'none' ? 'block' : 'none';
        }
    }

    // Initialize tax calculation on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Set initial display state based on tax type
        const taxType = document.querySelector('input[name="tax_type"]:checked')?.value || 'cgst_sgst';
        if (taxType === 'igst') {
            document.getElementById('cgst_sgst_section').style.display = 'none';
            document.getElementById('igst_section').style.display = 'block';
        } else {
            document.getElementById('cgst_sgst_section').style.display = 'block';
            document.getElementById('igst_section').style.display = 'none';
        }
        recalculateTax();
    });

    // Add Product Modal Functions
    let targetProductSelectIndex = null; // Store which product select to update after creation

    function openAddProductModal(index = null) {
        targetProductSelectIndex = index;
        document.getElementById('addProductModal').style.display = 'flex';
        document.getElementById('addProductForm').reset();
        document.getElementById('addProductModalErrors').style.display = 'none';
    }

    function closeAddProductModal() {
        document.getElementById('addProductModal').style.display = 'none';
        targetProductSelectIndex = null;
        document.getElementById('addProductForm').reset();
        document.getElementById('addProductModalErrors').style.display = 'none';
    }

    // Handle form submission
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        fetch('{{ route("products.store") }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Add new product to products array
                const newProduct = {
                    id: data.product.id,
                    name: data.product.name,
                    unit_id: data.product.unit_id,
                    unit_symbol: data.product.unit_symbol
                };
                products.push(newProduct);
                
                // Update all product dropdowns
                updateAllProductDropdowns(newProduct);
                
                // If a specific index was provided, select the new product in that dropdown
                if (targetProductSelectIndex !== null) {
                    const select = document.getElementById(`product_${targetProductSelectIndex}`);
                    if (select) {
                        select.value = newProduct.id;
                        onProductSelect(targetProductSelectIndex);
                    }
                }
                
                // Close modal
                closeAddProductModal();
                
                // Show success message
                alert('Product created successfully and added to the list!');
            } else {
                throw new Error(data.message || 'Failed to create product');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const errorsList = document.getElementById('addProductModalErrorsList');
            errorsList.innerHTML = '';
            
            if (error.errors) {
                // Display validation errors
                Object.keys(error.errors).forEach(key => {
                    const errorMessages = Array.isArray(error.errors[key]) ? error.errors[key] : [error.errors[key]];
                    errorMessages.forEach(msg => {
                        const li = document.createElement('li');
                        li.textContent = msg;
                        errorsList.appendChild(li);
                    });
                });
                document.getElementById('addProductModalErrors').style.display = 'block';
            } else if (error.message) {
                const li = document.createElement('li');
                li.textContent = error.message;
                errorsList.appendChild(li);
                document.getElementById('addProductModalErrors').style.display = 'block';
            } else {
                alert('Error creating product: Unknown error occurred');
            }
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    });

    function updateAllProductDropdowns(newProduct) {
        // Update all product select dropdowns in the items table
        const allProductSelects = document.querySelectorAll('select[id^="product_"]');
        allProductSelects.forEach(select => {
            // Check if option already exists
            const existingOption = Array.from(select.options).find(opt => opt.value == newProduct.id);
            if (!existingOption) {
                const option = document.createElement('option');
                option.value = newProduct.id;
                option.textContent = newProduct.name;
                select.appendChild(option);
            }
        });
    }

    // Close modal when clicking outside
    document.getElementById('addProductModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeAddProductModal();
        }
    });
</script>
@endpush
@endsection



