@extends('layouts.dashboard')

@section('title', 'Purchase Order - Edit')

@section('content')
@php
    $displayExpectedDate = function ($value) {
        if (empty($value)) {
            return '';
        }
        try {
            return \Carbon\Carbon::parse($value)->format('d-m-Y');
        } catch (\Exception $e) {
            return $value;
        }
    };
@endphp
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Edit Purchase Order</h2>
        <a href="{{ route('purchase-orders.index') }}" style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-list"></i> List
        </a>
    </div>

    @if($errors->any())
        <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:5px; margin-bottom:20px; border:1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin:10px 0 0 20px; padding:0;">
                @foreach($errors->all() as $error)
                    @php
                        // Format error messages to be more user-friendly
                        $formattedError = $error;
                        // Replace items.X.field with "Row X+1: Field Name"
                        if (preg_match('/items\.(\d+)\.(.+)/', $error, $matches)) {
                            $rowNumber = (int)$matches[1] + 1;
                            $fieldName = str_replace('_', ' ', $matches[2]);
                            $fieldName = ucwords($fieldName);
                            // Remove common validation suffixes
                            $fieldName = preg_replace('/\s+(is required|must be at least|must be|is invalid)$/i', '', $fieldName);
                            $formattedError = "Row {$rowNumber}: " . $fieldName . " - " . preg_replace('/^The items\.\d+\.\w+\s+/', '', $error);
                        }
                        // Replace field names with friendly names
                        $formattedError = str_replace('company id', 'Company Name', $formattedError);
                        $formattedError = str_replace('billing address id', 'Billing Address', $formattedError);
                        $formattedError = str_replace('po quantity', 'PO Quantity', $formattedError);
                        $formattedError = str_replace('item name', 'Item Name', $formattedError);
                        $formattedError = str_replace('item description', 'Item Description', $formattedError);
                        $formattedError = str_replace('ship to', 'Ship To', $formattedError);
                        $formattedError = str_replace('supplier id', 'Supplier', $formattedError);
                    @endphp
                    <li>{{ $formattedError }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('purchase-orders.update', $purchaseOrder->id) }}" method="POST" enctype="multipart/form-data" id="purchaseOrderForm">
        @csrf
        @method('PUT')

        {{-- General Information --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">General Information</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Purchase Order No</label>
                    <input type="text" value="{{ $purchaseOrder->po_no }}" readonly
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Purchase Indent No <span style="color:red;">*</span></label>
                    <select name="purchase_indent_id" id="purchase_indent_id" required
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        <option value="">Select Purchase Indent</option>
                        @foreach($purchaseIndents as $indent)
                            <option value="{{ $indent->id }}" {{ $purchaseOrder->purchase_indent_id == $indent->id ? 'selected' : '' }}>
                                {{ $indent->indent_no }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Branch selection (visible for users who have at least one branch available) --}}
                @if(isset($branches) && $branches->count() > 0)
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Branch</label>
                        <select name="branch_id"
                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                            @php $currentBranchId = old('branch_id', $selectedBranchId ?? $purchaseOrder->branch_id); @endphp
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $currentBranchId == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>

        {{-- Supplier Details --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Supplier Details</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Supplier Name <span style="color:red;">*</span></label>
                    <select name="supplier_id" id="supplier_id" required
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:1/-1;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 1</label>
                            <input type="text" name="supplier_address_line_1" id="supplier_address_line_1"
                                   value="{{ old('supplier_address_line_1', $purchaseOrder->supplier_address_line_1) }}"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 2</label>
                            <input type="text" name="supplier_address_line_2" id="supplier_address_line_2"
                                   value="{{ old('supplier_address_line_2', $purchaseOrder->supplier_address_line_2) }}"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">City</label>
                            <input type="text" name="supplier_city" id="supplier_city"
                                   value="{{ old('supplier_city', $purchaseOrder->supplier_city) }}"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">State</label>
                            <input type="text" name="supplier_state" id="supplier_state"
                                   value="{{ old('supplier_state', $purchaseOrder->supplier_state) }}"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Email</label>
                            <input type="email" name="supplier_email" id="supplier_email"
                                   value="{{ old('supplier_email', $purchaseOrder->supplier_email) }}"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">GST No</label>
                            <input type="text" name="supplier_gst_no" id="supplier_gst_no"
                                   value="{{ old('supplier_gst_no', $purchaseOrder->supplier_gst_no) }}"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ship To Section --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Ship To</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Ship To <span style="color:red;">*</span></label>
                    <select name="ship_to" id="ship_to" required
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        @php $shipToValue = old('ship_to', $purchaseOrder->ship_to); @endphp
                        <option value="">Select Ship To</option>
                        <option value="Customer" {{ $shipToValue == 'Customer' ? 'selected' : '' }}>Customer</option>
                        <option value="Subcontractor" {{ $shipToValue == 'Subcontractor' ? 'selected' : '' }}>Subcontractor</option>
                        <option value="Company" {{ $shipToValue == 'Company' ? 'selected' : '' }}>Company</option>
                    </select>
                </div>
                <div id="ship_to_dynamic_field" style="display:none;">
                    <label id="ship_to_label" style="display:block; margin-bottom:6px; color:#333; font-weight:500;"></label>
                    <select name="" id="ship_to_select"
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        <option value="">Select</option>
                    </select>
                </div>
            </div>
            <div id="ship_to_address_section" style="padding:0 20px 20px 20px;">
                <h4 style="margin:15px 0 10px 0; color:#333; font-size:16px;">Ship To Address</h4>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 1</label>
                        <input type="text" name="ship_to_address_line_1" id="ship_to_address_line_1"
                               value="{{ old('ship_to_address_line_1', $purchaseOrder->ship_to_address_line_1) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 2</label>
                        <input type="text" name="ship_to_address_line_2" id="ship_to_address_line_2"
                               value="{{ old('ship_to_address_line_2', $purchaseOrder->ship_to_address_line_2) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">City</label>
                        <input type="text" name="ship_to_city" id="ship_to_city"
                               value="{{ old('ship_to_city', $purchaseOrder->ship_to_city) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">State</label>
                        <input type="text" name="ship_to_state" id="ship_to_state"
                               value="{{ old('ship_to_state', $purchaseOrder->ship_to_state) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Pincode</label>
                        <input type="text" name="ship_to_pincode" id="ship_to_pincode"
                               value="{{ old('ship_to_pincode', $purchaseOrder->ship_to_pincode) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Email</label>
                        <input type="email" name="ship_to_email" id="ship_to_email"
                               value="{{ old('ship_to_email', $purchaseOrder->ship_to_email) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Contact No</label>
                        <input type="text" name="ship_to_contact_no" id="ship_to_contact_no"
                               value="{{ old('ship_to_contact_no', $purchaseOrder->ship_to_contact_no) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">GST No</label>
                        <input type="text" name="ship_to_gst_no" id="ship_to_gst_no"
                               value="{{ old('ship_to_gst_no', $purchaseOrder->ship_to_gst_no) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                </div>
            </div>
        </div>


        {{-- Billing Address --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Billing Address</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Billing Address Company Name <span style="color:red;">*</span></label>
                    <select name="billing_address_id" id="billing_address_id" required
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        <option value="">Select Billing Address</option>
                        @foreach($billingAddresses as $billing)
                            <option value="{{ $billing->id }}" {{ old('billing_address_id', $purchaseOrder->billing_address_id) == $billing->id ? 'selected' : '' }}>
                                {{ $billing->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:1/-1;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 1</label>
                            <input type="text" name="billing_address_line_1" id="billing_address_line_1"
                                   value="{{ old('billing_address_line_1', $purchaseOrder->billing_address_line_1) }}"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 2</label>
                            <input type="text" name="billing_address_line_2" id="billing_address_line_2"
                                   value="{{ old('billing_address_line_2', $purchaseOrder->billing_address_line_2) }}"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">City</label>
                            <input type="text" name="billing_city" id="billing_city"
                                   value="{{ old('billing_city', $purchaseOrder->billing_city) }}"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">State</label>
                            <input type="text" name="billing_state" id="billing_state"
                                   value="{{ old('billing_state', $purchaseOrder->billing_state) }}"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Email</label>
                            <input type="email" name="billing_email" id="billing_email"
                                   value="{{ old('billing_email', $purchaseOrder->billing_email) }}"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">GST No</label>
                            <input type="text" name="billing_gst_no" id="billing_gst_no"
                                   value="{{ old('billing_gst_no', $purchaseOrder->billing_gst_no) }}"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Information Section --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Product Information</h3>
                <button type="button" onclick="addProductRow()" style="padding:8px 16px; background:#28a745; color:white; border:none; border-radius:5px; font-size:14px; cursor:pointer; font-weight:500;">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>
            <div style="padding:20px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;" id="itemsTable">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Item Name <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Item Description</th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Pack Details <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">Approved Qty</th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">Already Raised PO Qty</th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">PO Quantity <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Expected Delivery Date</th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Unit</th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">Qty in KG</th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">Price <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">Amount</th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">PO Status</th>
                            <th style="padding:10px; text-align:center; color:#333; font-size:12px; width:80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrder->items as $index => $item)
                            @php
                                $isDisabled = $item->approved_quantity == $item->already_raised_po_qty;
                                $unit = isset($units) ? $units->firstWhere('id', $item->unit_id) : null;
                            @endphp
                            <tr data-index="{{ $index }}">
                                <td style="padding:6px 8px;">
                                    <input type="hidden" name="items[{{ $index }}][purchase_indent_item_id]" class="purchase_indent_item_id" value="{{ $item->purchase_indent_item_id }}">
                                    <input type="hidden" name="items[{{ $index }}][raw_material_id]" class="raw_material_id" value="{{ $item->raw_material_id }}">
                                    <select name="items[{{ $index }}][item_name]" class="item_name" required
                                            {{ $isDisabled ? 'disabled' : '' }}
                                            style="width:120px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; {{ $isDisabled ? 'background:#f8f9fa;' : '' }}">
                                        <option value="">Select Item</option>
                                        <option value="{{ $item->item_name }}" selected>{{ $item->item_name }}</option>
                                    </select>
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="text" name="items[{{ $index }}][item_description]" class="item_description"
                                           value="{{ old('items.'.$index.'.item_description', $item->item_description) }}"
                                           readonly
                                           style="width:150px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; background:#f8f9fa;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="text" name="items[{{ $index }}][pack_details]" class="pack_details" required
                                           value="{{ old('items.'.$index.'.pack_details', $item->pack_details) }}"
                                           {{ $isDisabled ? 'readonly' : '' }}
                                           style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; {{ $isDisabled ? 'background:#f8f9fa;' : '' }}">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="number" step="0.001" name="items[{{ $index }}][approved_quantity]" class="approved_quantity"
                                           value="{{ old('items.'.$index.'.approved_quantity', $item->approved_quantity) }}"
                                           readonly
                                           style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; text-align:right; background:#f8f9fa;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="number" step="0.001" name="items[{{ $index }}][already_raised_po_qty]" class="already_raised_po_qty"
                                           value="{{ old('items.'.$index.'.already_raised_po_qty', $item->already_raised_po_qty) }}"
                                           readonly
                                           style="width:120px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; text-align:right; background:#f8f9fa;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="number" step="1" min="1" name="items[{{ $index }}][po_quantity]" class="po_quantity" required
                                           value="{{ old('items.'.$index.'.po_quantity', (int)$item->po_quantity) }}"
                                           {{ $isDisabled ? 'readonly' : '' }}
                                           style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; text-align:right; {{ $isDisabled ? 'background:#f8f9fa;' : '' }}">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="text" name="items[{{ $index }}][expected_delivery_date]" class="expected_delivery_date date-input" placeholder="DD-MM-YYYY"
                                           value="{{ old('items.'.$index.'.expected_delivery_date', $displayExpectedDate($item->expected_delivery_date)) }}"
                                           {{ $isDisabled ? 'readonly' : '' }}
                                           style="width:140px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; {{ $isDisabled ? 'background:#f8f9fa;' : '' }}">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="hidden" name="items[{{ $index }}][unit_id]" class="unit_id"
                                           value="{{ old('items.'.$index.'.unit_id', $item->unit_id) }}">
                                    <input type="text" class="unit_symbol"
                                           value="{{ optional($unit)->symbol ?? '' }}"
                                           readonly
                                           style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; background:#f8f9fa;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="number" step="0.001" name="items[{{ $index }}][qty_in_kg]" class="qty_in_kg"
                                           value="{{ old('items.'.$index.'.qty_in_kg', $item->qty_in_kg) }}"
                                           {{ $isDisabled ? 'readonly' : '' }}
                                           style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; text-align:right; {{ $isDisabled ? 'background:#f8f9fa;' : '' }}">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="number" step="0.01" name="items[{{ $index }}][price]" class="price" required
                                           value="{{ old('items.'.$index.'.price', $item->price) }}"
                                           {{ $isDisabled ? 'readonly' : '' }}
                                           style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; text-align:right; {{ $isDisabled ? 'background:#f8f9fa;' : '' }}">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="number" step="0.01" name="items[{{ $index }}][amount]" class="amount"
                                           value="{{ old('items.'.$index.'.amount', $item->amount) }}"
                                           readonly
                                           style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; text-align:right; background:#f8f9fa;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <select name="items[{{ $index }}][po_status]" class="po_status"
                                            style="width:150px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px;">
                                        <option value="">Select Status</option>
                                        <option value="PO Placed" {{ $item->po_status == 'PO Placed' ? 'selected' : '' }}>PO Placed</option>
                                        <option value="PO Partially Placed" {{ $item->po_status == 'PO Partially Placed' ? 'selected' : '' }}>PO Partially Placed</option>
                                        <option value="Awaiting for Approval" {{ $item->po_status == 'Awaiting for Approval' ? 'selected' : '' }}>Awaiting for Approval</option>
                                        <option value="PO Not Placed" {{ $item->po_status == 'PO Not Placed' ? 'selected' : '' }}>PO Not Placed</option>
                                    </select>
                                </td>
                            <td style="padding:6px 8px; text-align:center;">
                                <button type="button" onclick="removeRow(this)" style="padding:6px 12px; background:#dc3545; color:white; border:none; border-radius:5px; font-size:12px; cursor:pointer;">
                                    <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @php
            $subtotal = $purchaseOrder->items->sum('amount');
            $discountPercent = $purchaseOrder->discount_percent ?? ($subtotal > 0 ? ($purchaseOrder->discount / $subtotal) * 100 : 0);
            $gstPercent = $purchaseOrder->gst_percent ?? 0;
            $taxTypeValue = old('tax_type', ($purchaseOrder->sgst > 0 ? 'cgst_sgst' : 'igst'));
            $totalTax = $taxTypeValue === 'cgst_sgst'
                ? (($purchaseOrder->gst / 2) + $purchaseOrder->sgst)
                : ($purchaseOrder->gst + $purchaseOrder->sgst);
        @endphp

                        {{-- Amount Calculation Section --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Amount Calculation</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 440px; gap:20px; align-items:flex-start;">
                {{-- GST Type --}}
                <div style="display:flex; gap:30px; align-items:center; justify-content:center; width:100%;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:14px; font-weight:600; color:#333; cursor:pointer;">
                        <input type="radio" name="tax_type" id="tax_type_cgst_sgst" value="cgst_sgst" {{ $taxTypeValue == 'cgst_sgst' ? 'checked' : '' }} required style="cursor:pointer;">
                        <span>CGST and SGST</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; font-size:14px; font-weight:600; color:#333; cursor:pointer;">
                        <input type="radio" name="tax_type" id="tax_type_igst" value="igst" {{ $taxTypeValue == 'igst' ? 'checked' : '' }} required style="cursor:pointer;">
                        <span>IGST</span>
                    </label>
                </div>

                {{-- Amount summary --}}
                <div style="width:100%; display:flex; flex-direction:column; gap:10px;">
                    {{-- Gross Amount (Total of items) --}}
                    <div style="display:flex; align-items:center; gap:12px;">
                        <label style="flex:0 0 120px; color:#333; font-weight:600; font-size:13px;">Total:</label>
                        <span style="width:30px; font-size:13px; color:#444; text-align:center;">Rs.</span>
                        <input type="text" id="total" value="{{ old('total', number_format($subtotal, 2)) }}" readonly
                               style="flex:1; min-width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                    </div>

                    {{-- Overall Discount (%) --}}
                    <div style="display:flex; align-items:center; gap:12px;">
                        <label style="flex:0 0 120px; color:#333; font-weight:600; font-size:13px;">Discount:</label>
                        <input type="number" step="0.01" min="0" name="discount_percent" id="discount_percent"
                               value="{{ old('discount_percent', number_format($discountPercent, 2)) }}"
                               style="width:80px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;"
                               placeholder="Enter in (%)">
                        <span style="width:30px; font-size:13px; color:#444; text-align:center;">Rs.</span>
                        <input type="number" step="0.01" min="0" name="discount" id="discount"
                               value="{{ old('discount', number_format($purchaseOrder->discount, 2)) }}"
                               style="flex:1; min-width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right; background:#f8f9fa;"
                               placeholder="Discount Amount">
                    </div>

                    {{-- GST (rate & amount) --}}
                    <div style="display:flex; align-items:center; gap:12px;">
                        <label style="flex:0 0 120px; color:#333; font-weight:600; font-size:13px;">GST <span style="color:red;">*</span></label>
                        <div style="display:flex; gap:10px; align-items:center; flex:1;">
                            <input type="number" step="0.01" min="0" name="gst_percent" id="gst_percent" placeholder="%" required
                                   value="{{ old('gst_percent', $purchaseOrder->gst_percent ?? '0') }}"
                                   style="width:80px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;">
                            <span style="width:30px; font-size:13px; color:#444; text-align:center;">Rs.</span>
                            <input type="number" step="0.01" min="0" name="gst" id="gst" readonly
                                   value="{{ old('gst', number_format($purchaseOrder->gst, 2)) }}"
                                   style="flex:1; min-width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right; background:#f8f9fa;">
                    </div>
                    </div>

                    {{-- CGST row (amount only for display) --}}
                    <div id="cgst_section" style="display:{{ $taxTypeValue == 'cgst_sgst' ? 'flex' : 'none' }}; align-items:center; gap:12px;">
                        <label style="flex:0 0 120px; color:#333; font-weight:600; font-size:13px;">CGST:</label>
                        <input type="text" id="cgst_amount" value="{{ $taxTypeValue == 'cgst_sgst' ? number_format($purchaseOrder->gst / 2, 2) : '0.00' }}" readonly
                               style="flex:1; min-width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                        <input type="hidden" id="cgst_percent" value="{{ $taxTypeValue == 'cgst_sgst' ? number_format($gstPercent / 2, 2) : '0' }}">
                    </div>

                    {{-- SGST row --}}
                    <div id="sgst_section" style="display:{{ $taxTypeValue == 'cgst_sgst' ? 'flex' : 'none' }}; align-items:center; gap:12px;">
                        <label style="flex:0 0 120px; color:#333; font-weight:600; font-size:13px;">SGST:</label>
                        <input type="text" name="sgst" id="sgst" value="{{ old('sgst', number_format($purchaseOrder->sgst, 2)) }}" readonly
                               style="flex:1; min-width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                        <input type="hidden" id="sgst_percent" value="{{ $taxTypeValue == 'cgst_sgst' ? number_format($gstPercent / 2, 2) : '0' }}">
                    </div>

                    {{-- IGST row --}}
                    <div id="igst_section" style="display:flex; align-items:center; gap:12px;">
                        <label style="flex:0 0 120px; color:#333; font-weight:600; font-size:13px;">IGST:</label>
                        <input type="text" name="igst" id="igst" value="{{ old('igst', number_format($purchaseOrder->gst + $purchaseOrder->sgst, 2)) }}" readonly
                               style="flex:1; min-width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                        <input type="hidden" id="igst_percent" value="{{ $taxTypeValue == 'igst' ? number_format($gstPercent, 2) : '0' }}">
                    </div>

                    {{-- Total Tax (sum of CGST+SGST or IGST) --}}
                    <div style="display:flex; align-items:center; gap:12px;">
                        <label style="flex:0 0 120px; color:#333; font-weight:600; font-size:13px;">Total Tax:</label>
                        <span style="width:30px; font-size:13px; color:#444; text-align:center;">Rs.</span>
                        <input type="text" id="total_tax" value="{{ number_format($totalTax, 2) }}" readonly
                               style="flex:1; min-width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                    </div>

                    {{-- Net Amount --}}
                    <div style="display:flex; align-items:center; gap:12px; margin-top:4px;">
                        <label style="flex:0 0 120px; color:#333; font-weight:700; font-size:13px;">Net Amount:</label>
                        <span style="width:30px; font-size:13px; font-weight:600; color:#444; text-align:center;">Rs.</span>
                        <input type="text" name="net_amount" id="net_amount" value="{{ old('net_amount', number_format($purchaseOrder->net_amount, 2)) }}" readonly
                               style="flex:1; min-width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right; font-weight:600; color:#4a4a4a;">
                    </div>
                </div>
            </div>
        </div>

        {{-- Terms and Conditions Section --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Terms and Conditions</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Freight/Charges</label>
                    <textarea name="freight_charges" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('freight_charges', $purchaseOrder->freight_charges) }}</textarea>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Terms of Payment</label>
                    <textarea name="terms_of_payment" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('terms_of_payment', $purchaseOrder->terms_of_payment) }}</textarea>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Special Conditions</label>
                    <textarea name="special_conditions" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('special_conditions', $purchaseOrder->special_conditions) }}</textarea>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Inspection</label>
                    <textarea name="inspection" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('inspection', $purchaseOrder->inspection) }}</textarea>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Name of Transport</label>
                    <input type="text" name="name_of_transport"
                           value="{{ old('name_of_transport', $purchaseOrder->name_of_transport) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Test Certificate</label>
                    <input type="text" name="transport_certificate"
                           value="{{ old('transport_certificate', $purchaseOrder->transport_certificate) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Incase Of Failure/Damage</label>
                    <textarea name="insurance_of_goods_damages" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('insurance_of_goods_damages', $purchaseOrder->insurance_of_goods_damages) }}</textarea>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Warranty Expiry</label>
                    <input type="text" name="warranty_expiry" class="warranty-expiry-date" placeholder="DD-MM-YYYY"
                           value="{{ old('warranty_expiry', optional($purchaseOrder->warranty_expiry)->format('d-m-Y')) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>
            </div>
        </div>

        <div style="display:flex; gap:15px; margin-top:10px;">
            <button type="submit" style="padding:12px 24px; background:#667eea; color:white; border:none; border-radius:5px; font-weight:500; cursor:pointer;">
                Update
            </button>
            <a href="{{ route('purchase-orders.index') }}" style="padding:12px 24px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500;">
                Cancel
            </a>
        </div>
    </form>
</div>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@include('purchase.purchase_orders.partials.scripts')
@endsection







