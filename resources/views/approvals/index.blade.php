@extends('layouts.dashboard')

@section('title', 'Pending Approvals - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Pending Approvals</h2>
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

    @if(session('info'))
        <div style="background: #d1ecf1; color: #0c5460; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #bee5eb;">
            {{ session('info') }}
        </div>
    @endif

    <!-- Pending Records -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">
                Pending Customer Orders
            </h3>
        </div>
        <div style="padding: 20px; overflow-x: auto;">
            @if($formName === 'customer_orders')
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Order No</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Order Date</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Tender No</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Net Amount</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingRecords as $order)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px; color: #333;">{{ $order->order_no }}</td>
                                <td style="padding: 12px; color: #333;">{{ optional($order->order_date)->format('d-m-Y') }}</td>
                                <td style="padding: 12px; color: #333;">{{ optional($order->tender)->tender_no }}</td>
                                <td style="padding: 12px; text-align: right; color: #333;">₹ {{ number_format($order->net_amount ?? 0, 2) }}</td>
                                <td style="padding: 12px; text-align: center;">
                                    <a href="{{ route('customer-orders.show', $order->id) }}" target="_blank"
                                       style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 3px; font-size: 12px; margin-right: 5px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <button onclick="openApproveModal({{ $order->id }})"
                                            style="padding: 6px 12px; background: #28a745; color: white; border: none; border-radius: 3px; font-size: 12px; cursor: pointer; margin-right: 5px;">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button onclick="openRejectModal({{ $order->id }})"
                                            style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 3px; font-size: 12px; cursor: pointer;">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="padding: 20px; text-align: center; color: #777;">
                                    No pending approvals found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <p style="padding: 20px; text-align: center; color: #777;">
                    Approval interface for this form is not yet implemented.
                </p>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 20px 0; color: #333;">Approve Record</h3>
        <form id="approveForm" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Remarks (Optional)</label>
                <textarea name="remarks" rows="4" placeholder="Enter approval remarks..."
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"></textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeApproveModal()"
                        style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit"
                        style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Approve
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <h3 style="margin: 0 0 20px 0; color: #333;">Reject Record</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Rejection Remarks <span style="color: red;">*</span></label>
                <textarea name="remarks" rows="4" placeholder="Enter rejection remarks..." required
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"></textarea>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()"
                        style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Cancel
                </button>
                <button type="submit"
                        style="padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openApproveModal(id) {
        const formName = '{{ $formName }}';
        document.getElementById('approveForm').action = '/approvals/' + formName + '/' + id + '/approve';
        document.getElementById('approveModal').style.display = 'flex';
    }

    function closeApproveModal() {
        document.getElementById('approveModal').style.display = 'none';
        document.getElementById('approveForm').reset();
    }

    function openRejectModal(id) {
        const formName = '{{ $formName }}';
        document.getElementById('rejectForm').action = '/approvals/' + formName + '/' + id + '/reject';
        document.getElementById('rejectModal').style.display = 'flex';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
        document.getElementById('rejectForm').reset();
    }

    // Close modals on outside click
    window.onclick = function(event) {
        const approveModal = document.getElementById('approveModal');
        const rejectModal = document.getElementById('rejectModal');
        if (event.target === approveModal) {
            closeApproveModal();
        }
        if (event.target === rejectModal) {
            closeRejectModal();
        }
    }
</script>
@endpush
@endsection

