<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('qc_material_inward_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_material_inward_id')->constrained('qc_material_inwards')->onDelete('cascade');
            $table->foreignId('material_inward_item_id')->constrained('material_inward_items')->onDelete('restrict');
            $table->foreignId('purchase_order_item_id')->nullable()->constrained('purchase_order_items')->onDelete('set null');
            $table->foreignId('raw_material_id')->nullable()->constrained('raw_materials')->onDelete('restrict');
            $table->text('item_description')->nullable();
            $table->decimal('received_qty', 15, 3)->default(0);
            $table->decimal('received_qty_in_kg', 15, 3)->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('restrict');
            $table->string('batch_no')->nullable();
            $table->string('supplier_invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->decimal('given_qty', 15, 3)->default(0);
            $table->decimal('accepted_qty', 15, 3)->default(0);
            $table->decimal('rejected_qty', 15, 3)->default(0);
            $table->text('rejection_reason')->nullable();
            $table->boolean('qc_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qc_material_inward_items');
    }
};
