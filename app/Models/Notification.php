<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'sender',
        'message',
        'action_type',
        'action_url',
        'related_id',
        'is_read',
        'read_at',
        'user_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get unread notifications count for Admin and Super Admin users
     */
    public static function getUnreadCountForAdmins()
    {
        return self::where('is_read', false)
            ->whereNull('user_id') // Global notifications (for all admins)
            ->count();
    }

    /**
     * Get unread notifications for Admin and Super Admin users
     */
    public static function getUnreadForAdmins($limit = 50)
    {
        return self::where('is_read', false)
            ->whereNull('user_id') // Global notifications (for all admins)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}

