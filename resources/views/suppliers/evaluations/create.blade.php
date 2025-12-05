@extends('layouts.dashboard')

@section('title', 'Supplier Evaluation - Create')

@section('content')
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Create Supplier Evaluation</h2>
        <a href="{{ route('supplier-evaluations.index') }}" style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
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

    <form action="{{ route('supplier-evaluations.store') }}" method="POST">
        @csrf

        {{-- Supplier Information --}}
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="background:#f8f9fa; padding:15px 20px; border-bottom:1px solid #dee2e6; border-radius:5px 5px 0 0;">
                <h3 style="margin:0; color:#667eea; font-size:18px; font-weight:600;">Supplier Information</h3>
            </div>
            <div style="padding:20px;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px; margin-bottom:15px;">
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Supplier Name <span style="color:red;">*</span></label>
                        <select name="supplier_id" id="supplier_id" required
                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Contact Person <span style="color:red;">*</span></label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;" required readonly>
                    </div>
                </div>

                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 1 <span style="color:red;">*</span></label>
                    <input type="text" name="address_line_1" id="address_line_1" value="{{ old('address_line_1') }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;" required readonly>
                </div>
                <div style="margin-bottom:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Address Line 2</label>
                    <input type="text" name="address_line_2" id="address_line_2" value="{{ old('address_line_2') }}"
                           style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;" readonly>
                </div>
                <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:15px;">
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">City <span style="color:red;">*</span></label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}"
                               style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;" required readonly>
                    </div>
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">State <span style="color:red;">*</span></label>
                        <select name="state" id="state" required
                                style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; background:#f8f9fa;" readonly>
                            <option value="">Select State</option>
                            @foreach($states as $stateOption)
                                <option value="{{ $stateOption }}" {{ old('state') == $stateOption ? 'selected' : '' }}>{{ $stateOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-top:15px;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Remarks</label>
                    <textarea name="supplier_remarks" id="supplier_remarks" rows="2"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;" readonly>{{ old('supplier_remarks') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Evaluation Criteria --}}
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
                        {{-- 2.1 Facilities and Capacity --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Supplier Facilities and Capacity Assessment</td>
                            <td style="padding:8px 10px; text-align:center;">25</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="facilities_capacity_rating" value="{{ old('facilities_capacity_rating', 0) }}"
                                       min="0" max="25" data-max="25" data-criterion="Supplier Facilities and Capacity Assessment"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="facilities_capacity_remarks" value="{{ old('facilities_capacity_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.2 Manufacturing Facility --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Manufacturing Facility</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="manufacturing_facility_rating" value="{{ old('manufacturing_facility_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Manufacturing Facility"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="manufacturing_facility_remarks" value="{{ old('manufacturing_facility_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.3 Business Volume --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Business Volume and Financial Stability</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="business_volume_rating" value="{{ old('business_volume_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Business Volume and Financial Stability"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="business_volume_remarks" value="{{ old('business_volume_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.4 Financial Stability & Supply Chain --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Financial Stability and Supply Chain</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="financial_stability_rating" value="{{ old('financial_stability_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Financial Stability and Supply Chain"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="financial_stability_remarks" value="{{ old('financial_stability_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.5 Decision Making --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Decision-Making Process</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="decision_making_rating" value="{{ old('decision_making_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Decision-Making Process"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="decision_making_remarks" value="{{ old('decision_making_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.6 Product / Service Complexity --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Purchased Product or Service Complexity</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="complexity_rating" value="{{ old('complexity_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Purchased Product or Service Complexity"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="complexity_remarks" value="{{ old('complexity_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.7 Tech, Competency & Customer Service (35) --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Technology, Competency &amp; Customer Service</td>
                            <td style="padding:8px 10px; text-align:center;">35</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="tech_competency_rating" value="{{ old('tech_competency_rating', 0) }}"
                                       min="0" max="35" data-max="35" data-criterion="Technology, Competency & Customer Service"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="tech_competency_remarks" value="{{ old('tech_competency_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.8 Supplier Technology --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Supplier Technology</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="supplier_technology_rating" value="{{ old('supplier_technology_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Supplier Technology"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="supplier_technology_remarks" value="{{ old('supplier_technology_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.9 Current Resources Availability --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Current Resources Availability</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="resources_availability_rating" value="{{ old('resources_availability_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Current Resources Availability"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="resources_availability_remarks" value="{{ old('resources_availability_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.10 Process Design Capabilities --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Process Design Capabilities</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="process_design_rating" value="{{ old('process_design_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Process Design Capabilities"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="process_design_remarks" value="{{ old('process_design_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.11 Manufacturing Capability --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Manufacturing Capability</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="manufacturing_capability_rating" value="{{ old('manufacturing_capability_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Manufacturing Capability"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="manufacturing_capability_remarks" value="{{ old('manufacturing_capability_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.12 Change Management --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Change Management Process</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="change_management_rating" value="{{ old('change_management_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Change Management Process"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="change_management_remarks" value="{{ old('change_management_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.13 Disaster Preparedness --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Disaster Preparedness and Contingency Plans</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="disaster_preparedness_rating" value="{{ old('disaster_preparedness_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Disaster Preparedness and Contingency Plans"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="disaster_preparedness_remarks" value="{{ old('disaster_preparedness_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.14 Equipment Monitoring --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Equipment Monitoring</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="equipment_monitoring_rating" value="{{ old('equipment_monitoring_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Equipment Monitoring"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="equipment_monitoring_remarks" value="{{ old('equipment_monitoring_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.15 Working Environment --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Working Environment</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="working_environment_rating" value="{{ old('working_environment_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Working Environment"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="working_environment_remarks" value="{{ old('working_environment_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.16 Product Testing Facility --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Product Testing Facility</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="product_testing_rating" value="{{ old('product_testing_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Product Testing Facility"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="product_testing_remarks" value="{{ old('product_testing_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.17 Storage and Handling --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Storage and Handling</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="storage_handling_rating" value="{{ old('storage_handling_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Storage and Handling"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="storage_handling_remarks" value="{{ old('storage_handling_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.18 Transportation Facility --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Transportation Facility</td>
                            <td style="padding:8px 10px; text-align:center;">5</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="transportation_rating" value="{{ old('transportation_rating', 0) }}"
                                       min="0" max="5" data-max="5" data-criterion="Transportation Facility"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="transportation_remarks" value="{{ old('transportation_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.19 Statutory & Regulatory --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">Statutory and Regulatory Requirements</td>
                            <td style="padding:8px 10px; text-align:center;">10</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="statutory_regulatory_rating" value="{{ old('statutory_regulatory_rating', 0) }}"
                                       min="0" max="10" data-max="10" data-criterion="Statutory and Regulatory Requirements"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="statutory_regulatory_remarks" value="{{ old('statutory_regulatory_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>

                        {{-- 2.20 QMS & Risk --}}
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:8px 10px;">QMS and Risk Management</td>
                            <td style="padding:8px 10px; text-align:center;">10</td>
                            <td style="padding:8px 10px; text-align:center;">
                                <input type="number" name="qms_risk_rating" value="{{ old('qms_risk_rating', 0) }}"
                                       min="0" max="10" data-max="10" data-criterion="QMS and Risk Management"
                                       class="rating-input"
                                       style="width:80px; padding:6px; border:1px solid #ddd; border-radius:4px; text-align:right;" required>
                            </td>
                            <td style="padding:8px 10px;">
                                <input type="text" name="qms_risk_remarks" value="{{ old('qms_risk_remarks') }}"
                                       style="width:100%; padding:6px; border:1px solid #ddd; border-radius:4px;">
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div style="margin-top:15px; display:flex; gap:20px; align-items:center;">
                    <div>
                        <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Total Score</label>
                        <input type="text" id="total_score_display" value="0" readonly
                               style="width:120px; padding:8px; border:1px solid #ddd; border-radius:5px; font-size:14px; text-align:right; background:#f8f9fa;">
                    </div>
                    <small style="color:#666;">Total will be calculated automatically when the form is submitted; this value is for quick reference only.</small>
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
                            <option value="{{ $result }}" {{ old('assessment_result') == $result ? 'selected' : '' }}>{{ $result }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Status <span style="color:red;">*</span></label>
                    <select name="status" required
                            style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px;">
                        @foreach($statusOptions as $status)
                            <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="grid-column:1 / span 2;">
                    <label style="display:block; margin-bottom:6px; color:#333; font-weight:500;">Final Remarks</label>
                    <textarea name="final_remarks" rows="3"
                              style="width:100%; padding:10px; border:1px solid #ddd; border-radius:5px; font-size:14px; resize:vertical;">{{ old('final_remarks') }}</textarea>
                </div>
            </div>
        </div>

        <div style="display:flex; gap:15px; margin-top:10px;">
            <button type="submit" style="padding:12px 24px; background:#667eea; color:white; border:none; border-radius:5px; font-weight:500; cursor:pointer;">
                Submit Evaluation
            </button>
            <a href="{{ route('supplier-evaluations.index') }}" style="padding:12px 24px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500;">
                Cancel
            </a>
        </div>
    </form>
</div>

<style>
    .toast-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    .toast-backdrop.show {
        display: flex;
    }
    .toast {
        background: #dc3545;
        color: white;
        padding: 18px 24px;
        border-radius: 8px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.25);
        min-width: 320px;
        max-width: 480px;
        text-align: center;
        font-size: 14px;
        font-weight: 500;
    }
</style>
<div id="toast-backdrop" class="toast-backdrop">
    <div id="toast" class="toast"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ratingInputs = document.querySelectorAll('input[name$=\"_rating\"]');
        const totalDisplay = document.getElementById('total_score_display');
        const form = document.querySelector('form');

        // Toast notification function (modal style)
        function showToast(message) {
            const backdrop = document.getElementById('toast-backdrop');
            const toast = document.getElementById('toast');
            if (!toast || !backdrop) return;
            toast.textContent = message;
            backdrop.classList.add('show');
            setTimeout(function() {
                backdrop.classList.remove('show');
            }, 4000);
        }

        // Validate individual rating input
        function validateRating(input) {
            const max = parseInt(input.getAttribute('data-max'), 10);
            const value = parseFloat(input.value);
            const criterion = input.getAttribute('data-criterion') || 'this criterion';
            
            if (isNaN(value) || value < 0) {
                input.value = '';
                return true;
            }
            
            if (value > max) {
                showToast('ActualRated is greater than maxpoint for ' + criterion);
                input.style.borderColor = '#dc3545';
                input.style.borderWidth = '2px';
                // Clear and refocus the invalid field
                input.value = '';
                input.focus();
                return false;
            } else {
                input.style.borderColor = '#ddd';
                input.style.borderWidth = '1px';
                return true;
            }
        }

        // Validate all ratings before form submission
        function validateAllRatings() {
            let isValid = true;
            ratingInputs.forEach(function(input) {
                if (!validateRating(input)) {
                    isValid = false;
                }
            });
            return isValid;
        }

        function recalc() {
            let total = 0;
            ratingInputs.forEach(function (input) {
                const val = parseFloat(input.value || '0');
                if (!isNaN(val) && val >= 0) {
                    const max = parseInt(input.getAttribute('data-max'), 10);
                    if (val <= max) {
                        total += val;
                    }
                }
            });
            if (totalDisplay) {
                totalDisplay.value = total;
            }
        }

        // Add validation on blur and input events
        ratingInputs.forEach(function (input) {
            input.addEventListener('blur', function() {
                validateRating(input);
                recalc();
            });
            input.addEventListener('input', function() {
                recalc();
                // Clear error styling on input
                const max = parseInt(input.getAttribute('data-max'), 10);
                const value = parseFloat(input.value);
                if (!isNaN(value) && value <= max) {
                    input.style.borderColor = '#ddd';
                    input.style.borderWidth = '1px';
                }
            });
        });

        // Form submission validation
        form.addEventListener('submit', function(e) {
            if (!validateAllRatings()) {
                e.preventDefault();
                showToast('Please correct all rating values before submitting.');
                return false;
            }
        });

        recalc();

        // Supplier auto‑populate
        const suppliersData = @json($suppliersData);
        const supplierSelect = document.getElementById('supplier_id');

        function populateSupplierFields(id) {
            const s = suppliersData[id];
            const byId = (x) => document.getElementById(x);
            if (!s) return;
            if (byId('contact_person')) byId('contact_person').value = s.contact_person || '';
            if (byId('address_line_1')) byId('address_line_1').value = s.address_line_1 || '';
            if (byId('address_line_2')) byId('address_line_2').value = s.address_line_2 || '';
            if (byId('city')) byId('city').value = s.city || '';
            if (byId('state')) byId('state').value = s.state || '';
            if (byId('pincode')) byId('pincode').value = s.pincode || '';
            if (byId('supplier_remarks')) byId('supplier_remarks').value = s.remarks || '';
        }

        if (supplierSelect) {
            supplierSelect.addEventListener('change', function () {
                if (this.value && suppliersData[this.value]) {
                    populateSupplierFields(this.value);
                }
            });

            // If already selected from old input, populate on load
            if (supplierSelect.value && suppliersData[supplierSelect.value]) {
                populateSupplierFields(supplierSelect.value);
            }
        }
    });
</script>
@endsection


