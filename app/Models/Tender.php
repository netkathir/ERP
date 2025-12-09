<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    use HasFactory;

    protected $fillable = [
        'tender_no',
        'customer_tender_no',
        'attended_by',
        'production_dept',
        'company_id',
        'contact_person',
        'billing_address_line_1',
        'billing_address_line_2',
        'billing_city',
        'billing_state',
        'billing_country',
        'billing_pincode',
        'publishing_date',
        'closing_date_time',
        'bidding_type',
        'tender_type',
        'contract_type',
        'bidding_system',
        'procure_from_approved_source',
        'validity_of_offer_days',
        'regular_developmental',
        'tender_document_cost',
        'emd',
        'ra_enabled',
        'ra_date_time',
        'pre_bid_conference_required',
        'pre_bid_conference_date',
        'inspection_agency',
        'tender_document_attachment',
        'financial_tabulation_attachment',
        'technical_spec_attachment',
        'technical_spec_rank',
        'tender_status',
        'bid_result',
        'branch_id',
    ];

    protected $casts = [
        'publishing_date' => 'date',
        'closing_date_time' => 'datetime',
        'ra_date_time' => 'datetime',
        'pre_bid_conference_date' => 'date',
        'tender_document_cost' => 'decimal:2',
        'emd' => 'decimal:2',
    ];

    public function attendedBy()
    {
        return $this->belongsTo(User::class, 'attended_by');
    }

    public function company()
    {
        return $this->belongsTo(Customer::class, 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function items()
    {
        return $this->hasMany(TenderItem::class);
    }

    public function financialTabulations()
    {
        return $this->hasMany(TenderFinancialTabulation::class);
    }

    public function remarks()
    {
        return $this->hasMany(TenderRemark::class);
    }
}
