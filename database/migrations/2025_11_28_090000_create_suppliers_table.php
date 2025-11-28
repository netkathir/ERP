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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            // Left column fields
            $table->string('nature')->nullable();
            $table->string('supplier_name');
            $table->string('address_line_1')->nullable();
            $table->string('city')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('gst')->nullable();
            $table->string('tan')->nullable();
            $table->text('nature_of_work')->nullable();
            $table->string('type_of_control')->nullable();
            $table->string('customer_approved')->nullable();
            $table->string('supplier_iso_certified')->nullable();
            $table->string('audit_frequency')->nullable();
            $table->date('revaluation_period')->nullable();
            $table->text('remarks')->nullable();

            // Right column fields
            $table->string('supplier_type')->nullable(); // Supplier, Sub Contractor
            $table->string('address_line_2')->nullable();
            $table->string('state')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('code')->nullable(); // Trader, Manufacturer, Dealer, calibration
            $table->string('pan')->nullable();
            $table->text('items')->nullable();
            $table->string('material_grade')->nullable();
            $table->text('applicable_requirements')->nullable();
            $table->date('certificate_validity')->nullable();
            $table->date('approved_date')->nullable();
            $table->text('supplier_development')->nullable();
            $table->string('qms_status')->nullable(); // Yes / No

            // Branch / audit info
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
        Schema::dropIfExists('suppliers');
    }
};


