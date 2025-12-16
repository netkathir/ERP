@extends('layouts.dashboard')

@section('title', 'Material Inward - Edit')

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
        <h2 style="color:#333; font-size:24px; margin:0;">Edit Material Inward</h2>
        <a href="{{ route('material-inwards.index') }}" style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
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

    <form action="{{ route('material-inwards.update', $materialInward->id) }}" method="POST" id="materialInwardForm">
        @csrf
        @method('PUT')

        {{-- Header Fields --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Header Information</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px; align-items:flex-end;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Material Inward</label>
                    <input type="text" value="{{ $materialInward->material_inward_no }}" readonly
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Purchase Order No <span style="color:red;">*</span></label>
                    <select name="purchase_order_id" id="purchase_order_id" required
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        <option value="">Select Purchase Order</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}" {{ old('purchase_order_id', $materialInward->purchase_order_id) == $po->id ? 'selected' : '' }}>
                                {{ $po->po_no }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Supplier Name</label>
                    <input type="text" id="supplier_name" value="{{ old('supplier_name', optional($materialInward->supplier)->supplier_name) }}" readonly
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;">
                </div>
            </div>
        </div>

        {{-- Item Details Table --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Item Details</h3>
            </div>
            <div style="padding:20px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;" id="itemsTable">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:10px; text-align:left; color:#333;">Item Name <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:right; color:#333;">PO Qty</th>
                            <th style="padding:10px; text-align:right; color:#333;">Pending Qty</th>
                            <th style="padding:10px; text-align:right; color:#333;">Received Qty <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333;">Unit</th>
                            <th style="padding:10px; text-align:right; color:#333;">Received Qty in Kg <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333;">Batch No</th>
                            <th style="padding:10px; text-align:right; color:#333;">Cost/unit</th>
                            <th style="padding:10px; text-align:right; color:#333;">Total</th>
                            <th style="padding:10px; text-align:left; color:#333;">Supplier Invoice No <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:left; color:#333;">Invoice date <span style="color:red;">*</span></th>
                            <th style="padding:10px; text-align:center; color:#333; width:80px;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        @php $oldItems = old('items', $materialInward->items); @endphp
                        @foreach($oldItems as $index => $item)
                            @php
                                $arrayItem = is_array($item) ? $item : $item->toArray();
                            @endphp
                            <tr data-index="{{ $index }}">
                                <td style="padding:6px 8px;">
                                    <select name="items[{{ $index }}][purchase_order_item_id]" class="purchase-order-item-id" required
                                            style="width:200px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                                        <option value="">Select Item</option>
                                    </select>
                                    <input type="hidden" name="items[{{ $index }}][raw_material_id]" class="raw-material-id" value="{{ $arrayItem['raw_material_id'] ?? '' }}">
                                    <input type="hidden" name="items[{{ $index }}][item_description]" class="item-description" value="{{ $arrayItem['item_description'] ?? '' }}">
                                </td>
                                <td style="padding:6px 8px; text-align:right;">
                                    <span class="po-qty">{{ $arrayItem['po_qty'] ?? 0 }}</span>
                                    <input type="hidden" name="items[{{ $index }}][po_qty]" value="{{ $arrayItem['po_qty'] ?? 0 }}">
                                </td>
                                <td style="padding:6px 8px; text-align:right;">
                                    <span class="pending-qty">{{ $arrayItem['pending_qty'] ?? 0 }}</span>
                                    <input type="hidden" name="items[{{ $index }}][pending_qty]" value="{{ $arrayItem['pending_qty'] ?? 0 }}">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="number" step="0.001" min="0.001" name="items[{{ $index }}][received_qty]" class="received-qty" required
                                           value="{{ $arrayItem['received_qty'] ?? '' }}"
                                           style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <span class="unit-symbol">{{ optional($item->unit ?? null)->symbol ?? '' }}</span>
                                    <input type="hidden" name="items[{{ $index }}][unit_id]" value="{{ $arrayItem['unit_id'] ?? '' }}">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="number" step="0.001" min="0" name="items[{{ $index }}][received_qty_in_kg]" class="received-qty-in-kg" required
                                           value="{{ $arrayItem['received_qty_in_kg'] ?? '' }}"
                                           style="width:120px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="text" name="items[{{ $index }}][batch_no]" class="batch-no"
                                           value="{{ $arrayItem['batch_no'] ?? '' }}"
                                           style="width:120px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="number" step="0.01" min="0" name="items[{{ $index }}][cost_per_unit]" class="cost-per-unit" required
                                           value="{{ $arrayItem['cost_per_unit'] ?? 0 }}"
                                           style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right;">
                                </td>
                                <td style="padding:6px 8px; text-align:right;">
                                    <input type="text" name="items[{{ $index }}][total]" class="total" readonly
                                           value="{{ number_format($arrayItem['total'] ?? 0, 2) }}"
                                           style="width:100px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px; text-align:right; background:#f8f9fa;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="text" name="items[{{ $index }}][supplier_invoice_no]" class="supplier-invoice-no" required
                                           value="{{ $arrayItem['supplier_invoice_no'] ?? '' }}"
                                           style="width:150px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                                </td>
                                <td style="padding:6px 8px;">
                                    <input type="text" name="items[{{ $index }}][invoice_date]" class="date-input invoice-date" placeholder="DD-MM-YYYY" inputmode="numeric" pattern="\\d{2}-\\d{2}-\\d{4}" required
                                           value="{{ $displayDate($arrayItem['invoice_date'] ?? now()->format('Y-m-d')) }}"
                                           style="width:120px; padding:6px; border:1px solid #ddd; border-radius:4px; font-size:13px;">
                                </td>
                                <td style="padding:6px 8px; text-align:center;">
                                    <button type="button" class="btn-add-row" style="padding:4px 8px; border:none; border-radius:4px; background:#28a745; color:white; cursor:pointer;">+</button>
                                    <button type="button" class="btn-remove-row" style="padding:4px 8px; border:none; border-radius:4px; background:#dc3545; color:white; cursor:pointer;">-</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Remarks --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Remarks</h3>
            </div>
            <div style="padding:20px;">
                <textarea name="remarks" rows="3"
                          style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('remarks', $materialInward->remarks) }}</textarea>
            </div>
        </div>

        <div style="display:flex; gap:15px; margin-top:10px;">
            <button type="submit" style="padding:12px 24px; background:#667eea; color:white; border:none; border-radius:5px; font-weight:500; cursor:pointer;">
                Update
            </button>
            <a href="{{ route('material-inwards.index') }}" style="padding:12px 24px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500;">
                Cancel
            </a>
        </div>
    </form>
</div>

@include('store.material_inwards.partials.scripts')
@endsection

