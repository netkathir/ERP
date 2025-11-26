<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialSubCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_material_category_id',
        'name',
        'description',
        'branch_id',
    ];

    public function rawMaterialCategory()
    {
        return $this->belongsTo(RawMaterialCategory::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
