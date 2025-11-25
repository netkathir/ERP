<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaInvoiceItem extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'proforma_invoice_id', 
        'product_id', 
        'unit_id', 
        'quantity', 
        'price', 
        'discount_percent', 
        'line_base_amount',
        'item_discount_amount',
        'line_total'
    ];

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
