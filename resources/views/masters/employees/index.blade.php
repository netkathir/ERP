@extends('layouts.dashboard')

@section('title', 'Employees - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Employees</h2>
        <a href="{{ route('employees.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Add Employee
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    @if($employees->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Employee Code</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Employee Name</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Department</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Designation</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Email</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Mobile No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Active</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $employee->employee_code }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $employee->name }}</td>
                            <td style="padding: 12px; color: #666;">{{ $employee->department->name ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $employee->designation->name ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $employee->email }}</td>
                            <td style="padding: 12px; color: #666;">{{ $employee->mobile_no }}</td>
                            <td style="padding: 12px; color: #666;">
                                <span style="padding: 4px 8px; border-radius: 3px; font-size: 12px; background: {{ $employee->active == 'Yes' ? '#d4edda' : '#f8d7da' }}; color: {{ $employee->active == 'Yes' ? '#155724' : '#721c24' }};">
                                    {{ $employee->active }}
                                </span>
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('employees.edit', $employee->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $employees->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No employees found.</p>
            <a href="{{ route('employees.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Create First Employee
            </a>
        </div>
    @endif
</div>
@endsection

