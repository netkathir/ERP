@extends('layouts.dashboard')

@section('title', 'Edit Discount - ERP System')

@section('content')
<div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="color: #333; font-size: 24px; margin: 0;">Edit Discount</h2>
        <a href="{{ route('discounts.index') }}" style="padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; display: inline-flex; align-items: center; gap: 8px;">
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

    <form action="{{ route('discounts.update', $discount->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div style="margin-bottom: 20px;">
            <label for="name" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Discount Name <span style="color: red;">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $discount->name) }}" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
            @error('name')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="type" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Discount Type <span style="color: red;">*</span></label>
            <select name="type" id="type" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                <option value="">Select Type</option>
                <option value="Item" {{ old('type', $discount->type) == 'Item' ? 'selected' : '' }}>Item-level</option>
                <option value="Overall" {{ old('type', $discount->type) == 'Overall' ? 'selected' : '' }}>Overall</option>
            </select>
            @error('type')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label for="percentage" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Percentage (%) <span style="color: red;">*</span></label>
            <input type="number" step="0.01" name="percentage" id="percentage" value="{{ old('percentage', $discount->percentage) }}" required
                style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;"
                placeholder="e.g., 10.00, 15.50">
            @error('percentage')
                <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $discount->is_active) ? 'checked' : '' }}
                    style="width: 18px; height: 18px; cursor: pointer;">
                <span style="color: #333; font-weight: 500;">Active</span>
            </label>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label for="start_date" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $discount->start_date) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                @error('start_date')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="end_date" style="display: block; margin-bottom: 8px; color: #333; font-weight: 500;">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $discount->end_date) }}"
                    style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px;">
                @error('end_date')
                    <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 15px; margin-top: 30px;">
            <a href="{{ route('discounts.index') }}" style="padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; font-weight: 500;">
                Cancel
            </a>
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 5px; font-weight: 500; cursor: pointer;">
                Update Discount
            </button>
        </div>
    </form>
</div>
@endsection
