@extends('layouts.dashboard')

@section('title', 'Edit Quotation - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Quotation #{{ $quotation->quotation_no }}</h2>
        <a href="{{ route('quotations.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
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

    <form action="{{ route('quotations.update', $quotation->id) }}" method="POST" id="quotationForm">
        @csrf
        @method('PUT')
        
        <!-- Header Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Quotation Details</h3>
            </div>
            <div style="padding: 20px;">
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Quotation No</label>
                        <input type="text" name="quotation_no" value="{{ old('quotation_no', $quotation->quotation_no) }}" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Date <span style="color: red;">*</span></label>
                        <input type="date" name="date" value="{{ old('date', $quotation->date->format('Y-m-d')) }}" required
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer <span style="color: red;">*</span></label>
                        <select name="customer_id" id="customer_id" required onchange="fetchCustomerDetails(this.value)"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $quotation->customer_id) == $customer->id ? 'selected' : '' }}>{{ $customer->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">GST Type <span style="color: red;">*</span></label>
                        <select name="gst_type" id="gst_type" required onchange="calculateAll()"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="intra" {{ old('gst_type', $quotation->gst_type) == 'intra' ? 'selected' : '' }}>Intra-State (CGST & SGST)</option>
                            <option value="inter" {{ old('gst_type', $quotation->gst_type) == 'inter' ? 'selected' : '' }}>Inter-State (IGST)</option>
                        </select>
                    </div>
                </div>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 15px;">
                    <h4 style="color: #667eea; font-size: 14px; margin-bottom: 10px;">Billing Address</h4>
                    <div style="margin-bottom: 10px;">
                        <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500; font-size: 13px;">Address Line 1</label>
                        <input type="text" name="billing_address_line_1" id="billing_address_line_1" value="{{ old('billing_address_line_1', $quotation->billing_address_line_1) }}"
                            style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px;">
                    </div>
                    <div style="margin-bottom: 10px;">
                        <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500; font-size: 13px;">Address Line 2 (Optional)</label>
                        <input type="text" name="billing_address_line_2" id="billing_address_line_2" value="{{ old('billing_address_line_2', $quotation->billing_address_line_2) }}"
                            style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                        <div>
                            <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500; font-size: 13px;">City</label>
                            <input type="text" name="billing_city" id="billing_city" value="{{ old('billing_city', $quotation->billing_city) }}"
                                style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500; font-size: 13px;">State</label>
                            <input type="text" name="billing_state" id="billing_state" value="{{ old('billing_state', $quotation->billing_state) }}"
                                style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 5px; color: #333; font-weight: 500; font-size: 13px;">Pincode</label>
                            <input type="text" name="billing_pincode" id="billing_pincode" value="{{ old('billing_pincode', $quotation->billing_pincode) }}"
                                style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 13px;"
                                pattern="[0-9]{6}" maxlength="6" placeholder="123456">
                        </div>
                    </div>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">GST No</label>
                    <input type="text" id="gst_no" value="{{ $quotation->customer->gst_no ?? '' }}" readonly
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa;">
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Products</h3>
                <button type="button" onclick="addProductRow()" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-plus"></i> Add Product
                </button>
            </div>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 25%;">Product</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 10%;">Unit</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 10%;">Price</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 10%;">Qty</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 10%;">Disc %</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 15%;">Total</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 5%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="productRows">
                        <!-- Rows will be added here dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="padding: 20px; display: flex; justify-content: flex-end;">
                <div style="width: 400px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 8px 0; color: #333; font-weight: 500;">Gross Amount:</td>
                            <td style="padding: 8px 0; text-align: right; color: #333; font-weight: 500;">₹<span id="grossAmount">0.00</span></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #333; font-weight: 500;">GST (%):</td>
                            <td style="padding: 8px 0; text-align: right;">
                                <input type="number" name="gst_percent" id="gst_percent" value="{{ old('gst_percent', $quotation->gst_percent ?? 0) }}" min="0" max="100" oninput="calculateAll()" step="0.01"
                                    style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #333; font-weight: 500;">GST Type:</td>
                            <td style="padding: 8px 0; text-align: right;">
                                <div style="display: flex; flex-direction: column; gap: 8px; align-items: flex-end;">
                                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                                        <input type="radio" name="summary_gst_type" id="summary_gst_type_intra" value="intra" {{ old('gst_type', $quotation->gst_type) == 'intra' ? 'checked' : '' }} onchange="updateGstType()" style="cursor: pointer;">
                                        <span>CGST & SGST</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: normal;">
                                        <input type="radio" name="summary_gst_type" id="summary_gst_type_inter" value="inter" {{ old('gst_type', $quotation->gst_type) == 'inter' ? 'checked' : '' }} onchange="updateGstType()" style="cursor: pointer;">
                                        <span>IGST</span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr id="cgst_sgst_row" style="display: {{ old('gst_type', $quotation->gst_type) == 'intra' ? 'table-row' : 'none' }};">
                            <td style="padding: 8px 0; color: #333; font-weight: 500;">CGST Amount:</td>
                            <td style="padding: 8px 0; text-align: right; color: #333; font-weight: 500;">₹<span id="cgstAmount">0.00</span></td>
                        </tr>
                        <tr id="cgst_sgst_row2" style="display: {{ old('gst_type', $quotation->gst_type) == 'intra' ? 'table-row' : 'none' }};">
                            <td style="padding: 8px 0; color: #333; font-weight: 500;">SGST Amount:</td>
                            <td style="padding: 8px 0; text-align: right; color: #333; font-weight: 500;">₹<span id="sgstAmount">0.00</span></td>
                        </tr>
                        <tr id="igst_row" style="display: {{ old('gst_type', $quotation->gst_type) == 'inter' ? 'table-row' : 'none' }};">
                            <td style="padding: 8px 0; color: #333; font-weight: 500;">IGST Amount:</td>
                            <td style="padding: 8px 0; text-align: right; color: #333; font-weight: 500;">₹<span id="igstAmount">0.00</span></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #333; font-weight: 500;">Overall Discount (%):</td>
                            <td style="padding: 8px 0; text-align: right;">
                                <input type="number" name="overall_discount_percent" id="overall_discount_percent" value="{{ old('overall_discount_percent', $quotation->overall_discount_percent ?? 0) }}" min="0" max="100" oninput="handleOverallDiscount()" step="0.01"
                                    style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #333; font-weight: 500;">Discount Amount:</td>
                            <td style="padding: 8px 0; text-align: right; color: #333; font-weight: 500;">₹<span id="discountAmount">0.00</span></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #333; font-weight: 500;">Sub Total:</td>
                            <td style="padding: 8px 0; text-align: right; color: #333; font-weight: 500;">₹<span id="subTotal">0.00</span></td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #333; font-weight: 500;">Freight Charges:</td>
                            <td style="padding: 8px 0; text-align: right;">
                                <input type="number" name="freight_charges" id="freight_charges" value="{{ old('freight_charges', $quotation->freight_charges) }}" oninput="calculateAll()" step="0.01"
                                    style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; text-align: right;">
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #333; font-weight: 500;">Total Tax:</td>
                            <td style="padding: 8px 0; text-align: right; color: #333; font-weight: 500;">₹<span id="totalTax">0.00</span></td>
                        </tr>
                        <tr style="border-top: 2px solid #dee2e6;">
                            <td style="padding: 12px 0; color: #333; font-weight: 600; font-size: 18px;">Net Amount:</td>
                            <td style="padding: 12px 0; text-align: right; color: #667eea; font-weight: 600; font-size: 18px;">₹<span id="netAmount">0.00</span></td>
                        </tr>
                    </table>
                    <input type="hidden" name="total_amount" id="input_total_amount">
                    <input type="hidden" name="net_amount" id="input_net_amount">
                    
                    <button type="submit" style="width: 100%; padding: 14px; background: #667eea; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: 600; cursor: pointer; margin-top: 20px;">
                        Update Quotation
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let rowCount = 0;
    const products = @json($products);
    const units = @json(\App\Models\Unit::all());
    const quotationItems = @json($quotation->items);

    function addProductRow(item = null) {
        rowCount++;
        const productId = item ? item.product_id : '';
        const unitId = item ? item.unit_id : '';
        const price = item ? item.price : '';
        const qty = item ? item.quantity : 1;
        const disc = item ? item.discount_percent : 0;
        const total = item ? item.total : '';

        const html = `
            <tr id="row_${rowCount}" style="border-bottom: 1px solid #dee2e6;">
                <td style="padding: 10px;">
                    <select name="products[${rowCount}][product_id]" onchange="fetchProductDetails(this, ${rowCount})" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        <option value="">Select Product</option>
                        ${products.map(p => `<option value="${p.id}" ${p.id == productId ? 'selected' : ''}>${p.name}</option>`).join('')}
                    </select>
                </td>
                <td style="padding: 10px;">
                    <select name="products[${rowCount}][unit_id]" id="unit_${rowCount}" readonly
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa; pointer-events: none;">
                        ${units.map(u => `<option value="${u.id}" ${u.id == unitId ? 'selected' : ''}>${u.symbol}</option>`).join('')}
                    </select>
                </td>
                <td style="padding: 10px;">
                    <input type="number" name="products[${rowCount}][price]" id="price_${rowCount}" value="${price}" step="0.01" min="0" required
                        oninput="calculateRow(${rowCount})"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </td>
                <td style="padding: 10px;">
                    <input type="number" name="products[${rowCount}][quantity]" id="qty_${rowCount}" value="${qty}" min="1" oninput="calculateRow(${rowCount})" required
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </td>
                <td style="padding: 10px;">
                    <input type="number" name="products[${rowCount}][discount_percent]" id="disc_${rowCount}" value="${disc}" min="0" max="100" oninput="handleItemDiscount(${rowCount})"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa;" ${item && item.discount_percent > 0 ? '' : 'disabled'}>
                </td>
                <td style="padding: 10px;">
                    <input type="text" name="products[${rowCount}][total]" id="total_${rowCount}" value="${total}" readonly
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa; font-weight: 500;">
                </td>
                <td style="padding: 10px; text-align: center;">
                    <button type="button" onclick="removeRow(${rowCount})" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 5px; font-size: 12px; cursor: pointer;">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        document.getElementById('productRows').insertAdjacentHTML('beforeend', html);
        
        if (item && item.product) {
            // Set product details if editing
            setTimeout(() => {
                document.getElementById(`unit_${rowCount}`).value = unitId;
                document.getElementById(`price_${rowCount}`).value = price;
                // Set default GST % from product GST rate if available
                const gstPercentField = document.getElementById('gst_percent');
                if (gstPercentField && gstPercentField.value == 0 && item.product.gst_rate) {
                    gstPercentField.value = item.product.gst_rate;
                }
            }, 100);
        }
    }

    function removeRow(id) {
        document.getElementById(`row_${id}`).remove();
        calculateAll();
    }

    function fetchCustomerDetails(id) {
        if(!id) return;
        fetch(`{{ url('quotations/customer') }}/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('billing_address_line_1').value = data.billing_address_line_1 || '';
                document.getElementById('billing_address_line_2').value = data.billing_address_line_2 || '';
                document.getElementById('billing_city').value = data.billing_city || '';
                document.getElementById('billing_state').value = data.billing_state || '';
                document.getElementById('billing_pincode').value = data.billing_pincode || '';
                document.getElementById('gst_no').value = data.gst_no || '';
            });
    }

    function fetchProductDetails(select, rowId) {
        const id = select.value;
        if(!id) return;
        
        fetch(`{{ url('quotations/product') }}/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById(`unit_${rowId}`).value = data.unit_id;
                // Set default GST % from product GST rate if available
                const gstPercentField = document.getElementById('gst_percent');
                if (gstPercentField && gstPercentField.value == 0 && data.gst_rate) {
                    gstPercentField.value = data.gst_rate;
                }
                calculateRow(rowId);
            });
    }

    function handleItemDiscount(rowId) {
        const discPercent = parseFloat(document.getElementById(`disc_${rowId}`).value) || 0;
        
        // If item discount is entered, disable overall discount
        if (discPercent > 0) {
            disableOverallDiscount();
        } else {
            // Check if any other item has discount
            const hasItemDiscount = checkItemDiscounts();
            if (!hasItemDiscount) {
                enableOverallDiscount();
            }
        }
        
        calculateRow(rowId);
    }

    function handleOverallDiscount() {
        const overallDisc = parseFloat(document.getElementById('overall_discount_percent').value) || 0;
        
        // If overall discount is entered, disable all item discounts
        if (overallDisc > 0) {
            disableItemDiscounts();
        } else {
            enableItemDiscounts();
        }
        
        calculateAll();
    }

    function disableOverallDiscount() {
        const overallDiscField = document.getElementById('overall_discount_percent');
        overallDiscField.disabled = true;
        overallDiscField.style.background = '#f8f9fa';
        overallDiscField.value = 0;
    }

    function enableOverallDiscount() {
        const overallDiscField = document.getElementById('overall_discount_percent');
        overallDiscField.disabled = false;
        overallDiscField.style.background = '#fff';
    }

    function disableItemDiscounts() {
        const rows = document.querySelectorAll('[id^="row_"]');
        rows.forEach(row => {
            const rowId = row.id.split('_')[1];
            const discField = document.getElementById(`disc_${rowId}`);
            discField.disabled = true;
            discField.style.background = '#f8f9fa';
            discField.value = 0;
        });
    }

    function enableItemDiscounts() {
        const rows = document.querySelectorAll('[id^="row_"]');
        rows.forEach(row => {
            const rowId = row.id.split('_')[1];
            const discField = document.getElementById(`disc_${rowId}`);
            discField.disabled = false;
            discField.style.background = '#fff';
        });
    }

    function checkItemDiscounts() {
        const rows = document.querySelectorAll('[id^="row_"]');
        for (let row of rows) {
            const rowId = row.id.split('_')[1];
            const discPercent = parseFloat(document.getElementById(`disc_${rowId}`).value) || 0;
            if (discPercent > 0) {
                return true;
            }
        }
        return false;
    }

    function calculateRow(rowId) {
        const price = parseFloat(document.getElementById(`price_${rowId}`).value) || 0;
        const qty = parseFloat(document.getElementById(`qty_${rowId}`).value) || 0;
        const discPercent = parseFloat(document.getElementById(`disc_${rowId}`).value) || 0;
        const overallDiscPercent = parseFloat(document.getElementById('overall_discount_percent').value) || 0;
        const gstPercent = parseFloat(document.getElementById('gst_percent').value) || 0;

        // Base Amount
        let baseAmount = price * qty;
        
        // Item-wise Discount (only if overall discount is not applied)
        let discountAmount = 0;
        let taxableAmount = baseAmount;
        
        if (overallDiscPercent === 0 && discPercent > 0) {
            discountAmount = (baseAmount * discPercent) / 100;
            taxableAmount = baseAmount - discountAmount;
        }

        // Total (without tax - tax will be calculated on subtotal)
        let total = taxableAmount;

        document.getElementById(`total_${rowId}`).value = total.toFixed(2);

        calculateAll();
    }

    function calculateAll() {
        let grossAmount = 0;
        let subTotal = 0;
        let totalTax = 0;
        let netAmount = 0;
        const overallDiscPercent = parseFloat(document.getElementById('overall_discount_percent').value) || 0;

        // Iterate over all rows to calculate gross amount (without any discounts)
        const rows = document.querySelectorAll('[id^="row_"]');
        rows.forEach(row => {
            const rowId = row.id.split('_')[1];
            const price = parseFloat(document.getElementById(`price_${rowId}`).value) || 0;
            const qty = parseFloat(document.getElementById(`qty_${rowId}`).value) || 0;
            grossAmount += (price * qty);
        });

        // Apply overall discount if applicable
        let discountAmount = 0;
        if (overallDiscPercent > 0) {
            discountAmount = (grossAmount * overallDiscPercent) / 100;
            subTotal = grossAmount - discountAmount;
        } else {
            // Calculate subtotal with item-wise discounts
            rows.forEach(row => {
                const rowId = row.id.split('_')[1];
                const total = parseFloat(document.getElementById(`total_${rowId}`).value) || 0;
                subTotal += total; // Subtotal is without tax
            });
        }

        // Calculate GST on subtotal
        const gstPercent = parseFloat(document.getElementById('gst_percent').value) || 0;
        let gstAmount = (subTotal * gstPercent) / 100;
        totalTax = gstAmount;

        const freight = parseFloat(document.getElementById('freight_charges').value) || 0;
        const freightTax = (freight * gstPercent) / 100;
        
        totalTax += freightTax;
        gstAmount = totalTax; // Update GST amount to include freight tax
        
        // Get GST Type from summary section (or header)
        const summaryGstType = document.querySelector('input[name="summary_gst_type"]:checked')?.value || document.getElementById('gst_type').value;
        
        // Calculate CGST/SGST or IGST based on type
        let cgstAmount = 0;
        let sgstAmount = 0;
        let igstAmount = 0;
        
        if (summaryGstType === 'intra') {
            // Intra-state: Split GST equally between CGST and SGST
            cgstAmount = gstAmount / 2;
            sgstAmount = gstAmount / 2;
            igstAmount = 0;
            
            // Show CGST/SGST rows, hide IGST row
            document.getElementById('cgst_sgst_row').style.display = 'table-row';
            document.getElementById('cgst_sgst_row2').style.display = 'table-row';
            document.getElementById('igst_row').style.display = 'none';
            
            document.getElementById('cgstAmount').innerText = cgstAmount.toFixed(2);
            document.getElementById('sgstAmount').innerText = sgstAmount.toFixed(2);
        } else {
            // Inter-state: Full IGST
            igstAmount = gstAmount;
            cgstAmount = 0;
            sgstAmount = 0;
            
            // Hide CGST/SGST rows, show IGST row
            document.getElementById('cgst_sgst_row').style.display = 'none';
            document.getElementById('cgst_sgst_row2').style.display = 'none';
            document.getElementById('igst_row').style.display = 'table-row';
            
            document.getElementById('igstAmount').innerText = igstAmount.toFixed(2);
        }
        
        netAmount = subTotal + totalTax + freight;

        document.getElementById('grossAmount').innerText = grossAmount.toFixed(2);
        document.getElementById('discountAmount').innerText = discountAmount.toFixed(2);
        document.getElementById('subTotal').innerText = subTotal.toFixed(2);
        document.getElementById('totalTax').innerText = totalTax.toFixed(2);
        document.getElementById('netAmount').innerText = netAmount.toFixed(2);

        document.getElementById('input_total_amount').value = subTotal.toFixed(2);
        document.getElementById('input_net_amount').value = netAmount.toFixed(2);
    }

    // Validate before form submission
    document.getElementById('quotationForm').addEventListener('submit', function(e) {
        const overallDisc = parseFloat(document.getElementById('overall_discount_percent').value) || 0;
        const hasItemDiscount = checkItemDiscounts();
        
        if (overallDisc > 0 && hasItemDiscount) {
            e.preventDefault();
            alert('You cannot apply both Item-wise Discount and Overall Discount. Please select only one type of discount.');
            return false;
        }
    });

    // Initialize discount state on page load
    setTimeout(() => {
        @if($quotation->overall_discount_percent > 0)
            // If overall discount exists, disable item discounts
            disableItemDiscounts();
        @else
            // Check if any item has discount
            const hasItemDisc = checkItemDiscounts();
            if (hasItemDisc) {
                disableOverallDiscount();
            } else {
                enableItemDiscounts();
            }
        @endif
    }, 600);

    // Load existing items
    @if($quotation->items->count() > 0)
        quotationItems.forEach(item => {
            addProductRow(item);
        });
    @else
        addProductRow();
    @endif

    // Set initial customer details
    @if($quotation->customer)
        document.getElementById('gst_no').value = '{{ $quotation->customer->gst_no ?? '' }}';
    @endif

    // Sync summary GST type with header GST type
    function updateGstType() {
        const summaryGstType = document.querySelector('input[name="summary_gst_type"]:checked')?.value;
        if (summaryGstType) {
            document.getElementById('gst_type').value = summaryGstType;
        }
        calculateAll();
    }
    
    // Sync header GST type with summary GST type
    document.getElementById('gst_type').addEventListener('change', function() {
        const headerGstType = this.value;
        if (headerGstType === 'intra') {
            document.getElementById('summary_gst_type_intra').checked = true;
        } else {
            document.getElementById('summary_gst_type_inter').checked = true;
        }
        calculateAll();
    });
    
    // Initialize summary GST type from header on page load
    document.addEventListener('DOMContentLoaded', function() {
        const headerGstType = document.getElementById('gst_type').value;
        if (headerGstType === 'intra') {
            document.getElementById('summary_gst_type_intra').checked = true;
        } else {
            document.getElementById('summary_gst_type_inter').checked = true;
        }
        calculateAll();
    });

    // Calculate on page load
    setTimeout(() => {
        calculateAll();
    }, 500);
</script>
@endpush
@endsection

