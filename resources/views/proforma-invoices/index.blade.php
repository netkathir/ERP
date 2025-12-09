@extends('layouts.dashboard')

@section('title', 'Proforma Invoices - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Proforma Invoices</h2>
        <a href="{{ route('proforma-invoices.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Create Proforma Invoice
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Search Section - Only show if there are items or a search query is active -->
    @if($invoices->count() > 0 || request('search'))
    <form method="GET" action="{{ route('proforma-invoices.index') }}" id="searchForm" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <div style="display: flex; gap: 15px; align-items: end;">
            <div style="flex: 1;">
                <label for="search" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Search:</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                    placeholder="Search by Invoice No, Customer, Date (dd-mm-yyyy)...">
            </div>
            <div>
                <a href="{{ route('proforma-invoices.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center;">
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
                const url = '{{ route("proforma-invoices.index") }}?' + params.toString();
                window.location.replace(url);
            });

            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    const formData = new FormData(searchForm);
                    const params = new URLSearchParams(formData);
                    const url = '{{ route("proforma-invoices.index") }}?' + params.toString();
                    window.location.replace(url);
                }, 500);
            });
        });
    </script>
    @endif

    @if($invoices->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 50px;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Invoice No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Date</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Customer</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Gross Amount</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Net Amount</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $index => $invoice)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; text-align: center; color: #666;">{{ ($invoices->currentPage() - 1) * $invoices->perPage() + $index + 1 }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $invoice->invoice_no }}</td>
                            <td style="padding: 12px; color: #666;">{{ $invoice->date }}</td>
                            <td style="padding: 12px; color: #666;">{{ $invoice->customer->company_name }}</td>
                            <td style="padding: 12px; text-align: right; color: #666;">₹{{ number_format($invoice->gross_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: right; color: #333; font-weight: 500;">₹{{ number_format($invoice->net_amount, 2) }}</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('proforma-invoices.show', $invoice->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('proforma-invoices.edit', $invoice->id) }}" style="padding: 6px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('proforma-invoices.destroy', $invoice->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this proforma invoice?');">
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

        <div style="margin-top: 20px;">
            {{ $invoices->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No proforma invoices found.</p>
            <a href="{{ route('proforma-invoices.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Create First Proforma Invoice
            </a>
        </div>
    @endif
</div>
@endsection

