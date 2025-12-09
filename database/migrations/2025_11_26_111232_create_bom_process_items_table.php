<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBomProcessItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bom_process_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bom_process_id')->constrained('bom_processes')->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained('raw_materials')->onDelete('cascade');
            $table->decimal('quantity', 15, 4)->default(0);
            $table->foreignId('unit_id')->constrained('units')->onDelete('restrict');
            $table->timestamps();
            
            // Ensure unique raw material per BOM process
            $table->unique(['bom_process_id', 'raw_material_id'], 'bom_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bom_process_items');
    }
}
