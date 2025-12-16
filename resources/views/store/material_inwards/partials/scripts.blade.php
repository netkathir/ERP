@php
    $materialInwardItemsForJs = old('items', isset($materialInward) ? $materialInward->items->map(function($item) {
        return [
            'purchase_order_item_id' => $item->purchase_order_item_id,
            'raw_material_id' => $item->raw_material_id,
            'item_description' => $item->item_description,
            'item_name' => $item->item_description,
            'po_qty' => $item->po_qty,
            'pending_qty' => $item->pending_qty,
            'unit_id' => $item->unit_id,
            'unit_symbol' => optional($item->unit)->symbol,
            'received_qty' => $item->received_qty,
            'received_qty_in_kg' => $item->received_qty_in_kg,
            'batch_no' => $item->batch_no,
            'cost_per_unit' => $item->cost_per_unit,
            'total' => $item->total,
            'supplier_invoice_no' => $item->supplier_invoice_no,
            'invoice_date' => $item->invoice_date ? $item->invoice_date->format('Y-m-d') : null,
        ];
    })->toArray() : []);
@endphp

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const purchaseOrderSelect = document.getElementById('purchase_order_id');
        const supplierNameInput = document.getElementById('supplier_name');
        const itemsTableBody = document.getElementById('itemsTableBody');
        const form = document.getElementById('materialInwardForm');
        const existingItems = @json($materialInwardItemsForJs);
        let currentPoItems = [];
        const existingItemsByPoItemId = {};
        existingItems.forEach((it) => {
            if (it.purchase_order_item_id) {
                existingItemsByPoItemId[it.purchase_order_item_id] = it;
            }
        });
        let itemIndex = 0;

        function formatDateForDisplay(date) {
            const d = typeof date === 'string' ? new Date(date) : date;
            if (!(d instanceof Date) || isNaN(d.getTime())) return '';
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}-${month}-${year}`;
        }

        function toIsoDate(value) {
            if (!value) return '';
            const parts = value.split('-');
            if (parts.length === 3) {
                const [day, month, year] = parts;
                if (day.length === 2 && month.length === 2 && year.length === 4) {
                    return `${year}-${month}-${day}`;
                }
            }
            const parsed = new Date(value);
            return isNaN(parsed.getTime()) ? value : parsed.toISOString().slice(0, 10);
        }

        function calculateTotal(row) {
            const receivedQty = parseFloat(row.querySelector('.received-qty').value) || 0;
            const costPerUnit = parseFloat(row.querySelector('.cost-per-unit').value) || 0;
            const total = receivedQty * costPerUnit;
            row.querySelector('.total').value = total.toFixed(2);
        }

        function validateReceivedQty(row) {
            const receivedQtyInput = row.querySelector('.received-qty');
            const pendingQty = parseFloat(row.querySelector('.pending-qty').textContent) || 0;
            const receivedQty = parseFloat(receivedQtyInput.value) || 0;

            if (receivedQty > pendingQty) {
                receivedQtyInput.setCustomValidity(`Received Qty cannot exceed Pending Qty (${pendingQty})`);
                receivedQtyInput.style.borderColor = '#dc3545';
            } else {
                receivedQtyInput.setCustomValidity('');
                receivedQtyInput.style.borderColor = '#ddd';
            }
        }

        function populateItemOptions(select, selectedId) {
            select.innerHTML = '<option value=\"\">Select Item</option>';
            currentPoItems.forEach((poItem) => {
                const opt = document.createElement('option');
                opt.value = poItem.id;
                opt.textContent = poItem.item_name || poItem.item_description || 'Item';
                if (String(selectedId) === String(poItem.id)) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
        }

        function applyItemDataToRow(item, row) {
            const rawMaterialInput = row.querySelector('.raw-material-id');
            const itemDescInput = row.querySelector('.item-description');
            const poQtySpan = row.querySelector('.po-qty');
            const poQtyInput = row.querySelector('input[name*=\"[po_qty]\"]');
            const pendingQtySpan = row.querySelector('.pending-qty');
            const pendingQtyInput = row.querySelector('input[name*=\"[pending_qty]\"]');
            const unitInput = row.querySelector('input[name*=\"[unit_id]\"]');
            const unitSymbolSpan = row.querySelector('.unit-symbol');
            const costPerUnitInput = row.querySelector('.cost-per-unit');

            if (rawMaterialInput) rawMaterialInput.value = item.raw_material_id || '';
            if (itemDescInput) itemDescInput.value = item.item_name || item.item_description || '';
            if (poQtySpan) poQtySpan.textContent = item.po_qty;
            if (poQtyInput) poQtyInput.value = item.po_qty;
            if (pendingQtySpan) pendingQtySpan.textContent = item.pending_qty;
            if (pendingQtyInput) pendingQtyInput.value = item.pending_qty;
            if (unitInput) unitInput.value = item.unit_id || '';
            if (unitSymbolSpan) unitSymbolSpan.textContent = item.unit_symbol || '';
            if (costPerUnitInput && item.cost_per_unit !== undefined) costPerUnitInput.value = item.cost_per_unit || 0;

            // Clear entry fields that are user-input to avoid stale data when switching items
            const receivedQtyInput = row.querySelector('.received-qty');
            const receivedQtyKgInput = row.querySelector('.received-qty-in-kg');
            const batchNoInput = row.querySelector('.batch-no');
            const supplierInvoiceInput = row.querySelector('.supplier-invoice-no');
            const invoiceDateInput = row.querySelector('.invoice-date');
            const totalInput = row.querySelector('.total');

            if (receivedQtyInput) receivedQtyInput.value = '';
            if (receivedQtyKgInput) receivedQtyKgInput.value = '';
            if (batchNoInput) batchNoInput.value = '';
            if (supplierInvoiceInput) supplierInvoiceInput.value = '';
            if (invoiceDateInput) invoiceDateInput.value = formatDateForDisplay(new Date());
            if (totalInput) totalInput.value = '0.00';

            calculateTotal(row);
            validateReceivedQty(row);
        }

        function getUsedPoItemIds() {
            return Array.from(itemsTableBody.querySelectorAll('.purchase-order-item-id'))
                .map(select => select.value)
                .filter(v => v);
        }

        function createItemRow(item) {
            const row = document.createElement('tr');
            row.dataset.index = itemIndex;
            const displayName = item.item_name || item.item_description || 'Item';
            row.innerHTML = `
                <td style="padding:6px 8px;">
                    <select name="items[${itemIndex}][purchase_order_item_id]" class="purchase-order-item-id" required
                            style="width:200px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                        <option value="">Select Item</option>
                    </select>
                    <input type="hidden" name="items[${itemIndex}][raw_material_id]" class="raw-material-id" value="${item.raw_material_id}">
                    <input type="hidden" name="items[${itemIndex}][item_description]" class="item-description" value="${displayName}">
                </td>
                <td style="padding:6px 8px; text-align:right;">
                    <span class="po-qty">${item.po_qty}</span>
                    <input type="hidden" name="items[${itemIndex}][po_qty]" value="${item.po_qty}">
                </td>
                <td style="padding:6px 8px; text-align:right;">
                    <span class="pending-qty">${item.pending_qty}</span>
                    <input type="hidden" name="items[${itemIndex}][pending_qty]" value="${item.pending_qty}">
                </td>
                <td style="padding:6px 8px;">
                    <input type="number" step="0.001" min="0.001" name="items[${itemIndex}][received_qty]" class="received-qty" required
                           style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;">
                </td>
                <td style="padding:6px 8px;">
                    <span class="unit-symbol">${item.unit_symbol || ''}</span>
                    <input type="hidden" name="items[${itemIndex}][unit_id]" value="${item.unit_id}">
                </td>
                <td style="padding:6px 8px;">
                    <input type="number" step="0.001" min="0" name="items[${itemIndex}][received_qty_in_kg]" class="received-qty-in-kg" required
                           style="width:120px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;">
                </td>
                <td style="padding:6px 8px;">
                    <input type="text" name="items[${itemIndex}][batch_no]" class="batch-no"
                           style="width:120px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                </td>
                <td style="padding:6px 8px;">
                    <input type="number" step="0.01" min="0" name="items[${itemIndex}][cost_per_unit]" class="cost-per-unit" required
                           value="${item.cost_per_unit || 0}"
                           style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;">
                </td>
                <td style="padding:6px 8px; text-align:right;">
                    <input type="text" name="items[${itemIndex}][total]" class="total" readonly
                           value="0.00"
                           style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right; background:#f8f9fa;">
                </td>
                <td style="padding:6px 8px;">
                    <input type="text" name="items[${itemIndex}][supplier_invoice_no]" class="supplier-invoice-no" required
                           style="width:150px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                </td>
                <td style="padding:6px 8px;">
                    <input type="text" name="items[${itemIndex}][invoice_date]" class="date-input invoice-date" placeholder="DD-MM-YYYY" inputmode="numeric" pattern="\\d{2}-\\d{2}-\\d{4}" required
                           value="${formatDateForDisplay(new Date())}"
                           style="width:120px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                </td>
                <td style="padding:6px 8px; text-align:center;">
                    <button type="button" class="btn-add-row" style="padding:4px 8px; border:none; border-radius:4px; background:#28a745; color:white; cursor:pointer;">+</button>
                    <button type="button" class="btn-remove-row" style="padding:4px 8px; border:none; border-radius:4px; background:#dc3545; color:white; cursor:pointer;">-</button>
                </td>
            `;

            // Set up the item select dropdown
            const itemSelect = row.querySelector('.purchase-order-item-id');
            populateItemOptions(itemSelect, item.id || item.purchase_order_item_id);

            // Prefill values when editing
            const receivedQtyInput = row.querySelector('.received-qty');
            const receivedQtyKgInput = row.querySelector('.received-qty-in-kg');
            const batchNoInput = row.querySelector('.batch-no');
            const costPerUnitInput = row.querySelector('.cost-per-unit');
            const totalInput = row.querySelector('.total');
            const supplierInvoiceInput = row.querySelector('.supplier-invoice-no');
            const invoiceDateInput = row.querySelector('.invoice-date');

            if (item.received_qty !== undefined) {
                receivedQtyInput.value = item.received_qty ?? '';
            }
            if (item.received_qty_in_kg !== undefined) {
                receivedQtyKgInput.value = item.received_qty_in_kg ?? '';
            }
            if (item.batch_no !== undefined) {
                batchNoInput.value = item.batch_no ?? '';
            }
            if (item.cost_per_unit !== undefined) {
                costPerUnitInput.value = item.cost_per_unit ?? 0;
            }
            if (item.supplier_invoice_no !== undefined) {
                supplierInvoiceInput.value = item.supplier_invoice_no ?? '';
            }
            if (item.invoice_date) {
                invoiceDateInput.value = formatDateForDisplay(item.invoice_date);
            }
            if (item.total !== undefined) {
                totalInput.value = parseFloat(item.total || 0).toFixed(2);
            } else {
                calculateTotal(row);
            }

            calculateTotal(row);
            validateReceivedQty(row);

            // Attach event listeners
            attachRowEvents(row);

            return row;
        }

        function attachRowEvents(row) {
            const receivedQtyInput = row.querySelector('.received-qty');
            const costPerUnitInput = row.querySelector('.cost-per-unit');
            const addBtn = row.querySelector('.btn-add-row');
            const removeBtn = row.querySelector('.btn-remove-row');

            if (receivedQtyInput) {
                receivedQtyInput.addEventListener('input', function() {
                    calculateTotal(row);
                    validateReceivedQty(row);
                });
            }

            if (costPerUnitInput) {
                costPerUnitInput.addEventListener('input', function() {
                    calculateTotal(row);
                });
            }

            if (addBtn) {
                addBtn.addEventListener('click', function() {
                    addNewRow();
                });
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    if (itemsTableBody.querySelectorAll('tr').length > 1) {
                        row.remove();
                    } else {
                        alert('At least one item row is required.');
                    }
                });
            }

            const itemSelect = row.querySelector('.purchase-order-item-id');
            if (itemSelect) {
                itemSelect.addEventListener('change', function() {
                    const selected = currentPoItems.find(it => String(it.id) === this.value);
                    if (selected) {
                        applyItemDataToRow(selected, row);
                    }
                });
            }
        }

        function addNewRow() {
            // Get the first row to clone structure
            const firstRow = itemsTableBody.querySelector('tr');
            if (!firstRow || firstRow.querySelector('.purchase-order-item-id')) {
                // Get items from current PO
                const poId = purchaseOrderSelect.value;
                if (!poId) {
                    alert('Please select a Purchase Order first.');
                    return;
                }
                loadPurchaseOrderItems(poId, true);
                return;
            }

            // Prevent adding more rows than available PO items
            const usedIds = getUsedPoItemIds();
            const nextItem = currentPoItems.find(it => !usedIds.includes(String(it.id)) && (it.pending_qty > 0 || existingItemsByPoItemId[it.id]));
            if (nextItem) {
                const row = createItemRow(nextItem);
                itemsTableBody.appendChild(row);
                itemIndex++;
            } else {
                alert('No more items available for this Purchase Order.');
            }
        }

        function loadPurchaseOrderItems(poId, addRow = false) {
            if (!poId) {
                itemsTableBody.innerHTML = '<tr><td colspan="12" style="padding:20px; text-align:center; color:#666;">Please select a Purchase Order to load items</td></tr>';
                supplierNameInput.value = '';
                currentPoItems = [];
                return;
            }

            const hasRows = !!itemsTableBody.querySelector('.purchase-order-item-id');
            if (!(addRow && hasRows)) {
                itemsTableBody.innerHTML = '<tr><td colspan="12" style="padding:20px; text-align:center; color:#666;"><i class="fas fa-spinner fa-spin"></i> Loading items...</td></tr>';
            }

            const excludeId = @json(isset($materialInward) ? $materialInward->id : null);
            const url = excludeId 
                ? `{{ url('material-inwards/purchase-order') }}/${poId}/items?exclude_material_inward_id=${excludeId}`
                : `{{ url('material-inwards/purchase-order') }}/${poId}/items`;
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        itemsTableBody.innerHTML = `<tr><td colspan="12" style="padding:20px; text-align:center; color:#dc3545;">${data.error}</td></tr>`;
                        return;
                    }

                    supplierNameInput.value = data.supplier_name || '';

                    if (data.items && data.items.length > 0) {
                        const itemsWithPrefill = data.items.map(item => {
                            const existing = existingItemsByPoItemId[item.id];
                            return existing ? { ...item, ...existing, id: item.id } : item;
                        });
                        currentPoItems = itemsWithPrefill;

                        // When adding a row, append instead of resetting existing rows
                        if (addRow && itemsTableBody.querySelector('.purchase-order-item-id')) {
                            const usedIds = getUsedPoItemIds();
                            const templateItem = itemsWithPrefill.find(it => !usedIds.includes(String(it.id)) && it.pending_qty > 0) || null;
                            if (templateItem) {
                                const row = createItemRow(templateItem);
                                itemsTableBody.appendChild(row);
                                itemIndex++;
                            } else {
                                alert('No more items available for this Purchase Order.');
                            }
                            return;
                        }

                        itemsTableBody.innerHTML = '';
                        itemIndex = 0;

                        itemsWithPrefill.forEach(item => {
                            if (item.pending_qty > 0 || addRow || existingItemsByPoItemId[item.id]) {
                                const row = createItemRow(item);
                                itemsTableBody.appendChild(row);
                                itemIndex++;
                            }
                        });

                        if (itemsTableBody.querySelectorAll('tr').length === 0) {
                            itemsTableBody.innerHTML = '<tr><td colspan="12" style="padding:20px; text-align:center; color:#666;">No items with pending quantity available.</td></tr>';
                        }
                    } else {
                        currentPoItems = [];
                        itemsTableBody.innerHTML = '<tr><td colspan="12" style="padding:20px; text-align:center; color:#666;">No items found for this Purchase Order.</td></tr>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    currentPoItems = [];
                    itemsTableBody.innerHTML = '<tr><td colspan="12" style="padding:20px; text-align:center; color:#dc3545;">Error loading items. Please try again.</td></tr>';
                });
        }

        // Load items when PO is selected
        if (purchaseOrderSelect) {
            purchaseOrderSelect.addEventListener('change', function() {
                loadPurchaseOrderItems(this.value);
            });

            // Load items if PO is already selected (on page load with old input)
            if (purchaseOrderSelect.value) {
                loadPurchaseOrderItems(purchaseOrderSelect.value);
            }
        }

        // Convert dates to ISO format before submit
        if (form) {
            form.addEventListener('submit', function() {
                const dateInputs = form.querySelectorAll('.date-input');
                dateInputs.forEach(function (input) {
                    input.value = toIsoDate(input.value);
                });
            });
        }
    });
</script>

