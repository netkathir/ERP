@extends('layouts.dashboard')

@section('title', 'Raw Materials - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Raw Materials</h2>
        <a href="{{ route('raw-materials.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-plus"></i> Add Raw Material
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

    @if($rawMaterials->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">S.No</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Category</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">SubCategory</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Raw Material</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Grade</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">Thickness</th>
                        <th style="padding: 12px; text-align: left; color: #333; font-weight: 600;">UOM</th>
                        <th style="padding: 12px; text-align: center; color: #333; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rawMaterials as $rawMaterial)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #666;">{{ ($rawMaterials->currentPage() - 1) * $rawMaterials->perPage() + $loop->iteration }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $rawMaterial->rawMaterialCategory->name ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $rawMaterial->rawMaterialSubCategory->name ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #333; font-weight: 500;">{{ $rawMaterial->name }}</td>
                            <td style="padding: 12px; color: #666;">{{ $rawMaterial->grade ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $rawMaterial->thickness ?? 'N/A' }}</td>
                            <td style="padding: 12px; color: #666;">{{ $rawMaterial->unit->name ?? 'N/A' }} ({{ $rawMaterial->unit->symbol ?? '' }})</td>
                            <td style="padding: 12px; text-align: center;">
                                <div style="display: flex; gap: 8px; justify-content: center;">
                                    <a href="{{ route('raw-materials.edit', $rawMaterial->id) }}" style="padding: 6px 12px; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px; font-size: 12px;">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('raw-materials.destroy', $rawMaterial->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this raw material?');">
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
            {{ $rawMaterials->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #666;">
            <p style="font-size: 18px; margin-bottom: 20px;">No raw materials found.</p>
            <a href="{{ route('raw-materials.create') }}" style="padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Create First Raw Material
            </a>
        </div>
    @endif
</div>
@endsection

