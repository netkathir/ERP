<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseIndentItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_indent_id',
        'raw_material_id',
        'item_description',
        'quantity',
        'unit_id',
        'schedule_date',
        'special_instructions',
        'supplier_id',
        'po_status',
        'lr_details',
        'booking_agency',
        'delivered_qty',
        'delivery_status',
        'po_remarks',
    ];

    protected $casts = [
        'schedule_date' => 'date',
    ];

    public function indent()
    {
        return $this->belongsTo(PurchaseIndent::class, 'purchase_indent_id');
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}


