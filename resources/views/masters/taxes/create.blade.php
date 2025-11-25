@extends('layouts.dashboard')

@section('title', 'Add Tax - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Add Tax</h2>
        <a href="{{ route('taxes.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('taxes.store') }}" method="POST">
        @csrf
        <div style="margin-bottom: 20px;">
            <label for="type" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Tax Type <span style="color: red;">*</span></label>
            <select name="type" id="type" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                <option value="">Select Type</option>
                <option value="CGST" {{ old('type') == 'CGST' ? 'selected' : '' }}>CGST</option>
                <option value="SGST" {{ old('type') == 'SGST' ? 'selected' : '' }}>SGST</option>
                <option value="IGST" {{ old('type') == 'IGST' ? 'selected' : '' }}>IGST</option>
            </select>
            @error('type')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="rate" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Rate (%) <span style="color: red;">*</span></label>
            <input type="number" step="0.01" name="rate" id="rate" value="{{ old('rate') }}" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="e.g., 9.00, 18.00">
            @error('rate')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="effective_date" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Effective Date</label>
            <input type="date" name="effective_date" id="effective_date" value="{{ old('effective_date') }}"
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('effective_date')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('taxes.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Save Tax
            </button>
        </div>
    </form>
</div>
@endsection
