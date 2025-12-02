<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProformaInvoice extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'invoice_no', 
        'date', 
        'customer_id', 
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_state',
        'billing_pincode',
        'gst_type', 
        'gst_percent',
        'overall_discount_percent',
        'freight_charges', 
        'gross_amount',
        'discount_amount',
        'taxable_amount',
        'cgst_amount',
        'sgst_amount',
        'igst_amount',
        'total_tax',
        'net_amount', 
        'status',
        'branch_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(ProformaInvoiceItem::class);
    }

    /**
     * Get the full billing address as a formatted string.
     */
    public function getBillingAddressAttribute()
    {
        $address = $this->billing_address_line_1 ?? '';
        if ($this->billing_address_line_2) {
            $address .= ', ' . $this->billing_address_line_2;
        }
        if ($this->billing_city) {
            $address .= ', ' . $this->billing_city;
        }
        if ($this->billing_state) {
            $address .= ', ' . $this->billing_state;
        }
        if ($this->billing_pincode) {
            $address .= ' - ' . $this->billing_pincode;
        }
        return $address;
    }
}
