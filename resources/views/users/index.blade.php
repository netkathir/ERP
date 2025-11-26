@extends('layouts.dashboard')

@section('title', 'Users - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Users</h2>
        <a href="{{ route('users.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Add New User
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if($users->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Name</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Email</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Mobile</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Role</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Branches</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $user->name }}</td>
                            <td style="padding: 12px; color: #666;">{{ $user->email }}</td>
                            <td style="padding: 12px; color: #666;">{{ $user->mobile ?? 'N/A' }}</td>
                            <td style="padding: 12px;">
                                @if($user->role)
                                    <span style="background: #667eea; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                                        {{ $user->role->name }}
                                    </span>
                                @else
                                    <span style="color: #999;">No Role</span>
                                @endif
                            </td>
                            <td style="padding: 12px;">
                                @if($user->branches && $user->branches->count() > 0)
                                    <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                        @foreach($user->branches as $branch)
                                            <span style="background: #f59e0b; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px;">
                                                {{ $branch->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span style="color: #999;">No Branches</span>
                                @endif
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('users.show', $user->id) }}" style="padding: 6px 12px; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        View
                                    </a>
                                    <a href="{{ route('users.edit', $user->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        Edit
                                    </a>
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="padding: 6px 12px; background: #dc3545; color: white; border: none; border-radius: 4px; font-size: 12px; cursor: pointer;">
                                            Delete
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
            {{ $users->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No users found.</p>
            <a href="{{ route('users.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Create First User
            </a>
        </div>
    @endif
</div>
@endsection

