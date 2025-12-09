<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Only Admin and Super Admin can view notifications
        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-indents', 'approve')) {
            abort(403, 'You do not have permission to view notifications.');
        }

        $notifications = Notification::getUnreadForAdmins(100);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $user = auth()->user();

        // Only Admin and Super Admin can mark as read
        if (!$user->isSuperAdmin() && !$user->hasPermission('purchase-indents', 'approve')) {
            abort(403, 'You do not have permission to mark notifications as read.');
        }

        $notification = Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->read_at = now();
        $notification->save();

        return response()->json(['success' => true]);
    }
}

