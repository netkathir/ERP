@extends('layouts.dashboard')

@section('title', 'Processes - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Processes</h2>
        <a href="{{ route('processes.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Add Process
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Search Section - Only show if there are items or a search query is active -->
    @if($processes->count() > 0 || request('search'))
    <form method="GET" action="{{ route('processes.index') }}" id="searchForm" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: flex; gap: 15px; align-items: end;">
            <div style="flex: 1;">
                <label for="search" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Search:</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Search by Process Name, Description, Date (dd-mm-yyyy)...">
            </div>
            <div>
                <a href="{{ route('processes.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center;">
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

            if (searchInput) {
                searchInput.focus();
                const length = searchInput.value.length;
                searchInput.setSelectionRange(length, length);
            }

            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(searchForm);
                const params = new URLSearchParams(formData);
                const url = '{{ route("processes.index") }}?' + params.toString();
                window.location.replace(url);
            });

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    const formData = new FormData(searchForm);
                    const params = new URLSearchParams(formData);
                    const url = '{{ route("processes.index") }}?' + params.toString();
                    window.location.replace(url);
                }, 500);
            });
        });
    </script>
    @endif

    @if($processes->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; white-space:nowrap;">
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
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; white-space:nowrap;">
                            <span style="display:inline-flex; align-items:center; gap:5px;">
                                Process Name
                                @php
                                    $newSortOrder = ($currentSortBy == 'name' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                @endphp
                                <a href="#" class="sort-link" data-sort-by="name" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                    @if($currentSortBy == 'name')
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
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Description</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($processes as $process)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($processes->currentPage() - 1) * $processes->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $process->name }}</td>
                            <td style="padding: 12px; color: #666;">{{ $process->description ?? 'N/A' }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('processes.edit', $process->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('processes.destroy', $process->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this process?');">
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
            {{ $processes->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No processes found.</p>
            <a href="{{ route('processes.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Create First Process
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
                tableBody.innerHTML = '<tr><td colspan="4" style="padding:20px; text-align:center; color:#666;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
            }
            
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort_by', sortBy);
            urlParams.set('sort_order', sortOrder);
            
            fetch('{{ route("processes.index") }}?' + urlParams.toString(), {
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
                
                window.history.pushState({}, '', '{{ route("processes.index") }}?' + urlParams.toString());
                updateSortIcons(sortBy, sortOrder);
            })
            .catch(error => {
                console.error('Error:', error);
                if (tableBody) tableBody.innerHTML = '<tr><td colspan="4" style="padding:20px; text-align:center; color:#dc3545;">Error loading data.</td></tr>';
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

