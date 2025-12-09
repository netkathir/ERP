@extends('layouts.dashboard')

@section('title', 'View Customer Order - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Customer Order Details</h2>
        <div style="display: flex; gap: 10px;">
            @if(auth()->user()->isSuperAdmin() || auth()->user()->hasPermission('tenders', 'approve'))
                @if($order->status !== 'Approved')
                    <form action="{{ route('customer-orders.approve', $order->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" 
                                style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;"
                                onclick="return confirm('Are you sure you want to approve this Customer Order?');">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    </form>
                @endif
            @endif
            <a href="{{ route('customer-orders.edit', $order->id) }}"
               style="padding: 10px 20px; background: #ffc107; color: #212529; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('customer-orders.index') }}"
               style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Header Section -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Customer Order</h3>
        </div>
        <div style="padding: 20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
            <div>
                <div style="color: #777; font-size: 13px; margin-bottom: 4px;">Customer Order No</div>
                <div style="color: #333; font-weight: 600;">{{ $order->order_no }}</div>
            </div>
            <div>
                <div style="color: #777; font-size: 13px; margin-bottom: 4px;">Order Date</div>
                <div style="color: #333; font-weight: 600;">{{ optional($order->order_date)->format('d-m-Y') }}</div>
            </div>
            <div>
                <div style="color: #777; font-size: 13px; margin-bottom: 4px;">Tender No</div>
                <div style="color: #333; font-weight: 600;">{{ optional($order->tender)->tender_no }}</div>
            </div>
        </div>
    </div>

    <!-- Items Section -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Items</h3>
        </div>
        <div style="padding: 20px; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Product Name</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Description</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PL Code</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Quantity</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Unit</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Price per Qty</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">-</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Amount</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PO SR No</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 10px; color: #333;">{{ optional($item->product)->name ?? optional($item->tenderItem)->title ?? '' }}</td>
                            <td style="padding: 10px; color: #555; max-width: 200px; word-wrap: break-word;">{{ $item->description }}</td>
                            <td style="padding: 10px; color: #555;">{{ $item->pl_code ?? '' }}</td>
                            <td style="padding: 10px; text-align: right; color: #333;">{{ number_format($item->ordered_qty, 2) }}</td>
                            <td style="padding: 10px; color: #333;">{{ optional($item->unit)->symbol ?? optional(optional($item->product)->unit)->symbol ?? optional(optional($item->tenderItem)->unit)->symbol ?? '' }}</td>
                            <td style="padding: 10px; text-align: right; color: #333;">₹ {{ number_format($item->unit_price ?? 0, 2) }}</td>
                            <td style="padding: 10px; text-align: right; color: #333;">-</td>
                            <td style="padding: 10px; text-align: right; color: #333; font-weight: 600;">₹ {{ number_format($item->line_amount ?? 0, 2) }}</td>
                            <td style="padding: 10px; color: #555;">{{ $item->po_sr_no }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="padding: 12px; text-align: center; color: #777;">
                                No items found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- GST / Tax and Amount Summary Section -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Left: Tax Type and Additional Charges -->
            <div>
                <div style="margin-bottom: 20px;">
                    <div style="color: #333; font-weight: 500; margin-bottom: 15px;">
                        Tax Type: <strong>{{ $order->tax_type == 'igst' ? 'IGST' : 'CGST and SGST' }}</strong>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <div style="color: #777; font-size: 13px; margin-bottom: 4px;">Freight</div>
                        <div style="color: #333; font-weight: 600;">₹ {{ number_format($order->freight ?? 0, 2) }}</div>
                    </div>
                    <div>
                        <div style="color: #777; font-size: 13px; margin-bottom: 4px;">Inspection Charges</div>
                        <div style="color: #333; font-weight: 600;">₹ {{ number_format($order->inspection_charges ?? 0, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Right: Amount Summary -->
            <div>
                <h4 style="margin: 0 0 15px 0; color: #333; font-size: 16px; font-weight: 600;">Amount</h4>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label style="color: #333; font-weight: 500;">Total:</label>
                        <div style="color: #333; font-weight: 600;">₹ {{ number_format($order->total_amount ?? 0, 2) }}</div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label style="color: #333; font-weight: 500;">GST %:</label>
                        <div style="color: #333; font-weight: 600;">{{ number_format($order->gst_percent ?? 0, 2) }}%</div>
                    </div>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <label style="color: #333; font-weight: 500;">GST Amount:</label>
                        <div style="color: #333; font-weight: 600;">₹ {{ number_format($order->gst_amount ?? 0, 2) }}</div>
                    </div>
                    
                    @if($order->tax_type == 'cgst_sgst')
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <label style="color: #333; font-weight: 500;">CGST:</label>
                            <div style="color: #333; font-weight: 600;">₹ {{ number_format($order->cgst_amount ?? 0, 2) }}</div>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <label style="color: #333; font-weight: 500;">SGST:</label>
                            <div style="color: #333; font-weight: 600;">₹ {{ number_format($order->sgst_amount ?? 0, 2) }}</div>
                        </div>
                    @else
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                            <label style="color: #333; font-weight: 500;">IGST:</label>
                            <div style="color: #333; font-weight: 600;">₹ {{ number_format($order->igst_amount ?? 0, 2) }}</div>
                        </div>
                    @endif
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 10px; border-top: 1px solid #eee;">
                        <label style="color: #333; font-weight: 600; font-size: 15px;">Net Amount:</label>
                        <div style="color: #667eea; font-weight: 700; font-size: 18px;">₹ {{ number_format($order->net_amount ?? 0, 2) }}</div>
                    </div>
                    
                    @if($order->amount_note)
                        <div style="margin-top: 10px;">
                            <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Note:</label>
                            <div style="padding: 10px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; white-space: pre-wrap;">{{ $order->amount_note }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Section -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Schedule</h3>
        </div>
        <div style="padding: 20px; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Product</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PO SR No</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Quantity</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Unit</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Start Date</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">End Date</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Inspection Clause</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->schedules as $s)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 10px; color: #333;">{{ optional(optional($s->customerOrderItem)->product)->name ?? optional(optional($s->customerOrderItem)->tenderItem)->title ?? '' }}</td>
                            <td style="padding: 10px; color: #333;">{{ $s->po_sr_no }}</td>
                            <td style="padding: 10px; text-align: right; color: #333;">{{ $s->quantity }}</td>
                            <td style="padding: 10px; color: #333;">{{ optional($s->unit)->symbol }}</td>
                            <td style="padding: 10px; color: #333;">{{ optional($s->start_date)->format('Y-m-d') }}</td>
                            <td style="padding: 10px; color: #333;">{{ optional($s->end_date)->format('Y-m-d') }}</td>
                            <td style="padding: 10px; color: #333;">{{ $s->inspection_clause }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 12px; text-align: center; color: #777;">
                                No schedule lines found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Amendment Section -->
    <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
            <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Amendments</h3>
        </div>
        <div style="padding: 20px; overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Product</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">PO SR No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Amendment No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Amendment Date</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Existing Qty</th>
                        <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">New Qty</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->amendments as $a)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 10px; color: #333;">{{ optional(optional($a->customerOrderItem)->product)->name ?? optional(optional($a->customerOrderItem)->tenderItem)->title ?? '' }}</td>
                            <td style="padding: 10px; color: #333;">{{ $a->po_sr_no }}</td>
                            <td style="padding: 10px; color: #333;">{{ $a->amendment_no }}</td>
                            <td style="padding: 10px; color: #333;">{{ optional($a->amendment_date)->format('Y-m-d') }}</td>
                            <td style="padding: 10px; text-align: right; color: #333;">{{ $a->existing_quantity }}</td>
                            <td style="padding: 10px; text-align: right; color: #333;">{{ $a->new_quantity }}</td>
                            <td style="padding: 10px; color: #333;">{{ $a->remarks }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding: 12px; text-align: center; color: #777;">
                                No amendments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection


