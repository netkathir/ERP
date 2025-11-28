<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'contact_person',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'pincode',
        'supplier_remarks',
        'facilities_capacity_rating',
        'facilities_capacity_remarks',
        'manufacturing_facility_rating',
        'manufacturing_facility_remarks',
        'business_volume_rating',
        'business_volume_remarks',
        'financial_stability_rating',
        'financial_stability_remarks',
        'decision_making_rating',
        'decision_making_remarks',
        'complexity_rating',
        'complexity_remarks',
        'tech_competency_rating',
        'tech_competency_remarks',
        'supplier_technology_rating',
        'supplier_technology_remarks',
        'resources_availability_rating',
        'resources_availability_remarks',
        'process_design_rating',
        'process_design_remarks',
        'manufacturing_capability_rating',
        'manufacturing_capability_remarks',
        'change_management_rating',
        'change_management_remarks',
        'disaster_preparedness_rating',
        'disaster_preparedness_remarks',
        'equipment_monitoring_rating',
        'equipment_monitoring_remarks',
        'working_environment_rating',
        'working_environment_remarks',
        'product_testing_rating',
        'product_testing_remarks',
        'storage_handling_rating',
        'storage_handling_remarks',
        'transportation_rating',
        'transportation_remarks',
        'statutory_regulatory_rating',
        'statutory_regulatory_remarks',
        'qms_risk_rating',
        'qms_risk_remarks',
        'total_score',
        'grand_total',
        'assessment_result',
        'status',
        'final_remarks',
        'branch_id',
        'created_by_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}


