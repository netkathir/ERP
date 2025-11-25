@extends('layouts.dashboard')

@section('title', 'Transactions - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Transactions</h2>
        @if(auth()->user()->isBranchUser())
            <a href="{{ route('transactions.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Create Transaction
            </a>
        @endif
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if($transactions->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left;">Reference</th>
                        <th style="padding: 12px; text-align: left;">Type</th>
                        <th style="padding: 12px; text-align: left;">Amount</th>
                        <th style="padding: 12px; text-align: left;">Status</th>
                        @if(auth()->user()->isSuperAdmin())
                            <th style="padding: 12px; text-align: left;">User</th>
                            <th style="padding: 12px; text-align: left;">Branch</th>
                        @endif
                        <th style="padding: 12px; text-align: left;">Date</th>
                        <th style="padding: 12px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; font-weight: 500;">{{ $transaction->reference_number }}</td>
                            <td style="padding: 12px;">{{ ucfirst($transaction->transaction_type) }}</td>
                            <td style="padding: 12px; font-weight: 600; color: #28a745;">${{ number_format($transaction->amount, 2) }}</td>
                            <td style="padding: 12px;">
                                <span style="padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;
                                    @if($transaction->status == 'completed') background: #d4edda; color: #155724;
                                    @elseif($transaction->status == 'pending') background: #fff3cd; color: #856404;
                                    @else background: #f8d7da; color: #721c24;
                                    @endif">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            @if(auth()->user()->isSuperAdmin())
                                <td style="padding: 12px;">{{ $transaction->user->name }}</td>
                                <td style="padding: 12px;">{{ $transaction->branch->name }}</td>
                            @endif
                            <td style="padding: 12px;">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('transactions.show', $transaction->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">View</a>
                                    @if(auth()->user()->isBranchUser() && $transaction->user_id == auth()->id())
                                        <a href="{{ route('transactions.edit', $transaction->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">Edit</a>
                                        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;">{{ $transactions->links() }}</div>
    @else
        <div style="text-align: center; padding: 40px;">
            <p style="color: #666; font-size: 16px;">No transactions found.</p>
            @if(auth()->user()->isBranchUser())
                <a href="{{ route('transactions.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; display: inline-block;">Create First Transaction</a>
            @endif
        </div>
    @endif
</div>
@endsection

