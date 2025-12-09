@extends('layouts.dashboard')

@section('title', 'Supplier Evaluation Details')

@section('content')
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Supplier Evaluation Details</h2>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('supplier-evaluations.edit', $evaluation->id) }}"
               style="padding:10px 20px; background:#28a745; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('supplier-evaluations.index') }}"
               style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    {{-- Supplier Info --}}
    <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
        <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
            <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Supplier Information</h3>
        </div>
        <div style="padding:20px;">
            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:20px;">
                <div>
                    <strong>Supplier Name:</strong>
                    <p style="margin:5px 0; color:#555;">{{ optional($evaluation->supplier)->supplier_name }}</p>
                </div>
                <div>
                    <strong>Contact Person:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $evaluation->contact_person }}</p>
                </div>
                <div>
                    <strong>Address Line 1:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $evaluation->address_line_1 }}</p>
                </div>
                <div>
                    <strong>Address Line 2:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $evaluation->address_line_2 }}</p>
                </div>
                <div>
                    <strong>City:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $evaluation->city }}</p>
                </div>
                <div>
                    <strong>State:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $evaluation->state }}</p>
                </div>
                <div>
                    <strong>Pincode:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $evaluation->pincode }}</p>
                </div>
                <div style="grid-column:1 / span 2;">
                    <strong>Remarks:</strong>
                    <p style="margin:5px 0; color:#555;">{{ $evaluation->supplier_remarks }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Evaluation Criteria --}}
    @php
        $rows = [
            ['field' => 'facilities_capacity', 'label' => 'Supplier Facilities and Capacity Assessment', 'max' => 25],
            ['field' => 'manufacturing_facility', 'label' => 'Manufacturing Facility', 'max' => 5],
            ['field' => 'business_volume', 'label' => 'Business Volume and Financial Stability', 'max' => 5],
            ['field' => 'financial_stability', 'label' => 'Financial Stability and Supply Chain', 'max' => 5],
            ['field' => 'decision_making', 'label' => 'Decision-Making Process', 'max' => 5],
            ['field' => 'complexity', 'label' => 'Purchased Product or Service Complexity', 'max' => 5],
            ['field' => 'tech_competency', 'label' => 'Technology, Competency & Customer Service', 'max' => 35],
            ['field' => 'supplier_technology', 'label' => 'Supplier Technology', 'max' => 5],
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
                    @foreach($rows as $row)
                        @php
                            $ratingField = $row['field'] . '_rating';
                            $remarksField = $row['field'] . '_remarks';
                        @endphp
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">{{ $row['label'] }}</td>
                            <td style="padding:8px 10px; text-align:center;">{{ $row['max'] }}</td>
                            <td style="padding:8px 10px; text-align:center;">{{ $evaluation->$ratingField }}</td>
                            <td style="padding:8px 10px;">{{ $evaluation->$remarksField }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top:15px;">
                <strong>Total Score:</strong>
                <span style="margin-left:8px; color:#333;">{{ $evaluation->total_score }}</span>
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
                <strong>Assessment Result:</strong>
                <p style="margin:5px 0; color:#555;">{{ $evaluation->assessment_result }}</p>
            </div>
            <div>
                <strong>Status:</strong>
                <p style="margin:5px 0; color:#555;">{{ $evaluation->status }}</p>
            </div>
            <div style="grid-column:1 / span 2;">
                <strong>Final Remarks:</strong>
                <p style="margin:5px 0; color:#555;">{{ $evaluation->final_remarks }}</p>
            </div>
        </div>
    </div>
</div>
@endsection


