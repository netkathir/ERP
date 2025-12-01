@extends('layouts.dashboard')

@section('title', 'Purchase Order - Create')

@section('content')
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Create Purchase Order</h2>
        <a href="{{ route('purchase-orders.index') }}" style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-list"></i> List
        </a>
    </div>

    @if($errors->any())
        <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:5px; margin-bottom:20px; border:1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin:10px 0 0 20px; padding:0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('purchase-orders.store') }}" method="POST" enctype="multipart/form-data" id="purchaseOrderForm">
        @csrf

        {{-- General Information --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">General Information</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Purchase Order No</label>
                    <input type="text" value="{{ $poNo }}" readonly
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Purchase Indent No</label>
                    <select name="purchase_indent_id" id="purchase_indent_id" 
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;" {{ $purchaseIndents->count() == 0 ? 'disabled' : '' }}>
                        <option value="">Select Purchase Indent</option>
                        @forelse($purchaseIndents as $indent)
                            <option value="{{ $indent->id }}">{{ $indent->indent_no }}</option>
                        @empty
                            <option value="" disabled>No approved purchase indents available</option>
                        @endforelse
                    </select>
                    @if($purchaseIndents->count() == 0)
                        <small style="color:#dc3545; display:block; margin-top:5px;">
                            No approved purchase indents with available quantity found. Please approve a purchase indent first.
                        </small>
                    @endif
                </div>

                {{-- Branch selection for Super Admin --}}
                @if(auth()->user()->isSuperAdmin() && isset($branches) && $branches->count() > 0)
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Branch</label>
                        <select name="branch_id"
                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                            @php $currentBranchId = old('branch_id', $selectedBranchId ?? null); @endphp
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

        {{-- Ship To Section --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Ship To</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Ship To <span style="color:#dc3545;">*</span></label>
                    <select name="ship_to" id="ship_to" 
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        <option value="">Select Ship To</option>
                        <option value="Customer">Customer</option>
                        <option value="Subcontractor">Subcontractor</option>
                        <option value="Company">Company</option>
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
            <div id="ship_to_address_section" style="padding:0 20px 20px 20px; display:none;">
                <h4 style="margin:15px 0 10px 0; color:#333; font-size:16px;">Ship To Address</h4>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 1</label>
                        <input type="text" name="ship_to_address_line_1" id="ship_to_address_line_1"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 2</label>
                        <input type="text" name="ship_to_address_line_2" id="ship_to_address_line_2"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">City</label>
                        <input type="text" name="ship_to_city" id="ship_to_city"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">State</label>
                        <input type="text" name="ship_to_state" id="ship_to_state"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Pincode</label>
                        <input type="text" name="ship_to_pincode" id="ship_to_pincode"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Email</label>
                        <input type="email" name="ship_to_email" id="ship_to_email"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Contact No</label>
                        <input type="text" name="ship_to_contact_no" id="ship_to_contact_no"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">GST No</label>
                        <input type="text" name="ship_to_gst_no" id="ship_to_gst_no"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    </div>
                </div>
            </div>
        </div>

        {{-- Supplier Address --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Supplier Address</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Supplier <span style="color:#dc3545;">*</span></label>
                    <select name="supplier_id" id="supplier_id" 
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:1/-1;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 1</label>
                            <input type="text" name="supplier_address_line_1" id="supplier_address_line_1"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 2</label>
                            <input type="text" name="supplier_address_line_2" id="supplier_address_line_2"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">City</label>
                            <input type="text" name="supplier_city" id="supplier_city"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">State</label>
                            <input type="text" name="supplier_state" id="supplier_state"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Email</label>
                            <input type="email" name="supplier_email" id="supplier_email"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">GST No</label>
                            <input type="text" name="supplier_gst_no" id="supplier_gst_no"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
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
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Billing Address <span style="color:#dc3545;">*</span></label>
                    <select name="billing_address_id" id="billing_address_id" 
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        <option value="">Select Billing Address</option>
                        @foreach($billingAddresses as $billing)
                            <option value="{{ $billing->id }}">{{ $billing->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:1/-1;">
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 1</label>
                            <input type="text" name="billing_address_line_1" id="billing_address_line_1"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 2</label>
                            <input type="text" name="billing_address_line_2" id="billing_address_line_2"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">City</label>
                            <input type="text" name="billing_city" id="billing_city"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">State</label>
                            <input type="text" name="billing_state" id="billing_state"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Email</label>
                            <input type="email" name="billing_email" id="billing_email"
                                   style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">GST No</label>
                            <input type="text" name="billing_gst_no" id="billing_gst_no"
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
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Item Name <span style="color:#dc3545;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Item Description <span style="color:#dc3545;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Pack Details</th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">Approved Qty</th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">Already Raised PO Qty</th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">PO Quantity <span style="color:#dc3545;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Expected Delivery Date</th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">Unit</th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">Qty in KG</th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">Price <span style="color:#dc3545;">*</span></th>
                            <th style="padding:10px; text-align:right; color:#333; font-size:12px;">Amount</th>
                            <th style="padding:10px; text-align:left; color:#333; font-size:12px;">PO Status</th>
                            <th style="padding:10px; text-align:center; color:#333; font-size:12px; width:80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-index="0">
                            <td style="padding:6px 8px;">
                                <input type="hidden" name="items[0][purchase_indent_item_id]" class="purchase_indent_item_id">
                                <input type="hidden" name="items[0][raw_material_id]" class="raw_material_id">
                                <select name="items[0][item_name]" class="item_name"
                                        style="width:120px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px;">
                                    <option value="">Select Item</option>
                                </select>
                            </td>
                            <td style="padding:6px 8px;">
                                <input type="text" name="items[0][item_description]" class="item_description" readonly
                                       style="width:150px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; background:#f8f9fa;">
                            </td>
                            <td style="padding:6px 8px;">
                                <input type="text" name="items[0][pack_details]" class="pack_details"
                                       style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px;">
                            </td>
                            <td style="padding:6px 8px;">
                                <input type="number" step="0.001" name="items[0][approved_quantity]" class="approved_quantity" readonly
                                       style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; text-align:right; background:#f8f9fa;">
                            </td>
                            <td style="padding:6px 8px;">
                                <input type="number" step="0.001" name="items[0][already_raised_po_qty]" class="already_raised_po_qty" readonly
                                       style="width:120px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; text-align:right; background:#f8f9fa;">
                            </td>
                            <td style="padding:6px 8px;">
                                <input type="number" step="0.001" min="0" name="items[0][po_quantity]" class="po_quantity"
                                       style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; text-align:right;">
                            </td>
                            <td style="padding:6px 8px;">
                                <input type="date" name="items[0][expected_delivery_date]" class="expected_delivery_date"
                                       style="width:140px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px;">
                            </td>
                            <td style="padding:6px 8px;">
                                <input type="hidden" name="items[0][unit_id]" class="unit_id">
                                <input type="text" class="unit_symbol" readonly
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; background:#f8f9fa;">
                            </td>
                            <td style="padding:6px 8px;">
                                <input type="number" step="0.001" min="0" name="items[0][qty_in_kg]" class="qty_in_kg"
                                       style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; text-align:right;">
                            </td>
                            <td style="padding:6px 8px;">
                                <input type="number" step="0.01" min="0" name="items[0][price]" class="price"
                                       style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; text-align:right;">
                            </td>
                            <td style="padding:6px 8px;">
                                <input type="number" step="0.01" name="items[0][amount]" class="amount" readonly
                                       style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px; text-align:right; background:#f8f9fa;">
                            </td>
                            <td style="padding:6px 8px;">
                                <select name="items[0][po_status]" class="po_status"
                                        style="width:150px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:12px;">
                                    <option value="">Select Status</option>
                                    <option value="PO Placed">PO Placed</option>
                                    <option value="PO Partially Placed">PO Partially Placed</option>
                                    <option value="Awaiting for Approval">Awaiting for Approval</option>
                                    <option value="PO Not Placed">PO Not Placed</option>
                                </select>
                            </td>
                            <td style="padding:6px 8px; text-align:center;">
                                <button type="button" class="btn-remove-row" onclick="removeRow(this)" style="padding:6px 12px; background:#dc3545; color:white; border:none; border-radius:5px; font-size:12px; cursor:pointer;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <small style="color:#666; display:block; margin-top:10px;">
                    Click <strong>Add Product</strong> button to add new item rows and <strong>trash icon</strong> to remove rows.
                </small>
            </div>
        </div>

        {{-- Amount Calculation Section --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Amount Calculation</h3>
            </div>
            <div style="padding:20px; display:flex; justify-content:flex-end;">
                {{-- Right aligned summary block (design only, names/IDs unchanged) --}}
                <div style="width:380px; display:flex; flex-direction:column; gap:10px;">
                    {{-- Gross Amount (Total of items) --}}
                    <div style="display:flex; align-items:center; gap:12px;">
                        <label style="flex:1; color:#333; font-weight:500; font-size:13px;">Gross Amount:</label>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span style="font-size:13px; color:#444;">₹</span>
                            <input type="text" id="total" value="0" readonly
                                   style="width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                        </div>
                    </div>

                    {{-- Overall Discount (%) --}}
                    <div style="display:flex; align-items:center; gap:12px;">
                        <label style="flex:1; color:#333; font-weight:500; font-size:14px;">Overall Discount (%):</label>
                        <input type="number" step="0.01" min="0" name="discount_percent" id="discount_percent"
                               value="0"
                               style="width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;">
                    </div>

                    {{-- GST Type below Discount (%) --}}
                    <div style="display:flex; align-items:flex-start; gap:12px; margin-top:2px;">
                        <label style="flex:1; color:#333; font-weight:500; font-size:14px;">GST Type:</label>
                        <div style="display:flex; flex-direction:column; gap:6px;">
                            <label style="display:flex; align-items:center; gap:6px; font-size:14px; cursor:pointer;">
                                <input type="radio" name="tax_type" id="tax_type_cgst_sgst" value="cgst_sgst" checked style="cursor:pointer;">
                                <span>CGST &amp; SGST</span>
                            </label>
                            <label style="display:flex; align-items:center; gap:6px; font-size:14px; cursor:pointer;">
                                <input type="radio" name="tax_type" id="tax_type_igst" value="igst" style="cursor:pointer;">
                                <span>IGST</span>
                            </label>
                        </div>
                    </div>

                    {{-- Discount Amount --}}
                    <div style="display:flex; align-items:center; gap:12px; margin-top:4px;">
                        <label style="flex:1; color:#333; font-weight:500; font-size:14px;">Discount Amount:</label>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span style="font-size:13px; color:#444;">₹</span>
                            <input type="number" step="0.01" min="0" name="discount" id="discount" value="0"
                                   style="width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;">
                        </div>
                    </div>

                    {{-- GST (rate & amount) --}}
                    <div style="display:flex; align-items:center; gap:12px;">
                        <label style="flex:1; color:#333; font-weight:500; font-size:14px;">GST (% / Amt):</label>
                        <div style="display:flex; gap:6px;">
                            <input type="number" step="0.01" min="0" name="gst_percent" id="gst_percent" placeholder="%"
                                   style="width:70px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;">
                            <input type="number" step="0.01" min="0" name="gst" id="gst" placeholder="Amount"
                                   style="width:90px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;">
                        </div>
                    </div>

                    {{-- CGST row (amount only for display) --}}
                    <div id="cgst_section" style="display:flex; align-items:center; gap:12px;">
                        <label style="flex:1; color:#333; font-weight:500; font-size:14px;">CGST:</label>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span style="font-size:13px; color:#444;">₹</span>
                            <input type="text" id="cgst_amount" value="0.00" readonly
                                   style="width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                        </div>
                        {{-- hidden percentage element kept for calculations --}}
                        <input type="hidden" id="cgst_percent" value="0">
                    </div>

                    {{-- SGST row --}}
                    <div id="sgst_section" style="display:flex; align-items:center; gap:12px;">
                        <label style="flex:1; color:#333; font-weight:500; font-size:14px;">SGST:</label>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span style="font-size:13px; color:#444;">₹</span>
                            <input type="text" name="sgst" id="sgst" value="0.00" readonly
                                   style="width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                        </div>
                        <input type="hidden" id="sgst_percent" value="0">
                    </div>

                    {{-- IGST row --}}
                    <div id="igst_section" style="display:none; align-items:center; gap:12px;">
                        <label style="flex:1; color:#333; font-weight:500; font-size:14px;">IGST:</label>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span style="font-size:13px; color:#444;">₹</span>
                            <input type="text" name="igst" id="igst" value="0.00" readonly
                                   style="width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                        </div>
                        <input type="hidden" id="igst_percent" value="0">
                    </div>

                    {{-- Total Tax (sum of CGST+SGST or IGST) --}}
                    <div style="display:flex; align-items:center; gap:12px;">
                        <label style="flex:1; color:#333; font-weight:500; font-size:14px;">Total Tax:</label>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span style="font-size:13px; color:#444;">₹</span>
                            <input type="text" id="total_tax" value="0.00" readonly
                                   style="width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                        </div>
                    </div>

                    {{-- Net Amount --}}
                    <div style="display:flex; align-items:center; gap:12px; margin-top:4px;">
                        <label style="flex:1; color:#333; font-weight:600; font-size:14px;">Net Amount:</label>
                        <div style="display:flex; align-items:center; gap:6px;">
                            <span style="font-size:13px; font-weight:600; color:#444;">₹</span>
                            <input type="text" name="net_amount" id="net_amount" value="0" readonly
                                   style="width:140px; padding:6px 10px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right; font-weight:600; color:#4a4a4a;">
                        </div>
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
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('freight_charges') }}</textarea>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Terms of Payment</label>
                    <textarea name="terms_of_payment" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('terms_of_payment') }}</textarea>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Special Conditions</label>
                    <textarea name="special_conditions" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('special_conditions') }}</textarea>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Inspection</label>
                    <textarea name="inspection" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('inspection') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Transport and Warranty Information Section --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Transport and Warranty Information</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Name of Transport</label>
                    <input type="text" name="name_of_transport"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Transport Certificate</label>
                    <input type="text" name="transport_certificate"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Insurance of Goods/Damages</label>
                    <textarea name="insurance_of_goods_damages" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('insurance_of_goods_damages') }}</textarea>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Warranty Expiry</label>
                    <input type="date" name="warranty_expiry"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                </div>
            </div>
        </div>

        {{-- File Upload and Remarks --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Additional Information</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Upload Document</label>
                    <input type="file" name="upload" accept=".pdf,.doc,.docx,.xls,.xlsx"
                           style="width:100%; padding:8px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    <small style="color:#666;">Attach supporting document (PDF, DOC, DOCX, XLS, XLSX).</small>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Remarks</label>
                    <textarea name="remarks" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>

        <div style="display:flex; gap:15px; margin-top:10px;">
            <button type="submit" style="padding:12px 24px; background:#667eea; color:white; border:none; border-radius:5px; font-weight:500; cursor:pointer;">
                Submit
            </button>
            <a href="{{ route('purchase-orders.index') }}" style="padding:12px 24px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500;">
                Cancel
            </a>
        </div>
    </form>
</div>

@include('purchase.purchase_orders.partials.scripts')
@endsection

