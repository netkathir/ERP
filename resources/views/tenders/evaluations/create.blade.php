@extends('layouts.dashboard')

@section('title', 'Tender Evaluation - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Tender Evaluation</h2>
        <a href="{{ route('tenders.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> List
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

    <form action="{{ route('tender-evaluations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- General Information -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">General Information</h3>
            </div>
            <div style="padding: 20px;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Tender No <span style="color: red;">*</span></label>
                        <select name="tender_id" id="tender_id" onchange="onTenderChange(this.value)" required
                                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Tender</option>
                            @foreach($tenders as $tender)
                                <option value="{{ $tender->id }}" {{ old('tender_id') == $tender->id ? 'selected' : '' }}>
                                    {{ $tender->tender_no }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Closing Date &amp; Time</label>
                        <input type="datetime-local" id="closing_date_time" readonly
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Evaluation Document <span style="color: red;">*</span></label>
                        <input type="file" name="evaluation_document" accept=".pdf,.xls,.xlsx,.doc,.docx" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tender Items to Evaluate -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Tender Items to Evaluate</h3>
            </div>
            <div style="padding: 20px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 25%;">Title</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 45%;">Description</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600; width: 10%;">Quantity</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 10%;">Unit</th>
                        </tr>
                    </thead>
                    <tbody id="evaluationItems">
                        <tr>
                            <td colspan="4" style="padding: 12px; text-align: center; color: #777;">
                                Select a Tender to view its items.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Submit Section -->
        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px;">
            <a href="{{ route('tender-evaluations.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                <i class="fas fa-list"></i> List
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-save"></i> Submit
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const tendersData = @json($evaluationsData);

    function onTenderChange(tenderId) {
        const closingField = document.getElementById('closing_date_time');
        const tbody = document.getElementById('evaluationItems');

        // Clear rows
        tbody.innerHTML = '';
        closingField.value = '';

        if (!tenderId || !tendersData[tenderId]) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" style="padding: 12px; text-align: center; color: #777;">
                        Select a Tender to view its items.
                    </td>
                </tr>
            `;
            return;
        }

        const t = tendersData[tenderId];
        if (t.closing_date_time) {
            closingField.value = t.closing_date_time;
        }

        if (!t.items || t.items.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" style="padding: 12px; text-align: center; color: #777;">
                        No items found for this tender.
                    </td>
                </tr>
            `;
            return;
        }

        t.items.forEach((item) => {
            const row = document.createElement('tr');
            row.style.borderBottom = '1px solid #dee2e6';
            row.innerHTML = `
                <td style="padding: 10px; color: #333;">${item.title || ''}</td>
                <td style="padding: 10px; color: #555; white-space: pre-wrap;">${item.description || ''}</td>
                <td style="padding: 10px; text-align: right; color: #333;">${item.qty || ''}</td>
                <td style="padding: 10px; color: #333;">${item.unit || ''}</td>
            `;
            tbody.appendChild(row);
        });
    }

    // If there was a previously selected tender (on validation error), restore its items
    document.addEventListener('DOMContentLoaded', function () {
        const initialTenderId = document.getElementById('tender_id').value;
        if (initialTenderId) {
            onTenderChange(initialTenderId);
        }
    });
</script>
@endpush
@endsection
