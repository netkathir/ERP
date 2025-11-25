<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class ModuleActionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'branches' => ['View', 'Create', 'Edit', 'Delete'],
            'users' => ['View', 'Create', 'Edit', 'Delete'],
            'roles' => ['View', 'Create', 'Edit', 'Delete'],
            'permissions' => ['View', 'Create', 'Edit', 'Delete'],
            'products' => ['View', 'Create', 'Edit', 'Delete', 'Export'],
            'units' => ['View', 'Create', 'Edit', 'Delete'],
            'customers' => ['View', 'Create', 'Edit', 'Delete', 'Export'],
            'quotations' => ['View', 'Create', 'Edit', 'Delete', 'Approve', 'Export', 'Print'],
            'proforma-invoices' => ['View', 'Create', 'Edit', 'Delete', 'Approve', 'Export', 'Print'],
            'tax' => ['View', 'Create', 'Edit', 'Delete'],
            'company-info' => ['View', 'Create', 'Edit', 'Delete'],
            'reports' => ['View', 'Export'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $slug = strtolower($module . '-' . $action);
                $name = ucfirst($module) . ' - ' . $action;
                
                Permission::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => $name,
                        'description' => "Permission to {$action} {$module}",
                        'module' => $module,
                        'action' => strtolower($action),
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('Module-action based permissions seeded successfully.');
    }
}
