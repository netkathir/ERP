@extends('layouts.dashboard')

@section('title', 'Subcontractor Evaluation - Edit')

@section('content')
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Edit Subcontractor Evaluation</h2>
        <a href="{{ route('subcontractor-evaluations.index') }}" style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-arrow-left"></i> Back to List
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

    <form action="{{ route('subcontractor-evaluations.update', $evaluation->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Subcontractor Information --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Subcontractor Information</h3>
            </div>
            <div style="padding:20px;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Subcontractor Name <span style="color:red;">*</span></label>
                        <select name="supplier_id" id="supplier_id" required
                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                            <option value="">Select Subcontractor</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id', $evaluation->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Contact Person <span style="color:red;">*</span></label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $evaluation->contact_person) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;" readonly required>
                    </div>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 1 <span style="color:red;">*</span></label>
                    <input type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1', $evaluation->address_line_1) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;" readonly required>
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 2</label>
                    <input type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2', $evaluation->address_line_2) }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;" readonly>
                </div>
                <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:15px;">
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">City <span style="color:red;">*</span></label>
                        <input type="text" name="city" id="city" value="{{ old('city', $evaluation->city) }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;" readonly required>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">State <span style="color:red;">*</span></label>
                        <select name="state" id="state" required
                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;" readonly>
                            <option value="">Select State</option>
                            @foreach($states as $stateOption)
                                <option value="{{ $stateOption }}" {{ old('state', $evaluation->state) == $stateOption ? 'selected' : '' }}>{{ $stateOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-top:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Remarks</label>
                    <textarea name="subcontractor_remarks" id="subcontractor_remarks" rows="2"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical; background:#f8f9fa;" readonly>{{ old('subcontractor_remarks', $evaluation->subcontractor_remarks) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Evaluation Criteria (same layout as create, but with values) --}}
        @php
            $ratings = [
                ['field' => 'facilities_capacity', 'label' => 'Subcontractor Facilities and Capacity Assessment', 'max' => 25],
                ['field' => 'manufacturing_facility', 'label' => 'Manufacturing Facility', 'max' => 5],
                ['field' => 'business_volume', 'label' => 'Business Volume and Financial Stability', 'max' => 5],
                ['field' => 'financial_stability', 'label' => 'Financial Stability and Supply Chain', 'max' => 5],
                ['field' => 'decision_making', 'label' => 'Decision-Making Process', 'max' => 5],
                ['field' => 'complexity', 'label' => 'Purchased Product or Service Complexity', 'max' => 5],
                ['field' => 'tech_competency', 'label' => 'Technology, Competency & Customer Service', 'max' => 35],
                ['field' => 'subcontractor_technology', 'label' => 'Subcontractor Technology', 'max' => 5],
                ['field' => 'resources_availability', 'label' => 'Current Resources Availability', 'max' => 5],
                ['field' => 'process_design', 'label' => 'Process Design Capabilities', 'max' => 5],
                ['field' => 'manufacturing_capability', 'label' => 'Manufacturing Capability', 'max' => 5],
                ['field' => 'change_management', 'label' => 'Change Management Process', 'max' => 5],
                ['field' => 'disaster_preparedness', 'label' => 'Disaster Preparedness and Contingency Plans', 'max' => 5],
                ['field' => 'equipment_monitoring', 'label' => 'Equipment Monitoring', 'max' => 5],
                ['field' => 'working_environment', 'label' => 'Working Environment', 'max' => 5],
                ['field' => 'product_testing', 'label' => 'Product Testing Facility', 'max' => 5],
                ['field' => 'storage_handling', 'label' => 'Storage and Handling', 'max' => 5],
                ['field' => 'transportation', 'label' => 'Transportation Facility', 'max' => 5],
                ['field' => 'statutory_regulatory', 'label' => 'Statutory and Regulatory Requirements', 'max' => 10],
                ['field' => 'qms_risk', 'label' => 'QMS and Risk Management', 'max' => 10],
            ];
        @endphp

        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Evaluation Criteria</h3>
            </div>
            <div style="padding:20px; overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:10px; text-align:left; color:#333;">Criterion</th>
                            <th style="padding:10px; text-align:center; color:#333; width:80px;">Max</th>
                            <th style="padding:10px; text-align:center; color:#333; width:120px;">Actual Rating</th>
                            <th style="padding:10px; text-align:left; color:#333;">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ratings as $row)
                            @php
                                $ratingField = $row['field'] . '_rating';
                                $remarksField = $row['field'] . '_remarks';
                            @endphp
                            <tr style="border-bottom:1px solid #eee;">
                                <td style="padding:8px 10px;">{{ $row['label'] }}</td>
                                <td style="padding:8px 10px; text-align:center;">{{ $row['max'] }}</td>
                                <td style="padding:8px 10px; text-align:center;">
                                    <input type="number" name="{{ $ratingField }}"
                                           value="{{ old($ratingField, $evaluation->$ratingField) }}"
                                           min="0" max="{{ $row['max'] }}" data-max="{{ $row['max'] }}" data-criterion="{{ $row['label'] }}"
                                           class="rating-input"
                                           style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                                </td>
                                <td style="padding:8px 10px;">
                                    <input type="text" name="{{ $remarksField }}"
                                           value="{{ old($remarksField, $evaluation->$remarksField) }}"
                                           style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="margin-top:15px; display:flex; gap:20px; align-items:center;">
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Total Score</label>
                        <input type="text" id="total_score_display" value="{{ $evaluation->total_score }}" readonly
                               style="width:120px; padding:8px; border:1px solid #ddd; border-radius:5px; font-size:14px; text-align:right; background:#f8f9fa;">
                    </div>
                    <small style="color:#666;">Total will be recalculated automatically when the form is submitted; this value is for quick reference only.</small>
                </div>
            </div>
        </div>

        {{-- Final Assessment --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Final Assessment</h3>
            </div>
            <div style="padding:20px; display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Assessment Result <span style="color:red;">*</span></label>
                    <select name="assessment_result" required
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        @foreach($assessmentResults as $result)
                            <option value="{{ $result }}" {{ old('assessment_result', $evaluation->assessment_result) == $result ? 'selected' : '' }}>{{ $result }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Status <span style="color:red;">*</span></label>
                    <select name="status" required
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        @foreach($statusOptions as $status)
                            <option value="{{ $status }}" {{ old('status', $evaluation->status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:1 / span 2;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Final Remarks</label>
                    <textarea name="final_remarks" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('final_remarks', $evaluation->final_remarks) }}</textarea>
                </div>
            </div>
        </div>

        <div style="display:flex; gap:15px; margin-top:10px;">
            <button type="submit" style="padding:12px 24px; background:#667eea; color:white; border:none; border-radius:5px; font-weight:500; cursor:pointer;">
                Update Evaluation
            </button>
            <a href="{{ route('subcontractor-evaluations.index') }}" style="padding:12px 24px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500;">
                Cancel
            </a>
        </div>
    </form>
</div>

@include('suppliers.subcontractor_evaluations.partials.scripts')
@endsection


