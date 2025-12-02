<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tableBody = document.querySelector('#itemsTable tbody');
        const rawMaterialsData = @json($rawMaterialsData);

        function getNextIndex() {
            const rows = tableBody.querySelectorAll('tr');
            return rows.length ? Math.max(...Array.from(rows).map(r => parseInt(r.dataset.index, 10))) + 1 : 0;
        }

        function attachRowEvents(row) {
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
        }

        function addRow() {
            const index = getNextIndex();
            const firstRow = tableBody.querySelector('tr');
            if (!firstRow) return;

            const newRow = firstRow.cloneNode(true);
            newRow.dataset.index = index;

            newRow.querySelectorAll('input, select').forEach(function (input) {
                const name = input.getAttribute('name') || '';
                const newName = name.replace(/items\[\d+\]/, 'items[' + index + ']');
                input.setAttribute('name', newName);

                if (input.type === 'number' || input.type === 'date') {
                    input.value = input.type === 'date' ? '' : 0;
                } else {
                    input.value = '';
                }
            });

            tableBody.appendChild(newRow);
            attachRowEvents(newRow);
        }

        tableBody.querySelectorAll('tr').forEach(attachRowEvents);
    });
</script>


