<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_no',
        'purchase_indent_id',
        'ship_to',
        'customer_id',
        'subcontractor_id',
        'company_id',
        'ship_to_address_line_1',
        'ship_to_address_line_2',
        'ship_to_city',
        'ship_to_state',
        'ship_to_pincode',
        'ship_to_email',
        'ship_to_contact_no',
        'ship_to_gst_no',
        'supplier_id',
        'supplier_address_line_1',
        'supplier_address_line_2',
        'supplier_city',
        'supplier_state',
        'supplier_email',
        'supplier_gst_no',
        'billing_address_id',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_state',
        'billing_email',
        'billing_gst_no',
        'tax_type',
        'gst',
        'gst_percent',
        'sgst',
        'cgst_percent',
        'cgst_amount',
        'igst_percent',
        'igst_amount',
        'total',
        'discount',
        'discount_percent',
        'net_amount',
        'freight_charges',
        'terms_of_payment',
        'special_conditions',
        'inspection',
        'name_of_transport',
        'transport_certificate',
        'insurance_of_goods_damages',
        'warranty_expiry',
        'upload_path',
        'upload_original_name',
        'status',
        'remarks',
        'branch_id',
        'created_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'gst' => 'decimal:2',
        'gst_percent' => 'decimal:2',
        'sgst' => 'decimal:2',
        'cgst_percent' => 'decimal:2',
        'cgst_amount' => 'decimal:2',
        'igst_percent' => 'decimal:2',
        'igst_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'discount' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'warranty_expiry' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function purchaseIndent()
    {
        return $this->belongsTo(PurchaseIndent::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function subcontractor()
    {
        return $this->belongsTo(Supplier::class, 'subcontractor_id');
    }

    public function company()
    {
        return $this->belongsTo(BillingAddress::class, 'company_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function billingAddress()
    {
        return $this->belongsTo(BillingAddress::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}

