@extends('layouts.dashboard')

@section('title', 'Transaction Details - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Transaction Details</h2>
        <div style="display: flex; gap: 10px;">
            @if(auth()->user()->isBranchUser() && $transaction->user_id == auth()->id())
                <a href="{{ route('transactions.edit', $transaction->id) }}" style="padding: 10px 20px; background: #ffc107; color: #333; text-decoration: none; border-radius: 5px; font-weight: 500;">Edit</a>
            @endif
            <a href="{{ route('transactions.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">Back to List</a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Reference Number</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $transaction->reference_number }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Transaction Type</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ ucfirst($transaction->transaction_type) }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Amount</label>
            <p style="color: #28a745; font-size: 20px; font-weight: 600; margin: 0 0 20px 0;">${{ number_format($transaction->amount, 2) }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Status</label>
            <p style="margin: 0 0 20px 0;">
                <span style="padding: 6px 16px; border-radius: 12px; font-size: 14px; font-weight: 500;
                    @if($transaction->status == 'completed') background: #d4edda; color: #155724;
                    @elseif($transaction->status == 'pending') background: #fff3cd; color: #856404;
                    @else background: #f8d7da; color: #721c24;
                    @endif">
                    {{ ucfirst($transaction->status) }}
                </span>
            </p>
        </div>

        @if(auth()->user()->isSuperAdmin())
            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">User</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $transaction->user->name }} ({{ $transaction->user->email }})</p>
            </div>

            <div>
                <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Branch</label>
                <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $transaction->branch->name }}</p>
            </div>
        @endif

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Created At</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $transaction->created_at->format('M d, Y H:i:s') }}</p>
        </div>

        <div>
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Updated At</label>
            <p style="color: #333; font-size: 16px; margin: 0 0 20px 0;">{{ $transaction->updated_at->format('M d, Y H:i:s') }}</p>
        </div>
    </div>

    @if($transaction->description)
        <div style="margin-bottom: 20px;">
            <label style="display: block; color: #666; font-weight: 500; margin-bottom: 5px;">Description</label>
            <p style="color: #333; font-size: 16px; margin: 0; padding: 15px; background: #f8f9fa; border-radius: 5px;">{{ $transaction->description }}</p>
        </div>
    @endif
</div>
@endsection

