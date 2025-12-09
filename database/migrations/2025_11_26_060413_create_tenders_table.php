<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenders', function (Blueprint $table) {
            $table->id();
            $table->string('tender_no')->unique();
            $table->string('customer_tender_no')->nullable();
            $table->foreignId('attended_by')->constrained('users');
            // Store department name as text; actual allowed values are managed in production_departments table
            $table->string('production_dept')->nullable();
            $table->foreignId('company_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('contact_person')->nullable();
            
            // Billing Address
            $table->text('billing_address_line_1')->nullable();
            $table->text('billing_address_line_2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('billing_pincode')->nullable();
            
            // Tender Details
            $table->date('publishing_date')->nullable();
            $table->dateTime('closing_date_time')->nullable();
            $table->enum('bidding_type', ['Online', 'Offline'])->nullable();
            $table->enum('tender_type', ['Open', 'Limited', 'Special Limited', 'Single'])->nullable();
            $table->enum('contract_type', ['Goods', 'Service'])->nullable();
            $table->enum('bidding_system', ['Single Packet', 'Two Packet'])->nullable();
            $table->enum('procure_from_approved_source', ['Yes', 'No'])->nullable();
            $table->decimal('tender_document_cost', 15, 2)->nullable();
            $table->decimal('emd', 15, 2)->nullable();
            $table->enum('ra_enabled', ['Yes', 'No'])->nullable();
            $table->dateTime('ra_date_time')->nullable();
            $table->enum('pre_bid_conference_required', ['Yes', 'No'])->nullable();
            $table->date('pre_bid_conference_date')->nullable();
            $table->string('inspection_agency')->nullable();
            $table->string('tender_document_attachment')->nullable();
            
            // Financial Tabulation Section
            $table->enum('tender_status', ['Bid not coated', 'Bid Coated'])->default('Bid not coated');
            $table->enum('bid_result', ['Bid Awarded', 'Bid not Awarded'])->nullable();
            
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenders');
    }
}
