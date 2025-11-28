@extends('layouts.dashboard')

@section('title', 'Customer Complaint Register')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:25px;">
        <h2 style="color:#333;font-size:24px;margin:0;">Customer Complaint Register</h2>
        <a href="{{ route('customer-complaints.create') }}"
           style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Add Complaint
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if($complaints->count() === 0)
        <p style="margin:0;color:#666;">No customer complaints found.</p>
    @else
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">ID</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Date</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Customer</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Product</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Complaint</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Status</th>
                            <th style="padding:12px; text-align:center; color:#333; font-weight:600; width:160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($complaints as $complaint)
                            <tr style="border-bottom:1px solid #dee2e6;">
                                <td style="padding:10px 12px; color:#333;">{{ $complaint->id }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($complaint->complaint_date)->format('d-m-Y') }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $complaint->customer_name }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($complaint->product)->name }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $complaint->complaint_type }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $complaint->status }}</td>
                                <td style="padding:10px 12px; text-align:center;">
                                    <a href="{{ route('customer-complaints.show', $complaint->id) }}"
                                       style="padding:6px 10px; background:#17a2b8; color:white; border-radius:4px; font-size:12px; text-decoration:none; margin-right:4px;">
                                        View
                                    </a>
                                    <a href="{{ route('customer-complaints.edit', $complaint->id) }}"
                                       style="padding:6px 10px; background:#ffc107; color:#212529; border-radius:4px; font-size:12px; text-decoration:none; margin-right:4px;">
                                        Edit
                                    </a>
                                    <form action="{{ route('customer-complaints.destroy', $complaint->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this complaint?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                style="padding:6px 10px; background:#dc3545; color:white; border:none; border-radius:4px; font-size:12px; cursor:pointer;">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top:15px;">
            {{ $complaints->links() }}
        </div>
    @endif
</div>
@endsection


