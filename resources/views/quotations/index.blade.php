@extends('layouts.dashboard')

@section('title', 'Quotations - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Quotations</h2>
        <a href="{{ route('quotations.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Create Quotation
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search Section - Only show if there are items or a search query is active -->
    @if($quotations->count() > 0 || request('search'))
    <form method="GET" action="{{ route('quotations.index') }}" id="searchForm" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: flex; gap: 15px; align-items: end;">
            <div style="flex: 1;">
                <label for="search" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Search:</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Search by Quotation No, Customer, Date (dd-mm-yyyy)...">
            </div>
            <div>
                <a href="{{ route('quotations.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center;">
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
                const url = '{{ route("quotations.index") }}?' + params.toString();
                window.location.replace(url);
            });

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    const formData = new FormData(searchForm);
                    const params = new URLSearchParams(formData);
                    const url = '{{ route("quotations.index") }}?' + params.toString();
                    window.location.replace(url);
                }, 500);
            });
        });
    </script>
    @endif

    @if($quotations->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 50px; white-space:nowrap;">
                            <span style="display:inline-flex; align-items:center; gap:5px; justify-content:center;">
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
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; white-space:nowrap;">
                            <span style="display:inline-flex; align-items:center; gap:5px;">
                                Quotation No
                                @php
                                    $newSortOrder = ($currentSortBy == 'quotation_no' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                @endphp
                                <a href="#" class="sort-link" data-sort-by="quotation_no" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                    @if($currentSortBy == 'quotation_no')
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
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; white-space:nowrap;">
                            <span style="display:inline-flex; align-items:center; gap:5px;">
                                Date
                                @php
                                    $newSortOrder = ($currentSortBy == 'quotation_date' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                @endphp
                                <a href="#" class="sort-link" data-sort-by="quotation_date" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                    @if($currentSortBy == 'quotation_date')
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
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; white-space:nowrap;">
                            <span style="display:inline-flex; align-items:center; gap:5px;">
                                Customer
                                @php
                                    $newSortOrder = ($currentSortBy == 'customer_name' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                @endphp
                                <a href="#" class="sort-link" data-sort-by="customer_name" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                    @if($currentSortBy == 'customer_name')
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
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Total Amount</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Net Amount</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quotations as $index => $quotation)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; text-align: center; color: #666;">{{ ($quotations->currentPage() - 1) * $quotations->perPage() + $index + 1 }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $quotation->quotation_no }}</td>
                            <td style="padding: 12px; color: #666;">
                                {{ optional($quotation->date)->format('d-m-Y') }}
                            </td>
                            <td style="padding: 12px; color: #666;">{{ $quotation->customer->company_name }}</td>
                            <td style="padding: 12px; text-align: right; color: #666;">₹{{ number_format($quotation->total_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">₹{{ number_format($quotation->net_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('quotations.show', $quotation->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('quotations.edit', $quotation->id) }}" style="padding: 6px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('quotations.destroy', $quotation->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this quotation?');">
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

        <div style="margin-top: 20px;" id="pagination-container">
            {{ $quotations->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No quotations found.</p>
            <a href="{{ route('quotations.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Create First Quotation
            </a>
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
                tableBody.innerHTML = '<tr><td colspan="7" style="padding:20px; text-align:center; color:#666;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            }
            
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort_by', sortBy);
            urlParams.set('sort_order', sortOrder);
            
            fetch('{{ route("quotations.index") }}?' + urlParams.toString(), {
                method: 'GET',
                headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html'},
                credentials: 'same-origin'
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableBody = doc.querySelector('table tbody');
                const newPagination = doc.querySelector('#pagination-container') || doc.querySelector('[style*="margin-top:20px"]');
                
                if (newTableBody && tableBody) tableBody.innerHTML = newTableBody.innerHTML;
                if (newPagination && paginationContainer) paginationContainer.innerHTML = newPagination.innerHTML;
                
                window.history.pushState({}, '', '{{ route("quotations.index") }}?' + urlParams.toString());
                updateSortIcons(sortBy, sortOrder);
            })
            .catch(error => {
                console.error('Error:', error);
                if (tableBody) tableBody.innerHTML = '<tr><td colspan="7" style="padding:20px; text-align:center; color:#dc3545;">Error loading data.</td></tr>';
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
