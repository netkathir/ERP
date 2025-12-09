@extends('layouts.dashboard')

@section('title', 'Edit Tender Evaluation - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Tender Evaluation</h2>
        <a href="{{ route('tender-evaluations.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $tender = $evaluation->tender;
    @endphp

    <form action="{{ route('tender-evaluations.update', $evaluation->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- General Information -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">General Information</h3>
            </div>
            <div style="padding: 20px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Tender No</label>
                    <input type="text" value="{{ optional($tender)->tender_no ?? '-' }}" readonly
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Closing Date &amp; Time</label>
                    <input type="text"
                           value="{{ $tender && $tender->closing_date_time ? $tender->closing_date_time->format('d-m-Y H:i') : '' }}"
                           readonly
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; background: #f8f9fa;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Evaluation Document</label>
                    <input type="file" name="evaluation_document" accept=".pdf,.xls,.xlsx,.doc,.docx"
                           style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                    @if($evaluation->evaluation_document)
                        <div style="margin-top: 5px;">
                            <a href="{{ asset('storage/' . $evaluation->evaluation_document) }}" target="_blank" style="color: #667eea; font-size: 12px;">
                                <i class="fas fa-file-download"></i> View current file
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tender Items (read-only) -->
        <div style="background: white; border: 1px solid #dee2e6; border-radius: 5px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="background: #f8f9fa; padding: 15px 20px; border-bottom: 1px solid #dee2e6; border-radius: 5px 5px 0 0;">
                <h3 style="margin: 0; color: #667eea; font-size: 18px; font-weight: 600;">Tender Items</h3>
            </div>
            <div style="padding: 20px; overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Title</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Description</th>
                            <th style="padding: 12px; text-align: right; color: #333; font-weight: 600;">Quantity</th>
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Unit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($tender && $tender->items->count())
                            @foreach($tender->items as $item)
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 10px; color: #333;">{{ $item->title }}</td>
                                    <td style="padding: 10px; color: #555; white-space: pre-wrap;">{{ $item->description }}</td>
                                    <td style="padding: 10px; text-align: right; color: #333;">{{ $item->qty }}</td>
                                    <td style="padding: 10px; color: #333;">{{ optional($item->unit)->symbol }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" style="padding: 12px; text-align: center; color: #777;">
                                    No items found for this tender.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display: flex; gap: 15px; justify-content: flex-end; margin-top: 30px;">
            <a href="{{ route('tender-evaluations.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: 600; cursor: pointer;">
                <i class="fas fa-save"></i> Update
            </button>
        </div>
    </form>
</div>
@endsection


