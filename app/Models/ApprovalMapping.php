<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'approval_master_id',
        'user_id',
        'approval_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'approval_order' => 'integer',
    ];

    /**
     * Get the approval master (form) this mapping belongs to
     */
    public function approvalMaster()
    {
        return $this->belongsTo(ApprovalMaster::class);
    }

    /**
     * Get the user who can approve
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

