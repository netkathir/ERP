@extends('layouts.dashboard')

@section('title', 'Subcontractor Evaluations - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Subcontractor Evaluations</h2>
        <a href="{{ route('subcontractor-evaluations.create') }}"
           style="padding:10px 20px; background:#667eea; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-plus"></i> New Evaluation
        </a>
    </div>

    @if(session('success'))
        <div style="background:#d4edda; color:#155724; padding:12px 15px; border-radius:5px; margin-bottom:20px; border:1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search Section - Only show if there are items or a search query is active -->
    @if($evaluations->count() > 0 || request('search'))
    <form method="GET" action="{{ route('subcontractor-evaluations.index') }}" id="searchForm" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: flex; gap: 15px; align-items: end;">
            <div style="flex: 1;">
                <label for="search" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Search:</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Search by ID, Subcontractor, Contact Person, Total Score, Assessment Result, Status, Date...">
            </div>
            <div>
                <a href="{{ route('subcontractor-evaluations.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center;">
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
                const url = '{{ route("subcontractor-evaluations.index") }}?' + params.toString();
                window.location.replace(url);
            });

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    const formData = new FormData(searchForm);
                    const params = new URLSearchParams(formData);
                    const url = '{{ route("subcontractor-evaluations.index") }}?' + params.toString();
                    window.location.replace(url);
                }, 500);
            });
        });
    </script>
    @endif

    @if($evaluations->count() === 0)
        <p style="margin:0; color:#666;">No subcontractor evaluations found.</p>
    @else
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">ID</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Subcontractor</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Contact Person</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Total Score</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Assessment Result</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Status</th>
                            <th style="padding:12px; text-align:center; color:#333; font-weight:600; width:200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluations as $evaluation)
                            <tr style="border-bottom:1px solid #dee2e6;">
                                <td style="padding:10px 12px; color:#333;">{{ $evaluation->id }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ optional($evaluation->supplier)->supplier_name }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $evaluation->contact_person }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $evaluation->total_score }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $evaluation->assessment_result }}</td>
                                <td style="padding:10px 12px; color:#333;">{{ $evaluation->status }}</td>
                                <td style="padding:10px 12px; text-align:center;">
                                    <a href="{{ route('subcontractor-evaluations.show', $evaluation->id) }}"
                                       style="padding:6px 10px; background:#17a2b8; color:white; border-radius:4px; font-size:12px; text-decoration:none; margin-right:4px;">
                                        View
                                    </a>
                                    <a href="{{ route('subcontractor-evaluations.edit', $evaluation->id) }}"
                                       style="padding:6px 10px; background:#ffc107; color:#212529; border-radius:4px; font-size:12px; text-decoration:none; margin-right:4px;">
                                        Edit
                                    </a>
                                    <form action="{{ route('subcontractor-evaluations.destroy', $evaluation->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this evaluation?');">
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

        <div style="margin-top:15px;">
            {{ $evaluations->links() }}
        </div>
    @endif
</div>
@endsection


