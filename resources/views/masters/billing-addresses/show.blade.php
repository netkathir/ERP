@extends('layouts.dashboard')

@section('title', 'View Billing Address - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Billing Address Details</h2>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('billing-addresses.edit', $billingAddress->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('billing-addresses.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 25px; border-radius: 5px;">
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
            <div style="color: #666; font-weight: 500;">Company Name:</div>
            <div style="color: #333; font-weight: 500;">{{ $billingAddress->company_name }}</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
            <div style="color: #666; font-weight: 500;">Address Line 1:</div>
            <div style="color: #333;">{{ $billingAddress->address_line_1 }}</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
            <div style="color: #666; font-weight: 500;">Address Line 2:</div>
            <div style="color: #333;">{{ $billingAddress->address_line_2 }}</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
            <div style="color: #666; font-weight: 500;">City:</div>
            <div style="color: #333;">{{ $billingAddress->city }}</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
            <div style="color: #666; font-weight: 500;">State:</div>
            <div style="color: #333;">{{ $billingAddress->state }}</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
            <div style="color: #666; font-weight: 500;">Pincode:</div>
            <div style="color: #333;">{{ $billingAddress->pincode }}</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
            <div style="color: #666; font-weight: 500;">Email:</div>
            <div style="color: #333;">{{ $billingAddress->email }}</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
            <div style="color: #666; font-weight: 500;">Contact No:</div>
            <div style="color: #333;">{{ $billingAddress->contact_no }}</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
            <div style="color: #666; font-weight: 500;">GST No:</div>
            <div style="color: #333;">{{ $billingAddress->gst_no }}</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
            <div style="color: #666; font-weight: 500;">Created At:</div>
            <div style="color: #333;">{{ $billingAddress->created_at->format('d-m-Y H:i:s') }}</div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
            <div style="color: #666; font-weight: 500;">Updated At:</div>
            <div style="color: #333;">{{ $billingAddress->updated_at->format('d-m-Y H:i:s') }}</div>
        </div>
    </div>
</div>
@endsection

