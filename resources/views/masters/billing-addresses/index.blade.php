@extends('layouts.dashboard')

@section('title', 'Billing Addresses - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Billing Addresses</h2>
        <a href="{{ route('billing-addresses.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Add Billing Address
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

    <!-- Search and Filter Section -->
    <form method="GET" action="{{ route('billing-addresses.index') }}" id="searchForm" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 15px; align-items: end;">
            <div>
                <label for="search" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Search (Company Name, Address, Pincode)</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Search...">
            </div>
            <div>
                <label for="filter_city" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Filter by City</label>
                <select name="filter_city" id="filter_city"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <option value="">All Cities</option>
                    @foreach($cities as $city)
                        <option value="{{ $city }}" {{ request('filter_city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filter_state" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Filter by State</label>
                <select name="filter_state" id="filter_state"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <option value="">All States</option>
                    @foreach($states as $state)
                        <option value="{{ $state }}" {{ request('filter_state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="filter_company" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Filter by Company</label>
                <input type="text" name="filter_company" id="filter_company" value="{{ request('filter_company') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Company Name">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="padding: 10px 20px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                    <i class="fas fa-search"></i> Search
                </button>
                <a href="{{ route('billing-addresses.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center;">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </div>
        <!-- Preserve sort and pagination parameters -->
        @if(request('sort'))
            <input type="hidden" name="sort" value="{{ request('sort') }}">
        @endif
        @if(request('direction'))
            <input type="hidden" name="direction" value="{{ request('direction') }}">
        @endif
        @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif
    </form>

    <!-- Bulk Actions -->
    <form method="POST" action="{{ route('billing-addresses.bulk-delete') }}" id="bulkDeleteForm" style="margin-bottom: 15px; display: none;">
        @csrf
        <button type="submit" onclick="return confirm('Are you sure you want to delete selected billing addresses?');" 
            style="padding: 8px 16px; background: #dc3545; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
            <i class="fas fa-trash"></i> Delete Selected
        </button>
    </form>

    @if($billingAddresses->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600; width: 40px;">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">
                            <a href="{{ route('billing-addresses.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'id', 'direction' => request('sort') == 'id' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" style="text-decoration: none; color: #333;">
                                S.No
                                @if(request('sort') == 'id')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort" style="color: #ccc;"></i>
                                @endif
                            </a>
                        </th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">
                            <a href="{{ route('billing-addresses.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'company_name', 'direction' => request('sort') == 'company_name' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" style="text-decoration: none; color: #333;">
                                Company Name
                                @if(request('sort') == 'company_name')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort" style="color: #ccc;"></i>
                                @endif
                            </a>
                        </th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">
                            <a href="{{ route('billing-addresses.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'address_line_1', 'direction' => request('sort') == 'address_line_1' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" style="text-decoration: none; color: #333;">
                                Address Line 1
                                @if(request('sort') == 'address_line_1')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort" style="color: #ccc;"></i>
                                @endif
                            </a>
                        </th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">
                            <a href="{{ route('billing-addresses.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'city', 'direction' => request('sort') == 'city' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" style="text-decoration: none; color: #333;">
                                City
                                @if(request('sort') == 'city')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort" style="color: #ccc;"></i>
                                @endif
                            </a>
                        </th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">
                            <a href="{{ route('billing-addresses.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'state', 'direction' => request('sort') == 'state' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" style="text-decoration: none; color: #333;">
                                State
                                @if(request('sort') == 'state')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort" style="color: #ccc;"></i>
                                @endif
                            </a>
                        </th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">
                            <a href="{{ route('billing-addresses.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'pincode', 'direction' => request('sort') == 'pincode' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" style="text-decoration: none; color: #333;">
                                Pincode
                                @if(request('sort') == 'pincode')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort" style="color: #ccc;"></i>
                                @endif
                            </a>
                        </th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">
                            <a href="{{ route('billing-addresses.index', array_merge(request()->except(['sort', 'direction']), ['sort' => 'email', 'direction' => request('sort') == 'email' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" style="text-decoration: none; color: #333;">
                                Email
                                @if(request('sort') == 'email')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fas fa-sort" style="color: #ccc;"></i>
                                @endif
                            </a>
                        </th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($billingAddresses as $billingAddress)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; text-align: center;">
                                <input type="checkbox" name="ids[]" value="{{ $billingAddress->id }}" class="row-checkbox" onchange="updateBulkDeleteButton()">
                            </td>
                            <td style="padding: 12px; color: #666;">{{ ($billingAddresses->currentPage() - 1) * $billingAddresses->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $billingAddress->company_name }}</td>
                            <td style="padding: 12px; color: #666;">{{ \Illuminate\Support\Str::limit($billingAddress->address_line_1, 30) }}</td>
                            <td style="padding: 12px; color: #666;">{{ $billingAddress->city }}</td>
                            <td style="padding: 12px; color: #666;">{{ $billingAddress->state }}</td>
                            <td style="padding: 12px; color: #666;">{{ $billingAddress->pincode }}</td>
                            <td style="padding: 12px; color: #666;">{{ $billingAddress->email }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('billing-addresses.show', $billingAddress->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('billing-addresses.edit', $billingAddress->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('billing-addresses.destroy', $billingAddress->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this billing address?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination and Records Per Page -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px; flex-wrap: wrap; gap: 15px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="color: #666;">Records per page:</span>
                <select name="per_page" id="per_page" onchange="changePerPage(this.value)" style="padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                    <option value="20" {{ request('per_page', 15) == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="color: #666;">Go to page:</span>
                <input type="number" id="goToPage" min="1" max="{{ $billingAddresses->lastPage() }}" value="{{ $billingAddresses->currentPage() }}" 
                    style="width: 60px; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                <button onclick="goToPage()" style="padding: 8px 16px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">Go</button>
            </div>
            <div>
                {{ $billingAddresses->appends(request()->except('page'))->links() }}
            </div>
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No billing addresses found.</p>
            <a href="{{ route('billing-addresses.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Create First Billing Address
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
        updateBulkDeleteButton();
    }

    function updateBulkDeleteButton() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const bulkDeleteForm = document.getElementById('bulkDeleteForm');
        
        if (checkboxes.length > 0) {
            bulkDeleteForm.style.display = 'block';
            // Update form with selected IDs
            const form = bulkDeleteForm;
            const existingInputs = form.querySelectorAll('input[name="ids[]"]');
            existingInputs.forEach(input => input.remove());
            
            checkboxes.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });
        } else {
            bulkDeleteForm.style.display = 'none';
        }
    }

    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.delete('page'); // Reset to first page
        window.location.href = url.toString();
    }

    function goToPage() {
        const page = document.getElementById('goToPage').value;
        const url = new URL(window.location.href);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    }
</script>
@endpush
@endsection

