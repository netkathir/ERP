<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_order_id',
        'tender_item_id',
        'product_id',
        'unit_id',
        'po_sr_no',
        'ordered_qty',
        'description',
        'pl_code',
        'unit_price',
        'installation_charges',
        'line_amount',
    ];

    protected $casts = [
        'ordered_qty' => 'decimal:2',
        'unit_price' => 'decimal:4',
        'installation_charges' => 'decimal:2',
        'line_amount' => 'decimal:2',
    ];

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    public function tenderItem()
    {
        return $this->belongsTo(TenderItem::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function schedules()
    {
        return $this->hasMany(CustomerOrderSchedule::class);
    }

    public function amendments()
    {
        return $this->hasMany(CustomerOrderAmendment::class);
    }
}


