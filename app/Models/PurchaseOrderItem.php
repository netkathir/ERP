<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'purchase_indent_item_id',
        'raw_material_id',
        'item_name',
        'item_description',
        'pack_details',
        'approved_quantity',
        'already_raised_po_qty',
        'po_quantity',
        'expected_delivery_date',
        'unit_id',
        'qty_in_kg',
        'price',
        'amount',
        'po_status',
        'lr_details',
        'booking_agency',
        'delivered_on',
        'delivered_qty',
        'delivery_status',
        'remarks',
    ];

    protected $casts = [
        'approved_quantity' => 'decimal:3',
        'already_raised_po_qty' => 'decimal:3',
        'po_quantity' => 'decimal:3',
        'qty_in_kg' => 'decimal:3',
        'price' => 'decimal:2',
        'amount' => 'decimal:2',
        'delivered_qty' => 'decimal:3',
        'expected_delivery_date' => 'date',
        'delivered_on' => 'date',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function purchaseIndentItem()
    {
        return $this->belongsTo(PurchaseIndentItem::class);
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

