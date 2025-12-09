<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BOMProcess extends Model
{
    use HasFactory;

    protected $table = 'bom_processes';

    protected $fillable = [
        'product_id',
        'process_id', // Keep in fillable temporarily since column still exists (nullable)
        'branch_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(BOMProcessItem::class, 'bom_process_id');
    }
}
