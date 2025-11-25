<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'view-users', 'module' => 'users', 'description' => 'View users list'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'module' => 'users', 'description' => 'Create new users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'module' => 'users', 'description' => 'Edit existing users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'module' => 'users', 'description' => 'Delete users'],
            
            // Organization Management
            ['name' => 'View Organizations', 'slug' => 'view-organizations', 'module' => 'organizations', 'description' => 'View organizations list'],
            ['name' => 'Create Organizations', 'slug' => 'create-organizations', 'module' => 'organizations', 'description' => 'Create new organizations'],
            ['name' => 'Edit Organizations', 'slug' => 'edit-organizations', 'module' => 'organizations', 'description' => 'Edit existing organizations'],
            ['name' => 'Delete Organizations', 'slug' => 'delete-organizations', 'module' => 'organizations', 'description' => 'Delete organizations'],
            
            // Branch Management
            ['name' => 'View Branches', 'slug' => 'view-branches', 'module' => 'branches', 'description' => 'View branches list'],
            ['name' => 'Create Branches', 'slug' => 'create-branches', 'module' => 'branches', 'description' => 'Create new branches'],
            ['name' => 'Edit Branches', 'slug' => 'edit-branches', 'module' => 'branches', 'description' => 'Edit existing branches'],
            ['name' => 'Delete Branches', 'slug' => 'delete-branches', 'module' => 'branches', 'description' => 'Delete branches'],
            
            // Role Management
            ['name' => 'View Roles', 'slug' => 'view-roles', 'module' => 'roles', 'description' => 'View roles list'],
            ['name' => 'Create Roles', 'slug' => 'create-roles', 'module' => 'roles', 'description' => 'Create new roles'],
            ['name' => 'Edit Roles', 'slug' => 'edit-roles', 'module' => 'roles', 'description' => 'Edit existing roles'],
            ['name' => 'Delete Roles', 'slug' => 'delete-roles', 'module' => 'roles', 'description' => 'Delete roles'],
            ['name' => 'Assign Permissions', 'slug' => 'assign-permissions', 'module' => 'roles', 'description' => 'Assign permissions to roles'],
            
            // Permission Management
            ['name' => 'View Permissions', 'slug' => 'view-permissions', 'module' => 'permissions', 'description' => 'View permissions list'],
            ['name' => 'Create Permissions', 'slug' => 'create-permissions', 'module' => 'permissions', 'description' => 'Create new permissions'],
            ['name' => 'Edit Permissions', 'slug' => 'edit-permissions', 'module' => 'permissions', 'description' => 'Edit existing permissions'],
            ['name' => 'Delete Permissions', 'slug' => 'delete-permissions', 'module' => 'permissions', 'description' => 'Delete permissions'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}

