<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'nature',
        'supplier_name',
        'address_line_1',
        'city',
        'contact_person',
        'email',
        'gst',
        'tan',
        'nature_of_work',
        'type_of_control',
        'customer_approved',
        'supplier_iso_certified',
        'audit_frequency',
        'revaluation_period',
        'remarks',
        'supplier_type',
        'address_line_2',
        'state',
        'contact_number',
        'code',
        'pan',
        'items',
        'material_grade',
        'applicable_requirements',
        'certificate_validity',
        'approved_date',
        'supplier_development',
        'qms_status',
        'branch_id',
        'created_by_id',
    ];

    protected $casts = [
        'revaluation_period' => 'date',
        'certificate_validity' => 'date',
        'approved_date' => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}


