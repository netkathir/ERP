<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcMaterialInwardItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'qc_material_inward_id',
        'material_inward_item_id',
        'purchase_order_item_id',
        'raw_material_id',
        'item_description',
        'received_qty',
        'received_qty_in_kg',
        'unit_id',
        'batch_no',
        'supplier_invoice_no',
        'invoice_date',
        'given_qty',
        'accepted_qty',
        'rejected_qty',
        'rejection_reason',
        'qc_completed',
    ];

    protected $casts = [
        'received_qty' => 'decimal:3',
        'received_qty_in_kg' => 'decimal:3',
        'given_qty' => 'decimal:3',
        'accepted_qty' => 'decimal:3',
        'rejected_qty' => 'decimal:3',
        'invoice_date' => 'date',
        'qc_completed' => 'boolean',
    ];

    public function qcMaterialInward()
    {
        return $this->belongsTo(QcMaterialInward::class);
    }

    public function materialInwardItem()
    {
        return $this->belongsTo(MaterialInwardItem::class);
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
