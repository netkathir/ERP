@extends('layouts.dashboard')

@section('title', ($viewOnly ?? false) ? 'View Production Work Order - ERP System' : (($workOrder->id ? 'Edit' : 'Create') . ' Production Work Order - ERP System'))

@section('content')
@php
    $displayDate = function ($value) {
        if (empty($value)) return '';
        try { return \Carbon\Carbon::parse($value)->format('d-m-Y'); } catch (\Exception $e) { return $value; }
    };
    $isEdit = (bool) $workOrder->id;
    $viewOnly = $viewOnly ?? false;
    $dis = $viewOnly ? ' disabled' : '';
@endphp
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1); max-width:1200px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">{{ $viewOnly ? 'View' : ($isEdit ? 'Edit' : 'Create') }} Production Work Order</h2>
        <div style="display:flex; gap:10px;">
            @if($viewOnly)
            <a href="{{ route('work-orders.edit', $workOrder->id) }}" style="padding:10px 20px; background:#ffc107; color:#333; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;"><i class="fas fa-edit"></i> Edit</a>
            @endif
            <a href="{{ route('work-orders.index') }}" style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-list"></i> List
            </a>
        </div>
    </div>

    @if($errors->any())
        <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:5px; margin-bottom:20px;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin:10px 0 0 20px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ $isEdit ? route('work-orders.update', $workOrder->id) : route('work-orders.store') }}" method="POST" enctype="multipart/form-data" id="workOrderForm">
        @csrf
        @if($isEdit) @method('PUT') @endif

        {{-- Sales Details (Create only) --}}
        @if(!$isEdit)
        <div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; margin-bottom:20px; padding:20px;">
            <h3 style="margin:0 0 15px 0; font-size:16px; color:#333;">Sales Details</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div>
                    <label style="display:block; margin-bottom:6px; font-weight:500;">Sales Type <span style="color:red;">*</span></label>
                    <select name="sales_type" id="sales_type" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}>
                        <option value="Tender" {{ old('sales_type', $workOrder->sales_type) == 'Tender' ? 'selected' : '' }}>Tender</option>
                        <option value="Enquiry" {{ old('sales_type', $workOrder->sales_type) == 'Enquiry' ? 'selected' : '' }}>Enquiry</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; font-weight:500;">Customer Production Order No <span style="color:red;">*</span></label>
                    <select name="po_id" id="po_select" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}>
                        <option value="">Select PO</option>
                        @foreach($customerOrders as $co)
                            <option value="{{ $co->id }}" data-type="customer_order" data-sales-type="Tender" data-po="{{ $co->customer_po_no ?? $co->production_order_no ?? $co->order_no ?? '' }}" data-cust-po="{{ $co->customer_po_no ?? '' }}" {{ old('po_id') == $co->id && old('po_type') == 'customer_order' ? 'selected' : '' }}>{{ $co->customer_po_no ?? $co->production_order_no ?? $co->order_no ?? 'CO-'.$co->id }}</option>
                        @endforeach
                        @foreach($proformaInvoices as $pi)
                            <option value="{{ $pi->id }}" data-type="proforma_invoice" data-sales-type="Enquiry" data-po="{{ $pi->invoice_no ?? '' }}" data-cust-po="" {{ old('po_id') == $pi->id && old('po_type') == 'proforma_invoice' ? 'selected' : '' }}>{{ $pi->invoice_no ?? 'PI-'.$pi->id }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="po_type" id="po_type" value="{{ old('po_type', 'customer_order') }}">
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; font-weight:500;">Work Order No</label>
                    <input type="text" id="work_order_no_display" readonly value="{{ $workOrder->work_order_no ?? '' }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
                </div>
            </div>
        </div>
        @endif

        {{-- Existing Work Order --}}
        <div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; margin-bottom:20px; padding:20px;">
            <h3 style="margin:0 0 15px 0; font-size:16px; color:#333;">Existing Work Order (Reference)</h3>
            @if($isEdit)
                <div>
                    <label style="display:block; margin-bottom:6px; font-weight:500;">Existing Work Order No</label>
                    <input type="text" value="{{ optional($workOrder->existingWorkOrder)->work_order_no ?? '-- None --' }}" readonly style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; background:#e5e7eb;">
                </div>
            @else
                <div style="display:flex; gap:15px; align-items:flex-end;">
                    <div style="flex:1;">
                        <label style="display:block; margin-bottom:6px; font-weight:500;">Existing Work Order No</label>
                        <select name="existing_work_order_id" id="existing_wo" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}>
                            <option value="">-- None (New) --</option>
                            @foreach($existingWorkOrders as $ewo)
                                <option value="{{ $ewo->id }}" {{ old('existing_work_order_id', $workOrder->existing_work_order_id) == $ewo->id ? 'selected' : '' }}>{{ $ewo->work_order_no }}</option>
                            @endforeach
                        </select>
                    </div>
                    <a href="{{ route('work-orders.create') }}?from=" id="newFromExisting" style="padding:10px 20px; background:#667eea; color:white; text-decoration:none; border-radius:6px; display:none;">Create New from Selected</a>
                </div>
            @endif
        </div>

        {{-- Document Upload --}}
        <div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; margin-bottom:20px; padding:20px;">
            <h3 style="margin:0 0 15px 0; font-size:16px; color:#333;">Document Upload</h3>
            <div>
                <label style="display:block; margin-bottom:6px; font-weight:500;">Excel Template</label>
                @if(!$viewOnly)<input type="file" name="document" accept=".xls,.xlsx" style="padding:8px; border:1px solid #ddd; border-radius:6px;">@else<span style="color:#666;">—</span>@endif
                @if($isEdit && $workOrder->document_path)
                    <small style="color:#666;">Current: {{ basename($workOrder->document_path) }}</small>
                @endif
            </div>
        </div>

        {{-- Title & Worker Details --}}
        <div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; margin-bottom:20px; padding:20px;">
            <h3 style="margin:0 0 15px 0; font-size:16px; color:#333;">Title & Worker Details</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div style="grid-column:1/-1;">
                    <label style="display:block; margin-bottom:6px; font-weight:500;">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $workOrder->title) }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; font-weight:500;">Worker Type <span style="color:red;">*</span></label>
                    <select name="worker_type" id="worker_type" required style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}>
                        <option value="Employee" {{ old('worker_type', $workOrder->worker_type) == 'Employee' ? 'selected' : '' }}>Employee</option>
                        <option value="Sub-Contractor" {{ old('worker_type', $workOrder->worker_type) == 'Sub-Contractor' ? 'selected' : '' }}>Sub-Contractor</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; font-weight:500;">Work Name</label>
                    <select name="worker_id" id="worker_id" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}>
                        <option value="">Select</option>
                        @foreach($employees as $e)
                            <option value="{{ $e->id }}" data-type="Employee" {{ old('worker_id', $workOrder->worker_id) == $e->id && old('worker_type', $workOrder->worker_type) == 'Employee' ? 'selected' : '' }}>{{ $e->name }}</option>
                        @endforeach
                        @foreach($subcontractors as $s)
                            <option value="{{ $s->id }}" data-type="Sub-Contractor" {{ old('worker_id', $workOrder->worker_id) == $s->id && old('worker_type', $workOrder->worker_type) == 'Sub-Contractor' ? 'selected' : '' }}>{{ $s->supplier_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:1/-1;">
                    <label style="display:block; margin-bottom:6px; font-weight:500;">Product Name</label>
                    <input type="text" name="product_name" value="{{ old('product_name', $workOrder->product_name) }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}>
                </div>
            </div>
        </div>

        {{-- Quantity Selection (Radio) - Dynamic --}}
        <div id="qty-selection-section" style="background:#f1f5f9; border:1px solid #dee2e6; border-radius:8px; margin-bottom:20px; padding:20px; position:relative;">
            <span style="position:absolute; right:50px; top:15px; font-size:12px; color:#667eea; margin-right:46px;">(Based on the selection, the contents change)</span>
            @if(!$viewOnly)<button type="button" id="addQtyBlockBtn" style="display:none; position:absolute; right:15px; top:12px; width:36px; height:36px; background:#667eea; color:white; border:none; border-radius:6px; font-size:20px; line-height:1; cursor:pointer; padding:0; align-items:center; justify-content:center;" title="Add set block">+</button>@endif
            <h3 style="margin:0 0 15px 0; font-size:16px; color:#333;">Quantity Selection</h3>
            <div style="margin-bottom:15px;">
                <label style="margin-right:20px;"><input type="radio" name="quantity_type" value="Sets" {{ old('quantity_type', $workOrder->quantity_type) == 'Sets' ? 'checked' : '' }}{{ $dis }}> SETS</label>
                <label style="margin-right:20px;"><input type="radio" name="quantity_type" value="Sub Sets" {{ old('quantity_type', $workOrder->quantity_type) == 'Sub Sets' ? 'checked' : '' }}{{ $dis }}> SUB SETS</label>
                <label style="margin-right:20px;"><input type="radio" name="quantity_type" value="Nos" {{ old('quantity_type', $workOrder->quantity_type) == 'Nos' ? 'checked' : '' }}{{ $dis }}> NOS</label>
                <label><input type="radio" name="quantity_type" value="Others" {{ old('quantity_type', $workOrder->quantity_type) == 'Others' ? 'checked' : '' }}{{ $dis }}> OTHERS</label>
            </div>

            <div id="qty-fields-sets" class="qty-fields" style="display:none;">
                <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:15px; margin-bottom:15px;">
                    <div><label style="display:block; margin-bottom:4px;">No of sets</label><input type="number" step="1" min="0" name="no_of_sets" id="no_of_sets" value="{{ old('no_of_sets', $workOrder->no_of_sets) }}" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;"{{ $dis }}> <span>sets</span></div>
                    <div><label style="display:block; margin-bottom:4px;">Starting set no</label><input type="number" step="1" min="0" name="starting_set_no" id="starting_set_no" value="{{ old('starting_set_no', $workOrder->starting_set_no) }}" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;"{{ $dis }}></div>
                    <div><label style="display:block; margin-bottom:4px;">Ending set no</label><input type="text" id="ending_set_no_display" readonly style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px; background:#e5e7eb;"> <span style="font-size:12px;">(automatic)</span></div>
                </div>
            </div>
            <div id="qty-fields-subsets" class="qty-fields" style="display:none;">
                <div id="qty-blocks-subsets" class="qty-blocks-container" style="display:flex; flex-direction:column; gap:20px; align-items:stretch;">
                    <div class="qty-block-wrapper" style="display:flex; gap:10px; align-items:flex-start; width:100%;">
                        <span class="qty-remove-placeholder" style="width:36px; flex-shrink:0;"></span>
                        <div class="qty-block" data-type="subsets" data-block-index="0" style="flex:1; padding:15px; background:#fff; border:1px solid #e2e8f0; border-radius:8px;">
                            <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:15px; margin-bottom:0;">
                            <div><label style="display:block; margin-bottom:4px;">No of sets</label><input type="number" step="1" min="0" class="qty-no-of-sets" name="no_of_sets" value="{{ old('no_of_sets', $workOrder->no_of_sets) }}" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;"{{ $dis }}> sets</div>
                            <div><label style="display:block; margin-bottom:4px;">Starting set no</label><input type="number" step="1" min="0" class="qty-starting-set-no" name="starting_set_no" value="{{ old('starting_set_no', $workOrder->starting_set_no) }}" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;"{{ $dis }}></div>
                            <div><label style="display:block; margin-bottom:4px;">Ending set no</label><input type="text" class="qty-ending-display" readonly style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px; background:#e5e7eb;"> (automatic)</div>
                            <div><label style="display:block; margin-bottom:4px;">No of sub sets/set</label><input type="number" step="1" min="0" class="qty-sub-sets-per-set" name="no_of_sub_sets_per_set" value="{{ old('no_of_sub_sets_per_set', $workOrder->no_of_sub_sets_per_set) }}" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;"{{ $dis }}> sub sets</div>
                            <div><label style="display:block; margin-bottom:4px;">Total sub sets</label><input type="text" class="qty-total-display" readonly style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px; background:#e5e7eb;"> (automatic) sub sets</div>
                        </div>
                            <div class="block-reference-table-container" style="margin-top:12px;">
                                <label style="display:block; margin-bottom:6px; font-weight:500; color:#333;">Reference view table</label>
                                <p style="font-size:12px; color:#666; margin-bottom:6px;">(table creation based on data entry)</p>
                                <div style="overflow-x:auto;">
                                    <table class="block-reference-table" style="width:auto; border-collapse:collapse; border:1px solid #dee2e6;">
                                        <thead><tr class="block-ref-header-row" style="background:#f8f9fa; border-bottom:2px solid #dee2e6;"></tr></thead>
                                        <tbody class="block-ref-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="qty-fields-nos" class="qty-fields" style="display:none;">
                <div id="qty-blocks-nos" class="qty-blocks-container" style="display:flex; flex-direction:column; gap:20px; align-items:stretch;">
                    <div class="qty-block-wrapper" style="display:flex; gap:10px; align-items:flex-start; width:100%;">
                        <span class="qty-remove-placeholder" style="width:36px; flex-shrink:0;"></span>
                        <div class="qty-block" data-type="nos" data-block-index="0" style="flex:1; padding:15px; background:#fff; border:1px solid #e2e8f0; border-radius:8px;">
                            <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:15px; margin-bottom:0;">
                            <div><label style="display:block; margin-bottom:4px;">No of sets</label><input type="number" step="1" min="0" class="qty-no-of-sets" name="no_of_sets" value="{{ old('no_of_sets', $workOrder->no_of_sets) }}" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;"{{ $dis }}> sets</div>
                            <div><label style="display:block; margin-bottom:4px;">Starting set no</label><input type="number" step="1" min="0" class="qty-starting-set-no" name="starting_set_no" value="{{ old('starting_set_no', $workOrder->starting_set_no) }}" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;"{{ $dis }}></div>
                            <div><label style="display:block; margin-bottom:4px;">Ending set no</label><input type="text" class="qty-ending-display" readonly style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px; background:#e5e7eb;"> <span style="font-size:12px;">(automatic)</span></div>
                            <div><label style="display:block; margin-bottom:4px;">No of quantity / sets</label><input type="number" step="1" min="0" class="qty-quantity-per-set" name="quantity_per_set" value="{{ old('quantity_per_set', $workOrder->quantity_per_set) }}" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;"{{ $dis }}> nos</div>
                            <div><label style="display:block; margin-bottom:4px;">total No of Quantity nos</label><input type="text" class="qty-total-display" readonly style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px; background:#e5e7eb;"> <span style="font-size:12px;">(automatic)</span></div>
                        </div>
                            <div class="block-reference-table-container" style="margin-top:12px;">
                                <label style="display:block; margin-bottom:6px; font-weight:500; color:#333;">Reference view table</label>
                                <p style="font-size:12px; color:#666; margin-bottom:6px;">(table creation based on data entry)</p>
                                <div style="overflow-x:auto;">
                                    <table class="block-reference-table" style="width:auto; border-collapse:collapse; border:1px solid #dee2e6;">
                                        <thead><tr class="block-ref-header-row" style="background:#f8f9fa; border-bottom:2px solid #dee2e6;"></tr></thead>
                                        <tbody class="block-ref-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="qty-fields-others" class="qty-fields" style="display:none;">
                <div id="qty-blocks-others" class="qty-blocks-container" style="display:flex; flex-direction:column; gap:20px; align-items:stretch;">
                    <div class="qty-block-wrapper" style="display:flex; gap:10px; align-items:flex-start; width:100%;">
                        <span class="qty-remove-placeholder" style="width:36px; flex-shrink:0;"></span>
                        <div class="qty-block" data-type="others" data-block-index="0" style="flex:1; padding:15px; background:#fff; border:1px solid #e2e8f0; border-radius:8px;">
                            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:15px;">
                            <div><label style="display:block; margin-bottom:4px;">No of quantity</label><input type="number" step="1" min="0" class="qty-no-of-qty" name="no_of_quantity" value="{{ old('no_of_quantity', $workOrder->no_of_quantity) }}" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;"{{ $dis }}> nos</div>
                            <div><label style="display:block; margin-bottom:4px;">Starting quantity no</label><input type="number" step="1" min="0" class="qty-starting-qty-no" name="starting_quantity_no" value="{{ old('starting_quantity_no', $workOrder->starting_quantity_no) }}" style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px;"{{ $dis }}></div>
                            <div><label style="display:block; margin-bottom:4px;">Ending quantity no</label><input type="text" class="qty-ending-display" readonly style="width:100%; padding:8px; border:1px solid #d1d5db; border-radius:6px; background:#e5e7eb;"> (automatic)</div>
                        </div>
                            <div class="block-reference-table-container" style="margin-top:12px;">
                                <label style="display:block; margin-bottom:6px; font-weight:500; color:#333;">Reference view table</label>
                                <p style="font-size:12px; color:#666; margin-bottom:6px;">(table creation based on data entry)</p>
                                <div style="overflow-x:auto;">
                                    <table class="block-reference-table" style="width:auto; border-collapse:collapse; border:1px solid #dee2e6;">
                                        <thead><tr class="block-ref-header-row" style="background:#f8f9fa; border-bottom:2px solid #dee2e6;"></tr></thead>
                                        <tbody class="block-ref-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reference view table (Sets only - single block) --}}
            <div id="reference-view-table-container" style="margin-top:15px; display:none;">
                <label style="display:block; margin-bottom:8px; font-weight:500; color:#333;">Reference view table</label>
                <p style="font-size:12px; color:#666; margin-bottom:8px;">(table creation based on data entry)</p>
                <div style="overflow-x:auto;">
                    <table id="reference-view-table" style="width:auto; border-collapse:collapse; border:1px solid #dee2e6;">
                        <thead>
                            <tr id="reference-table-row" style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                                <th id="reference-table-header" style="padding:10px 12px; text-align:left; color:#333; font-weight:600; border:1px solid #dee2e6;">SET NO</th>
                            </tr>
                        </thead>
                        <tbody id="reference-table-body"></tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Additional Fields --}}
        <div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; margin-bottom:20px; padding:20px;">
            <h3 style="margin:0 0 15px 0; font-size:16px; color:#333;">Additional Fields</h3>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div><label style="display:block; margin-bottom:6px; font-weight:500;">Thickness</label><input type="text" name="thickness" value="{{ old('thickness', $workOrder->thickness) }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}></div>
                <div><label style="display:block; margin-bottom:6px; font-weight:500;">Color</label><input type="text" name="color" value="{{ old('color', $workOrder->color) }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}></div>
                <div><label style="display:block; margin-bottom:6px; font-weight:500;">Nature of work</label><input type="text" name="nature_of_work" value="{{ old('nature_of_work', $workOrder->nature_of_work) }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}></div>
                <div><label style="display:block; margin-bottom:6px; font-weight:500;">Batch no</label><input type="text" name="batch_no" value="{{ old('batch_no', $workOrder->batch_no) }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}></div>
                <div><label style="display:block; margin-bottom:6px; font-weight:500;">Drawing No</label><input type="text" name="drawing_no" value="{{ old('drawing_no', $workOrder->drawing_no) }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}></div>
                <div><label style="display:block; margin-bottom:6px; font-weight:500;">Completion date</label><input type="date" name="completion_date" value="{{ old('completion_date', optional($workOrder->completion_date)->format('Y-m-d')) }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}></div>
                <div><label style="display:block; margin-bottom:6px; font-weight:500;">Layup sequence</label><input type="text" name="layup_sequence" value="{{ old('layup_sequence', $workOrder->layup_sequence) }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}></div>
                <div><label style="display:block; margin-bottom:6px; font-weight:500;">Date</label><input type="date" name="work_order_date" value="{{ old('work_order_date', $workOrder->work_order_date ? $workOrder->work_order_date->format('Y-m-d') : now()->format('Y-m-d')) }}" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}></div>
            </div>
        </div>

        {{-- Raw Materials Table --}}
        <div style="background:#f8f9fa; border:1px solid #dee2e6; border-radius:8px; margin-bottom:20px; padding:20px;">
            <h3 style="margin:0 0 15px 0; font-size:16px; color:#333;">Raw Materials (Material Request)</h3>
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;" id="rawMaterialsTable">
                    <thead>
                        <tr style="background:#f1f5f9; border-bottom:2px solid #dee2e6;">
                            <th style="padding:10px; text-align:left;">Sl.No</th>
                            <th style="padding:10px; text-align:left;">Raw materials</th>
                            <th style="padding:10px; text-align:left;">Work order quantity</th>
                            <th style="padding:10px; text-align:left;">UOM</th>
                            <th style="padding:10px; width:60px;"></th>
                        </tr>
                    </thead>
                    <tbody id="rawMaterialsBody">
                        @php
                            $rmRows = old('raw_materials', $workOrder->rawMaterials->map(function ($rm) {
                                return [
                                    'raw_material_id' => $rm->raw_material_id,
                                    'work_order_quantity' => $rm->work_order_quantity,
                                ];
                            })->toArray() ?: [['raw_material_id' => '', 'work_order_quantity' => '']]);
                        @endphp
                        @foreach($rmRows as $idx => $row)
                        <tr data-rm-idx="{{ $idx }}">
                            <td style="padding:8px;">{{ $idx + 1 }}</td>
                            <td style="padding:8px;">
                                <select name="raw_materials[{{ $idx }}][raw_material_id]" class="rm-select" style="width:100%; min-width:180px; padding:8px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}>
                                    <option value="">Select</option>
                                    @foreach($rawMaterials as $rm)
                                        @php
                                            $unitSymbol = ($rm->unit && $rm->unit->symbol) ? $rm->unit->symbol : 'N/A';
                                        @endphp
                                        <option value="{{ $rm->id }}" data-unit="{{ $unitSymbol }}" data-unit-id="{{ $rm->unit_id ?? '' }}" {{ ($row['raw_material_id'] ?? '') == $rm->id ? 'selected' : '' }}>{{ $rm->name }} @if($rm->grade) - {{ $rm->grade }} @endif</option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="padding:8px;"><input type="number" step="0.01" min="0" name="raw_materials[{{ $idx }}][work_order_quantity]" value="{{ $row['work_order_quantity'] ?? '' }}" style="width:120px; padding:8px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}></td>
                            @php
                                $rmModel = $rawMaterials->firstWhere('id', $row['raw_material_id'] ?? 0);
                                $uomSymbol = ($rmModel && $rmModel->unit && $rmModel->unit->symbol) ? $rmModel->unit->symbol : 'N/A';
                            @endphp
                            <td style="padding:8px;"><span class="rm-uom">{{ $uomSymbol }}</span></td>
                            <td style="padding:8px;">@if(!$viewOnly)<button type="button" class="rm-remove" style="background:#dc3545; color:white; border:none; padding:6px 10px; border-radius:4px; cursor:pointer;"><i class="fas fa-trash"></i></button>@endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(!$viewOnly)<button type="button" id="addRmRow" style="margin-top:10px; padding:8px 16px; background:#28a745; color:white; border:none; border-radius:6px; cursor:pointer;"><i class="fas fa-plus"></i> Add Row</button>@endif
        </div>

        {{-- Remarks & Submit --}}
        <div style="margin-bottom:20px;">
            <label style="display:block; margin-bottom:6px; font-weight:500;">Remarks</label>
            <textarea name="remarks" rows="3" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:6px;"{{ $dis }}>{{ old('remarks', $workOrder->remarks) }}</textarea>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:15px;">
            <div>
                <label style="display:block; margin-bottom:4px; font-weight:500;">Created by</label>
                <input type="text" value="{{ $viewOnly ? (optional($workOrder->creator)->name ?? '') : (auth()->user()->name ?? '') }}" readonly style="padding:10px; border:1px solid #ddd; border-radius:6px; background:#f8f9fa;">
            </div>
            <div style="display:flex; gap:10px;">
                <a href="{{ route('work-orders.index') }}" style="padding:12px 24px; background:#6c757d; color:white; text-decoration:none; border-radius:6px; font-weight:500;">List</a>
                @if(!$viewOnly)<button type="submit" style="padding:12px 24px; background:#28a745; color:white; border:none; border-radius:6px; font-weight:500; cursor:pointer;">SUBMIT</button>@endif
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function() {
    const viewOnly = @json($viewOnly ?? false);
    const rawMaterialsData = @json($rawMaterialsData);
    const quantityBlocksEdit = @json(old('qty_blocks', $workOrder->quantity_blocks ?? []));
    const rmSelectHtml = `<option value="">Select</option>` + Object.entries(rawMaterialsData).map(([id, d]) =>
        `<option value="${id}" data-unit="${d.unit_symbol}" data-unit-id="${d.unit_id || ''}">${d.name || id}</option>`
    ).join('');

    function getVisibleBlocksContainer() {
        const t = document.querySelector('input[name="quantity_type"]:checked')?.value || 'Sets';
        const map = { 'Sub Sets': 'qty-blocks-subsets', 'Nos': 'qty-blocks-nos', 'Others': 'qty-blocks-others' };
        const id = map[t];
        return id ? document.getElementById(id) : null;
    }

    function getBlockContainerByType(type) {
        if (type === 'Sets') return document.getElementById('qty-fields-sets');
        const map = { 'Sub Sets': 'qty-blocks-subsets', 'Nos': 'qty-blocks-nos', 'Others': 'qty-blocks-others' };
        const id = map[type];
        return id ? document.getElementById(id) : null;
    }

    function clearQtyBlockByType(type) {
        if (type === 'Sets') {
            const noOfSets = document.getElementById('no_of_sets');
            const startingSetNo = document.getElementById('starting_set_no');
            const endingDisplay = document.getElementById('ending_set_no_display');
            if (noOfSets) noOfSets.value = '';
            if (startingSetNo) startingSetNo.value = '';
            if (endingDisplay) endingDisplay.value = '';
            return;
        }
        const container = getBlockContainerByType(type);
        if (!container) return;
        container.querySelectorAll('.qty-block-wrapper .qty-block').forEach(blk => {
            blk.querySelectorAll('input:not([readonly])').forEach(inp => {
                inp.value = '';
            });
            const ed = blk.querySelector('.qty-ending-display');
            const tot = blk.querySelector('.qty-total-display');
            if (ed) ed.value = '';
            if (tot) tot.value = '';
        });
    }

    const qtyBlockState = { 'Sets': null, 'Sub Sets': null, 'Nos': null, 'Others': null };

    function saveBlockValues(type) {
        if (type === 'Sets') {
            const noOfSets = document.getElementById('no_of_sets');
            const startingSetNo = document.getElementById('starting_set_no');
            if (!noOfSets && !startingSetNo) return null;
            return {
                no_of_sets: noOfSets ? noOfSets.value : '',
                starting_set_no: startingSetNo ? startingSetNo.value : ''
            };
        }
        const container = getBlockContainerByType(type);
        if (!container) return null;
        const blocks = [];
        container.querySelectorAll('.qty-block-wrapper .qty-block').forEach(blk => {
            const typeKey = blk.dataset.type;
            if (typeKey === 'subsets') {
                blocks.push({
                    no_of_sets: blk.querySelector('.qty-no-of-sets')?.value ?? '',
                    starting_set_no: blk.querySelector('.qty-starting-set-no')?.value ?? '',
                    no_of_sub_sets_per_set: blk.querySelector('.qty-sub-sets-per-set')?.value ?? ''
                });
            } else if (typeKey === 'nos') {
                blocks.push({
                    no_of_sets: blk.querySelector('.qty-no-of-sets')?.value ?? '',
                    starting_set_no: blk.querySelector('.qty-starting-set-no')?.value ?? '',
                    quantity_per_set: blk.querySelector('.qty-quantity-per-set')?.value ?? ''
                });
            } else if (typeKey === 'others') {
                blocks.push({
                    no_of_quantity: blk.querySelector('.qty-no-of-qty')?.value ?? '',
                    starting_quantity_no: blk.querySelector('.qty-starting-qty-no')?.value ?? ''
                });
            }
        });
        return blocks.length ? blocks : null;
    }

    function restoreBlockValues(type, data) {
        if (!data) {
            clearQtyBlockByType(type);
            return;
        }
        if (type === 'Sets') {
            const noOfSets = document.getElementById('no_of_sets');
            const startingSetNo = document.getElementById('starting_set_no');
            if (noOfSets) noOfSets.value = data.no_of_sets ?? '';
            if (startingSetNo) startingSetNo.value = data.starting_set_no ?? '';
            return;
        }
        const container = getBlockContainerByType(type);
        if (!container) return;
        const typeKey = type === 'Sub Sets' ? 'subsets' : (type === 'Nos' ? 'nos' : 'others');
        const blocks = Array.isArray(data) ? data : [data];
        let wrappers = container.querySelectorAll('.qty-block-wrapper');
        for (let i = 0; i < blocks.length; i++) {
            if (i >= wrappers.length) {
                addQtyBlock();
                wrappers = container.querySelectorAll('.qty-block-wrapper');
            }
            const blk = wrappers[i]?.querySelector('.qty-block');
            if (!blk) break;
            const blockData = blocks[i] || {};
            if (typeKey === 'subsets') {
                const no = blk.querySelector('.qty-no-of-sets');
                const start = blk.querySelector('.qty-starting-set-no');
                const sub = blk.querySelector('.qty-sub-sets-per-set');
                if (no) no.value = blockData.no_of_sets ?? '';
                if (start) start.value = blockData.starting_set_no ?? '';
                if (sub) sub.value = blockData.no_of_sub_sets_per_set ?? '';
            } else if (typeKey === 'nos') {
                const no = blk.querySelector('.qty-no-of-sets');
                const start = blk.querySelector('.qty-starting-set-no');
                const qty = blk.querySelector('.qty-quantity-per-set');
                if (no) no.value = blockData.no_of_sets ?? '';
                if (start) start.value = blockData.starting_set_no ?? '';
                if (qty) qty.value = blockData.quantity_per_set ?? '';
            } else if (typeKey === 'others') {
                const n = blk.querySelector('.qty-no-of-qty');
                const start = blk.querySelector('.qty-starting-qty-no');
                if (n) n.value = blockData.no_of_quantity ?? '';
                if (start) start.value = blockData.starting_quantity_no ?? '';
            }
        }
    }

    function syncQtyFields() {
        const t = document.querySelector('input[name="quantity_type"]:checked')?.value || 'Sets';
        const blockId = 'qty-fields-' + t.toLowerCase().replace(' ','');
        document.querySelectorAll('.qty-fields').forEach(el => {
            const visible = el.id === blockId;
            el.style.display = visible ? 'block' : 'none';
            el.querySelectorAll('input[name]').forEach(inp => inp.disabled = !visible);
        });

        const addBtn = document.getElementById('addQtyBlockBtn');
        if (addBtn) addBtn.style.display = ['Sub Sets','Nos','Others'].includes(t) ? 'flex' : 'none';

        if (t === 'Sets') {
            const no = parseInt(document.getElementById('no_of_sets')?.value || 0, 10);
            const start = parseInt(document.getElementById('starting_set_no')?.value || 0, 10);
            const ed = document.getElementById('ending_set_no_display');
            if (ed) ed.value = no && start ? (start + no - 1) : '';
        } else {
        const container = getVisibleBlocksContainer();
        if (container) {
            container.querySelectorAll('.qty-block-wrapper .qty-block').forEach(blk => {
                    const no = parseInt(blk.querySelector('.qty-no-of-sets, .qty-no-of-qty')?.value || 0, 10);
                    const start = parseInt(blk.querySelector('.qty-starting-set-no, .qty-starting-qty-no')?.value || 0, 10);
                    const ed = blk.querySelector('.qty-ending-display');
                    if (ed) ed.value = no && start ? (start + no - 1) : '';
                    const sub = parseFloat(blk.querySelector('.qty-sub-sets-per-set, .qty-quantity-per-set')?.value || 0, 10);
                    const tot = blk.querySelector('.qty-total-display');
                    if (tot) tot.value = no && sub ? (no * sub) : '';
                });
            }
        }
        updateReferenceTable();
    }

    function collectBlocksData() {
        const container = getVisibleBlocksContainer();
        if (!container) return null;
        const blocks = [];
        container.querySelectorAll('.qty-block-wrapper .qty-block').forEach(blk => {
            const type = blk.dataset.type;
            if (type === 'subsets') {
                const no = parseInt(blk.querySelector('.qty-no-of-sets')?.value || 0, 10);
                const start = parseInt(blk.querySelector('.qty-starting-set-no')?.value || 0, 10);
                const sub = parseFloat(blk.querySelector('.qty-sub-sets-per-set')?.value || 0, 10);
                blocks.push({ no, start, sub, qtyPerSet: sub });
            } else if (type === 'nos') {
                const no = parseInt(blk.querySelector('.qty-no-of-sets')?.value || 0, 10);
                const start = parseInt(blk.querySelector('.qty-starting-set-no')?.value || 0, 10);
                const qtyPerSet = parseInt(blk.querySelector('.qty-quantity-per-set')?.value || 0, 10);
                blocks.push({ no, start, sub: qtyPerSet, qtyPerSet });
            } else if (type === 'others') {
                const q = parseInt(blk.querySelector('.qty-no-of-qty')?.value || 0, 10);
                const start = parseInt(blk.querySelector('.qty-starting-qty-no')?.value || 0, 10);
                blocks.push({ no: q, start, sub: 0, qtyPerSet: 0 });
            }
        });
        return blocks;
    }

    const cellStyle = 'padding:10px 12px; text-align:center; color:#333; border:1px solid #dee2e6;';
    const headerCellStyle = 'padding:10px 12px; text-align:left; color:#333; font-weight:600; border:1px solid #dee2e6;';

    function renderBlockReferenceTable(blk, t) {
        const headerRow = blk.querySelector('.block-ref-header-row');
        const bodyEl = blk.querySelector('.block-ref-body');
        if (!headerRow) return;
        const headerLabel = t === 'Others' ? 'QUANTITY NO' : 'SET NO';
        const type = blk.dataset.type;
        let no = 0, start = 0, qtyPerSet = 0;
        if (type === 'subsets') {
            no = parseInt(blk.querySelector('.qty-no-of-sets')?.value || 0, 10);
            start = parseInt(blk.querySelector('.qty-starting-set-no')?.value || 0, 10);
            qtyPerSet = parseFloat(blk.querySelector('.qty-sub-sets-per-set')?.value || 0, 10);
        } else if (type === 'nos') {
            no = parseInt(blk.querySelector('.qty-no-of-sets')?.value || 0, 10);
            start = parseInt(blk.querySelector('.qty-starting-set-no')?.value || 0, 10);
            qtyPerSet = parseInt(blk.querySelector('.qty-quantity-per-set')?.value || 0, 10);
        } else if (type === 'others') {
            no = parseInt(blk.querySelector('.qty-no-of-qty')?.value || 0, 10);
            start = parseInt(blk.querySelector('.qty-starting-qty-no')?.value || 0, 10);
        }
        const end = no && start ? (start + no - 1) : 0;
        if (start > 0 && end >= start) {
            const cells = [];
            for (let i = start; i <= end; i++) cells.push('<td style="' + cellStyle + '">' + i + '</td>');
            headerRow.innerHTML = '<th style="' + headerCellStyle + '">' + headerLabel + '</th>' + cells.join('');
            if (bodyEl) {
                if (qtyPerSet > 0) {
                    const seq = Array.from({length: qtyPerSet}, (_, j) => j + 1).join(',');
                    const qtyCells = [];
                    for (let i = start; i <= end; i++) qtyCells.push('<td style="' + cellStyle + '">' + seq + '</td>');
                    bodyEl.innerHTML = '<tr style="background:#fff;"><th style="' + headerCellStyle + '">' + (t === 'Nos' ? 'QUANTITY' : 'SUB SET NO') + '</th>' + qtyCells.join('') + '</tr>';
                } else {
                    bodyEl.innerHTML = '';
                }
            }
        } else {
            headerRow.innerHTML = '<th style="' + headerCellStyle + '">' + headerLabel + '</th><td style="padding:10px 12px; color:#666; font-style:italic; border:1px solid #dee2e6;">Enter values above to generate</td>';
            if (bodyEl) bodyEl.innerHTML = '';
        }
    }

    function updateReferenceTable() {
        const sectionContainer = document.getElementById('reference-view-table-container');
        const rowEl = document.getElementById('reference-table-row');
        const bodyEl = document.getElementById('reference-table-body');
        const t = document.querySelector('input[name="quantity_type"]:checked')?.value || 'Sets';
        const blockId = 'qty-fields-' + t.toLowerCase().replace(' ','');
        const visibleBlock = document.getElementById(blockId);
        if (!visibleBlock || visibleBlock.style.display === 'none') {
            if (sectionContainer) sectionContainer.style.display = 'none';
            return;
        }

        if (t === 'Sets') {
            if (sectionContainer) sectionContainer.style.display = 'block';
            const headerLabel = 'SET NO';
            const no = parseInt(document.getElementById('no_of_sets')?.value || 0, 10);
            const start = parseInt(document.getElementById('starting_set_no')?.value || 0, 10);
            const end = no && start ? (start + no - 1) : 0;
            if (rowEl) {
                if (start > 0 && end >= start) {
                    const cells = [];
                    for (let i = start; i <= end; i++) cells.push('<td style="' + cellStyle + '">' + i + '</td>');
                    rowEl.innerHTML = '<th id="reference-table-header" style="' + headerCellStyle + '">' + headerLabel + '</th>' + cells.join('');
                    if (bodyEl) bodyEl.innerHTML = '';
                } else {
                    rowEl.innerHTML = '<th id="reference-table-header" style="' + headerCellStyle + '">' + headerLabel + '</th><td style="padding:10px 12px; color:#666; font-style:italic; border:1px solid #dee2e6;">Enter values above to generate</td>';
                    if (bodyEl) bodyEl.innerHTML = '';
                }
            }
            return;
        }

        if (sectionContainer) sectionContainer.style.display = 'none';
        const blocksContainer = getVisibleBlocksContainer();
        if (blocksContainer) {
            blocksContainer.querySelectorAll('.qty-block-wrapper .qty-block').forEach(blk => renderBlockReferenceTable(blk, t));
        }
    }

    const qtyRemoveBtnHtml = '<button type="button" class="qty-remove-block" style="width:36px;height:36px;background:#dc3545;color:white;border:none;border-radius:6px;font-size:18px;line-height:1;cursor:pointer;flex-shrink:0;padding:0;" title="Remove block">−</button>';

    function addQtyBlock() {
        const container = getVisibleBlocksContainer();
        if (!container) return;
        const firstWrapper = container.querySelector('.qty-block-wrapper');
        if (!firstWrapper) return;
        const firstBlock = firstWrapper.querySelector('.qty-block');
        if (!firstBlock) return;
        const idx = container.querySelectorAll('.qty-block-wrapper').length;
        const cloneWrapper = firstWrapper.cloneNode(true);
        const cloneBlock = cloneWrapper.querySelector('.qty-block');
        const placeholder = cloneWrapper.querySelector('.qty-remove-placeholder');
        if (placeholder && !viewOnly) {
            const removeBtn = document.createElement('div');
            removeBtn.innerHTML = qtyRemoveBtnHtml;
            placeholder.replaceWith(removeBtn.firstElementChild);
        }
        cloneBlock.dataset.blockIndex = idx;
        cloneBlock.querySelectorAll('input').forEach(inp => {
            if (inp.classList.contains('qty-ending-display') || inp.classList.contains('qty-total-display')) return;
            inp.value = '';
            if (inp.name === 'no_of_sets') inp.name = 'qty_blocks[' + idx + '][no_of_sets]';
            else if (inp.name === 'starting_set_no') inp.name = 'qty_blocks[' + idx + '][starting_set_no]';
            else if (inp.name === 'no_of_sub_sets_per_set') inp.name = 'qty_blocks[' + idx + '][no_of_sub_sets_per_set]';
            else if (inp.name === 'quantity_per_set') inp.name = 'qty_blocks[' + idx + '][quantity_per_set]';
            else if (inp.name === 'no_of_quantity') inp.name = 'qty_blocks[' + idx + '][no_of_quantity]';
            else if (inp.name === 'starting_quantity_no') inp.name = 'qty_blocks[' + idx + '][starting_quantity_no]';
        });
        container.appendChild(cloneWrapper);
        reindexQtyBlocks(container);
        syncQtyFields();
    }

    function removeQtyBlock(wrapperEl) {
        const container = getVisibleBlocksContainer();
        if (!container || !wrapperEl || !wrapperEl.parentNode) return;
        const wrappers = container.querySelectorAll('.qty-block-wrapper');
        if (wrappers.length <= 1) return;
        wrapperEl.remove();
        reindexQtyBlocks(container);
        syncQtyFields();
    }

    function getFieldFromInput(inp) {
        if (inp.classList.contains('qty-no-of-sets')) return 'no_of_sets';
        if (inp.classList.contains('qty-starting-set-no')) return 'starting_set_no';
        if (inp.classList.contains('qty-sub-sets-per-set')) return 'no_of_sub_sets_per_set';
        if (inp.classList.contains('qty-quantity-per-set')) return 'quantity_per_set';
        if (inp.classList.contains('qty-no-of-qty')) return 'no_of_quantity';
        if (inp.classList.contains('qty-starting-qty-no')) return 'starting_quantity_no';
        const m = inp.name.match(/qty_blocks\[\d+\]\[(\w+)\]/);
        return m ? m[1] : null;
    }

    function reindexQtyBlocks(container) {
        if (!container) return;
        container.querySelectorAll('.qty-block-wrapper .qty-block').forEach((blk, i) => {
            blk.dataset.blockIndex = i;
            blk.querySelectorAll('input').forEach(inp => {
                if (inp.classList.contains('qty-ending-display') || inp.classList.contains('qty-total-display')) return;
                const field = getFieldFromInput(inp);
                if (!field) return;
                inp.name = i === 0 ? field : 'qty_blocks[' + i + '][' + field + ']';
            });
        });
    }

    function syncRmSelects() {
        document.querySelectorAll('#rawMaterialsBody .rm-select').forEach((sel, i) => {
            sel.name = 'raw_materials[' + i + '][raw_material_id]';
            const next = sel.closest('tr').querySelector('input[name*="work_order_quantity"]');
            if (next) next.name = 'raw_materials[' + i + '][work_order_quantity]';
        });
        document.querySelectorAll('#rawMaterialsBody tr').forEach((tr, i) => {
            tr.querySelector('td:first-child').textContent = i + 1;
        });
    }

    let previousQtyType = document.querySelector('input[name="quantity_type"]:checked')?.value || 'Sets';
    document.querySelectorAll('input[name="quantity_type"]').forEach(r => r.addEventListener('change', function() {
        const newType = document.querySelector('input[name="quantity_type"]:checked')?.value || 'Sets';
        if (previousQtyType !== newType) {
            qtyBlockState[previousQtyType] = saveBlockValues(previousQtyType);
            restoreBlockValues(newType, qtyBlockState[newType] ?? null);
            previousQtyType = newType;
        }
        syncQtyFields();
    }));
    document.getElementById('no_of_sets')?.addEventListener('input', syncQtyFields);
    document.getElementById('starting_set_no')?.addEventListener('input', syncQtyFields);
    document.querySelectorAll('.qty-blocks-container').forEach(c => {
        c.addEventListener('input', function(e) {
            if (e.target.matches('input:not([readonly])')) syncQtyFields();
        });
        c.addEventListener('click', function(e) {
            const btn = e.target.closest('.qty-remove-block');
            if (btn) {
                const wrapper = btn.closest('.qty-block-wrapper');
                if (wrapper) removeQtyBlock(wrapper);
            }
        });
    });
    if (!viewOnly) document.getElementById('addQtyBlockBtn')?.addEventListener('click', addQtyBlock);
    syncQtyFields();
    qtyBlockState[previousQtyType] = saveBlockValues(previousQtyType);

    function loadQuantityBlocksFromEdit() {
        if (!Array.isArray(quantityBlocksEdit) || quantityBlocksEdit.length <= 1) return;
        const t = document.querySelector('input[name="quantity_type"]:checked')?.value;
        if (!['Sub Sets','Nos','Others'].includes(t)) return;
        const container = getVisibleBlocksContainer();
        if (!container) return;
        const firstWrapper = container.querySelector('.qty-block-wrapper');
        if (!firstWrapper) return;
        for (let i = 1; i < quantityBlocksEdit.length; i++) {
            const b = quantityBlocksEdit[i] || {};
            const cloneWrapper = firstWrapper.cloneNode(true);
            const cloneBlock = cloneWrapper.querySelector('.qty-block');
            if (!cloneBlock) return;
            const placeholder = cloneWrapper.querySelector('.qty-remove-placeholder');
            if (placeholder && !viewOnly) {
                const removeBtn = document.createElement('div');
                removeBtn.innerHTML = qtyRemoveBtnHtml;
                placeholder.replaceWith(removeBtn.firstElementChild);
            }
            cloneBlock.dataset.blockIndex = i;
            cloneBlock.querySelectorAll('input').forEach(inp => {
                if (inp.classList.contains('qty-ending-display') || inp.classList.contains('qty-total-display')) return;
                const field = getFieldFromInput(inp);
                if (field) {
                    inp.name = 'qty_blocks[' + i + '][' + field + ']';
                    if (b[field] !== undefined && b[field] !== null) inp.value = b[field];
                }
            });
            container.appendChild(cloneWrapper);
        }
        reindexQtyBlocks(container);
        syncQtyFields();
    }
    if (quantityBlocksEdit && quantityBlocksEdit.length > 1) {
        const t = document.querySelector('input[name="quantity_type"]:checked')?.value;
        if (['Sub Sets','Nos','Others'].includes(t)) {
            loadQuantityBlocksFromEdit();
            syncQtyFields();
            qtyBlockState[t] = saveBlockValues(t);
        }
    }

    if (!viewOnly) document.getElementById('addRmRow')?.addEventListener('click', function() {
        const tbody = document.getElementById('rawMaterialsBody');
        const idx = tbody.querySelectorAll('tr').length;
        const tr = document.createElement('tr');
        tr.dataset.rmIdx = idx;
        tr.innerHTML = '<td>' + (idx + 1) + '</td><td><select name="raw_materials[' + idx + '][raw_material_id]" class="rm-select" style="width:100%;min-width:180px;padding:8px;border:1px solid #ddd;border-radius:6px;">' + rmSelectHtml + '</select></td><td><input type="number" step="0.01" min="0" name="raw_materials[' + idx + '][work_order_quantity]" style="width:120px;padding:8px;border:1px solid #ddd;border-radius:6px;"></td><td><span class="rm-uom">N/A</span></td><td><button type="button" class="rm-remove" style="background:#dc3545;color:white;border:none;padding:6px 10px;border-radius:4px;cursor:pointer;"><i class="fas fa-trash"></i></button></td>';
        tbody.appendChild(tr);
        tr.querySelector('.rm-select').addEventListener('change', function() {
            const opt = this.selectedOptions[0];
            tr.querySelector('.rm-uom').textContent = opt?.dataset?.unit || 'N/A';
        });
        tr.querySelector('.rm-remove').addEventListener('click', function() {
            tr.remove();
            syncRmSelects();
        });
    });

    document.getElementById('rawMaterialsBody')?.addEventListener('change', function(e) {
        if (e.target.classList.contains('rm-select')) {
            const opt = e.target.selectedOptions[0];
            e.target.closest('tr').querySelector('.rm-uom').textContent = opt?.dataset?.unit || 'N/A';
        }
    });
    document.getElementById('rawMaterialsBody')?.addEventListener('click', function(e) {
        if (e.target.closest('.rm-remove')) {
            e.target.closest('tr').remove();
            syncRmSelects();
        }
    });

    document.getElementById('worker_type')?.addEventListener('change', function() {
        const type = this.value;
        const sel = document.getElementById('worker_id');
        Array.from(sel.options).forEach(o => {
            o.style.display = (o.value === '' || o.dataset.type === type) ? '' : 'none';
        });
        sel.value = '';
    });
    document.getElementById('worker_type')?.dispatchEvent(new Event('change'));

    function fetchNextWorkOrderNo() {
        const salesType = document.getElementById('sales_type')?.value || 'Tender';
        const poSelect = document.getElementById('po_select');
        const poType = document.getElementById('po_type');
        const display = document.getElementById('work_order_no_display');
        if (!poSelect || !display) return;
        const poId = poSelect.value;
        if (!poId) { display.value = ''; return; }
        const url = '{{ route("work-orders.next-no") }}?sales_type=' + encodeURIComponent(salesType) + '&po_type=' + encodeURIComponent(poType?.value || 'customer_order') + '&po_id=' + encodeURIComponent(poId);
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => { if (data.work_order_no) display.value = data.work_order_no; })
            .catch(() => {});
    }

    document.getElementById('po_select')?.addEventListener('change', function() {
        const opt = this.selectedOptions[0];
        if (opt) {
            document.getElementById('po_type').value = opt.dataset.type || 'customer_order';
        }
        fetchNextWorkOrderNo();
    });

    document.getElementById('sales_type')?.addEventListener('change', function() {
        fetchNextWorkOrderNo();
    });

    document.getElementById('existing_wo')?.addEventListener('change', function() {
        const v = this.value;
        const a = document.getElementById('newFromExisting');
        if (a) { a.style.display = v ? 'inline-block' : 'none'; a.href = v ? '{{ route("work-orders.create") }}?from=' + v : '#'; }
    });
    document.getElementById('existing_wo')?.dispatchEvent(new Event('change'));

    document.getElementById('sales_type')?.addEventListener('change', function() {
        const st = this.value;
        const sel = document.getElementById('po_select');
        Array.from(sel.options).forEach(o => {
            if (o.value === '') { o.style.display = ''; return; }
            o.style.display = (o.dataset.salesType || '') === st ? '' : 'none';
        });
        sel.value = '';
        fetchNextWorkOrderNo();
    });
    document.getElementById('sales_type')?.dispatchEvent(new Event('change'));
    fetchNextWorkOrderNo();
})();
</script>
@endpush
@endsection
