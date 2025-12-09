<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Customer Orders (Header)
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->date('order_date')->nullable();
            $table->foreignId('tender_id')->constrained('tenders')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->timestamps();
        });

        // Customer Order Items (linked to Tender Items)
        Schema::create('customer_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_order_id')->constrained('customer_orders')->onDelete('cascade');
            $table->foreignId('tender_item_id')->constrained('tender_items')->onDelete('cascade');
            $table->string('po_sr_no')->nullable();
            $table->decimal('ordered_qty', 15, 2)->default(0);
            $table->timestamps();
        });

        // Schedule lines
        Schema::create('customer_order_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_order_id')->constrained('customer_orders')->onDelete('cascade');
            $table->foreignId('customer_order_item_id')->constrained('customer_order_items')->onDelete('cascade');
            $table->decimal('quantity', 15, 2);
            $table->foreignId('unit_id')->constrained('units');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('inspection_clause')->nullable();
            $table->timestamps();
        });

        // Amendment lines
        Schema::create('customer_order_amendments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_order_id')->constrained('customer_orders')->onDelete('cascade');
            $table->foreignId('customer_order_item_id')->constrained('customer_order_items')->onDelete('cascade');
            $table->string('amendment_no')->nullable();
            $table->date('amendment_date');
            $table->decimal('existing_quantity', 15, 2)->nullable();
            $table->decimal('new_quantity', 15, 2);
            $table->text('existing_info')->nullable();
            $table->text('new_info')->nullable();
            $table->text('remarks')->nullable();
            $table->string('file_path')->nullable();
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
        Schema::dropIfExists('customer_order_amendments');
        Schema::dropIfExists('customer_order_schedules');
        Schema::dropIfExists('customer_order_items');
        Schema::dropIfExists('customer_orders');
    }
};


