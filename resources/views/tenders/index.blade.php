@extends('layouts.dashboard')

@section('title', 'Tenders - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Tenders</h2>
        <a href="{{ route('tenders.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Create Tender
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

    @if($tenders->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 50px;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Tender No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Customer Tender No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Company</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Attended By</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Publishing Date</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Closing Date</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Status</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenders as $index => $tender)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; text-align: center; color: #666;">{{ ($tenders->currentPage() - 1) * $tenders->perPage() + $index + 1 }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $tender->tender_no }}</td>
                            <td style="padding: 12px; color: #666;">{{ $tender->customer_tender_no ?? '-' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $tender->company->company_name ?? '-' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $tender->attendedBy->name ?? '-' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $tender->publishing_date ? $tender->publishing_date->format('d/m/Y') : '-' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $tender->closing_date_time ? $tender->closing_date_time->format('d/m/Y H:i') : '-' }}</td>
                            <td style="padding: 12px;">
                                <span style="background: #6c757d; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                                    {{ $tender->tender_status }}
                                </span>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('tenders.show', $tender->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('tenders.edit', $tender->id) }}" style="padding: 6px 12px; background: #28a745; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('tenders.destroy', $tender->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this tender?');">
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
            {{ $tenders->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No tenders found.</p>
            <a href="{{ route('tenders.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Create First Tender
            </a>
        </div>
    @endif
</div>
@endsection

