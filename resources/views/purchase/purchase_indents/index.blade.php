@extends('layouts.dashboard')

@section('title', 'Purchase Indents')

@section('content')
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Purchase Indents</h2>
        <a href="{{ route('purchase-indents.create') }}"
           style="padding:10px 20px; background:#667eea; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-plus"></i> New Purchase Indent
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

    <!-- Search Section - Only show if there are items or a search query is active -->
    @if($indents->count() > 0 || request('search'))
    <form method="GET" action="{{ route('purchase-indents.index') }}" id="searchForm" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: flex; gap: 15px; align-items: end;">
            <div style="flex: 1;">
                <label for="search" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Search:</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Search by Indent No, Status, Date (dd-mm-yyyy)...">
            </div>
            <div>
                <a href="{{ route('purchase-indents.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center;">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const searchForm = document.getElementById('searchForm');
            let searchTimeout;

            // Auto-focus the search input
            if (searchInput) {
                searchInput.focus();
                // Place cursor at the end of the text
                const length = searchInput.value.length;
                searchInput.setSelectionRange(length, length);
            }

            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(searchForm);
                const params = new URLSearchParams(formData);
                const url = '{{ route("purchase-indents.index") }}?' + params.toString();
                window.location.replace(url);
            });

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    const formData = new FormData(searchForm);
                    const params = new URLSearchParams(formData);
                    const url = '{{ route("purchase-indents.index") }}?' + params.toString();
                    window.location.replace(url);
                }, 500);
            });
        });
    </script>
    @endif

    @if($indents->count() === 0)
        <p style="margin:0; color:#666;">No purchase indents found.</p>
    @else
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:12px; text-align:center; color:#333; font-weight:600; width:50px; white-space:nowrap;">
                                <span style="display:inline-flex; align-items:center; gap:5px;">
                                    S.No
                                    @php
                                        $currentSortBy = request('sort_by', 'id');
                                        $currentSortOrder = request('sort_order', 'desc');
                                        $newSortOrder = ($currentSortBy == 'id' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="id" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'id')
                                            @if($currentSortOrder == 'desc')
                                                <i class="fas fa-sort-down"></i>
                                            @else
                                                <i class="fas fa-sort-up"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort" style="opacity:0.3;"></i>
                                        @endif
                                    </a>
                                </span>
                            </th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600; white-space:nowrap;">
                                <span style="display:inline-flex; align-items:center; gap:5px;">
                                    Purchase Indent No
                                    @php
                                        $newSortOrder = ($currentSortBy == 'indent_no' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="indent_no" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'indent_no')
                                            @if($currentSortOrder == 'desc')
                                                <i class="fas fa-sort-down"></i>
                                            @else
                                                <i class="fas fa-sort-up"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort" style="opacity:0.3;"></i>
                                        @endif
                                    </a>
                                </span>
                            </th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600; white-space:nowrap;">
                                Purchase Indent Raised By
                            </th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600; white-space:nowrap;">
                                Item
                            </th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600; white-space:nowrap;">
                                <span style="display:inline-flex; align-items:center; gap:5px;">
                                    Approval Status
                                    @php
                                        $newSortOrder = ($currentSortBy == 'status' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="status" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'status')
                                            @if($currentSortOrder == 'desc')
                                                <i class="fas fa-sort-down"></i>
                                            @else
                                                <i class="fas fa-sort-up"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort" style="opacity:0.3;"></i>
                                        @endif
                                    </a>
                                </span>
                            </th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600; white-space:nowrap;">
                                Purchase Order Status
                            </th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600; white-space:nowrap;">
                                <span style="display:inline-flex; align-items:center; gap:5px;">
                                    Purchase Indent Submission Date
                                    @php
                                        $newSortOrder = ($currentSortBy == 'created_at' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="created_at" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'created_at')
                                            @if($currentSortOrder == 'desc')
                                                <i class="fas fa-sort-down"></i>
                                            @else
                                                <i class="fas fa-sort-up"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-sort" style="opacity:0.3;"></i>
                                        @endif
                                    </a>
                                </span>
                            </th>
                            <th style="padding:12px; text-align:center; color:#333; font-weight:600; white-space:nowrap;">
                                Purchase Order Access
                            </th>
                            <th style="padding:12px; text-align:center; color:#333; font-weight:600; width:220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($indents as $index => $indent)
                            @php
                                // Get Purchase Indent Raised By (updater if approved, otherwise creator)
                                $raisedBy = $indent->status === 'Approved' && $indent->updater ? $indent->updater->name : optional($indent->creator)->name;
                                
                                // Get items list
                                $itemsList = $indent->items->map(function($item) {
                                    return optional($item->rawMaterial)->name ?? 'N/A';
                                })->unique()->take(3)->implode(', ');
                                if ($indent->items->count() > 3) {
                                    $itemsList .= ' (+' . ($indent->items->count() - 3) . ' more)';
                                }
                                if (empty($itemsList)) {
                                    $itemsList = 'No items';
                                }
                                
                                // Get Purchase Order Status (aggregate from items)
                                $poStatuses = $indent->items->pluck('po_status')->filter()->unique();
                                $poStatus = $poStatuses->count() > 0 ? $poStatuses->implode(', ') : 'Not Placed';
                                
                                // Check if purchase order exists for this indent
                                $hasPurchaseOrder = $indent->items->whereNotNull('po_status')->count() > 0;
                            @endphp
                            <tr style="border-bottom:1px solid #dee2e6;">
                                <td style="padding:10px 12px; text-align:center; color:#666;">{{ ($indents->currentPage() - 1) * $indents->perPage() + $index + 1 }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $indent->indent_no ?? 'N/A' }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $raisedBy ?? 'N/A' }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $itemsList }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $indent->status }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $poStatus }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($indent->created_at)->format('d-m-Y') ?? 'N/A' }}</td>
                                <td style="padding:10px 12px; text-align:center;">
                                    @if($indent->status === 'Approved')
                                        <a href="{{ route('purchase-orders.create', ['purchase_indent_id' => $indent->id]) }}"
                                           style="padding:6px 10px; background:#007bff; color:white; border-radius:4px; font-size:12px; text-decoration:none; display:inline-block;">
                                            Access PO
                                        </a>
                                    @else
                                        <span style="padding:6px 10px; background:#6c757d; color:white; border-radius:4px; font-size:12px; display:inline-block; cursor:not-allowed; opacity:0.6;">
                                            Access PO
                                        </span>
                                    @endif
                                </td>
                                <td style="padding:10px 12px; text-align:center;">
                                    <div style="display:flex; align-items:center; justify-content:center; gap:4px; flex-wrap:nowrap;">
                                        <a href="{{ route('purchase-indents.show', $indent->id) }}"
                                           style="padding:6px 10px; background:#17a2b8; color:white; border-radius:4px; font-size:12px; text-decoration:none; white-space:nowrap; display:inline-block;">
                                            View
                                        </a>
                                        <a href="{{ route('purchase-indents.edit', $indent->id) }}"
                                           style="padding:6px 10px; background:#ffc107; color:#212529; border-radius:4px; font-size:12px; text-decoration:none; white-space:nowrap; display:inline-block;">
                                            Edit
                                        </a>
                                        <form action="{{ route('purchase-indents.destroy', $indent->id) }}" method="POST" style="display:inline-block; margin:0;" onsubmit="return confirm('Are you sure you want to delete this purchase indent?');">
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

        <div style="margin-top:15px;" id="pagination-container">
            {{ $indents->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortLinks = document.querySelectorAll('.sort-link');
    const tableBody = document.querySelector('table tbody');
    const paginationContainer = document.getElementById('pagination-container');
    
    sortLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const sortBy = this.getAttribute('data-sort-by');
            const sortOrder = this.getAttribute('data-sort-order');
            
            if (tableBody) {
                tableBody.innerHTML = '<tr><td colspan="9" style="padding:20px; text-align:center; color:#666;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            }
            
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort_by', sortBy);
            urlParams.set('sort_order', sortOrder);
            
            fetch('{{ route("purchase-indents.index") }}?' + urlParams.toString(), {
                method: 'GET',
                headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html'},
                credentials: 'same-origin'
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableBody = doc.querySelector('table tbody');
                const newPagination = doc.querySelector('#pagination-container') || doc.querySelector('[style*="margin-top:15px"]');
                
                if (newTableBody && tableBody) tableBody.innerHTML = newTableBody.innerHTML;
                if (newPagination && paginationContainer) paginationContainer.innerHTML = newPagination.innerHTML;
                
                window.history.pushState({}, '', '{{ route("purchase-indents.index") }}?' + urlParams.toString());
                updateSortIcons(sortBy, sortOrder);
            })
            .catch(error => {
                console.error('Error:', error);
                if (tableBody) tableBody.innerHTML = '<tr><td colspan="9" style="padding:20px; text-align:center; color:#dc3545;">Error loading data.</td></tr>';
            });
        });
    });
    
    function updateSortIcons(activeSortBy, activeSortOrder) {
        sortLinks.forEach(link => {
            const sortBy = link.getAttribute('data-sort-by');
            const icon = link.querySelector('i');
            if (sortBy === activeSortBy) {
                icon.className = activeSortOrder === 'desc' ? 'fas fa-sort-down' : 'fas fa-sort-up';
                icon.style.opacity = '1';
                link.setAttribute('data-sort-order', activeSortOrder === 'desc' ? 'asc' : 'desc');
            } else {
                icon.className = 'fas fa-sort';
                icon.style.opacity = '0.3';
            }
        });
    }
});
</script>
@endpush
@endsection


