@extends('layouts.dashboard')

@section('title', 'QC Material Inward')

@section('content')
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">QC Material Inward</h2>
        <div style="display:flex; gap:10px; align-items:center;">
            <form method="GET" action="{{ route('qc-material-inwards.index') }}" id="searchForm" style="margin:0; display:flex; gap:10px;">
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    style="padding:8px 12px; border:1px solid #ddd; border-radius:5px; font-size:14px; width:250px;"
                    placeholder="Search...">
                @if(request('sort_by'))
                    <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                @endif
                @if(request('sort_order'))
                    <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
                @endif
            </form>
            <button type="button" id="viewBtn" onclick="viewSelected()"
                style="padding:10px 20px; background:#17a2b8; color:white; border:none; border-radius:5px; font-weight:500; cursor:pointer; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-eye"></i> View
            </button>
            <a href="{{ route('qc-material-inwards.create') }}"
               style="padding:10px 20px; background:#667eea; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
                <i class="fas fa-plus"></i> Add
            </a>
        </div>
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

    @if($qcMaterialInwards->count() === 0)
        <p style="margin:0; color:#666;">No QC material inwards found.</p>
    @else
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:12px; text-align:center; color:#333; font-weight:600; width:50px;">
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" style="cursor:pointer;">
                            </th>
                            <th style="padding:12px; text-align:center; color:#333; font-weight:600; white-space:nowrap;">
                                <span style="display:inline-flex; align-items:center; gap:5px;">
                                    S.No
                                </span>
                            </th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600; white-space:nowrap;">
                                <span style="display:inline-flex; align-items:center; gap:5px;">
                                    QC Material No
                                    @php
                                        $currentSortBy = request('sort_by', 'created_at');
                                        $currentSortOrder = request('sort_order', 'desc');
                                        $newSortOrder = ($currentSortBy == 'qc_material_no' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="qc_material_no" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'qc_material_no')
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
                                    MRN No
                                    @php
                                        $newSortOrder = ($currentSortBy == 'mrn_no' && $currentSortOrder == 'desc') ? 'asc' : 'desc';
                                    @endphp
                                    <a href="#" class="sort-link" data-sort-by="mrn_no" data-sort-order="{{ $newSortOrder }}" style="text-decoration:none; color:#333; display:inline-flex; align-items:center;">
                                        @if($currentSortBy == 'mrn_no')
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
                                    Created On
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($qcMaterialInwards as $index => $qcMaterialInward)
                            <tr style="border-bottom:1px solid #dee2e6;">
                                <td style="padding:10px 12px; text-align:center;">
                                    <input type="checkbox" class="row-checkbox" value="{{ $qcMaterialInward->id }}" onchange="updateViewButton()" style="cursor:pointer;">
                                </td>
                                <td style="padding:10px 12px; text-align:center; color:#666;">{{ ($qcMaterialInwards->currentPage() - 1) * $qcMaterialInwards->perPage() + $index + 1 }}</td>
                                <td style="padding:10px 12px; color:#333;">
                                    <a href="{{ route('qc-material-inwards.show', $qcMaterialInward->id) }}" style="color:#667eea; text-decoration:none; font-weight:500;">
                                        {{ $qcMaterialInward->qc_material_no }}
                                    </a>
                                </td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($qcMaterialInward->materialInward)->material_inward_no ?? 'N/A' }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($qcMaterialInward->purchaseOrder)->po_no ?? 'N/A' }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($qcMaterialInward->supplier)->supplier_name ?? 'N/A' }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($qcMaterialInward->created_at)->setTimezone('Asia/Kolkata')->format('d-m-Y h:i A') ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div style="margin-top:15px;">
            {{ $qcMaterialInwards->links() }}
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('search');
        const searchForm = document.getElementById('searchForm');
        let searchTimeout;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    searchForm.submit();
                }, 500);
            });
        }

        // Sorting functionality
        document.querySelectorAll('.sort-link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const sortBy = this.getAttribute('data-sort-by');
                const sortOrder = this.getAttribute('data-sort-order');
                
                const url = new URL(window.location.href);
                url.searchParams.set('sort_by', sortBy);
                url.searchParams.set('sort_order', sortOrder);
                
                window.location.href = url.toString();
            });
        });
    });

    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(function(checkbox) {
            checkbox.checked = selectAll.checked;
        });
        updateViewButton();
    }

    function updateViewButton() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const viewBtn = document.getElementById('viewBtn');
        if (viewBtn) {
            viewBtn.style.opacity = checkedBoxes.length === 1 ? '1' : '0.6';
            viewBtn.style.cursor = checkedBoxes.length === 1 ? 'pointer' : 'not-allowed';
        }
    }

    function viewSelected() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Please select a record to view.');
            return;
        }
        if (checkedBoxes.length > 1) {
            alert('Please select only one record to view.');
            return;
        }
        const id = checkedBoxes[0].value;
        window.location.href = '{{ route("qc-material-inwards.show", ":id") }}'.replace(':id', id);
    }

    // Initialize view button state
    updateViewButton();
</script>
@endsection

