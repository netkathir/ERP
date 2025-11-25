<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Permission;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'role_id',
        'entity_id',
        'organization_id',
        'branch_id',
        'status',
        'last_login_at',
        'created_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    /**
     * Get the role that owns the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the entity that owns the user.
     */
    public function entity()
    {
        return $this->belongsTo(Entity::class);
    }

    /**
     * Get the OTP verifications for the user.
     */
    public function otpVerifications()
    {
        return $this->hasMany(OtpVerification::class);
    }

    /**
     * Get the organization that owns the user.
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the branch that owns the user (legacy single branch).
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get all branches assigned to the user (many-to-many).
     */
    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'user_branch');
    }

    /**
     * Check if user is Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->slug === 'super-admin';
    }

    /**
     * Check if user is Branch User.
     */
    public function isBranchUser(): bool
    {
        return $this->role && $this->role->slug === 'branch-user';
    }

    /**
     * Check if user has access to a specific branch.
     */
    public function hasAccessToBranch(int $branchId): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }
        return $this->branches()->where('branches.id', $branchId)->exists();
    }

    /**
     * Get the user who created this user.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if user is active.
     */
    public function isActive(): bool
    {
        // If status column doesn't exist or is null, treat as active (backward compatibility)
        if (!isset($this->status) || $this->status === null) {
            return true;
        }
        return $this->status === 'active';
    }

    /**
     * Check if user is locked.
     */
    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }

    /**
     * Update last login timestamp.
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $module, string $action = 'view'): bool
    {
        // Super Admin has all permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Ensure role is loaded
        if (!$this->relationLoaded('role')) {
            $this->load('role');
        }

        // Check if user's role has the permission
        if (!$this->role) {
            return false;
        }

        // Ensure role permissions are loaded
        if (!$this->role->relationLoaded('permissions')) {
            $this->role->load('permissions');
        }

        $permission = Permission::where('module', $module)
            ->where('action', $action)
            ->where('is_active', true)
            ->first();

        if (!$permission) {
            return false;
        }

        return $this->role->permissions->contains('id', $permission->id);
    }
}
