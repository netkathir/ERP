<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BOMProcessItem extends Model
{
    use HasFactory;

    protected $table = 'bom_process_items';

    protected $fillable = [
        'bom_process_id',
        'process_id',
        'raw_material_id',
        'quantity',
        'unit_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
    ];

    public function bomProcess()
    {
        return $this->belongsTo(BOMProcess::class, 'bom_process_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function process()
    {
        return $this->belongsTo(Process::class);
    }
}
