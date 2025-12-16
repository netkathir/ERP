@extends('layouts.dashboard')

@section('title', 'Purchase Indent - Create')

@section('content')
@php
    $displayDate = function ($value) {
        if (empty($value)) {
            return '';
        }
        try {
            return \Carbon\Carbon::parse($value)->format('d-m-Y');
        } catch (\Exception $e) {
            return $value;
        }
    };
@endphp
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Create Purchase Indent</h2>
        <a href="{{ route('purchase-indents.index') }}" style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
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

    <form action="{{ route('purchase-indents.store') }}" method="POST" enctype="multipart/form-data" id="purchaseIndentForm">
        @csrf

        {{-- General Information --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">General Information</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:flex-end;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Purchase Indent No</label>
                    <input type="text" name="indent_no" value="{{ $indentNo }}" readonly
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Indent Date <span style="color:red;">*</span></label>
                    <input type="text" name="indent_date" id="indent_date" class="date-input" placeholder="DD-MM-YYYY" inputmode="numeric"
                           value="{{ $displayDate(old('indent_date', now()->format('Y-m-d'))) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;" required>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Created By</label>
                    <input type="text" value="{{ auth()->user()->name ?? '' }}" readonly
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Upload (XLS/XLSX)</label>
                    <input type="file" name="upload" accept=".xls,.xlsx"
                           style="width:100%; padding:8px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                    <small style="color:#666;">Attach supporting document (Excel template).</small>
                </div>
            </div>
        </div>

        {{-- Purchase Indent Details --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Purchase Indent Details</h3>
            </div>
            <div style="padding:20px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;" id="itemsTable">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:10px; text-align:left; color:#333;">Date</th>
                            <th style="padding:10px; text-align:left; color:#333;">Item <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333;">Item Description</th>
                            <th style="padding:10px; text-align:right; color:#333;">Qty <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333;">Unit</th>
                            <th style="padding:10px; text-align:left; color:#333;">Schedule Date <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333;">Special Instructions <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333;">Supplier <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:center; color:#333; width:80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $oldItems = old('items', [ [] ]); @endphp
                        @foreach($oldItems as $index => $oldItem)
                            <tr data-index="{{ $index }}">
                                <td style="padding:6px 8px;">
                                    <input type="text" name="items[{{ $index }}][created_date]" class="date-input table-date-input" placeholder="DD-MM-YYYY" inputmode="numeric"
                                           value="{{ $displayDate(old('items.'.$index.'.created_date', now()->format('Y-m-d'))) }}"
                                           style="width:140px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <select name="items[{{ $index }}][raw_material_id]" class="item-select" required
                                            style="width:180px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                                        <option value="">Select Item</option>
                                        @foreach($rawMaterials as $rm)
                                            <option value="{{ $rm->id }}" {{ old('items.'.$index.'.raw_material_id') == $rm->id ? 'selected' : '' }}>
                                                {{ $rm->name }} @if($rm->grade) - {{ $rm->grade }} @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="text" name="items[{{ $index }}][item_description]" class="item-description"
                                           value="{{ old('items.'.$index.'.item_description') }}"
                                           style="width:220px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="number" step="1" min="1" name="items[{{ $index }}][quantity]" required
                                           value="{{ old('items.'.$index.'.quantity', '') }}"
                                           style="width:90px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <select class="unit-select"
                                            style="width:120px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; background:#f8f9fa;"
                                            disabled>
                                        <option value="">Select Unit</option>
                                        @foreach($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('items.'.$index.'.unit_id') == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->symbol ?? $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="items[{{ $index }}][unit_id]" class="unit-hidden"
                                           value="{{ old('items.'.$index.'.unit_id') }}">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="text" name="items[{{ $index }}][schedule_date]" class="date-input table-date-input" placeholder="DD-MM-YYYY" inputmode="numeric" required
                                           value="{{ $displayDate(old('items.'.$index.'.schedule_date')) }}"
                                           style="width:140px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="text" name="items[{{ $index }}][special_instructions]" required
                                           value="{{ old('items.'.$index.'.special_instructions') }}"
                                           style="width:220px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <select name="items[{{ $index }}][supplier_id]" class="supplier-select" required
                                            style="width:180px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('items.'.$index.'.supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->supplier_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td style="padding:6px 8px; text-align:center;">
                                    <button type="button" class="btn-add-row" style="padding:4px 8px; border:none; border-radius:4px; background:#28a745; color:white; cursor:pointer;">+</button>
                                    <button type="button" class="btn-remove-row" style="padding:4px 8px; border:none; border-radius:4px; background:#dc3545; color:white; cursor:pointer;">-</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <small style="color:#666; display:block; margin-top:10px;">
                    Use <strong>+</strong> to add new item rows and <strong>-</strong> to remove rows.
                </small>
                <div style="margin-top:15px; padding-top:15px; border-top:1px solid #dee2e6; text-align:left;">
                    <strong style="color:#333; font-size:14px;">Total Items: <span id="totalItemsCount">0</span></strong>
                </div>
            </div>
        </div>

        {{-- Remarks --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Additional Remarks</h3>
            </div>
            <div style="padding:20px;">
                <textarea name="remarks" rows="3"
                          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('remarks') }}</textarea>
            </div>
        </div>

        <div style="display:flex; gap:15px; margin-top:10px;">
            <button type="submit" style="padding:12px 24px; background:#667eea; color:white; border:none; border-radius:5px; font-weight:500; cursor:pointer;">
                Submit
            </button>
            <a href="{{ route('purchase-indents.index') }}" style="padding:12px 24px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500;">
                Cancel
            </a>
        </div>
    </form>
</div>

<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@include('purchase.purchase_indents.partials.scripts')
@endsection


