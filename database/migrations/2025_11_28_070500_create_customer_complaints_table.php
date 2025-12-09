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
        Schema::create('customer_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->text('customer_address')->nullable();
            $table->text('site_address')->nullable();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('attended_by_id')->constrained('users')->onDelete('cascade');
            $table->string('complaint_type')->default('Product defect');
            $table->string('complaint_reg_no')->nullable();
            $table->decimal('quantity', 15, 3)->nullable();
            $table->text('complaint_details')->nullable();
            $table->text('correction')->nullable();
            $table->string('location')->nullable();
            $table->text('remarks_from_customer')->nullable();
            $table->date('complaint_date')->nullable();
            $table->text('root_cause_analysis')->nullable();
            $table->text('preventive_action')->nullable();
            $table->text('corrective_action')->nullable();
            $table->date('closed_on')->nullable();
            $table->string('status')->default('Open');
            $table->string('attachment_path')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_complaints');
    }
};


