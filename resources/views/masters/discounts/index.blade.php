@extends('layouts.dashboard')

@section('title', 'Discounts - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Discounts</h2>
        <a href="{{ route('discounts.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Add Discount
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if($discounts->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Name</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Type</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Percentage</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Status</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($discounts as $discount)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($discounts->currentPage() - 1) * $discounts->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $discount->name }}</td>
                            <td style="padding: 12px; color: #666;">{{ $discount->type }}</td>
                            <td style="padding: 12px; text-align: right; color: #666;">{{ $discount->percentage }}%</td>
                            <td style="padding: 12px; text-align: center;">
                                @if($discount->is_active)
                                    <span style="background: #28a745; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">Active</span>
                                @else
                                    <span style="background: #dc3545; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">Inactive</span>
                                @endif
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('discounts.edit', $discount->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this discount?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $discounts->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No discounts found.</p>
            <a href="{{ route('discounts.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Create First Discount
            </a>
        </div>
    @endif
</div>
@endsection
