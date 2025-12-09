<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_address',
        'site_address',
        'product_id',
        'attended_by_id',
        'complaint_type',
        'complaint_reg_no',
        'quantity',
        'complaint_details',
        'correction',
        'location',
        'remarks_from_customer',
        'remarks',
        'complaint_date',
        'root_cause_analysis',
        'preventive_action',
        'corrective_action',
        'closed_on',
        'status',
        'attachment_path',
        'branch_id',
        'created_by_id',
    ];

    protected $casts = [
        'complaint_date' => 'date',
        'closed_on' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attendedBy()
    {
        return $this->belongsTo(User::class, 'attended_by_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}


