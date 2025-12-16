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
        Schema::create('material_inward_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_inward_id')->constrained('material_inwards')->onDelete('cascade');
            $table->foreignId('purchase_order_item_id')->nullable()->constrained('purchase_order_items')->onDelete('set null');
            $table->foreignId('raw_material_id')->nullable()->constrained('raw_materials')->onDelete('restrict');
            $table->text('item_description')->nullable();
            
            // PO Information (read-only, from PO)
            $table->decimal('po_qty', 15, 3)->default(0);
            $table->decimal('pending_qty', 15, 3)->default(0);
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('restrict');
            
            // Received Information
            $table->decimal('received_qty', 15, 3)->default(0);
            $table->decimal('received_qty_in_kg', 15, 3)->nullable();
            $table->string('batch_no')->nullable();
            $table->decimal('cost_per_unit', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            
            // Invoice Information
            $table->string('supplier_invoice_no');
            $table->date('invoice_date');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_inward_items');
    }
};
