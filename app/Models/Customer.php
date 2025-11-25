<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'company_name',
        'contact_name', 
        'gst_no', 
        'billing_address_line_1', 
        'billing_address_line_2', 
        'billing_city', 
        'billing_state', 
        'billing_pincode',
        'shipping_address_line_1', 
        'shipping_address_line_2', 
        'shipping_city', 
        'shipping_state', 
        'shipping_pincode',
        'contact_info',
        'branch_id'
    ];

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

    /**
     * Get the full shipping address as a formatted string.
     */
    public function getShippingAddressAttribute()
    {
        $address = $this->shipping_address_line_1 ?? '';
        if ($this->shipping_address_line_2) {
            $address .= ', ' . $this->shipping_address_line_2;
        }
        if ($this->shipping_city) {
            $address .= ', ' . $this->shipping_city;
        }
        if ($this->shipping_state) {
            $address .= ', ' . $this->shipping_state;
        }
        if ($this->shipping_pincode) {
            $address .= ' - ' . $this->shipping_pincode;
        }
        return $address;
    }
}
