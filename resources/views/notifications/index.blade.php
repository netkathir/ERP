@extends('layouts.dashboard')

@section('title', 'Notifications')

@section('content')
<div style="background:white; padding:30px; border-radius:10px; box-shadow:0 2px 4px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
        <h2 style="color:#333; font-size:24px; margin:0;">Notifications</h2>
        <a href="{{ route('dashboard') }}" style="padding:10px 20px; background:#6c757d; color:white; text-decoration:none; border-radius:5px; font-weight:500; display:inline-flex; align-items:center; gap:8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if($notifications->count() === 0)
        <div style="text-align:center; padding:40px; color:#666;">
            <i class="fas fa-bell-slash" style="font-size:48px; color:#ccc; margin-bottom:15px;"></i>
            <p style="font-size:18px; margin:0;">No notifications found.</p>
        </div>
    @else
        <div style="background:white; border:1px solid #dee2e6; border-radius:5px; box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8f9fa; border-bottom:2px solid #dee2e6;">
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Sender</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Message</th>
                            <th style="padding:12px; text-align:left; color:#333; font-weight:600;">Time</th>
                            <th style="padding:12px; text-align:center; color:#333; font-weight:600; width:150px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notification)
                            <tr style="border-bottom:1px solid #dee2e6; {{ $loop->even ? 'background:#f8f9fa;' : '' }}">
                                <td style="padding:12px; color:#007bff; font-weight:500;">{{ $notification->sender }}</td>
                                <td style="padding:12px; color:#333;">
                                    @php
                                        $indent = $notification->related_id ? \App\Models\PurchaseIndent::find($notification->related_id) : null;
                                        $indentNo = $indent ? $indent->indent_no : '';
                                        $message = $notification->message;
                                        if ($indentNo) {
                                            $message = str_replace('Purchase Indent (' . $indentNo . ')', '<strong>Purchase Indent (' . $indentNo . ')</strong>', $message);
                                        }
                                    @endphp
                                    {!! $message !!}
                                </td>
                                <td style="padding:12px; color:#dc3545; font-size:13px;">
                                    {{ $notification->created_at->diffForHumans() }}
                                </td>
                                <td style="padding:12px; text-align:center;">
                                    @if($notification->action_url)
                                        <a href="{{ $notification->action_url }}" 
                                           style="padding:6px 12px; background:#007bff; color:white; border-radius:4px; font-size:12px; text-decoration:none; display:inline-flex; align-items:center; gap:5px;">
                                            {{ ucfirst($notification->action_type) }}
                                            <i class="fas fa-arrow-left" style="transform: rotate(180deg);"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection

