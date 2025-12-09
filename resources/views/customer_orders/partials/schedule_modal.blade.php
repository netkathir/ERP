<div id="scheduleModal" class="modal" style="display:none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1050; align-items: center; justify-content: center;">
    <div style="background: white; width: 900px; max-width: 95%; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
        <div style="padding: 15px 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="margin: 0; color: #333;">Schedule</h4>
            <button type="button" onclick="CustomerOrderScheduleModal.close()" style="background: transparent; border: none; font-size: 20px; cursor: pointer;">&times;</button>
        </div>
        <div style="padding: 20px;">
            <!-- Parent Information -->
            <div style="margin-top: 18px; background: #f1f5f9; border-radius: 8px; padding: 18px 18px 14px 18px;">
                <h5 style="margin: 0 0 12px 0; font-size: 15px; font-weight: 600; color: #0f172a;">Parent Information</h5>
                <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 24px;">
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:#0f172a; margin-bottom:4px;">
                            Tender No <span style="color:#ef4444;">*</span>
                        </label>
                        <input id="schedule_tender_no" type="text" readonly
                               style="width:100%; padding:9px 10px; border-radius:6px; border:1px solid #d1d5db; background:#e5e7eb; font-size:13px; color:#111827;">
                        <p style="margin:4px 0 0 0; font-size:12px; color:#6b7280;">Inherited from Customer Order</p>
                    </div>
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:#0f172a; margin-bottom:4px;">
                            Product Name <span style="color:#ef4444;">*</span>
                        </label>
                        <select id="schedule_product_dropdown" onchange="CustomerOrderScheduleModal.onProductSelect()"
                                style="width:100%; padding:9px 10px; border-radius:6px; border:1px solid #d1d5db; font-size:13px; color:#111827; display:none;">
                            <option value="">Select Product</option>
                        </select>
                        <input id="schedule_product_name" type="text" readonly
                               style="width:100%; padding:9px 10px; border-radius:6px; border:1px solid #d1d5db; background:#e5e7eb; font-size:13px; color:#111827;">
                        <p id="schedule_ordered_qty_info" style="margin:4px 0 0 0; font-size:12px; color:#6b7280;"></p>
                    </div>
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:#0f172a; margin-bottom:4px;">
                            PO SR No <span style="color:#ef4444;">*</span>
                        </label>
                        <input id="schedule_po_sr_no" type="text" oninput="CustomerOrderScheduleModal.updatePoSrNo(this.value)"
                               style="width:100%; padding:9px 10px; border-radius:6px; border:1px solid #d1d5db; background:#ffffff; font-size:13px; color:#111827;">
                        <p style="margin:4px 0 0 0; font-size:12px; color:#6b7280;">Auto-populated from selected product (you can edit if needed)</p>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-bottom: 10px;">
                <button type="button" onclick="CustomerOrderScheduleModal.addRow()" style="padding: 6px 12px; background: #28a745; color: white; border: none; border-radius: 4px; font-size: 13px; cursor: pointer;">
                    <i class="fas fa-plus"></i> Add Row
                </button>
            </div>

            <div style="overflow-x: auto; max-height: 350px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 8px; text-align: right; color: #333; font-weight: 600;">Quantity</th>
                            <th style="padding: 8px; text-align: left; color: #333; font-weight: 600;">Unit</th>
                            <th style="padding: 8px; text-align: left; color: #333; font-weight: 600;">Start Date</th>
                            <th style="padding: 8px; text-align: left; color: #333; font-weight: 600;">End Date</th>
                            <th style="padding: 8px; text-align: left; color: #333; font-weight: 600;">Inspection Clause</th>
                            <th style="padding: 8px; text-align: center; color: #333; font-weight: 600;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="scheduleModalBody">
                        <!-- rows -->
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="CustomerOrderScheduleModal.close()" style="padding: 8px 16px; background: #6c757d; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;">
                    Cancel
                </button>
                <button type="button" onclick="CustomerOrderScheduleModal.save()" style="padding: 8px 16px; background: #667eea; color: white; border: none; border-radius: 4px; font-size: 14px; cursor: pointer;">
                    Save
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.CustomerOrderScheduleModal = (function () {
        let currentItem = null;
        let availableItems = [];
        let schedulesRef = [];
        let unitsRef = [];
        let onSaveCb = null;

        function open(item, availableItemsList, allSchedules, units, onSave) {
            availableItems = availableItemsList || [];
            unitsRef = units;
            onSaveCb = onSave;

            // Parent information header fields
            const tenderSelect = document.getElementById('tender_id');
            const tenderText = tenderSelect && tenderSelect.selectedOptions.length
                ? tenderSelect.selectedOptions[0].text
                : '';

            document.getElementById('schedule_tender_no').value = tenderText;

            // Option 1: Item pre-selected (from row selection)
            if (item && item.index !== undefined) {
                currentItem = item;
                schedulesRef = allSchedules.filter(s => s.item_index === item.index);
                
                // Show readonly input, hide dropdown
                document.getElementById('schedule_product_dropdown').style.display = 'none';
                document.getElementById('schedule_product_name').style.display = 'block';
                document.getElementById('schedule_product_name').value = item.product_name || '';
                document.getElementById('schedule_po_sr_no').value = item.po_sr_no || '';
                document.getElementById('schedule_ordered_qty_info').textContent =
                    item.ordered_qty ? `Ordered Qty: ${item.ordered_qty} ${item.unit_symbol || ''}` : '';
            } else {
                // Option 2: No row selected - show dropdown
                currentItem = null;
                schedulesRef = [];
                
                // Show dropdown, hide readonly input
                const dropdown = document.getElementById('schedule_product_dropdown');
                const readonly = document.getElementById('schedule_product_name');
                dropdown.style.display = 'block';
                readonly.style.display = 'none';
                
                // Populate dropdown
                dropdown.innerHTML = '<option value="">Select Product</option>' +
                    availableItems.map((it, idx) => 
                        `<option value="${idx}" data-index="${it.index}" data-po="${it.po_sr_no || ''}" data-qty="${it.ordered_qty}" data-unit="${it.unit_symbol || ''}">${it.product_name}</option>`
                    ).join('');
                
                document.getElementById('schedule_po_sr_no').value = '';
                document.getElementById('schedule_ordered_qty_info').textContent = '';
            }

            renderRows();
            document.getElementById('scheduleModal').style.display = 'flex';
        }

        function onProductSelect() {
            const dropdown = document.getElementById('schedule_product_dropdown');
            const selectedOption = dropdown.options[dropdown.selectedIndex];
            if (!selectedOption || !selectedOption.value) {
                currentItem = null;
                document.getElementById('schedule_product_name').value = '';
                document.getElementById('schedule_po_sr_no').value = '';
                document.getElementById('schedule_ordered_qty_info').textContent = '';
                schedulesRef = [];
                renderRows();
                return;
            }
            
            const itemIndex = parseInt(selectedOption.dataset.index);
            const item = availableItems.find(it => it.index === itemIndex);
            if (item) {
                currentItem = item;
                document.getElementById('schedule_product_name').value = item.product_name;
                document.getElementById('schedule_ordered_qty_info').textContent =
                    item.ordered_qty ? `Ordered Qty: ${item.ordered_qty} ${item.unit_symbol || ''}` : '';
                
                // Load existing schedules for this item
                const allSchedules = window.schedules || [];
                schedulesRef = allSchedules.filter(s => s.item_index === item.index);

                // Prefer PO SR No from existing schedule rows, fall back to item
                const existingPo = schedulesRef.length ? (schedulesRef[0].po_sr_no || '') : (item.po_sr_no || '');
                document.getElementById('schedule_po_sr_no').value = existingPo;
                if (existingPo) {
                    currentItem.po_sr_no = existingPo;
                }

                renderRows();
            }
        }

        function close() {
            document.getElementById('scheduleModal').style.display = 'none';
        }

        function renderRows() {
            const tbody = document.getElementById('scheduleModalBody');
            tbody.innerHTML = '';

            // When no product is selected yet, just show an info row.
            // Do NOT call addRow() here, otherwise it will trigger the
            // "Please select a product first." alert as soon as the modal opens.
            if (!currentItem) {
                tbody.innerHTML = `<tr>
                    <td colspan="6" style="padding: 10px; text-align: center; color: #777;">
                        Select a product above to add schedule rows.
                    </td>
                </tr>`;
                return;
            }

            if (schedulesRef.length === 0) {
                addRow();
                return;
            }

            schedulesRef.forEach((s, idx) => appendRowElement(s, idx));
            updateTotal();
        }

        function appendRowElement(s, idx) {
            const tbody = document.getElementById('scheduleModalBody');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td style="padding: 6px;">
                    <input type="number" step="0.01" min="0" value="${s.quantity || ''}" onchange="CustomerOrderScheduleModal.updateField(${idx}, 'quantity', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; text-align: right; font-size: 13px;">
                </td>
                <td style="padding: 6px;">
                    <select onchange="CustomerOrderScheduleModal.updateField(${idx}, 'unit_id', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                        ${unitsRef.map(u => `<option value="${u.id}" ${s.unit_id == u.id ? 'selected' : ''}>${u.symbol}</option>`).join('')}
                    </select>
                </td>
                <td style="padding: 6px;">
                    <input type="date" value="${s.start_date || ''}" onchange="CustomerOrderScheduleModal.updateField(${idx}, 'start_date', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                </td>
                <td style="padding: 6px;">
                    <input type="date" value="${s.end_date || ''}" onchange="CustomerOrderScheduleModal.updateField(${idx}, 'end_date', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                </td>
                <td style="padding: 6px;">
                    <input type="text" value="${s.inspection_clause || ''}" onchange="CustomerOrderScheduleModal.updateField(${idx}, 'inspection_clause', this.value)" style="width: 100%; padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 13px;">
                </td>
                <td style="padding: 6px; text-align: center;">
                    <button type="button" onclick="CustomerOrderScheduleModal.removeRow(${idx})" style="padding: 4px 8px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        }

        function addRow() {
            if (!currentItem) {
                alert('Please select a product first.');
                return;
            }
            const newRow = {
                item_index: currentItem.index,
                product_name: currentItem.product_name,
                po_sr_no: currentItem.po_sr_no,
                ordered_qty: currentItem.ordered_qty,
                quantity: '',
                unit_id: unitsRef.length ? unitsRef[0].id : null,
                unit_symbol: unitsRef.length ? unitsRef[0].symbol : '',
                start_date: '',
                end_date: '',
                inspection_clause: '',
            };
            schedulesRef.push(newRow);
            renderRows();
        }

        function removeRow(idx) {
            schedulesRef.splice(idx, 1);
            renderRows();
        }

        function updateField(idx, field, value) {
            schedulesRef[idx][field] = value;
            if (field === 'unit_id') {
                const u = unitsRef.find(x => x.id == value);
                schedulesRef[idx].unit_symbol = u ? u.symbol : '';
            }
            if (field === 'quantity') {
                updateTotal();
            }
        }

        function updatePoSrNo(value) {
            if (currentItem) {
                currentItem.po_sr_no = value;
                const poInput = document.querySelector(`input[name="items[${currentItem.index}][po_sr_no]"]`);
                if (poInput) {
                    poInput.value = value;
                }
            }
            schedulesRef.forEach(s => {
                s.po_sr_no = value;
            });
        }

        function updateTotal() {
            if (!currentItem) return;
            const total = schedulesRef.reduce((sum, s) => sum + (parseFloat(s.quantity || '0') || 0), 0);
            // Optional: You can add a total info display here if needed
        }

        function save() {
            if (!currentItem) {
                alert('Please select a product first.');
                return;
            }
            
            // Validation: required fields + qty <= ordered
            let total = 0;
            for (const s of schedulesRef) {
                const q = parseFloat(s.quantity || '0') || 0;
                total += q;
                if (!s.start_date || !s.end_date) {
                    alert('Start Date and End Date are required for all schedule rows.');
                    return;
                }
            }
            if (total > currentItem.ordered_qty) {
                alert(`Total scheduled quantity (${total}) cannot exceed ordered quantity (${currentItem.ordered_qty}) for this product.`);
                return;
            }

            // Merge back into global schedules array
            const allSchedules = window.schedules || [];
            const others = allSchedules.filter(s => s.item_index !== currentItem.index);
            window.schedules = others.concat(schedulesRef);

            if (typeof onSaveCb === 'function') {
                onSaveCb(window.schedules);
            }
            close();
        }

        return {
            open,
            close,
            addRow,
            removeRow,
            updateField,
            save,
            onProductSelect,
            updatePoSrNo,
        };
    })();
</script>
@endpush


