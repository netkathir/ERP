@extends('layouts.dashboard')

@section('title', 'Create BOM Process - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">BOM Process</h2>
        <a href="{{ route('bom-processes.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
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

    <form action="{{ route('bom-processes.store') }}" method="POST" id="bomProcessForm">
        @csrf
        
        <div style="margin-bottom: 25px;">
            <label for="product_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Product Name <span style="color: red;">*</span></label>
            <select name="product_id" id="product_id" required
                style="width: 100%; max-width: 400px; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                <option value="">-- Select Product --</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
            @error('product_id')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <!-- Raw Materials Table -->
        <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px; padding: 20px; margin-bottom: 25px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Raw Materials</h3>
                <button type="button" onclick="addRow()" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>
            
            <div style="overflow-x: auto;">
                <table id="itemsTable" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #e9ecef; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Process <span style="color: red;">*</span></th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Raw Material <span style="color: red;">*</span></th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Quantity <span style="color: red;">*</span></th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">UOM <span style="color: red;">*</span></th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        <!-- Rows will be added dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display: flex; gap: 15px; justify-content: space-between; align-items: center; margin-top: 30px;">
            <div>
                <a href="{{ route('bom-processes.index') }}" style="padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-list"></i> List
                </a>
            </div>
            <div style="display: flex; gap: 15px;">
                <button type="button" onclick="clearForm()" style="padding: 12px 24px; background: #6c757d; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-eraser"></i> Clear
                </button>
                <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                    <i class="fas fa-save"></i> Submit
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let rowCount = 0;
    const processes = @json($processes);
    const rawMaterials = @json($rawMaterials);
    const units = @json($units);

    function getSelectedRawMaterials() {
        const selects = document.querySelectorAll('select[name*="[raw_material_id]"]');
        const selected = [];
        selects.forEach(select => {
            if (select.value) {
                selected.push(select.value);
            }
        });
        return selected;
    }

    function updateRawMaterialOptions() {
        const selected = getSelectedRawMaterials();
        const selects = document.querySelectorAll('select[name*="[raw_material_id]"]');
        
        selects.forEach(select => {
            const currentValue = select.value;
            const options = select.querySelectorAll('option');
            
            options.forEach(option => {
                if (option.value === '' || option.value === currentValue) {
                    option.style.display = '';
                } else {
                    // Hide if already selected in another row
                    option.style.display = selected.includes(option.value) && option.value !== currentValue ? 'none' : '';
                }
            });
        });
    }

    function addRow() {
        rowCount++;
        const tbody = document.getElementById('itemsTableBody');
        const row = document.createElement('tr');
        row.id = `row_${rowCount}`;
        row.innerHTML = `
            <td style="padding: 10px;">
                <select name="items[${rowCount}][process_id]" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <option value="">-- Select Process --</option>
                    ${processes.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                </select>
            </td>
            <td style="padding: 10px;">
                <select name="items[${rowCount}][raw_material_id]" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    onchange="updateRawMaterialOptions()">
                    <option value="">-- Select Raw Material --</option>
                    ${rawMaterials.map(rm => `<option value="${rm.id}">${rm.name}</option>`).join('')}
                </select>
            </td>
            <td style="padding: 10px;">
                <input type="number" name="items[${rowCount}][quantity]" step="0.0001" min="0.0001" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="0.0000">
            </td>
            <td style="padding: 10px;">
                <select name="items[${rowCount}][unit_id]" required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <option value="">-- Select UOM --</option>
                    ${units.map(unit => `<option value="${unit.id}">${unit.name} (${unit.symbol})</option>`).join('')}
                </select>
            </td>
            <td style="padding: 10px; text-align: center;">
                <button type="button" onclick="removeRow(${rowCount})" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    }

    function removeRow(rowId) {
        const row = document.getElementById(`row_${rowId}`);
        if (row) {
            row.remove();
            updateRawMaterialOptions();
        }
    }

    function clearForm() {
        document.getElementById('bomProcessForm').reset();
        document.getElementById('itemsTableBody').innerHTML = '';
        rowCount = 0;
    }

    function loadProducts() {
        // This function can be used to filter products based on selected process if needed
        // For now, all products are shown
    }

    // Add initial row on page load
    document.addEventListener('DOMContentLoaded', function() {
        addRow();
        // Update options after loading
        setTimeout(updateRawMaterialOptions, 100);
    });
</script>
@endpush
@endsection

