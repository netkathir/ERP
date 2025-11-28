@extends('layouts.dashboard')

@section('title', 'Manage Permissions - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0 0 5px 0;">Assign Permissions to Role</h2>
        <p style="color: #666; margin: 0; font-size: 14px;">
            <i class="fas fa-user-shield" style="color: #667eea;"></i> 
            Select a role and assign permissions
        </p>
    </div>

    <form action="{{ route('role-permissions.store') }}" method="POST" id="roleSelectForm" style="background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        @csrf
        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <label for="role_id" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">
                    <i class="fas fa-user-shield" style="color: #667eea; margin-right: 5px;"></i>
                    Select Role <span style="color: #dc3545;">*</span>
                </label>
                <select name="role_id" id="role_id" required
                        style="width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 5px; font-size: 14px; background: white;">
                    <option value="">-- Select a Role --</option>
                    @foreach($allRoles as $r)
                        <option value="{{ $r->id }}" {{ $r->id == $role->id ? 'selected' : '' }}>
                            {{ $r->name }}
                            @if($r->description)
                                - {{ $r->description }}
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('role_id').addEventListener('change', function() {
            if (this.value) {
                document.getElementById('roleSelectForm').submit();
            }
        });
    </script>

    <div style="background: #e7f3ff; padding: 12px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #667eea;">
        <p style="margin: 0; color: #333; font-size: 14px;">
            <i class="fas fa-info-circle" style="color: #667eea; margin-right: 8px;"></i>
            <strong>Selected Role:</strong> {{ $role->name }}
            @if($role->description)
                - {{ $role->description }}
            @endif
        </p>
    </div>

    <div style="background: #e7f3ff; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #667eea;">
        <div style="display: flex; align-items: start; gap: 10px;">
            <i class="fas fa-info-circle" style="color: #667eea; font-size: 20px; margin-top: 2px;"></i>
            <div style="flex: 1;">
                <strong style="color: #333; display: block; margin-bottom: 8px; font-size: 16px;">Permission Types Explained:</strong>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-top: 10px;">
                    <div style="background: white; padding: 15px; border-radius: 5px; border: 2px solid #667eea;">
                        <strong style="color: #667eea; display: block; margin-bottom: 8px; font-size: 16px;">
                            <i class="fas fa-eye"></i> Read (View)
                        </strong>
                        <p style="margin: 0; color: #666; font-size: 13px; line-height: 1.6;">
                            <strong>View only.</strong> User can view the list and details but <strong>cannot make any changes</strong> (no editing, adding, or deleting).
                        </p>
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                            <small style="color: #999;">✓ View list<br>✓ View details<br>✗ Cannot edit<br>✗ Cannot add<br>✗ Cannot delete</small>
                        </div>
                    </div>
                    <div style="background: white; padding: 15px; border-radius: 5px; border: 2px solid #28a745;">
                        <strong style="color: #28a745; display: block; margin-bottom: 8px; font-size: 16px;">
                            <i class="fas fa-edit"></i> Write (Edit/Add)
                        </strong>
                        <p style="margin: 0; color: #666; font-size: 13px; line-height: 1.6;">
                            <strong>Edit or Add.</strong> User can edit existing records or add new ones but <strong>cannot delete</strong> existing records.
                        </p>
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                            <small style="color: #999;">✓ View list<br>✓ View details<br>✓ Edit records<br>✓ Add new records<br>✗ Cannot delete</small>
                        </div>
                    </div>
                    <div style="background: white; padding: 15px; border-radius: 5px; border: 2px solid #dc3545;">
                        <strong style="color: #dc3545; display: block; margin-bottom: 8px; font-size: 16px;">
                            <i class="fas fa-trash"></i> Delete (Full Access)
                        </strong>
                        <p style="margin: 0; color: #666; font-size: 13px; line-height: 1.6;">
                            <strong>Full control.</strong> User can Read, Edit, Add, and Delete. This is the most powerful permission, giving complete control.
                        </p>
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                            <small style="color: #999;">✓ View list<br>✓ View details<br>✓ Edit records<br>✓ Add new records<br>✓ Delete records</small>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 15px; padding: 12px; background: #fff3cd; border-radius: 5px; border-left: 4px solid #ffc107;">
                    <p style="margin: 0; color: #856404; font-size: 13px;">
                        <strong>Note:</strong> Each form (like "Units", "Customers", etc.) can have these three permission types assigned independently. 
                        For example, a role can have Read access to "Units" but Write access to "Customers".
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if($permissions->count() > 0)
        @php
            /**
             * Map each form (permission.form_name) to the sidebar menu section.
             * This ensures the Permissions UI always follows the left sidebar menu structure.
             */
            $menuMap = [
                // System Admin
                'branches'                  => 'System Admin',
                'users'                     => 'System Admin',
                'roles'                     => 'System Admin',
                // Role Permissions / Permission form (support multiple name variants)
                'role-permissions'          => 'System Admin',
                'Role Permissions'          => 'System Admin',
                'permission'                => 'System Admin',
                'Permission'                => 'System Admin',
                'permissions'               => 'System Admin',
                'Permissions'               => 'System Admin',
                // Explicit mapping for the Assign Roles‑Permissions page
                'assign-roles-permissions'  => 'System Admin',

                // Tender Sales (map all known variants of form names)
                'tenders'                   => 'Tender Sales',
                'Tenders'                   => 'Tender Sales',
                // Customer Order forms
                'customer-orders'           => 'Tender Sales',
                'customer-order'            => 'Tender Sales',
                'Customer Orders'           => 'Tender Sales',
                'Customer Order'            => 'Tender Sales',
                // Tender Evaluation forms
                'tender-evaluations'        => 'Tender Sales',
                'tender-evaluation'         => 'Tender Sales',
                'Tender Evaluations'        => 'Tender Sales',
                'Tender Evaluation'         => 'Tender Sales',

                // Transactions / Sales
                'transactions'          => 'Transactions',
                'quotations'            => 'Sales',
                'proforma-invoices'     => 'Sales',

                // Masters
                'units'                     => 'Masters',
                'customers'                 => 'Masters',
                'products'                  => 'Masters',
                'raw-material-categories'   => 'Masters',
                'raw-material-sub-categories' => 'Masters',
                'product-categories'        => 'Masters',
                'processes'                 => 'Masters',
                'bom-processes'             => 'Masters',
                'raw-materials'             => 'Masters',
                'departments'               => 'Masters',
                'designations'              => 'Masters',
                'production-departments'    => 'Masters',
                'employees'                 => 'Masters',
                'billing-addresses'         => 'Masters',

                // Settings
                'company-information'   => 'Settings',
                'company-info'          => 'Settings',
            ];

            // Group permissions by sidebar section (Menu)
            $groupedPermissions = $permissions->groupBy(function($perm) use ($menuMap) {
                $formName = $perm->form_name ?? $perm->name ?? '';
                return $menuMap[$formName] ?? 'Other Forms';
            });

            // Order modules to match sidebar approximate order
            $moduleOrder = [
                'System Admin'      => 1,
                'Tender Sales'      => 2,
                'Transactions'      => 3,
                'Sales'             => 4,
                'Masters'           => 5,
                'Settings'          => 6,
                'Other Forms'       => 999,
            ];

            $groupedPermissions = $groupedPermissions->sortBy(function ($_, $key) use ($moduleOrder) {
                return $moduleOrder[$key] ?? 500;
            });
        @endphp

        <form action="{{ route('role-permissions.update', $role->id) }}" method="POST">
            @csrf
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Page</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 100px;">Read</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 100px;">Write</th>
                            <th style="padding: 12px; text-align: center; color: #333; font-weight: 600; width: 100px;">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedPermissions as $moduleName => $modulePermissions)
                            {{-- Module header row (like sidebar section title) --}}
                            <tr>
                                <td colspan="4" style="padding: 10px 12px; background: #f1f5f9; font-weight: 600; color: #111827; border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6;">
                                    {{ $moduleName }}
                                </td>
                            </tr>

                            @foreach($modulePermissions as $permission)
                            @php
                                // Check if role has this permission attached
                                $rolePermission = $role->permissions->find($permission->id);
                                $read = $rolePermission ? ($rolePermission->pivot->read ?? false) : false;
                                $write = $rolePermission ? ($rolePermission->pivot->write ?? false) : false;
                                $delete = $rolePermission ? ($rolePermission->pivot->delete ?? false) : false;
                            @endphp
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                    <td style="padding: 10px 12px; color: #333;">
                                        {{ $permission->form_name ?? $permission->name ?? 'N/A' }}
                                    </td>
                                    <td style="padding: 10px 12px; text-align: center;">
                                    <input type="checkbox" 
                                        id="read_{{ $permission->id }}" 
                                        name="permissions[{{ $permission->id }}][read]" 
                                        value="1" 
                                        {{ $read ? 'checked' : '' }}
                                        style="width: 20px; height: 20px; cursor: pointer;">
                                </td>
                                    <td style="padding: 10px 12px; text-align: center;">
                                    <input type="checkbox" 
                                        id="write_{{ $permission->id }}" 
                                        name="permissions[{{ $permission->id }}][write]" 
                                        value="1" 
                                        {{ $write ? 'checked' : '' }}
                                        style="width: 20px; height: 20px; cursor: pointer;">
                                </td>
                                    <td style="padding: 10px 12px; text-align: center;">
                                    <input type="checkbox" 
                                        id="delete_{{ $permission->id }}" 
                                        name="permissions[{{ $permission->id }}][delete]" 
                                        value="1" 
                                        {{ $delete ? 'checked' : '' }}
                                        style="width: 20px; height: 20px; cursor: pointer;">
                                </td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 25px; display: flex; gap: 15px;">
                <button type="submit" style="padding: 12px 24px; background: #28a745; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                    <i class="fas fa-save"></i> Save Permissions
                </button>
                <a href="{{ route('role-permissions.select') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </form>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No permissions/forms found.</p>
            <a href="{{ route('permissions.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Create First Permission/Form
            </a>
        </div>
    @endif
</div>
@endsection
