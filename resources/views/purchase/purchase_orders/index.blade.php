@extends('layouts.dashboard')

@section('title', 'Purchase Orders')

@section('content')
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Purchase Orders</h2>
        <a href="{{ route('purchase-orders.create') }}"
           style="padding:10px 20px; background:#667eea; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-plus"></i> New Purchase Order
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
    @if($purchaseOrders->count() > 0 || request('search'))
    <form method="GET" action="{{ route('purchase-orders.index') }}" id="searchForm" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: flex; gap: 15px; align-items: end;">
            <div style="flex: 1;">
                <label for="search" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Search:</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Search by PO No, Indent No, Date (dd-mm-yyyy)...">
            </div>
            <div>
                <a href="{{ route('purchase-orders.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center;">
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
                const url = '{{ route("purchase-orders.index") }}?' + params.toString();
                window.location.replace(url);
            });

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    const formData = new FormData(searchForm);
                    const params = new URLSearchParams(formData);
                    const url = '{{ route("purchase-orders.index") }}?' + params.toString();
                    window.location.replace(url);
                }, 500);
            });
        });
    </script>
    @endif

    @if($purchaseOrders->count() === 0)
        <p style="margin:0; color:#666;">No purchase orders found.</p>
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
                                    Purchase Order No
                                    @php
                                        $newSortOrder = ($currentSortBy == 'po_no' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="po_no" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'po_no')
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
                                    Supplier Name
                                    @php
                                        $newSortOrder = ($currentSortBy == 'supplier_name' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="supplier_name" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'supplier_name')
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
                                        $newSortOrder = ($currentSortBy == 'purchase_indent_no' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="purchase_indent_no" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'purchase_indent_no')
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
                                    Material Inward Status
                                    @php
                                        $newSortOrder = ($currentSortBy == 'material_inward_status' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="material_inward_status" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'material_inward_status')
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
                                    Purchase Indent Raised User Name
                                    @php
                                        $newSortOrder = ($currentSortBy == 'purchase_indent_raised_user' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="purchase_indent_raised_user" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'purchase_indent_raised_user')
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
                            <th style="padding:12px; text-align:center; color:#333; font-weight:600; width:220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseOrders as $index => $po)
                            @php
                                // Calculate Material Inward Status based on PO Qty vs Received Qty
                                $allYetToReceive = true;
                                $allFullyReceived = true;
                                $hasAnyReceived = false;
                                
                                foreach ($po->items as $poItem) {
                                    // Sum total received_qty for this PO item from all Material Inward Items
                                    $totalReceivedQty = (float) \App\Models\MaterialInwardItem::where('purchase_order_item_id', $poItem->id)
                                        ->sum('received_qty');
                                    
                                    $poQty = (float) $poItem->po_quantity;
                                    
                                    if ($totalReceivedQty > 0) {
                                        $allYetToReceive = false;
                                        $hasAnyReceived = true;
                                    }
                                    
                                    if ($totalReceivedQty != $poQty) {
                                        $allFullyReceived = false;
                                    }
                                }
                                
                                // Determine overall status
                                if ($po->items->isEmpty()) {
                                    $materialInwardStatus = 'N/A';
                                } elseif ($allYetToReceive) {
                                    $materialInwardStatus = 'Yet to Receive';
                                } elseif ($allFullyReceived) {
                                    $materialInwardStatus = 'Fully Received';
                                } else {
                                    $materialInwardStatus = 'Partially Received';
                                }
                            @endphp
                            <tr style="border-bottom:1px solid #dee2e6;">
                                <td style="padding:10px 12px; text-align:center; color:#666;">{{ ($purchaseOrders->currentPage() - 1) * $purchaseOrders->perPage() + $index + 1 }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $po->po_no }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($po->supplier)->supplier_name ?? 'N/A' }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($po->purchaseIndent)->indent_no ?? 'N/A' }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $materialInwardStatus }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional(optional($po->purchaseIndent)->creator)->name ?? 'N/A' }}</td>
                                <td style="padding:10px 12px; text-align:center;">
                                    <a href="{{ route('purchase-orders.show', $po->id) }}"
                                       style="padding:6px 10px; background:#17a2b8; color:white; border-radius:4px; font-size:12px; text-decoration:none; margin-right:4px;">
                                        View
                                    </a>
                                    <a href="{{ route('purchase-orders.edit', $po->id) }}"
                                       style="padding:6px 10px; background:#ffc107; color:#212529; border-radius:4px; font-size:12px; text-decoration:none; margin-right:4px;">
                                        Edit
                                    </a>
                                    <form action="{{ route('purchase-orders.destroy', $po->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this purchase order?');">
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

        <div style="margin-top:15px;" id="pagination-container">
            {{ $purchaseOrders->links() }}
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
            
            // Show loading state
            if (tableBody) {
                const originalContent = tableBody.innerHTML;
                tableBody.innerHTML = '<tr><td colspan="7" style="padding:20px; text-align:center; color:#666;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            }
            
            // Get current URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort_by', sortBy);
            urlParams.set('sort_order', sortOrder);
            
            // Make AJAX request
            fetch('{{ route("purchase-orders.index") }}?' + urlParams.toString(), {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
                credentials: 'same-origin'
            })
            .then(response => response.text())
            .then(html => {
                // Parse the response HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Extract table body content
                const newTableBody = doc.querySelector('table tbody');
                const newPagination = doc.querySelector('#pagination-container') || doc.querySelector('[style*="margin-top:15px"]');
                
                if (newTableBody && tableBody) {
                    tableBody.innerHTML = newTableBody.innerHTML;
                }
                
                // Update pagination
                if (newPagination && paginationContainer) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                }
                
                // Update URL without reload
                window.history.pushState({}, '', '{{ route("purchase-orders.index") }}?' + urlParams.toString());
                
                // Update sort icons in headers
                updateSortIcons(sortBy, sortOrder);
                
                // Re-attach event listeners to new sort links (in case pagination changed)
                attachSortListeners();
            })
            .catch(error => {
                console.error('Error sorting data:', error);
                if (tableBody) {
                    tableBody.innerHTML = '<tr><td colspan="7" style="padding:20px; text-align:center; color:#dc3545;">Error loading data. Please refresh the page.</td></tr>';
                }
            });
        });
    });
    
    function updateSortIcons(activeSortBy, activeSortOrder) {
        sortLinks.forEach(link => {
            const sortBy = link.getAttribute('data-sort-by');
            const icon = link.querySelector('i');
            
            if (sortBy === activeSortBy) {
                // Update to show active sort direction
                if (activeSortOrder === 'desc') {
                    icon.className = 'fas fa-sort-down';
                } else {
                    icon.className = 'fas fa-sort-up';
                }
                icon.style.opacity = '1';
                // Update data attribute for next click
                link.setAttribute('data-sort-order', activeSortOrder === 'desc' ? 'asc' : 'desc');
            } else {
                // Reset to inactive state
                icon.className = 'fas fa-sort';
                icon.style.opacity = '0.3';
            }
        });
    }
    
    function attachSortListeners() {
        const newSortLinks = document.querySelectorAll('.sort-link');
        newSortLinks.forEach(link => {
            if (!link.hasAttribute('data-listener-attached')) {
                link.setAttribute('data-listener-attached', 'true');
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const sortBy = this.getAttribute('data-sort-by');
                    const sortOrder = this.getAttribute('data-sort-order');
                    
                    // Trigger the same sorting logic
                    const urlParams = new URLSearchParams(window.location.search);
                    urlParams.set('sort_by', sortBy);
                    urlParams.set('sort_order', sortOrder);
                    
                    window.location.href = '{{ route("purchase-orders.index") }}?' + urlParams.toString();
                });
            }
        });
    }
});
</script>
@endpush
@endsection

