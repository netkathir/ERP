<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('purchase_indent_item_id')->nullable()->constrained('purchase_indent_items')->onDelete('set null');
            $table->foreignId('raw_material_id')->nullable()->constrained('raw_materials')->onDelete('restrict');
            
            // Item Information
            $table->string('item_name')->nullable();
            $table->text('item_description')->nullable();
            $table->text('pack_details')->nullable();
            $table->decimal('approved_quantity', 15, 3)->default(0);
            $table->decimal('already_raised_po_qty', 15, 3)->default(0);
            $table->decimal('po_quantity', 15, 3)->default(0);
            $table->date('expected_delivery_date')->nullable();
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('restrict');
            $table->decimal('qty_in_kg', 15, 3)->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            
            // PO Status and Delivery Information
            $table->string('po_status')->nullable(); // PO Placed, PO Partially Placed, Awaiting for Approval, PO Not Placed
            $table->string('lr_details')->nullable();
            $table->string('booking_agency')->nullable();
            $table->date('delivered_on')->nullable();
            $table->decimal('delivered_qty', 15, 3)->nullable();
            $table->enum('delivery_status', ['Completed', 'Incomplete', 'Partially Completed'])->nullable();
            $table->text('remarks')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};

