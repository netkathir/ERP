<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOrderAmendment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_order_id',
        'customer_order_item_id',
        'po_sr_no',
        'amendment_no',
        'amendment_date',
        'existing_quantity',
        'new_quantity',
        'existing_info',
        'new_info',
        'remarks',
        'file_path',
    ];

    protected $casts = [
        'amendment_date' => 'date',
        'existing_quantity' => 'decimal:2',
        'new_quantity' => 'decimal:2',
    ];

    public function customerOrder()
    {
        return $this->belongsTo(CustomerOrder::class);
    }

    public function customerOrderItem()
    {
        return $this->belongsTo(CustomerOrderItem::class);
    }
}


