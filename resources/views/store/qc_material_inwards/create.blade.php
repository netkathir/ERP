@extends('layouts.dashboard')

@section('title', 'QC Material Inward - Create')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-search--dropdown .select2-search__field {
        padding: 6px;
        border: 1px solid #aaa;
        border-radius: 4px;
    }
    .select2-container--default .select2-search--dropdown {
        padding: 10px;
    }
    
    /* Responsive Design */
    .qc-material-inward-container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    .qc-header-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .qc-header-title {
        color: #333;
        font-size: 24px;
        margin: 0;
        flex: 1;
        min-width: 200px;
    }
    
    .qc-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }
    
    .qc-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
        align-items: flex-end;
    }
    
    .qc-form-section {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        width: 100%;
        box-sizing: border-box;
    }
    
    .qc-table-wrapper {
        padding: 20px;
        overflow-x: auto;
        width: 100%;
        box-sizing: border-box;
    }
    
    .qc-table {
        width: 100%;
        min-width: 1200px;
        border-collapse: collapse;
    }
    
    .qc-table th,
    .qc-table td {
        padding: 8px;
        white-space: nowrap;
        font-size: 13px;
    }
    
    .qc-form-buttons {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }
    
    /* Responsive breakpoints */
    @media screen and (max-width: 1400px) {
        .qc-table {
            min-width: 1100px;
        }
    }
    
    @media screen and (max-width: 1200px) {
        .qc-form-grid {
            grid-template-columns: 1fr 1fr;
        }
        .qc-table {
            min-width: 1000px;
        }
    }
    
    @media screen and (max-width: 992px) {
        .qc-material-inward-container {
            padding: 15px;
        }
        
        .qc-header-row {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .qc-header-actions {
            width: 100%;
            justify-content: space-between;
        }
        
        .qc-form-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .qc-table-wrapper {
            padding: 15px;
        }
        
        .qc-table {
            min-width: 900px;
            font-size: 12px;
        }
        
        .qc-table th,
        .qc-table td {
            padding: 6px;
            font-size: 12px;
        }
        
        .qc-form-buttons {
            flex-direction: column;
        }
        
        .qc-form-buttons a,
        .qc-form-buttons button {
            width: 100%;
        }
    }
    
    @media screen and (max-width: 768px) {
        .qc-material-inward-container {
            padding: 10px;
        }
        
        .qc-header-title {
            font-size: 20px;
        }
        
        .qc-table {
            min-width: 800px;
        }
        
        .qc-table th,
        .qc-table td {
            padding: 5px;
            font-size: 11px;
        }
    }
    
    /* Select2 responsive fixes */
    .select2-container {
        width: 100% !important;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    .select2-dropdown {
        max-width: 100%;
    }
    
    @media screen and (max-width: 992px) {
        .select2-container {
            font-size: 14px;
        }
    }
</style>
@endpush

@section('content')
<div class="qc-material-inward-container">
    <div class="qc-header-row">
        <h2 class="qc-header-title">QC Material Inward</h2>
        <div class="qc-header-actions">
            <div style="text-align:right; color:#666; font-size:14px;">
                <strong>Date:</strong> {{ date('d-m-Y') }}
            </div>
            <a href="{{ route('qc-material-inwards.index') }}" style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px; white-space:nowrap;">
                <i class="fas fa-list"></i> List
            </a>
        </div>
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

    <form action="{{ route('qc-material-inwards.store') }}" method="POST" id="qcMaterialInwardForm">
        @csrf

        {{-- Header Fields --}}
        <div class="qc-form-section">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Header Information</h3>
            </div>
            <div style="padding:20px;" class="qc-form-grid">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Purchase Order <span style="color:red;">*</span></label>
                    <select name="purchase_order_id" id="purchase_order_id" class="select2-dropdown" required
                            style="width:100%;">
                        <option value="">Select Purchase Order</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}" {{ old('purchase_order_id') == $po->id ? 'selected' : '' }}>
                                {{ $po->po_no }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">MNO No <span style="color:red;">*</span></label>
                    <select name="material_inward_id" id="material_inward_id" class="select2-dropdown" required
                            style="width:100%;" disabled>
                        <option value="">Select MNO No</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Supplier Name</label>
                    <input type="text" id="supplier_name" value="" readonly
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;">
                </div>
            </div>
        </div>

        {{-- Item Details Table --}}
        <div class="qc-form-section">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">QC Details</h3>
            </div>
            <div class="qc-table-wrapper">
                <table class="qc-table" id="itemsTable">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:10px; text-align:left; color:#333; font-weight:600;">Item Description</th>
                            <th style="padding:10px; text-align:right; color:#333; font-weight:600;">Received Qty</th>
                            <th style="padding:10px; text-align:left; color:#333; font-weight:600;">Unit</th>
                            <th style="padding:10px; text-align:right; color:#333; font-weight:600;">Received Qty in Kg</th>
                            <th style="padding:10px; text-align:left; color:#333; font-weight:600;">Batch No</th>
                            <th style="padding:10px; text-align:left; color:#333; font-weight:600;">Supplier Invoice No</th>
                            <th style="padding:10px; text-align:left; color:#333; font-weight:600;">Invoice Date</th>
                            <th style="padding:10px; text-align:right; color:#333; font-weight:600;">Given Qty</th>
                            <th style="padding:10px; text-align:right; color:#333; font-weight:600;">Accepted Qty <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:right; color:#333; font-weight:600;">Rejected Qty</th>
                            <th style="padding:10px; text-align:left; color:#333; font-weight:600;">Rejection Reason</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <tr>
                            <td colspan="11" style="padding:20px; text-align:center; color:#666;">
                                Please select Purchase Order and MNO No to load items
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="qc-form-buttons">
            <a href="{{ route('qc-material-inwards.index') }}" style="padding:12px 24px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; text-align:center; display:inline-block;">
                List
            </a>
            <button type="submit" style="padding:12px 24px; background:#667eea; color:white; border:none; border-radius:5px; font-weight:500; cursor:pointer; min-width:120px;">
                Submit
            </button>
        </div>
    </form>
</div>

@push('scripts')
<!-- jQuery (required for Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Initialize Select2 for Purchase Order dropdown
    jQuery(document).ready(function($) {
        $('#purchase_order_id').select2({
            placeholder: 'Select Purchase Order',
            allowClear: true,
            width: '100%'
        });

        // Initialize Select2 for MNO No dropdown (will be enabled when PO is selected)
        $('#material_inward_id').select2({
            placeholder: 'Select MNO No',
            allowClear: true,
            width: '100%',
            disabled: true
        });

        // Handle Purchase Order change
        $('#purchase_order_id').on('change', function() {
            const poId = $(this).val();
            loadMaterialInwards(poId);
        });
    });

    function loadMaterialInwards(poId) {
        const mrnSelect = $('#material_inward_id');
        const supplierNameInput = document.getElementById('supplier_name');
        
        if (!poId) {
            mrnSelect.empty().append('<option value="">Select MNO No</option>');
            mrnSelect.prop('disabled', true).trigger('change');
            supplierNameInput.value = '';
            clearItemsTable();
            return;
        }

        mrnSelect.prop('disabled', true);
        mrnSelect.empty().append('<option value="">Loading...</option>').trigger('change');

        fetch(`{{ url('qc-material-inwards/purchase-order') }}/${poId}/material-inwards`)
            .then(response => response.json())
            .then(data => {
                mrnSelect.empty().append('<option value="">Select MNO No</option>');
                if (data.material_inwards && data.material_inwards.length > 0) {
                    data.material_inwards.forEach(function(mrn) {
                        mrnSelect.append(new Option(mrn.material_inward_no, mrn.id, false, false));
                    });
                    mrnSelect.prop('disabled', false).trigger('change');
                } else {
                    mrnSelect.append('<option value="">No MRNs available</option>').trigger('change');
                }
                supplierNameInput.value = data.supplier_name || '';
            })
            .catch(error => {
                console.error('Error loading material inwards:', error);
                mrnSelect.empty().append('<option value="">Error loading MRNs</option>').trigger('change');
                alert('Error loading Material Inward records. Please try again.');
            });
    }

    // Handle MNO No change
    $('#material_inward_id').on('change', function() {
        const mrnId = $(this).val();
        loadMaterialInwardItems(mrnId);
    });

    function loadMaterialInwardItems(mrnId) {
        const tbody = document.getElementById('itemsTableBody');
        
        if (!mrnId) {
            clearItemsTable();
            return;
        }

        tbody.innerHTML = '<tr><td colspan="11" style="padding:20px; text-align:center; color:#666;">Loading items...</td></tr>';

        fetch(`{{ url('qc-material-inwards/material-inward') }}/${mrnId}/items`)
            .then(response => response.json())
            .then(data => {
                if (data.items && data.items.length > 0) {
                    tbody.innerHTML = '';
                    data.items.forEach(function(item, index) {
                        const row = document.createElement('tr');
                        row.style.borderBottom = '1px solid #dee2e6';
                        
                        const invoiceDate = item.invoice_date || '';
                        
                        row.innerHTML = `
                            <td style="padding:10px;">
                                <input type="hidden" name="items[${index}][material_inward_item_id]" value="${item.material_inward_item_id}">
                                <input type="text" value="${escapeHtml(item.item_description)}" readonly
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa;">
                            </td>
                            <td style="padding:10px; text-align:right;">
                                <input type="text" value="${Math.floor(parseFloat(item.received_qty) || 0)}" readonly
                                       style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                            </td>
                            <td style="padding:10px;">
                                <input type="text" value="${escapeHtml(item.unit_symbol)}" readonly
                                       style="width:60px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:center;">
                            </td>
                            <td style="padding:10px; text-align:right;">
                                <input type="text" value="${item.received_qty_in_kg || ''}" readonly
                                       style="width:120px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                            </td>
                            <td style="padding:10px;">
                                <input type="text" value="${escapeHtml(item.batch_no || '')}" readonly
                                       style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa;">
                            </td>
                            <td style="padding:10px;">
                                <input type="text" value="${escapeHtml(item.supplier_invoice_no || '')}" readonly
                                       style="width:120px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa;">
                            </td>
                            <td style="padding:10px;">
                                <input type="text" value="${invoiceDate}" readonly
                                       style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa;">
                            </td>
                            <td style="padding:10px; text-align:right;">
                                <input type="text" value="${Math.floor(parseFloat(item.given_qty) || 0)}" readonly
                                       style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                            </td>
                            <td style="padding:10px; text-align:right;">
                                <input type="number" name="items[${index}][accepted_qty]" 
                                       value="" 
                                       min="0" step="1" required
                                       data-received-qty="${Math.floor(parseFloat(item.received_qty) || 0)}"
                                       onkeydown="return preventDecimalInput(event)"
                                       oninput="calculateRejectedQty(this, ${index})"
                                       onblur="roundToWholeNumber(this); calculateRejectedQty(this, ${index})"
                                       style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;">
                            </td>
                            <td style="padding:10px; text-align:right;">
                                <input type="text" name="items[${index}][rejected_qty]" 
                                       value="" 
                                       readonly
                                       required
                                       style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa; text-align:right;">
                            </td>
                            <td style="padding:10px;">
                                <textarea name="items[${index}][rejection_reason]" 
                                          rows="2"
                                          placeholder="Enter rejection reason if any"
                                          style="min-width:150px; max-width:250px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; resize:vertical; width:100%;"></textarea>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="11" style="padding:20px; text-align:center; color:#666;">No items available for QC</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error loading items:', error);
                tbody.innerHTML = '<tr><td colspan="11" style="padding:20px; text-align:center; color:#dc3545;">Error loading items. Please try again.</td></tr>';
                alert('Error loading Material Inward items. Please try again.');
            });
    }

    function preventDecimalInput(event) {
        // Prevent decimal point, comma, 'e', 'E', '+', '-' keys
        if (event.key === '.' || event.key === ',' || event.key === 'e' || event.key === 'E' || event.key === '+' || event.key === '-') {
            event.preventDefault();
            return false;
        }
        return true;
    }

    function roundToWholeNumber(input) {
        if (input.value && input.value.trim() !== '') {
            const numValue = parseFloat(input.value);
            if (!isNaN(numValue)) {
                input.value = Math.floor(Math.abs(numValue));
            }
        }
    }

    function calculateRejectedQty(acceptedQtyInput, index) {
        const row = acceptedQtyInput.closest('tr');
        const rejectedQtyInput = row.querySelector('input[name="items[' + index + '][rejected_qty]"]');
        const receivedQty = parseInt(acceptedQtyInput.getAttribute('data-received-qty')) || 0;
        const acceptedQtyValue = acceptedQtyInput.value.trim();
        
        // Remove any decimals while typing
        if (acceptedQtyValue && acceptedQtyValue.includes('.')) {
            acceptedQtyInput.value = Math.floor(parseFloat(acceptedQtyValue) || 0);
        }
        
        // If accepted qty is empty, clear rejected qty
        if (acceptedQtyValue === '' || acceptedQtyInput.value === '') {
            rejectedQtyInput.value = '';
            checkRejectionReason(rejectedQtyInput, index);
            return;
        }
        
        const acceptedQty = parseInt(acceptedQtyInput.value) || 0;
        const rejectedQty = receivedQty - acceptedQty;
        
        if (rejectedQty >= 0) {
            rejectedQtyInput.value = rejectedQty;
            checkRejectionReason(rejectedQtyInput, index);
        } else {
            alert('Accepted Qty cannot exceed Received Qty (' + receivedQty + ')');
            acceptedQtyInput.value = receivedQty;
            rejectedQtyInput.value = '0';
        }
    }

    function checkRejectionReason(rejectedQtyInput, index) {
        const row = rejectedQtyInput.closest('tr');
        const rejectionReasonTextarea = row.querySelector('textarea[name="items[' + index + '][rejection_reason]"]');
        const currentValue = rejectedQtyInput.value.trim();
        const rejectedQty = currentValue === '' ? 0 : parseInt(currentValue) || 0;
        
        if (rejectedQty > 0) {
            rejectionReasonTextarea.setAttribute('required', 'required');
            rejectionReasonTextarea.style.borderColor = '#dc3545';
        } else {
            rejectionReasonTextarea.removeAttribute('required');
            rejectionReasonTextarea.style.borderColor = '#ddd';
        }
    }

    function clearItemsTable() {
        const tbody = document.getElementById('itemsTableBody');
        tbody.innerHTML = '<tr><td colspan="11" style="padding:20px; text-align:center; color:#666;">Please select Purchase Order and MNO No to load items</td></tr>';
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    // Form validation
    document.getElementById('qcMaterialInwardForm').addEventListener('submit', function(e) {
        const acceptedQtyInputs = document.querySelectorAll('input[name*="[accepted_qty]"]');
        let isValid = true;
        let errorMessage = '';

        acceptedQtyInputs.forEach(function(input, index) {
            const row = input.closest('tr');
            const rejectedQtyInput = row.querySelector('input[name*="[rejected_qty]"]');
            const receivedQty = parseInt(input.getAttribute('data-received-qty')) || 0;
            const acceptedQtyValue = input.value.trim();
            const rejectedQtyValue = rejectedQtyInput.value.trim();
            const rejectionReasonTextarea = row.querySelector('textarea[name*="[rejection_reason]"]');

            // Check if Accepted Qty is filled
            if (acceptedQtyValue === '') {
                isValid = false;
                errorMessage = 'Please enter Accepted Qty for all items.';
                return;
            }

            const acceptedQty = parseInt(acceptedQtyValue) || 0;
            const rejectedQty = rejectedQtyValue === '' ? 0 : parseInt(rejectedQtyValue) || 0;

            // Check if Accepted + Rejected = Received
            if (acceptedQty + rejectedQty !== receivedQty) {
                isValid = false;
                errorMessage = 'Accepted Qty + Rejected Qty must equal Received Qty (' + receivedQty + ') for all items.';
                return;
            }

            // Check if Rejection Reason is provided when Rejected Qty > 0
            if (rejectedQty > 0 && !rejectionReasonTextarea.value.trim()) {
                isValid = false;
                errorMessage = 'Rejection Reason is required when Rejected Qty is greater than 0.';
                return;
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert(errorMessage);
            return false;
        }
    });
</script>
@endpush
@endsection

