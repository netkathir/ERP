<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tableBody = document.querySelector('#itemsTable tbody');
        const rawMaterialsData = @json($rawMaterialsData);
        const form = document.getElementById('purchaseIndentForm');
        const isApprovedIndent = @json(isset($indent) && $indent->status === 'Approved');

        // Initialize Flatpickr for Indent Date
        const indentDateInput = document.getElementById('indent_date');
        if (indentDateInput) {
            // Parse existing value if it's in DD-MM-YYYY format
            let initialDate = null;
            const currentValue = indentDateInput.value;
            if (currentValue && currentValue.match(/^\d{2}-\d{2}-\d{4}$/)) {
                const [day, month, year] = currentValue.split('-');
                initialDate = new Date(year, month - 1, day);
            }
            
            const fp = flatpickr(indentDateInput, {
                dateFormat: "d-m-Y",
                allowInput: true,
                clickOpens: true,
                placeholder: "DD-MM-YYYY",
                defaultDate: initialDate || (currentValue ? currentValue : null)
            });
            
            // Ensure the value is properly formatted after initialization
            if (currentValue && currentValue.match(/^\d{2}-\d{2}-\d{4}$/)) {
                indentDateInput.value = currentValue;
            }
        }

        // Function to initialize date pickers for table date inputs
        function initializeDatePickers() {
            const dateInputs = document.querySelectorAll('.table-date-input:not([data-fp-initialized])');
            dateInputs.forEach(function(input) {
                // Parse existing value if it's in DD-MM-YYYY format
                let initialDate = null;
                const currentValue = input.value;
                if (currentValue && currentValue.match(/^\d{2}-\d{2}-\d{4}$/)) {
                    const [day, month, year] = currentValue.split('-');
                    initialDate = new Date(year, month - 1, day);
                }
                
                const fp = flatpickr(input, {
                    dateFormat: "d-m-Y",
                    allowInput: true,
                    clickOpens: true,
                    placeholder: "DD-MM-YYYY",
                    defaultDate: initialDate || (currentValue ? currentValue : null)
                });
                
                // Ensure the value is properly formatted after initialization
                if (currentValue && currentValue.match(/^\d{2}-\d{2}-\d{4}$/)) {
                    input.value = currentValue;
                }
                
                input.setAttribute('data-fp-initialized', 'true');
            });
        }

        // Initialize date pickers for existing rows
        initializeDatePickers();

        // Function to update total items count
        function updateTotalItemsCount() {
            const totalItemsElement = document.getElementById('totalItemsCount');
            if (totalItemsElement && tableBody) {
                const rowCount = tableBody.querySelectorAll('tr').length;
                totalItemsElement.textContent = rowCount;
            }
        }

        // Initialize total items count on page load
        updateTotalItemsCount();

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

        function getNextIndex() {
            const rows = tableBody.querySelectorAll('tr');
            return rows.length ? Math.max(...Array.from(rows).map(r => parseInt(r.dataset.index, 10))) + 1 : 0;
        }

        function attachRowEvents(row) {
            if (isApprovedIndent) {
                return; // Do not attach interactive events when approved
            }
            const addBtn = row.querySelector('.btn-add-row');
            const removeBtn = row.querySelector('.btn-remove-row');
            const itemSelect = row.querySelector('.item-select');
            const descInput = row.querySelector('.item-description');
            const unitSelect = row.querySelector('.unit-select');

            if (addBtn) {
                addBtn.addEventListener('click', function () {
                    addRow();
                });
            }

            if (removeBtn) {
                removeBtn.addEventListener('click', function () {
                    if (tableBody.querySelectorAll('tr').length > 1) {
                        row.remove();
                        updateTotalItemsCount();
                    }
                });
            }

            if (itemSelect) {
                itemSelect.addEventListener('change', function () {
                    const id = this.value;
                    const data = rawMaterialsData[id];
                    if (data && unitSelect && data.unit_id) {
                        // Always set unit based on selected item
                        unitSelect.value = data.unit_id;
                        // If there is a hidden unit field in the same row, keep it in sync
                        const row = this.closest('tr');
                        if (row) {
                            const hiddenUnit = row.querySelector('.unit-hidden');
                            if (hiddenUnit) {
                                hiddenUnit.value = data.unit_id;
                            }
                        }
                    }
                });
            }

            // Prevent decimal input in quantity fields
            const quantityInput = row.querySelector('input[name*="[quantity]"]');
            if (quantityInput && quantityInput.type === 'number') {
                // Round any existing decimal values on page load (handle cases like "5.000", "10.000", etc.)
                if (quantityInput.value) {
                    const numValue = parseFloat(quantityInput.value);
                    if (!isNaN(numValue)) {
                        const roundedValue = Math.round(numValue);
                        // Always round to integer, even if it looks like a whole number (e.g., "5.000")
                        if (quantityInput.value.toString().includes('.') || numValue % 1 !== 0) {
                            quantityInput.value = roundedValue;
                        } else {
                            quantityInput.value = roundedValue; // Ensure it's stored as integer
                        }
                        // Ensure minimum value of 1
                        if (roundedValue < 1) {
                            quantityInput.value = 1;
                        }
                    }
                }
                
                quantityInput.addEventListener('input', function() {
                    // Remove any decimal point and digits after it
                    if (this.value && this.value.includes('.')) {
                        this.value = Math.floor(parseFloat(this.value) || 0);
                    }
                    // Only validate minimum if value is entered (allow empty while typing)
                    if (this.value && parseFloat(this.value) < 1) {
                        this.value = 1;
                    }
                });
                
                quantityInput.addEventListener('blur', function() {
                    // Only validate if value is entered (allow empty for required validation)
                    if (this.value) {
                        // Round to nearest integer on blur
                        if (parseFloat(this.value) % 1 !== 0) {
                            this.value = Math.round(parseFloat(this.value));
                        }
                        // Ensure minimum value of 1
                        const numValue = parseFloat(this.value);
                        if (numValue < 1) {
                            this.value = 1;
                        }
                    }
                });
                
                quantityInput.addEventListener('keydown', function(e) {
                    // Prevent decimal point and comma from being entered
                    if (e.key === '.' || e.key === ',' || e.key === 'e' || e.key === 'E' || e.key === '+' || e.key === '-') {
                        e.preventDefault();
                    }
                });
            }
        }

        function addRow() {
            if (isApprovedIndent) {
                return;
            }
            const index = getNextIndex();
            const firstRow = tableBody.querySelector('tr');
            if (!firstRow) return;

            const newRow = firstRow.cloneNode(true);
            newRow.dataset.index = index;

            newRow.querySelectorAll('input, select').forEach(function (input) {
                const name = input.getAttribute('name') || '';
                const newName = name.replace(/items\[\d+\]/, 'items[' + index + ']');
                input.setAttribute('name', newName);
                const inputName = newName.toLowerCase();

                // Clear quantity fields to allow manual entry (min is 1 for validation)
                if (inputName.includes('quantity') && input.type === 'number') {
                    input.value = '';
                } else if (input.type === 'number') {
                    input.value = 0;
                } else {
                    input.value = '';
                }
                
                if (input.classList.contains('date-input')) {
                    input.value = formatDateForDisplay(new Date());
                    // Remove the data-fp-initialized attribute so the new input can be initialized
                    input.removeAttribute('data-fp-initialized');
                }
                
                // Preserve required attribute for: Item, Quantity, Schedule Date, Special Instructions, Supplier
                if (inputName.includes('raw_material_id') || 
                    inputName.includes('quantity') || 
                    inputName.includes('schedule_date') || 
                    inputName.includes('special_instructions') ||
                    inputName.includes('supplier_id')) {
                    input.setAttribute('required', 'required');
                }
                
                // Set min value and step for quantity to ensure it's a whole number (no decimals)
                if (inputName.includes('quantity') && input.type === 'number') {
                    input.setAttribute('min', '1');
                    input.setAttribute('step', '1');
                }
            });

            tableBody.appendChild(newRow);
            attachRowEvents(newRow);
            
            // Initialize date pickers for the new row
            initializeDatePickers();
            
            // Update total items count
            updateTotalItemsCount();
        }

        tableBody.querySelectorAll('tr').forEach(attachRowEvents);

        if (form) {
            form.addEventListener('submit', function () {
                const dateInputs = form.querySelectorAll('.date-input');
                dateInputs.forEach(function (input) {
                    // Get the actual value from Flatpickr instance if it exists
                    const fpInstance = input._flatpickr;
                    if (fpInstance) {
                        // Flatpickr already formats it as d-m-Y, so we can use it directly
                        // But we need to convert to ISO format for backend
                        const value = input.value;
                        if (value) {
                            input.value = toIsoDate(value);
                        }
                    } else {
                        input.value = toIsoDate(input.value);
                    }
                });
            });
        }
    });
</script>

