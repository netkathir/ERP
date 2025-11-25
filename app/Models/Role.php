<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'is_system_role',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system_role' => 'boolean',
    ];

    /**
     * Get the users for the role.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the permissions for the role.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Check if role is a system role (protected).
     */
    public function isSystemRole(): bool
    {
        return $this->is_system_role === true;
    }

    /**
     * Check if role can be deleted.
     */
    public function canBeDeleted(): bool
    {
        return !$this->isSystemRole() && $this->slug !== 'super-admin';
    }
}
