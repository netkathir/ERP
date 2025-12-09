<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_indent_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_indent_id')->constrained('purchase_indents')->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained('raw_materials')->onDelete('restrict');
            $table->string('item_description')->nullable();
            $table->decimal('quantity', 15, 3);
            $table->foreignId('unit_id')->constrained('units')->onDelete('restrict');
            $table->date('schedule_date');
            $table->text('special_instructions');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('restrict');

            // PO tracking fields (visible in edit)
            $table->string('po_status')->nullable();
            $table->string('lr_details')->nullable();
            $table->string('booking_agency')->nullable();
            $table->decimal('delivered_qty', 15, 3)->nullable();
            $table->string('delivery_status')->nullable();
            $table->text('po_remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_indent_items');
    }
};


