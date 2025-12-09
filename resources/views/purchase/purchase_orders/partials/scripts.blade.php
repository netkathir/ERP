<style>
    .warning-modal-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    .warning-modal-backdrop.show {
        display: flex;
    }
    .warning-modal {
        background: white;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        min-width: 400px;
        max-width: 500px;
        overflow: hidden;
        animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown {
        from {
            transform: translateY(-20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    .warning-modal-header {
        background: #ffc107;
        color: #856404;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        font-size: 16px;
    }
    .warning-modal-header i {
        font-size: 20px;
    }
    .warning-modal-body {
        padding: 20px;
        color: #333;
        font-size: 14px;
        line-height: 1.5;
    }
    .warning-modal-footer {
        padding: 12px 20px;
        background: #f8f9fa;
        display: flex;
        justify-content: flex-end;
        border-top: 1px solid #dee2e6;
    }
    .warning-modal-btn {
        padding: 8px 20px;
        background: #ffc107;
        color: #856404;
        border: none;
        border-radius: 5px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
    }
    .warning-modal-btn:hover {
        background: #ffca2c;
    }
</style>

<div id="warning-modal-backdrop" class="warning-modal-backdrop">
    <div class="warning-modal">
        <div class="warning-modal-header">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Warning</span>
        </div>
        <div class="warning-modal-body" id="warning-modal-message">
            <!-- Message will be inserted here -->
        </div>
        <div class="warning-modal-footer">
            <button type="button" class="warning-modal-btn" onclick="closeWarningModal()">OK</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.querySelector('#itemsTable tbody');
    const purchaseIndentSelect = document.getElementById('purchase_indent_id');
    const shipToSelect = document.getElementById('ship_to');
    const supplierSelect = document.getElementById('supplier_id');
    const billingAddressSelect = document.getElementById('billing_address_id');
    
    const customers = @json($customers);
    const suppliers = @json($suppliers);
    const billingAddresses = @json($billingAddresses);
    
    // Route URLs for API calls - using route helper to handle subdirectory installations
    // Generate route URLs with placeholder ID, then replace in JavaScript
    @php
        $placeholderId = 'ROUTE_ID_PLACEHOLDER_12345';
        $purchaseIndentItemsUrlPattern = route('purchase-orders.purchase-indent.items', ['id' => $placeholderId]);
        $purchaseIndentItemsDisplayUrlPattern = route('purchase-orders.purchase-indent.items.display', ['id' => $placeholderId]);
        $customerDetailsUrlPattern = route('purchase-orders.customer', ['id' => $placeholderId]);
        $supplierDetailsUrlPattern = route('purchase-orders.supplier', ['id' => $placeholderId]);
        $billingAddressDetailsUrlPattern = route('purchase-orders.billing-address', ['id' => $placeholderId]);
    @endphp
    const purchaseIndentItemsUrl = @json($purchaseIndentItemsUrlPattern);
    const customerDetailsUrl = @json($customerDetailsUrlPattern);
    const supplierDetailsUrl = @json($supplierDetailsUrlPattern);
    const billingAddressDetailsUrl = @json($billingAddressDetailsUrlPattern);
    const routeIdPlaceholder = 'ROUTE_ID_PLACEHOLDER_12345';

    // Warning Modal Functions
    window.showWarningModal = function(message) {
        const backdrop = document.getElementById('warning-modal-backdrop');
        const messageEl = document.getElementById('warning-modal-message');
        if (!backdrop || !messageEl) return;
        messageEl.textContent = message;
        backdrop.classList.add('show');
    };

    window.closeWarningModal = function() {
        const backdrop = document.getElementById('warning-modal-backdrop');
        if (backdrop) {
            backdrop.classList.remove('show');
        }
    };

    // Close modal on backdrop click
    document.getElementById('warning-modal-backdrop')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeWarningModal();
        }
    });

    // Purchase Indent Selection - Load Items
    if (purchaseIndentSelect) {
        purchaseIndentSelect.addEventListener('change', function() {
            const indentId = this.value;
            
            if (indentId) {
                // Show loading state
                const loadingMsg = document.createElement('div');
                loadingMsg.id = 'loading-msg';
                loadingMsg.textContent = 'Loading items...';
                loadingMsg.style.cssText = 'padding: 10px; background: #f0f0f0; margin: 10px 0; border-radius: 5px;';
                purchaseIndentSelect.parentElement.appendChild(loadingMsg);
                
                const url = purchaseIndentItemsUrl.replace(routeIdPlaceholder, indentId);
                fetch(url, {
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
                        const loadingMsg = document.getElementById('loading-msg');
                        if (loadingMsg) loadingMsg.remove();
                        
                        if (!response.ok) {
                            // Try to parse error message
                            return response.text().then(text => {
                                try {
                                    const err = JSON.parse(text);
                                    throw new Error(err.error || `Server error (${response.status})`);
                                } catch (e) {
                                    throw new Error(`Server error (${response.status}): ${response.statusText}`);
                                }
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Check if there's an error in the response
                        if (data.error) {
                            alert(data.error);
                            return;
                        }
                        
                        // Store items globally for dropdown population
                        purchaseIndentItems = Array.isArray(data) ? data : [];
                        
                        // Clear existing rows except first
                        const rows = tableBody.querySelectorAll('tr');
                        for (let i = 1; i < rows.length; i++) {
                            rows[i].remove();
                        }
                        
                        // Clear first row
                        const firstRow = tableBody.querySelector('tr[data-index="0"]');
                        if (firstRow) {
                            clearRow(firstRow);
                        }
                        
                        // Populate dropdowns in all rows with items
                        populateItemNameDropdowns();
                        
                        // Auto-populate first row with first item if available
                        if (purchaseIndentItems.length > 0) {
                            const firstRow = tableBody.querySelector('tr[data-index="0"]');
                            if (firstRow) {
                                populateRowFromIndentItem(firstRow, purchaseIndentItems[0], 0);
                            }
                        }
                    })
                    .catch(error => {
                        const loadingMsg = document.getElementById('loading-msg');
                        if (loadingMsg) loadingMsg.remove();
                        console.error('Error loading purchase indent items:', error);
                        alert('Error loading purchase indent items: ' + (error.message || 'Unknown error'));
                    });
            } else {
                // Clear all rows
                const rows = tableBody.querySelectorAll('tr');
                for (let i = 1; i < rows.length; i++) {
                    rows[i].remove();
                }
                const firstRow = tableBody.querySelector('tr[data-index="0"]');
                if (firstRow) {
                    clearRow(firstRow);
                }
            }
        });
    }

    // Ship To Selection - Show Dynamic Fields
    if (shipToSelect) {
        shipToSelect.addEventListener('change', function() {
            const shipTo = this.value;
            const dynamicField = document.getElementById('ship_to_dynamic_field');
            const addressSection = document.getElementById('ship_to_address_section');
            const select = document.getElementById('ship_to_select');
            const label = document.getElementById('ship_to_label');
            
            if (shipTo) {
                dynamicField.style.display = 'block';
                addressSection.style.display = 'block';
                
                // Clear select
                select.innerHTML = '<option value="">Select</option>';
                
                if (shipTo === 'Customer') {
                    label.textContent = 'Customer Name';
                    select.name = 'customer_id';
                    customers.forEach(customer => {
                        const option = document.createElement('option');
                        option.value = customer.id;
                        option.textContent = customer.company_name;
                        select.appendChild(option);
                    });
                } else if (shipTo === 'Subcontractor') {
                    label.textContent = 'Subcontractor Name';
                    select.name = 'subcontractor_id';
                    suppliers.forEach(supplier => {
                        const option = document.createElement('option');
                        option.value = supplier.id;
                        option.textContent = supplier.supplier_name;
                        select.appendChild(option);
                    });
                } else if (shipTo === 'Company') {
                    label.textContent = 'Company Name';
                    select.name = 'company_id';
                    billingAddresses.forEach(billing => {
                        const option = document.createElement('option');
                        option.value = billing.id;
                        option.textContent = billing.company_name;
                        select.appendChild(option);
                    });
                }
            } else {
                dynamicField.style.display = 'none';
                addressSection.style.display = 'none';
            }
        });
    }

    // Ship To Select Change - Load Address
    document.addEventListener('change', function(e) {
        if (e.target.id === 'ship_to_select') {
            const shipTo = shipToSelect.value;
            const selectedId = e.target.value;
            
            if (shipTo === 'Customer' && selectedId) {
                fetch(customerDetailsUrl.replace(routeIdPlaceholder, selectedId))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('ship_to_address_line_1').value = data.shipping_address_line_1 || '';
                        document.getElementById('ship_to_address_line_2').value = data.shipping_address_line_2 || '';
                        document.getElementById('ship_to_city').value = data.shipping_city || '';
                        document.getElementById('ship_to_state').value = data.shipping_state || '';
                        document.getElementById('ship_to_pincode').value = data.shipping_pincode || '';
                        document.getElementById('ship_to_email').value = data.email || '';
                        document.getElementById('ship_to_contact_no').value = data.contact_no || '';
                        document.getElementById('ship_to_gst_no').value = data.gst_no || '';
                    });
            } else if (shipTo === 'Subcontractor' && selectedId) {
                fetch(supplierDetailsUrl.replace(routeIdPlaceholder, selectedId))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('ship_to_address_line_1').value = data.address_line_1 || '';
                        document.getElementById('ship_to_address_line_2').value = data.address_line_2 || '';
                        document.getElementById('ship_to_city').value = data.city || '';
                        document.getElementById('ship_to_state').value = data.state || '';
                        document.getElementById('ship_to_email').value = data.email || '';
                        document.getElementById('ship_to_contact_no').value = '';
                        document.getElementById('ship_to_gst_no').value = data.gst || '';
                    });
            } else if (shipTo === 'Company' && selectedId) {
                fetch(billingAddressDetailsUrl.replace(routeIdPlaceholder, selectedId))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('ship_to_address_line_1').value = data.address_line_1 || '';
                        document.getElementById('ship_to_address_line_2').value = data.address_line_2 || '';
                        document.getElementById('ship_to_city').value = data.city || '';
                        document.getElementById('ship_to_state').value = data.state || '';
                        document.getElementById('ship_to_email').value = data.email || '';
                        document.getElementById('ship_to_contact_no').value = '';
                        document.getElementById('ship_to_gst_no').value = data.gst_no || '';
                    });
            }
        }
    });

    // Supplier Selection - Load Address
    if (supplierSelect) {
        supplierSelect.addEventListener('change', function() {
            const supplierId = this.value;
            if (supplierId) {
                fetch(supplierDetailsUrl.replace(routeIdPlaceholder, supplierId))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('supplier_address_line_1').value = data.address_line_1 || '';
                        document.getElementById('supplier_address_line_2').value = data.address_line_2 || '';
                        document.getElementById('supplier_city').value = data.city || '';
                        document.getElementById('supplier_state').value = data.state || '';
                        document.getElementById('supplier_email').value = data.email || '';
                        document.getElementById('supplier_gst_no').value = data.gst || '';
                    });
            }
        });
    }

    // Billing Address Selection - Load Address
    if (billingAddressSelect) {
        billingAddressSelect.addEventListener('change', function() {
            const billingId = this.value;
            if (billingId) {
                fetch(billingAddressDetailsUrl.replace(routeIdPlaceholder, billingId))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('billing_address_line_1').value = data.address_line_1 || '';
                        document.getElementById('billing_address_line_2').value = data.address_line_2 || '';
                        document.getElementById('billing_city').value = data.city || '';
                        document.getElementById('billing_state').value = data.state || '';
                        document.getElementById('billing_email').value = data.email || '';
                        document.getElementById('billing_gst_no').value = data.gst_no || '';
                    });
            }
        });
    }

    // Row Management Functions
    function getNextIndex() {
        const rows = tableBody.querySelectorAll('tr');
        return rows.length ? Math.max(...Array.from(rows).map(r => parseInt(r.dataset.index, 10))) + 1 : 0;
    }

    function addProductRow() {
        const index = getNextIndex();
        const firstRow = tableBody.querySelector('tr');
        if (!firstRow) return null;

        const newRow = firstRow.cloneNode(true);
        newRow.dataset.index = index;

        newRow.querySelectorAll('input, select').forEach(function (input) {
            const name = input.getAttribute('name') || '';
            if (name) {
                const newName = name.replace(/items\[\d+\]/, 'items[' + index + ']');
                input.setAttribute('name', newName);
            }

            // Handle PO Quantity field separately (must be empty, not 0, since min="1")
            if (input.classList.contains('po_quantity')) {
                input.value = '';
            } else if (input.type === 'number' || input.type === 'date') {
                input.value = input.type === 'date' ? '' : 0;
            } else if (input.type !== 'hidden') {
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0; // Reset to first option
                } else {
                    input.value = '';
                }
            }
        });

        // Update remove button onclick
        const removeBtn = newRow.querySelector('.btn-remove-row');
        if (removeBtn) {
            removeBtn.setAttribute('onclick', 'removeRow(this)');
        }

        // Remove data-listener-attached attribute from cloned row's item_name select
        // This ensures the event listener will be added for the new row
        const clonedItemNameSelect = newRow.querySelector('.item_name');
        if (clonedItemNameSelect) {
            clonedItemNameSelect.removeAttribute('data-listener-attached');
        }

        tableBody.appendChild(newRow);
        
        // Populate dropdown only for the new row (preserves existing selections)
        populateItemNameDropdowns(newRow);
        
        attachRowEvents(newRow);
        return newRow;
    }

    // Make functions globally accessible
    window.addProductRow = addProductRow;
    window.removeRow = function(button) {
        const row = button.closest('tr');
        if (row && tableBody.querySelectorAll('tr').length > 1) {
            row.remove();
            calculateTotals();
        }
    };

    function clearRow(row) {
        row.querySelectorAll('input, select').forEach(function(input) {
            if (input.type !== 'hidden' && !input.classList.contains('approved_quantity') && !input.classList.contains('already_raised_po_qty')) {
                input.value = '';
            } else if (input.classList.contains('approved_quantity') || input.classList.contains('already_raised_po_qty')) {
                input.value = 0;
            }
        });
    }

    // Populate all Item Name dropdowns with purchase indent items
    function populateItemNameDropdowns(specificRow = null) {
        const rowsToProcess = specificRow ? [specificRow] : tableBody.querySelectorAll('tr');
        
        rowsToProcess.forEach(row => {
            const itemNameSelect = row.querySelector('.item_name');
            if (itemNameSelect) {
                // Store the currently selected value to preserve it
                const currentValue = itemNameSelect.value;
                
                // Check if event listener already exists to avoid duplicates
                const hasListener = itemNameSelect.hasAttribute('data-listener-attached');
                
                // Clear existing options
                itemNameSelect.innerHTML = '<option value="">Select Item</option>';
                
                // Add all items from purchase indent
                purchaseIndentItems.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.item_name || '';
                    option.textContent = item.item_name || '';
                    option.dataset.itemId = item.id || item.purchase_indent_item_id || '';
                    option.dataset.rawMaterialId = item.raw_material_id || '';
                    option.dataset.itemDescription = item.item_description || '';
                    option.dataset.approvedQuantity = item.approved_quantity || 0;
                    option.dataset.alreadyRaisedQty = item.already_raised_po_qty || 0;
                    option.dataset.unitId = item.unit_id || '';
                    option.dataset.unitSymbol = item.unit_symbol || '';
                    itemNameSelect.appendChild(option);
                });
                
                // Restore the previously selected value if it exists in the new options
                if (currentValue) {
                    // Check if the value still exists in the dropdown
                    const optionExists = Array.from(itemNameSelect.options).some(opt => opt.value === currentValue);
                    if (optionExists) {
                        itemNameSelect.value = currentValue;
                    }
                }
                
                // Add change event listener only if it doesn't exist yet
                if (!hasListener) {
                    itemNameSelect.addEventListener('change', function() {
                        const selectedOption = this.options[this.selectedIndex];
                        if (selectedOption && selectedOption.dataset.itemId) {
                            const row = this.closest('tr');
                            const purchaseIndentItemId = row.querySelector('.purchase_indent_item_id');
                            const rawMaterialId = row.querySelector('.raw_material_id');
                            const itemDescription = row.querySelector('.item_description');
                            const approvedQuantity = row.querySelector('.approved_quantity');
                            const alreadyRaisedQty = row.querySelector('.already_raised_po_qty');
                            const unitId = row.querySelector('.unit_id');
                            const unitSymbol = row.querySelector('.unit_symbol');
                            
                            if (purchaseIndentItemId) purchaseIndentItemId.value = selectedOption.dataset.itemId;
                            if (rawMaterialId) rawMaterialId.value = selectedOption.dataset.rawMaterialId;
                            if (itemDescription) itemDescription.value = selectedOption.dataset.itemDescription;
                            if (approvedQuantity) approvedQuantity.value = selectedOption.dataset.approvedQuantity;
                            if (alreadyRaisedQty) alreadyRaisedQty.value = selectedOption.dataset.alreadyRaisedQty;
                            if (unitId) unitId.value = selectedOption.dataset.unitId;
                            if (unitSymbol) unitSymbol.value = selectedOption.dataset.unitSymbol;
                            
                            calculateRowAmount(row);
                            calculateTotals();
                        }
                    });
                    itemNameSelect.setAttribute('data-listener-attached', 'true');
                }
            }
        });
    }
    
    function populateRowFromIndentItem(row, item, index) {
        if (!row) {
            console.error('Row not found for index:', index);
            return;
        }
        
        const purchaseIndentItemId = row.querySelector('.purchase_indent_item_id');
        const rawMaterialId = row.querySelector('.raw_material_id');
        const itemName = row.querySelector('.item_name');
        const itemDescription = row.querySelector('.item_description');
        const approvedQuantity = row.querySelector('.approved_quantity');
        const alreadyRaisedQty = row.querySelector('.already_raised_po_qty');
        const unitId = row.querySelector('.unit_id');
        const unitSymbol = row.querySelector('.unit_symbol');
        
        if (purchaseIndentItemId) purchaseIndentItemId.value = item.id || item.purchase_indent_item_id || '';
        if (rawMaterialId) rawMaterialId.value = item.raw_material_id || '';
        
        // Select the item in dropdown
        if (itemName && item.item_name) {
            itemName.value = item.item_name;
            // Trigger change event to populate other fields
            itemName.dispatchEvent(new Event('change'));
        }
        
        if (itemDescription) itemDescription.value = item.item_description || '';
        if (approvedQuantity) approvedQuantity.value = item.approved_quantity || 0;
        if (alreadyRaisedQty) alreadyRaisedQty.value = item.already_raised_po_qty || 0;
        if (unitId) unitId.value = item.unit_id || '';
        if (unitSymbol) unitSymbol.value = item.unit_symbol || '';
    }

    // Make functions globally accessible
    window.addProductRow = addProductRow;
    window.removeRow = function(button) {
        const row = button.closest('tr');
        if (row && tableBody.querySelectorAll('tr').length > 1) {
            row.remove();
            calculateTotals();
        }
    };

    function attachRowEvents(row) {
        const poQuantityInput = row.querySelector('.po_quantity');
        const approvedQtyInput = row.querySelector('.approved_quantity');
        const alreadyRaisedInput = row.querySelector('.already_raised_po_qty');
        const qtyInKgInput = row.querySelector('.qty_in_kg');
        const priceInput = row.querySelector('.price');
        const amountInput = row.querySelector('.amount');
        const purchaseIndentItemIdInput = row.querySelector('.purchase_indent_item_id');

        // PO Quantity Validation
        if (poQuantityInput) {
            poQuantityInput.addEventListener('blur', function() {
                // Ensure only whole numbers (no decimals)
                let poQty = parseInt(this.value) || 0;
                if (this.value && this.value.includes('.')) {
                    poQty = Math.floor(parseFloat(this.value)) || 0;
                    this.value = poQty;
                }
                if (poQty < 1) {
                    poQty = 0;
                    this.value = '';
                } else {
                    this.value = poQty;
                }
                
                const approvedQty = parseFloat(approvedQtyInput.value) || 0;
                const alreadyRaised = parseFloat(alreadyRaisedInput.value) || 0;
                const remainingQty = approvedQty - alreadyRaised;
                const purchaseIndentItemId = purchaseIndentItemIdInput ? purchaseIndentItemIdInput.value : null;

                // If no purchase indent item ID, skip cross-row validation
                if (!purchaseIndentItemId) {
                    if (poQty > remainingQty) {
                        showWarningModal('PO Quantity should not be greater than Approved Qty as per the sum of Raised and PO Qty.');
                        const maxAllowed = Math.floor(remainingQty);
                        this.value = maxAllowed > 0 ? maxAllowed : '';
                    }
                    calculateRowAmount(row);
                    calculateTotals();
                    return;
                }

                // Calculate total PO Quantity across all rows for the same purchase_indent_item_id
                let totalPoQtyAcrossRows = 0;
                const allRows = tableBody.querySelectorAll('tr');
                allRows.forEach(otherRow => {
                    if (otherRow === row) {
                        // Include current row's value
                        totalPoQtyAcrossRows += poQty;
                    } else {
                        const otherPurchaseIndentItemId = otherRow.querySelector('.purchase_indent_item_id');
                        const otherPoQtyInput = otherRow.querySelector('.po_quantity');
                        if (otherPurchaseIndentItemId && otherPoQtyInput && 
                            otherPurchaseIndentItemId.value === purchaseIndentItemId) {
                            const otherPoQty = parseInt(otherPoQtyInput.value) || 0;
                            totalPoQtyAcrossRows += otherPoQty;
                        }
                    }
                });

                // Check if total exceeds remaining quantity
                if (totalPoQtyAcrossRows > remainingQty) {
                    showWarningModal('PO Quantity has already been fully or partially processed in other rows. Total PO Quantity across all rows cannot exceed the remaining quantity (Approved Qty - Already Raised PO Qty).');
                    // Calculate maximum allowed for this row
                    const otherRowsTotal = totalPoQtyAcrossRows - poQty;
                    const maxAllowedForThisRow = Math.floor(Math.max(0, remainingQty - otherRowsTotal));
                    this.value = maxAllowedForThisRow > 0 ? maxAllowedForThisRow : '';
                } else if (poQty > remainingQty) {
                    // Single row validation
                    showWarningModal('PO Quantity should not be greater than Approved Qty as per the sum of Raised and PO Qty.');
                    const maxAllowed = Math.floor(remainingQty);
                    this.value = maxAllowed > 0 ? maxAllowed : '';
                }
                calculateRowAmount(row);
                calculateTotals();
            });

            // Also validate on input to provide real-time feedback
            poQuantityInput.addEventListener('input', function() {
                // Remove any decimal points and ensure only integers
                if (this.value.includes('.')) {
                    this.value = Math.floor(parseFloat(this.value)) || '';
                }
                
                let poQty = parseInt(this.value) || 0;
                const approvedQty = parseFloat(approvedQtyInput.value) || 0;
                const alreadyRaised = parseFloat(alreadyRaisedInput.value) || 0;
                const remainingQty = approvedQty - alreadyRaised;
                const purchaseIndentItemId = purchaseIndentItemIdInput ? purchaseIndentItemIdInput.value : null;

                if (!purchaseIndentItemId || remainingQty <= 0) {
                    return;
                }

                // Calculate total PO Quantity across all rows for the same purchase_indent_item_id
                let totalPoQtyAcrossRows = 0;
                const allRows = tableBody.querySelectorAll('tr');
                allRows.forEach(otherRow => {
                    if (otherRow === row) {
                        totalPoQtyAcrossRows += poQty;
                    } else {
                        const otherPurchaseIndentItemId = otherRow.querySelector('.purchase_indent_item_id');
                        const otherPoQtyInput = otherRow.querySelector('.po_quantity');
                        if (otherPurchaseIndentItemId && otherPoQtyInput && 
                            otherPurchaseIndentItemId.value === purchaseIndentItemId) {
                            const otherPoQty = parseInt(otherPoQtyInput.value) || 0;
                            totalPoQtyAcrossRows += otherPoQty;
                        }
                    }
                });

                // If total exceeds remaining, adjust the value
                if (totalPoQtyAcrossRows > remainingQty) {
                    const otherRowsTotal = totalPoQtyAcrossRows - poQty;
                    const maxAllowedForThisRow = Math.floor(Math.max(0, remainingQty - otherRowsTotal));
                    if (poQty > maxAllowedForThisRow) {
                        this.value = maxAllowedForThisRow > 0 ? maxAllowedForThisRow : '';
                    }
                }
                calculateRowAmount(row);
                calculateTotals();
            });
        }

        // Amount Calculation
        if (priceInput) {
            priceInput.addEventListener('input', function() {
                calculateRowAmount(row);
                calculateTotals();
            });
        }

        if (qtyInKgInput) {
            qtyInKgInput.addEventListener('input', function() {
                calculateRowAmount(row);
                calculateTotals();
            });
        }

        if (poQuantityInput) {
            poQuantityInput.addEventListener('input', function() {
                calculateRowAmount(row);
                calculateTotals();
            });
        }
    }

    function calculateRowAmount(row) {
        const poQty = parseInt(row.querySelector('.po_quantity').value) || 0;
        const qtyInKg = parseFloat(row.querySelector('.qty_in_kg').value) || 0;
        const price = parseFloat(row.querySelector('.price').value) || 0;
        const amountInput = row.querySelector('.amount');

        let amount = 0;
        if (qtyInKg > 0) {
            amount = qtyInKg * price;
        } else {
            amount = poQty * price;
        }

        amountInput.value = amount.toFixed(2);
    }

    function calculateTotals() {
        // 1) Sum item amounts to get TOTAL (before discount & GST)
        let subtotal = 0;
        tableBody.querySelectorAll('tr').forEach(row => {
            const amount = parseFloat(row.querySelector('.amount').value) || 0;
            subtotal += amount;
        });

        // Show TOTAL (items only)
        document.getElementById('total').value = subtotal.toFixed(2);

        // 2) Discount based on TOTAL
        const discountPercent = parseFloat(document.getElementById('discount_percent').value) || 0;
        let discountAmount = parseFloat(document.getElementById('discount').value) || 0;

        if (discountPercent > 0 && subtotal > 0) {
            discountAmount = (subtotal * discountPercent) / 100;
            document.getElementById('discount').value = discountAmount.toFixed(2);
        }

        const amountAfterDiscount = Math.max(subtotal - discountAmount, 0);

        // 3) GST based on amount AFTER discount (i.e. using TOTAL)
        const taxType = document.querySelector('input[name="tax_type"]:checked')?.value || 'cgst_sgst';
        const gstPercent = parseFloat(document.getElementById('gst_percent').value) || 0;
        const gstAmount = (amountAfterDiscount * gstPercent) / 100;
        document.getElementById('gst').value = gstAmount.toFixed(2);

        // Split GST into CGST/SGST or IGST
        if (taxType === 'cgst_sgst') {
            const cgstAmount = gstAmount / 2;
            const sgstAmount = gstAmount / 2;

            document.getElementById('cgst_percent').value = (gstPercent / 2).toFixed(2);
            document.getElementById('cgst_amount').value = cgstAmount.toFixed(2);
            document.getElementById('sgst_percent').value = (gstPercent / 2).toFixed(2);
            document.getElementById('sgst').value = sgstAmount.toFixed(2);

            // Clear IGST
            document.getElementById('igst_percent').value = '0';
            document.getElementById('igst').value = '0.00';
        } else {
            document.getElementById('igst_percent').value = gstPercent.toFixed(2);
            document.getElementById('igst').value = gstAmount.toFixed(2);

            // Clear CGST/SGST
            document.getElementById('cgst_percent').value = '0';
            document.getElementById('cgst_amount').value = '0.00';
            document.getElementById('sgst_percent').value = '0';
            document.getElementById('sgst').value = '0.00';
        }

        // 4) Total Tax and Net Amount
        const totalTaxInput = document.getElementById('total_tax');
        if (totalTaxInput) {
            totalTaxInput.value = gstAmount.toFixed(2);
        }

        const netAmount = amountAfterDiscount + gstAmount;
        document.getElementById('net_amount').value = netAmount.toFixed(2);
    }

    // Tax Type Selection Handler
    document.querySelectorAll('input[name="tax_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const taxType = this.value;
            const cgstSection = document.getElementById('cgst_section');
            const sgstSection = document.getElementById('sgst_section');
            const igstSection = document.getElementById('igst_section');
            
            if (taxType === 'cgst_sgst') {
                cgstSection.style.display = 'flex';
                sgstSection.style.display = 'flex';
                igstSection.style.display = 'none';
                // Clear IGST values
                document.getElementById('igst_percent').value = '0';
                document.getElementById('igst').value = '0.00';
            } else {
                cgstSection.style.display = 'none';
                sgstSection.style.display = 'none';
                igstSection.style.display = 'flex';
                // Clear CGST/SGST values
                document.getElementById('cgst_percent').value = '0';
                document.getElementById('cgst_amount').value = '0.00';
                document.getElementById('sgst_percent').value = '0';
                document.getElementById('sgst').value = '0.00';
            }
            calculateTotals();
        });
    });

    // Amount Calculation Section Listeners
    const gstPercentInput = document.getElementById('gst_percent');
    const gstAmountInput = document.getElementById('gst');
    const discountPercentInput = document.getElementById('discount_percent');
    const discountAmountInput = document.getElementById('discount');

    if (gstPercentInput) {
        gstPercentInput.addEventListener('input', function() {
            calculateTotals();
        });
    }

    if (gstAmountInput) {
        gstAmountInput.addEventListener('input', function() {
            // If GST amount is manually entered, calculate percentage
            const subtotal = Array.from(tableBody.querySelectorAll('tr')).reduce((sum, row) => {
                return sum + (parseFloat(row.querySelector('.amount').value) || 0);
            }, 0);
            
            if (subtotal > 0) {
                const gstPercent = (parseFloat(this.value) / subtotal) * 100;
                document.getElementById('gst_percent').value = gstPercent.toFixed(2);
            }
            calculateTotals();
        });
    }

    if (discountPercentInput) {
        discountPercentInput.addEventListener('input', function() {
            calculateTotals();
        });
    }

    if (discountAmountInput) {
        discountAmountInput.addEventListener('input', function() {
            // If discount amount is manually entered, calculate percentage
            const subtotal = Array.from(tableBody.querySelectorAll('tr')).reduce((sum, row) => {
                return sum + (parseFloat(row.querySelector('.amount').value) || 0);
            }, 0);
            
            if (subtotal > 0) {
                const discountPercent = (parseFloat(this.value) / subtotal) * 100;
                document.getElementById('discount_percent').value = discountPercent.toFixed(2);
            }
            calculateTotals();
        });
    }

    // Attach events to existing rows
    tableBody.querySelectorAll('tr').forEach(attachRowEvents);
});
</script>

