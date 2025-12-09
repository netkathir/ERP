@extends('layouts.dashboard')

@section('title', 'Create Tender - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Create Tender</h2>
        <a href="{{ route('tenders.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back to List
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

    <form action="{{ route('tenders.store') }}" method="POST" id="tenderForm" enctype="multipart/form-data">
        @csrf
        
        <!-- General Information Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">General Information</h3>
            </div>
            <div style="padding: 20px;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Tender No <span style="color: red;">*</span></label>
                        <input type="text" name="tender_no" value="{{ $tenderNo }}" readonly
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Customer Tender No</label>
                        <input type="text" name="customer_tender_no" value="{{ old('customer_tender_no') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Attended By</label>
                        <input type="text" value="{{ $user->name }}" readonly
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Production Dept</label>
                        <select name="production_dept" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select Department</option>
                            @foreach($productionDepartments as $dept)
                                <option value="{{ $dept->name }}" {{ old('production_dept') == $dept->name ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Company Name</label>
                        <div style="display: flex; gap: 10px;">
                            <select name="company_id" id="company_id" onchange="fetchCustomerDetails(this.value)" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                                <option value="">Select Company</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('company_id') == $customer->id ? 'selected' : '' }}>{{ $customer->company_name }}</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="openAddCustomerModal()" style="padding: 10px 15px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contact Person</label>
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing Address Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Billing Address</h3>
            </div>
            <div style="padding: 20px;">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 1</label>
                    <input type="text" name="billing_address_line_1" id="billing_address_line_1" value="{{ old('billing_address_line_1') }}"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Address Line 2</label>
                    <input type="text" name="billing_address_line_2" id="billing_address_line_2" value="{{ old('billing_address_line_2') }}"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
                <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">City</label>
                        <input type="text" name="billing_city" id="billing_city" value="{{ old('billing_city') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">State/Province</label>
                        <input type="text" name="billing_state" id="billing_state" value="{{ old('billing_state') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Country</label>
                        <input type="text" name="billing_country" id="billing_country" value="{{ old('billing_country') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">PinCode</label>
                        <input type="text" name="billing_pincode" id="billing_pincode" value="{{ old('billing_pincode') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tender Details Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Tender Details</h3>
            </div>
            <div style="padding: 20px;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Publishing Date</label>
                        <input type="date" name="publishing_date" value="{{ old('publishing_date') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Closing Date/Time</label>
                        <input type="datetime-local" name="closing_date_time" value="{{ old('closing_date_time') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Bidding Type</label>
                        <select name="bidding_type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select</option>
                            <option value="Online" {{ old('bidding_type') == 'Online' ? 'selected' : '' }}>Online</option>
                            <option value="Offline" {{ old('bidding_type') == 'Offline' ? 'selected' : '' }}>Offline</option>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Tender Type</label>
                        <select name="tender_type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select</option>
                            <option value="Open" {{ old('tender_type') == 'Open' ? 'selected' : '' }}>Open</option>
                            <option value="Limited" {{ old('tender_type') == 'Limited' ? 'selected' : '' }}>Limited</option>
                            <option value="Special Limited" {{ old('tender_type') == 'Special Limited' ? 'selected' : '' }}>Special Limited</option>
                            <option value="Single" {{ old('tender_type') == 'Single' ? 'selected' : '' }}>Single</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contract Type</label>
                        <select name="contract_type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select</option>
                            <option value="Goods" {{ old('contract_type') == 'Goods' ? 'selected' : '' }}>Goods</option>
                            <option value="Service" {{ old('contract_type') == 'Service' ? 'selected' : '' }}>Service</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Bidding System</label>
                        <select name="bidding_system" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select</option>
                            <option value="Single Packet" {{ old('bidding_system') == 'Single Packet' ? 'selected' : '' }}>Single Packet</option>
                            <option value="Two Packet" {{ old('bidding_system') == 'Two Packet' ? 'selected' : '' }}>Two Packet</option>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Procure from Approved Source</label>
                        <select name="procure_from_approved_source" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select</option>
                            <option value="Yes" {{ old('procure_from_approved_source') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('procure_from_approved_source') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Tender Document Cost</label>
                        <input type="number" name="tender_document_cost" step="0.01" value="{{ old('tender_document_cost') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">EMD (Earnest Money Deposit)</label>
                        <input type="number" name="emd" step="0.01" value="{{ old('emd') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">RA Enabled</label>
                        <select name="ra_enabled" id="ra_enabled" onchange="toggleRADate()" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select</option>
                            <option value="Yes" {{ old('ra_enabled') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('ra_enabled') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">RA Date & Time</label>
                        <input type="datetime-local" name="ra_date_time" id="ra_date_time" value="{{ old('ra_date_time') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;" disabled>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Pre-Bid Conference Required</label>
                        <select name="pre_bid_conference_required" id="pre_bid_conference_required" onchange="togglePreBidDate()" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="">Select</option>
                            <option value="Yes" {{ old('pre_bid_conference_required') == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('pre_bid_conference_required') == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Pre-Bid Conference Date</label>
                        <input type="date" name="pre_bid_conference_date" id="pre_bid_conference_date" value="{{ old('pre_bid_conference_date') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;" disabled>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Inspection Agency</label>
                        <input type="text" name="inspection_agency" value="{{ old('inspection_agency') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Tender Document Attachment</label>
                        <input type="file" name="tender_document_attachment" accept=".pdf,.doc,.docx"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Regular / Developmental</label>
                        <select name="regular_developmental" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="Regular" {{ old('regular_developmental', 'Regular') == 'Regular' ? 'selected' : '' }}>Regular</option>
                            <option value="Developmental" {{ old('regular_developmental') == 'Developmental' ? 'selected' : '' }}>Developmental</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500; background: #ff9800; color: #fff; padding: 4px 8px; display: inline-block; border-radius: 3px;">Validity of Offer (Days)</label>
                        <input type="number" name="validity_of_offer_days" min="0" value="{{ old('validity_of_offer_days') }}"
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Tender Items Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Tender Items</h3>
                <button type="button" onclick="addItemRow()" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-plus"></i> Add Item
                </button>
            </div>
            <div style="overflow-x: auto; padding: 20px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PL Code</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Title</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Description</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Delivery Location</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Qty</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Unit</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Request for Price</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Price Received</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Price Quoted</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Action</th>
                    </tr>
                    </thead>
                    <tbody id="itemRows">
                        <!-- Rows will be added here dynamically -->
                    </tbody>
                </table>

                <!-- Hidden Unit Options Template -->
                <select id="unitOptionsTemplate" style="display: none;">
                    <option value="">Select Unit</option>
                    @foreach($units as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->symbol }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Technical Specifications Section (will be handled per item) -->

        <!-- Tender Status & Bid Result (Header) -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Tender Status & Bid Result</h3>
            </div>
            <div style="padding: 20px;">
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Tender Status</label>
                        <select name="tender_status" id="tender_status" onchange="toggleBidResult()" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="Bid not coated" {{ old('tender_status', 'Bid not coated') == 'Bid not coated' ? 'selected' : '' }}>Bid Not Quoted</option>
                            <option value="Bid Coated" {{ old('tender_status') == 'Bid Coated' ? 'selected' : '' }}>Bid Quoted</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Bid Result</label>
                        <select name="bid_result" id="bid_result" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;" disabled>
                            <option value="">Select</option>
                            <option value="Bid Awarded" {{ old('bid_result') == 'Bid Awarded' ? 'selected' : '' }}>Bid Awarded</option>
                            <option value="Bid not Awarded" {{ old('bid_result') == 'Bid not Awarded' ? 'selected' : '' }}>Bid not Awarded</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Financial Tabulation</label>
                        <input type="file" name="financial_tabulation_attachment" accept=".pdf,.doc,.docx"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Technical Specification</label>
                        <input type="file" name="technical_spec_attachment" accept=".pdf,.doc,.docx"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Rank</label>
                        <input type="text" name="technical_spec_rank" value="{{ old('technical_spec_rank') }}"
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bid Closing Information Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Bid Closing Information</h3>
                <button type="button" onclick="addFinancialTabulationRow()" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>
            <div style="padding: 20px;">
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                                <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PL Number</th>
                                <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Bid Closed Date</th>
                                <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="financialTabulationRows">
                            <!-- Rows will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Remarks Section -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Remarks</h3>
            </div>
            <div style="padding: 20px;">
                <div style="margin-bottom: 15px;">
                    <button type="button" onclick="addRemarkRow()" style="padding: 8px 16px; background: #28a745; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; font-weight: 500; margin-bottom: 15px;">
                        <i class="fas fa-plus"></i> Add Remark
                    </button>
                </div>
                <div id="remarkRows">
                    <!-- Rows will be added here dynamically -->
                </div>
            </div>
        </div>

        <!-- Submit Section -->
        <div style="display: flex; gap: 15px; justify-content: space-between; align-items: center; margin-top: 30px;">
            <a href="{{ route('tenders.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                <i class="fas fa-list"></i> List
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-save"></i> Submit
            </button>
        </div>
    </form>
</div>

<!-- Add Customer Modal -->
<div id="addCustomerModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 5% auto; padding: 30px; border-radius: 10px; width: 90%; max-width: 600px; max-height: 90vh; overflow-y: auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #333; font-size: 20px;">Add Customer</h3>
            <button type="button" onclick="closeAddCustomerModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">&times;</button>
        </div>
        <form id="addCustomerForm" action="{{ route('customers.store') }}" method="POST" onsubmit="saveCustomer(event)">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contact Name</label>
                <input type="text" name="contact_name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Designation</label>
                <input type="text" name="designation" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Company Name <span style="color: red;">*</span></label>
                <input type="text" name="company_name" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Contact Number</label>
                <input type="text" name="contact_info" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">GST No</label>
                <input type="text" name="gst_no" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Billing Address Line 1</label>
                <input type="text" name="billing_address_line_1" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Billing Address Line 2</label>
                <input type="text" name="billing_address_line_2" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">City</label>
                    <input type="text" name="billing_city" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">State</label>
                    <input type="text" name="billing_state" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Pincode</label>
                    <input type="text" name="billing_pincode" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </div>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" onclick="closeAddCustomerModal()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">Submit</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let itemRowCount = 0;
    let financialTabulationRowCount = 0;
    let remarkRowCount = 0;

    function addItemRow() {
        itemRowCount++;
        const html = `
            <tr id="item_row_${itemRowCount}" style="border-bottom: 1px solid #dee2e6;">
                <td style="padding: 10px;">
                    <input type="text" name="items[${itemRowCount}][pl_code]" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </td>
                <td style="padding: 10px;">
                    <input type="text" name="items[${itemRowCount}][title]" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </td>
                <td style="padding: 10px;">
                    <textarea name="items[${itemRowCount}][description]" rows="2" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"></textarea>
                    <button type="button" onclick="showDescriptionModal('${itemRowCount}')" style="margin-top: 5px; padding: 4px 8px; background: #667eea; color: white; border: none; border-radius: 3px; font-size: 12px; cursor: pointer;">Show More</button>
                </td>
                <td style="padding: 10px;">
                    <input type="text" name="items[${itemRowCount}][delivery_location]" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </td>
                <td style="padding: 10px;">
                    <input type="number" name="items[${itemRowCount}][qty]" step="0.01" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </td>
                <td style="padding: 10px;">
                    <select name="items[${itemRowCount}][unit_id]" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        ${document.getElementById('unitOptionsTemplate').innerHTML}
                    </select>
                </td>
                <td style="padding: 10px;">
                    <select name="items[${itemRowCount}][request_for_price]" onchange="handleRequestForPrice(${itemRowCount}, this.value)" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        <option value="No">No</option>
                        <option value="Yes">Yes</option>
                    </select>
                </td>
                <td style="padding: 10px;">
                    <input type="number" name="items[${itemRowCount}][price_received]" step="0.01" readonly
                           style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background-color: #f8f9fa;">
                </td>
                <td style="padding: 10px;">
                    <input type="number" name="items[${itemRowCount}][price_quoted]" step="0.01" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </td>
                <td style="padding: 10px; text-align: center;">
                    <button type="button" onclick="removeItemRow(${itemRowCount})" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 5px; font-size: 12px; cursor: pointer;">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        document.getElementById('itemRows').insertAdjacentHTML('beforeend', html);
    }

    function removeItemRow(id) {
        document.getElementById(`item_row_${id}`).remove();
    }

    function addFinancialTabulationRow() {
        financialTabulationRowCount++;
        const html = `
            <tr id="financial_tab_row_${financialTabulationRowCount}" style="border-bottom: 1px solid #dee2e6;">
                <td style="padding: 10px;">
                    <input type="text" name="financial_tabulations[${financialTabulationRowCount}][pl_number]" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </td>
                <td style="padding: 10px;">
                    <input type="date" name="financial_tabulations[${financialTabulationRowCount}][bid_closed_date]" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                </td>
                <td style="padding: 10px; text-align: center;">
                    <button type="button" onclick="removeFinancialTabulationRow(${financialTabulationRowCount})" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 5px; font-size: 12px; cursor: pointer;">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        document.getElementById('financialTabulationRows').insertAdjacentHTML('beforeend', html);
    }

    function removeFinancialTabulationRow(id) {
        document.getElementById(`financial_tab_row_${id}`).remove();
    }

    function addRemarkRow() {
        remarkRowCount++;
        const html = `
            <div id="remark_row_${remarkRowCount}" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #dee2e6;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h4 style="margin: 0; color: #333;">Remark #${remarkRowCount}</h4>
                    <button type="button" onclick="removeRemarkRow(${remarkRowCount})" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 5px; font-size: 12px; cursor: pointer;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Date</label>
                        <input type="date" name="remarks[${remarkRowCount}][date]" value="{{ date('Y-m-d') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Corrigendum File</label>
                        <input type="file" name="remarks[${remarkRowCount}][corrigendum_file]" accept=".pdf,.doc,.docx" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </div>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Remarks</label>
                    <textarea name="remarks[${remarkRowCount}][remarks]" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"></textarea>
                </div>
            </div>
        `;
        document.getElementById('remarkRows').insertAdjacentHTML('beforeend', html);
    }

    function removeRemarkRow(id) {
        document.getElementById(`remark_row_${id}`).remove();
    }

    function fetchCustomerDetails(id) {
        const contactPerson = document.getElementById('contact_person');
        const addr1 = document.getElementById('billing_address_line_1');
        const addr2 = document.getElementById('billing_address_line_2');
        const city = document.getElementById('billing_city');
        const state = document.getElementById('billing_state');
        const country = document.getElementById('billing_country');
        const pincode = document.getElementById('billing_pincode');

        if (!id) {
            // Clear and make fields editable if no company selected
            [contactPerson, addr1, addr2, city, state, country, pincode].forEach(input => {
                if (input) {
                    input.value = '';
                    input.readOnly = false;
                }
            });
            return;
        }

        fetch(`{{ url('tenders/customer') }}/${id}`)
            .then(response => response.json())
            .then(data => {
                if (contactPerson) {
                    contactPerson.value = data.contact_name || '';
                    contactPerson.readOnly = true;
                }
                if (addr1) {
                    addr1.value = data.billing_address_line_1 || '';
                    addr1.readOnly = true;
                }
                if (addr2) {
                    addr2.value = data.billing_address_line_2 || '';
                    addr2.readOnly = true;
                }
                if (city) {
                    city.value = data.billing_city || '';
                    city.readOnly = true;
                }
                if (state) {
                    state.value = data.billing_state || '';
                    state.readOnly = true;
                }
                if (country) {
                    country.value = data.billing_country || '';
                    country.readOnly = true;
                }
                if (pincode) {
                    pincode.value = data.billing_pincode || '';
                    pincode.readOnly = true;
                }
            });
    }

    function toggleRADate() {
        const raEnabled = document.getElementById('ra_enabled').value;
        const raDateTime = document.getElementById('ra_date_time');
        if (raEnabled === 'Yes') {
            raDateTime.disabled = false;
        } else {
            raDateTime.disabled = true;
            raDateTime.value = '';
        }
    }

    function togglePreBidDate() {
        const preBidRequired = document.getElementById('pre_bid_conference_required').value;
        const preBidDate = document.getElementById('pre_bid_conference_date');
        if (preBidRequired === 'Yes') {
            preBidDate.disabled = false;
        } else {
            preBidDate.disabled = true;
            preBidDate.value = '';
        }
    }

    function toggleBidResult() {
        const tenderStatus = document.getElementById('tender_status').value;
        const bidResult = document.getElementById('bid_result');
        if (tenderStatus === 'Bid Coated') {
            bidResult.disabled = false;
        } else {
            bidResult.disabled = true;
            bidResult.value = '';
        }
    }

    function handleRequestForPrice(rowId, value) {
        if (value === 'Yes') {
            // Send notification (you can implement this with AJAX)
            alert('Notification will be sent for price request');
        }
    }

    function openAddCustomerModal() {
        document.getElementById('addCustomerModal').style.display = 'block';
    }

    function closeAddCustomerModal() {
        document.getElementById('addCustomerModal').style.display = 'none';
        document.getElementById('addCustomerForm').reset();
    }

    function saveCustomer(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw data;
                });
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success && data.customer) {
                const companySelect = document.getElementById('company_id');
                if (companySelect) {
                    const option = document.createElement('option');
                    option.value = data.customer.id;
                    option.textContent = data.customer.company_name;
                    option.selected = true;
                    companySelect.appendChild(option);

                    // Trigger fetch of billing/contact details
                    fetchCustomerDetails(data.customer.id);
                }
                closeAddCustomerModal();
            } else {
                alert('Customer saved, but response was unexpected.');
            }
        })
        .catch(error => {
            let message = 'Failed to save customer.';
            if (error && error.errors) {
                const firstKey = Object.keys(error.errors)[0];
                if (firstKey) {
                    message = error.errors[firstKey][0] || message;
                }
            }
            alert(message);
        });
    }

    function showDescriptionModal(rowId) {
        const description = document.querySelector(`textarea[name="items[${rowId}][description]"]`).value;
        alert('Description:\n\n' + description);
    }

    // Rebuild rows from old input on validation error, otherwise add one empty row
    @php $oldItems = old('items'); @endphp
    @if($oldItems)
        @foreach($oldItems as $idx => $item)
            itemRowCount++;
            const oldItemHtml{{ $idx }} = `
                <tr id="item_row_${itemRowCount}" style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 10px;">
                        <input type="text" name="items[${itemRowCount}][pl_code]" value="{{ $item['pl_code'] ?? '' }}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </td>
                    <td style="padding: 10px;">
                        <input type="text" name="items[${itemRowCount}][title]" value="{{ $item['title'] ?? '' }}" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </td>
                    <td style="padding: 10px;">
                        <textarea name="items[${itemRowCount}][description]" rows="2" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">{{ $item['description'] ?? '' }}</textarea>
                        <button type="button" onclick="showDescriptionModal('${itemRowCount}')" style="margin-top: 5px; padding: 4px 8px; background: #667eea; color: white; border: none; border-radius: 3px; font-size: 12px; cursor: pointer;">Show More</button>
                    </td>
                    <td style="padding: 10px;">
                        <input type="text" name="items[${itemRowCount}][delivery_location]" value="{{ $item['delivery_location'] ?? '' }}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </td>
                    <td style="padding: 10px;">
                        <input type="number" name="items[${itemRowCount}][qty]" value="{{ $item['qty'] ?? '' }}" step="0.01" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </td>
                    <td style="padding: 10px;">
                        <select name="items[${itemRowCount}][unit_id]" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            ${document.getElementById('unitOptionsTemplate').innerHTML}
                        </select>
                    </td>
                    <td style="padding: 10px;">
                        <select name="items[${itemRowCount}][request_for_price]" onchange="handleRequestForPrice(${itemRowCount}, this.value)" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                            <option value="No" {{ (isset($item['request_for_price']) && $item['request_for_price'] === 'No') ? 'selected' : '' }}>No</option>
                            <option value="Yes" {{ (isset($item['request_for_price']) && $item['request_for_price'] === 'Yes') ? 'selected' : '' }}>Yes</option>
                        </select>
                    </td>
                    <td style="padding: 10px;">
                        <input type="number" name="items[${itemRowCount}][price_received]" value="{{ $item['price_received'] ?? '' }}" step="0.01" readonly
                               style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background-color: #f8f9fa;">
                    </td>
                    <td style="padding: 10px;">
                        <input type="number" name="items[${itemRowCount}][price_quoted]" value="{{ $item['price_quoted'] ?? '' }}" step="0.01" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </td>
                    <td style="padding: 10px; text-align: center;">
                        <button type="button" onclick="removeItemRow(${itemRowCount})" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 5px; font-size: 12px; cursor: pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            document.getElementById('itemRows').insertAdjacentHTML('beforeend', oldItemHtml{{ $idx }});
        @endforeach
    @else
        addItemRow();
    @endif

    // Rebuild financial tabulations from old input
    @php $oldTabs = old('financial_tabulations'); @endphp
    @if($oldTabs)
        @foreach($oldTabs as $fidx => $tab)
            financialTabulationRowCount++;
            const oldFtHtml{{ $fidx }} = `
                <tr id="financial_tab_row_${financialTabulationRowCount}" style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 10px;">
                        <input type="text" name="financial_tabulations[${financialTabulationRowCount}][pl_number]" value="{{ $tab['pl_number'] ?? '' }}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </td>
                    <td style="padding: 10px;">
                        <input type="date" name="financial_tabulations[${financialTabulationRowCount}][bid_closed_date]" value="{{ $tab['bid_closed_date'] ?? '' }}" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    </td>
                    <td style="padding: 10px; text-align: center;">
                        <button type="button" onclick="removeFinancialTabulationRow(${financialTabulationRowCount})" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 5px; font-size: 12px; cursor: pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            document.getElementById('financialTabulationRows').insertAdjacentHTML('beforeend', oldFtHtml{{ $fidx }});
        @endforeach
    @endif

    // Rebuild remarks from old input
    @php $oldRemarks = old('remarks'); @endphp
    @if($oldRemarks)
        @foreach($oldRemarks as $ridx => $remark)
            remarkRowCount++;
            const oldRemarkHtml{{ $ridx }} = `
                <div id="remark_row_${remarkRowCount}" style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #dee2e6;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h4 style="margin: 0; color: #333;">Remark #${remarkRowCount}</h4>
                        <button type="button" onclick="removeRemarkRow(${remarkRowCount})" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 5px; font-size: 12px; cursor: pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Date</label>
                            <input type="date" name="remarks[${remarkRowCount}][date]" value="{{ $remark['date'] ?? date('Y-m-d') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        </div>
                        <div>
                            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Corrigendum File</label>
                            <input type="file" name="remarks[${remarkRowCount}][corrigendum_file]" accept=".pdf,.doc,.docx" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                        </div>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Remarks</label>
                        <textarea name="remarks[${remarkRowCount}][remarks]" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">{{ $remark['remarks'] ?? '' }}</textarea>
                    </div>
                </div>
            `;
            document.getElementById('remarkRows').insertAdjacentHTML('beforeend', oldRemarkHtml{{ $ridx }});
        @endforeach
    @endif
</script>
@endpush
@endsection

