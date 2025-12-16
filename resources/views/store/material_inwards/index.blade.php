@extends('layouts.dashboard')

@section('title', 'Material Inwards')

@section('content')
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Material Inwards</h2>
        <a href="{{ route('material-inwards.create') }}"
           style="padding:10px 20px; background:#667eea; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-plus"></i> New Material Inward
        </a>
    </div>

    @if(session('success'))
        <div style="background:#d4edda; color:#155724; padding:12px 15px; border-radius:5px; margin-bottom:20px; border:1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background:#f8d7da; color:#721c24; padding:12px 15px; border-radius:5px; margin-bottom:20px; border:1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    @if($materialInwards->count() === 0)
        <p style="margin:0; color:#666;">No material inwards found.</p>
    @else
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:12px; text-align:center; color:#333; font-weight:600;">S.No</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Material Inward No</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Purchase Order No</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Supplier Name</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Created By</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Created At</th>
                            <th style="padding:12px; text-align:center; color:#333; font-weight:600; width:220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materialInwards as $index => $materialInward)
                            <tr style="border-bottom:1px solid #dee2e6;">
                                <td style="padding:10px 12px; text-align:center; color:#666;">{{ ($materialInwards->currentPage() - 1) * $materialInwards->perPage() + $index + 1 }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $materialInward->material_inward_no }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($materialInward->purchaseOrder)->po_no ?? 'N/A' }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($materialInward->supplier)->supplier_name ?? 'N/A' }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($materialInward->creator)->name ?? 'N/A' }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($materialInward->created_at)->format('d-m-Y') ?? 'N/A' }}</td>
                                <td style="padding:10px 12px; text-align:center;">
                                    <div style="display:flex; align-items:center; justify-content:center; gap:4px; flex-wrap:nowrap;">
                                        <a href="{{ route('material-inwards.show', $materialInward->id) }}"
                                           style="padding:6px 10px; background:#17a2b8; color:white; border-radius:4px; font-size:12px; text-decoration:none; white-space:nowrap; display:inline-block;">
                                            View
                                        </a>
                                        <a href="{{ route('material-inwards.edit', $materialInward->id) }}"
                                           style="padding:6px 10px; background:#ffc107; color:#212529; border-radius:4px; font-size:12px; text-decoration:none; white-space:nowrap; display:inline-block;">
                                            Edit
                                        </a>
                                        <form action="{{ route('material-inwards.destroy', $materialInward->id) }}" method="POST" style="display:inline-block; margin:0;" onsubmit="return confirm('Are you sure you want to delete this material inward?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    style="padding:6px 10px; background:#dc3545; color:white; border:none; border-radius:4px; font-size:12px; cursor:pointer; white-space:nowrap;">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top:15px;">
            {{ $materialInwards->links() }}
        </div>
    @endif
</div>
@endsection

