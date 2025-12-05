@extends('layouts.dashboard')

@section('title', 'Edit Raw Material - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 900px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Raw Material Master</h2>
        <a href="{{ route('raw-materials.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
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

    <form action="{{ route('raw-materials.update', $rawMaterial->id) }}" method="POST" id="rawMaterialForm">
        @csrf
        @method('PUT')
        <div style="margin-bottom: 20px;">
            <label for="raw_material_category_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Raw Material Category <span style="color: red;">*</span></label>
            <select name="raw_material_category_id" id="raw_material_category_id" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: white;">
                <option value="">Select</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('raw_material_category_id', $rawMaterial->raw_material_category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('raw_material_category_id')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="raw_material_sub_category_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Raw Material SubCategory</label>
            <select name="raw_material_sub_category_id" id="raw_material_sub_category_id"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: white;">
                <option value="">Select</option>
                @foreach($subCategories as $subCategory)
                    <option value="{{ $subCategory->id }}" {{ old('raw_material_sub_category_id', $rawMaterial->raw_material_sub_category_id) == $subCategory->id ? 'selected' : '' }}>
                        {{ $subCategory->name }}
                    </option>
                @endforeach
            </select>
            @error('raw_material_sub_category_id')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Raw Material <span style="color: red;">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $rawMaterial->name) }}" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="e.g., Copper Sheet, Aluminum Bar">
            @error('name')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="grade" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Grade</label>
            <input type="text" name="grade" id="grade" value="{{ old('grade', $rawMaterial->grade) }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="e.g., A, B">
            @error('grade')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="thickness" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Thickness</label>
            <input type="text" name="thickness" id="thickness" value="{{ old('thickness', $rawMaterial->thickness) }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="e.g., 5mm, 10mm">
            @error('thickness')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="batch_required" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Batch Required <span style="color: red;">*</span></label>
            <select name="batch_required" id="batch_required" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: white;">
                <option value="No" {{ old('batch_required', $rawMaterial->batch_required) == 'No' ? 'selected' : '' }}>No</option>
                <option value="Yes" {{ old('batch_required', $rawMaterial->batch_required) == 'Yes' ? 'selected' : '' }}>Yes</option>
            </select>
            @error('batch_required')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="qc_applicable" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">QC Applicable <span style="color: red;">*</span></label>
            <select name="qc_applicable" id="qc_applicable" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: white;">
                <option value="No" {{ old('qc_applicable', $rawMaterial->qc_applicable) == 'No' ? 'selected' : '' }}>No</option>
                <option value="Yes" {{ old('qc_applicable', $rawMaterial->qc_applicable) == 'Yes' ? 'selected' : '' }}>Yes</option>
            </select>
            @error('qc_applicable')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="test_certificate_applicable" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Test Certificate Applicable <span style="color: red;">*</span></label>
            <select name="test_certificate_applicable" id="test_certificate_applicable" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: white;">
                <option value="No" {{ old('test_certificate_applicable', $rawMaterial->test_certificate_applicable) == 'No' ? 'selected' : '' }}>No</option>
                <option value="Yes" {{ old('test_certificate_applicable', $rawMaterial->test_certificate_applicable) == 'Yes' ? 'selected' : '' }}>Yes</option>
            </select>
            @error('test_certificate_applicable')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="unit_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">UOM <span style="color: red;">*</span></label>
            <select name="unit_id" id="unit_id" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: white;">
                <option value="">Select</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ old('unit_id', $rawMaterial->unit_id) == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }} ({{ $unit->symbol }})
                    </option>
                @endforeach
            </select>
            @error('unit_id')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="sop" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">SOP</label>
            <textarea name="sop" id="sop" rows="4"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; resize: vertical;"
                placeholder="Enter SOP for handling the raw material">{{ old('sop', $rawMaterial->sop) }}</textarea>
            @error('sop')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <button type="button" onclick="clearForm()" style="padding: 12px 24px; background: #6c757d; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-eraser"></i> Clear
            </button>
            <a href="{{ route('raw-materials.index') }}" style="padding: 12px 24px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-list"></i> List
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                <i class="fas fa-save"></i> Update
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function loadSubCategories(categoryId, preserveValue = null) {
        const subCategorySelect = document.getElementById('raw_material_sub_category_id');
        
        if (!categoryId) {
            subCategorySelect.innerHTML = '<option value="">Select Raw Material Category first</option>';
            return;
        }

        // Show loading state
        subCategorySelect.innerHTML = '<option value="">Loading...</option>';
        subCategorySelect.disabled = true;

        // Fetch subcategories via AJAX
        const url = `{{ route('raw-materials.sub-categories') }}?category_id=${categoryId}`;
        console.log('Fetching subcategories from:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.json().then(err => {
                    console.error('Error response:', err);
                    throw new Error(err.error || `HTTP error! status: ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Subcategories data:', data);
            subCategorySelect.innerHTML = '<option value="">Select</option>';
            // Check if data is an array
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(subCategory => {
                    const option = document.createElement('option');
                    option.value = subCategory.id;
                    option.textContent = subCategory.name || 'N/A';
                    // Preserve the selected value if provided
                    if (preserveValue && subCategory.id == preserveValue) {
                        option.selected = true;
                    }
                    subCategorySelect.appendChild(option);
                });
            } else if (data && data.error) {
                // Handle error response
                subCategorySelect.innerHTML = '<option value="">Error: ' + data.error + '</option>';
            } else {
                subCategorySelect.innerHTML = '<option value="">No subcategories found</option>';
            }
            subCategorySelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading subcategories:', error);
            subCategorySelect.innerHTML = '<option value="">Error loading subcategories. Please try again.</option>';
            subCategorySelect.disabled = false;
        });
    }

    // Load subcategories when category changes
    document.getElementById('raw_material_category_id').addEventListener('change', function() {
        const currentSubCategoryValue = document.getElementById('raw_material_sub_category_id').value;
        loadSubCategories(this.value, currentSubCategoryValue);
    });

    // Load subcategories on page load if category is already selected
    document.addEventListener('DOMContentLoaded', function() {
        const categorySelect = document.getElementById('raw_material_category_id');
        const subCategorySelect = document.getElementById('raw_material_sub_category_id');
        
        if (categorySelect && categorySelect.value) {
            const selectedCategoryId = categorySelect.value;
            const currentSubCategoryValue = subCategorySelect.value;
            loadSubCategories(selectedCategoryId, currentSubCategoryValue);
        }
    });

    function clearForm() {
        document.getElementById('rawMaterialForm').reset();
        // Restore original values
        document.getElementById('raw_material_category_id').value = '{{ $rawMaterial->raw_material_category_id }}';
        document.getElementById('raw_material_sub_category_id').value = '{{ $rawMaterial->raw_material_sub_category_id }}';
        document.getElementById('name').value = '{{ $rawMaterial->name }}';
        document.getElementById('grade').value = '{{ $rawMaterial->grade ?? '' }}';
        document.getElementById('thickness').value = '{{ $rawMaterial->thickness ?? '' }}';
        document.getElementById('batch_required').value = '{{ $rawMaterial->batch_required }}';
        document.getElementById('qc_applicable').value = '{{ $rawMaterial->qc_applicable }}';
        document.getElementById('test_certificate_applicable').value = '{{ $rawMaterial->test_certificate_applicable }}';
        document.getElementById('unit_id').value = '{{ $rawMaterial->unit_id }}';
        document.getElementById('sop').value = '{{ $rawMaterial->sop ?? '' }}';
        
        // Reload subcategories for the restored category
        const categoryId = document.getElementById('raw_material_category_id').value;
        const subCategoryId = document.getElementById('raw_material_sub_category_id').value;
        if (categoryId) {
            loadSubCategories(categoryId, subCategoryId);
        }
        
        // Clear any validation messages
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(msg => msg.remove());
    }
</script>
@endpush
@endsection

