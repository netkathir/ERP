@extends('layouts.dashboard')

@section('title', 'Production Work Order - ERP System')
echo "<pre>";
print_r("apoajpfojdiofhdisofgosdgn");
echo "</pre>";
exit;
@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="margin-bottom: 15px;">
        <span style="color: #666; font-size: 14px;">PRODUCTION</span>
        <h2 style="color: #333; font-size: 24px; margin: 8px 0 20px 0;">PRODUCTION WORK ORDER</h2>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <form method="GET" action="{{ route('work-orders.index') }}" id="searchForm" style="display: flex; align-items: center; gap: 15px; flex: 1; min-width: 200px;">
            <label for="search" style="color: #333; font-weight: 500; white-space: nowrap;">SEARCH</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                style="flex: 1; max-width: 300px; padding: 10px 15px; border: 1px solid #ddd; border-radius: 20px; font-size: 14px;"
                placeholder="Search work orders...">
        </form>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('work-orders.create') }}" style="padding: 10px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 20px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; font-size: 14px;">
                <i class="fas fa-plus"></i> ADD
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if($workOrders->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.NO</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PO.NO</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">CUST. PO.NO</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">SALES TYPE</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">DATE</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">TITLE</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Work Order No</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($workOrders as $wo)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($workOrders->currentPage() - 1) * $workOrders->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333;">{{ $wo->production_order_no ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #333;">{{ $wo->customer_po_no ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #333;">{{ $wo->sales_type ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $wo->created_at->format('d-m-Y') }}</td>
                            <td style="padding: 12px; color: #333;">{{ Str::limit($wo->title ?? 'N/A', 40) }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $wo->work_order_no }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                                    <a href="{{ route('work-orders.edit', $wo->id) }}" style="padding: 6px 14px; background: #667eea; color: white; text-decoration: none; border-radius: 15px; font-size: 12px;">
                                        <i class="fas fa-edit"></i> EDIT
                                    </a>
                                    <a href="{{ route('work-orders.show', $wo->id) }}" style="padding: 6px 14px; background: #28a745; color: white; text-decoration: none; border-radius: 15px; font-size: 12px;">
                                        <i class="fas fa-eye"></i> VIEW
                                    </a>
                                    <form action="{{ route('work-orders.destroy', $wo->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this work order?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: 6px 14px; background: #dc3545; color: white; border: none; border-radius: 15px; font-size: 12px; cursor: pointer;">
                                            <i class="fas fa-trash"></i> DELETE
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
            {{ $workOrders->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No work orders found.</p>
            <a href="{{ route('work-orders.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                <i class="fas fa-plus"></i> Add First Work Order
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search');
    const searchForm = document.getElementById('searchForm');
    let searchTimeout;
    if (searchInput && searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(searchForm);
            const params = new URLSearchParams(formData);
            window.location.replace('{{ route("work-orders.index") }}?' + params.toString());
        });
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                const formData = new FormData(searchForm);
                const params = new URLSearchParams(formData);
                window.location.replace('{{ route("work-orders.index") }}?' + params.toString());
            }, 500);
        });
    }
});
</script>
@endpush
@endsection
