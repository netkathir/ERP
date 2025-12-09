<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_no')->unique();
            $table->foreignId('purchase_indent_id')->nullable()->constrained('purchase_indents')->onDelete('set null');
            
            // Ship To Section
            $table->enum('ship_to', ['Customer', 'Subcontractor', 'Company'])->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignId('subcontractor_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->foreignId('company_id')->nullable()->constrained('billing_addresses')->onDelete('set null');
            
            // Ship To Address (populated based on selection)
            $table->string('ship_to_address_line_1')->nullable();
            $table->string('ship_to_address_line_2')->nullable();
            $table->string('ship_to_city')->nullable();
            $table->string('ship_to_state')->nullable();
            $table->string('ship_to_pincode')->nullable();
            $table->string('ship_to_email')->nullable();
            $table->string('ship_to_contact_no')->nullable();
            $table->string('ship_to_gst_no')->nullable();
            
            // Supplier Address
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('supplier_address_line_1')->nullable();
            $table->string('supplier_address_line_2')->nullable();
            $table->string('supplier_city')->nullable();
            $table->string('supplier_state')->nullable();
            $table->string('supplier_email')->nullable();
            $table->string('supplier_gst_no')->nullable();
            
            // Billing Address
            $table->foreignId('billing_address_id')->nullable()->constrained('billing_addresses')->onDelete('set null');
            $table->string('billing_address_line_1')->nullable();
            $table->string('billing_address_line_2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_gst_no')->nullable();
            
            // Amount Calculation Section
            $table->decimal('gst', 15, 2)->default(0);
            $table->decimal('sgst', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            
            // Terms and Conditions Section
            $table->text('freight_charges')->nullable();
            $table->text('terms_of_payment')->nullable();
            $table->text('special_conditions')->nullable();
            $table->text('inspection')->nullable();
            
            // Transport and Warranty Information Section
            $table->string('name_of_transport')->nullable();
            $table->string('transport_certificate')->nullable();
            $table->text('insurance_of_goods_damages')->nullable();
            $table->date('warranty_expiry')->nullable();
            
            // File Upload
            $table->string('upload_path')->nullable();
            $table->string('upload_original_name')->nullable();
            
            // Status and tracking
            $table->string('status')->default('Draft'); // Draft, Submitted, Approved, Rejected
            $table->text('remarks')->nullable();
            
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->unsignedBigInteger('updated_by_id')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};

