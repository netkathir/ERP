<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_category_id',
        'raw_material_sub_category_id',
        'name',
        'grade',
        'thickness',
        'batch_required',
        'qc_applicable',
        'test_certificate_applicable',
        'unit_id',
        'sop',
        'branch_id',
    ];

    public function rawMaterialCategory()
    {
        return $this->belongsTo(RawMaterialCategory::class);
    }

    public function rawMaterialSubCategory()
    {
        return $this->belongsTo(RawMaterialSubCategory::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
