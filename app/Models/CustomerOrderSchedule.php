<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOrderSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_order_id',
        'customer_order_item_id',
        'po_sr_no',
        'quantity',
        'unit_id',
        'start_date',
        'end_date',
        'inspection_clause',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    public function customerOrderItem()
    {
        return $this->belongsTo(CustomerOrderItem::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}


