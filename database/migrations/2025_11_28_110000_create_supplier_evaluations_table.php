<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('supplier_evaluations', function (Blueprint $table) {
            $table->id();

            // Supplier information
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('contact_person');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('pincode');
            $table->text('supplier_remarks')->nullable();

            // Evaluation criteria ratings (numeric) and remarks
            $table->unsignedInteger('facilities_capacity_rating');
            $table->text('facilities_capacity_remarks')->nullable();

            $table->unsignedInteger('manufacturing_facility_rating');
            $table->text('manufacturing_facility_remarks')->nullable();

            $table->unsignedInteger('business_volume_rating');
            $table->text('business_volume_remarks')->nullable();

            $table->unsignedInteger('financial_stability_rating');
            $table->text('financial_stability_remarks')->nullable();

            $table->unsignedInteger('decision_making_rating');
            $table->text('decision_making_remarks')->nullable();

            $table->unsignedInteger('complexity_rating');
            $table->text('complexity_remarks')->nullable();

            $table->unsignedInteger('tech_competency_rating');
            $table->text('tech_competency_remarks')->nullable();

            $table->unsignedInteger('supplier_technology_rating');
            $table->text('supplier_technology_remarks')->nullable();

            $table->unsignedInteger('resources_availability_rating');
            $table->text('resources_availability_remarks')->nullable();

            $table->unsignedInteger('process_design_rating');
            $table->text('process_design_remarks')->nullable();

            $table->unsignedInteger('manufacturing_capability_rating');
            $table->text('manufacturing_capability_remarks')->nullable();

            $table->unsignedInteger('change_management_rating');
            $table->text('change_management_remarks')->nullable();

            $table->unsignedInteger('disaster_preparedness_rating');
            $table->text('disaster_preparedness_remarks')->nullable();

            $table->unsignedInteger('equipment_monitoring_rating');
            $table->text('equipment_monitoring_remarks')->nullable();

            $table->unsignedInteger('working_environment_rating');
            $table->text('working_environment_remarks')->nullable();

            $table->unsignedInteger('product_testing_rating');
            $table->text('product_testing_remarks')->nullable();

            $table->unsignedInteger('storage_handling_rating');
            $table->text('storage_handling_remarks')->nullable();

            $table->unsignedInteger('transportation_rating');
            $table->text('transportation_remarks')->nullable();

            $table->unsignedInteger('statutory_regulatory_rating');
            $table->text('statutory_regulatory_remarks')->nullable();

            $table->unsignedInteger('qms_risk_rating');
            $table->text('qms_risk_remarks')->nullable();

            // Calculated totals
            $table->unsignedInteger('total_score')->default(0);
            $table->unsignedInteger('grand_total')->default(0);

            // Final assessment
            $table->string('assessment_result'); // Approved, Needs Improvement, Not Approved
            $table->string('status'); // Pending, Approved, Rejected
            $table->text('final_remarks')->nullable();

            // Meta
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->unsignedBigInteger('created_by_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_evaluations');
    }
};


