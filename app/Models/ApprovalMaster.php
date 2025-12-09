<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_name',
        'display_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the approval mappings for this form
     */
    public function mappings()
    {
        return $this->hasMany(ApprovalMapping::class);
    }

    /**
     * Get active approval mappings
     */
    public function activeMappings()
    {
        return $this->hasMany(ApprovalMapping::class)->where('is_active', true);
    }

    /**
     * Get users who can approve this form
     */
    public function approvers()
    {
        return $this->belongsToMany(User::class, 'approval_mappings')
            ->wherePivot('is_active', true)
            ->withPivot('approval_order')
            ->orderBy('approval_mappings.approval_order');
    }
}

