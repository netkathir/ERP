<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialInwardItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_inward_id',
        'purchase_order_item_id',
        'raw_material_id',
        'item_description',
        'po_qty',
        'pending_qty',
        'unit_id',
        'received_qty',
        'received_qty_in_kg',
        'batch_no',
        'cost_per_unit',
        'total',
        'supplier_invoice_no',
        'invoice_date',
    ];

    protected $casts = [
        'po_qty' => 'decimal:3',
        'pending_qty' => 'decimal:3',
        'received_qty' => 'decimal:3',
        'received_qty_in_kg' => 'decimal:3',
        'cost_per_unit' => 'decimal:2',
        'total' => 'decimal:2',
        'invoice_date' => 'date',
    ];

    public function materialInward()
    {
        return $this->belongsTo(MaterialInward::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
