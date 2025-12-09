@extends('layouts.dashboard')

@section('title', 'Tender Evaluations - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Tender Evaluations</h2>
        <a href="{{ route('tender-evaluations.create') }}" style="padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> New Evaluation
        </a>
    </div>

    <!-- Search Section - Only show if there are items or a search query is active -->
    @if($evaluations->count() > 0 || request('search'))
    <form method="GET" action="{{ route('tender-evaluations.index') }}" id="searchForm" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: flex; gap: 15px; align-items: end;">
            <div style="flex: 1;">
                <label for="search" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Search:</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Search by Tender No, Date (dd-mm-yyyy)...">
            </div>
            <div>
                <a href="{{ route('tender-evaluations.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center;">
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
                const url = '{{ route("tender-evaluations.index") }}?' + params.toString();
                window.location.replace(url);
            });

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    const formData = new FormData(searchForm);
                    const params = new URLSearchParams(formData);
                    const url = '{{ route("tender-evaluations.index") }}?' + params.toString();
                    window.location.replace(url);
                }, 500);
            });
        });
    </script>
    @endif

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 50px;">S.No</th>
                    <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Tender No</th>
                    <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Closing Date &amp; Time</th>
                    <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Evaluation Document</th>
                    <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Evaluated On</th>
                    <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($evaluations as $index => $evaluation)
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px; text-align: center; color: #333;">
                            {{ ($evaluations->currentPage() - 1) * $evaluations->perPage() + $index + 1 }}
                        </td>
                        <td style="padding: 10px; color: #333;">
                            {{ optional($evaluation->tender)->tender_no ?? '-' }}
                        </td>
                        <td style="padding: 10px; color: #555;">
                            @if(optional($evaluation->tender)->closing_date_time)
                                {{ optional($evaluation->tender->closing_date_time)->format('d-m-Y H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 10px;">
                            @if($evaluation->evaluation_document)
                                <a href="{{ asset('storage/' . $evaluation->evaluation_document) }}" target="_blank" style="color: #667eea; text-decoration: none; font-weight: 500;">
                                    <i class="fas fa-file-download"></i> View
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding: 10px; color: #555;">
                            {{ optional($evaluation->created_at)->format('d-m-Y H:i') }}
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('tender-evaluations.show', $evaluation->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('tender-evaluations.edit', $evaluation->id) }}" style="padding: 6px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form action="{{ route('tender-evaluations.destroy', $evaluation->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this evaluation?');" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 12px; text-align: center; color: #777;">
                            No tender evaluations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $evaluations->links() }}
    </div>
</div>
@endsection


