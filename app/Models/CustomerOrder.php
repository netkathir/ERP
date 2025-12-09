<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_no',
        'order_date',
        'tender_id',
        'branch_id',
        'customer_po_no',
        'customer_po_date',
        'customer_tender_no',
        'inspection_agency',
        'billing_address_line1',
        'billing_address_line2',
        'billing_city',
        'billing_state',
        'billing_pincode',
        'billing_gst_no',
        'delivery_address_line1',
        'delivery_address_line2',
        'delivery_city',
        'delivery_state',
        'delivery_pincode',
        'delivery_contact_no',
        'tax_type',
        'total_amount',
        'gst_percent',
        'gst_amount',
        'cgst_percent',
        'cgst_amount',
        'sgst_percent',
        'sgst_amount',
        'igst_percent',
        'igst_amount',
        'freight',
        'inspection_charges',
        'net_amount',
        'amount_note',
        'drawing_path',
        'attachment_path',
        'production_order_no',
        'production_order_date',
        'gf_mat_details',
        'resin_type',
        'packing_instructions',
        'prepared_by_id',
        'status',
        'updated_by_id',
    ];

    protected $casts = [
        'order_date' => 'date',
        'customer_po_date' => 'date',
        'production_order_date' => 'date',
    ];

    public function tender()
    {
        return $this->belongsTo(Tender::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(CustomerOrderItem::class);
    }

    public function schedules()
    {
        return $this->hasMany(CustomerOrderSchedule::class);
    }

    public function amendments()
    {
        return $this->hasMany(CustomerOrderAmendment::class);
    }

    public function preparedBy()
    {
        return $this->belongsTo(User::class, 'prepared_by_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

}


