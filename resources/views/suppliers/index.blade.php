@extends('layouts.dashboard')

@section('title', 'Suppliers - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Suppliers</h2>
        <a href="{{ route('suppliers.create') }}" style="padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Add Supplier
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search Section - Only show if there are items or a search query is active -->
    @if($suppliers->count() > 0 || request('search'))
    <form method="GET" action="{{ route('suppliers.index') }}" id="searchForm" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: flex; gap: 15px; align-items: end;">
            <div style="flex: 1;">
                <label for="search" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Search:</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Search by Supplier Name, City, Type, Code, Date (dd-mm-yyyy)...">
            </div>
            <div>
                <a href="{{ route('suppliers.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center;">
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
                const length = searchInput.value.length;
                searchInput.setSelectionRange(length, length);
            }

            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(searchForm);
                const params = new URLSearchParams(formData);
                const url = '{{ route("suppliers.index") }}?' + params.toString();
                window.location.replace(url);
            });

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    const formData = new FormData(searchForm);
                    const params = new URLSearchParams(formData);
                    const url = '{{ route("suppliers.index") }}?' + params.toString();
                    window.location.replace(url);
                }, 500);
            });
        });
    </script>
    @endif

    @if($suppliers->count() === 0)
        <p style="margin:0;color:#666;">No suppliers found.</p>
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
                                    City
                                    @php
                                        $newSortOrder = ($currentSortBy == 'city' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="city" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'city')
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
                                    Type
                                    @php
                                        $newSortOrder = ($currentSortBy == 'supplier_type' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="supplier_type" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'supplier_type')
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
                                    Code
                                    @php
                                        $newSortOrder = ($currentSortBy == 'code' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="code" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'code')
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
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Audit Frequency</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Qms Status</th>
                            <th style="padding:12px; text-align:center; color:#333; font-weight:600; width:200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suppliers as $index => $supplier)
                            <tr style="border-bottom:1px solid #dee2e6;">
                                <td style="padding:10px 12px; text-align:center; color:#666;">{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $index + 1 }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $supplier->supplier_name }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $supplier->city }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $supplier->supplier_type }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $supplier->code }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $supplier->audit_frequency }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $supplier->qms_status }}</td>
                                <td style="padding:10px 12px; text-align:center;">
                                    <a href="{{ route('suppliers.show', $supplier->id) }}"
                                       style="padding:6px 10px; background:#17a2b8; color:white; border-radius:4px; font-size:12px; text-decoration:none; margin-right:4px;">
                                        View
                                    </a>
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}"
                                       style="padding:6px 10px; background:#ffc107; color:#212529; border-radius:4px; font-size:12px; text-decoration:none; margin-right:4px;">
                                        Edit
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this supplier?');">
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
            {{ $suppliers->links() }}
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
                tableBody.innerHTML = '<tr><td colspan="8" style="padding:20px; text-align:center; color:#666;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            }
            
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort_by', sortBy);
            urlParams.set('sort_order', sortOrder);
            
            fetch('{{ route("suppliers.index") }}?' + urlParams.toString(), {
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
                
                window.history.pushState({}, '', '{{ route("suppliers.index") }}?' + urlParams.toString());
                updateSortIcons(sortBy, sortOrder);
            })
            .catch(error => {
                console.error('Error:', error);
                if (tableBody) tableBody.innerHTML = '<tr><td colspan="8" style="padding:20px; text-align:center; color:#dc3545;">Error loading data.</td></tr>';
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


