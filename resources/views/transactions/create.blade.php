@extends('layouts.dashboard')

@section('title', 'Create Transaction - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <h2 style="color: #333; margin-bottom: 25px;">Create New Transaction</h2>

    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf

        @if(auth()->user()->isBranchUser() && auth()->user()->branches->count() > 1)
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Branch <span style="color: red;">*</span></label>
                <select name="branch_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">Select Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', session('selected_branch_id')) == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }} - {{ $branch->address ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                @error('branch_id')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
            </div>
        @elseif(auth()->user()->isBranchUser() && auth()->user()->branches->count() == 1)
            <input type="hidden" name="branch_id" value="{{ auth()->user()->branches->first()->id }}">
        @endif

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Transaction Type <span style="color: red;">*</span></label>
            <select name="transaction_type" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="">Select Type</option>
                <option value="sale" {{ old('transaction_type') == 'sale' ? 'selected' : '' }}>Sale</option>
                <option value="purchase" {{ old('transaction_type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                <option value="transfer" {{ old('transaction_type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                <option value="payment" {{ old('transaction_type') == 'payment' ? 'selected' : '' }}>Payment</option>
                <option value="refund" {{ old('transaction_type') == 'refund' ? 'selected' : '' }}>Refund</option>
            </select>
            @error('transaction_type')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Amount <span style="color: red;">*</span></label>
            <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;"
                placeholder="Enter amount">
            @error('amount')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Description</label>
            <textarea name="description" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;" placeholder="Enter transaction description">{{ old('description') }}</textarea>
            @error('description')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Status</label>
            <select name="status" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            @error('status')<p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>@enderror
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('transactions.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">Cancel</a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; cursor: pointer;">Save</button>
        </div>
    </form>
</div>
@endsection

