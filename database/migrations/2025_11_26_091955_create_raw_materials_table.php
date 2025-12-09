<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_category_id')->constrained('raw_material_categories')->onDelete('cascade');
            $table->foreignId('raw_material_sub_category_id')->constrained('raw_material_sub_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('grade')->nullable();
            $table->string('thickness')->nullable();
            $table->enum('batch_required', ['Yes', 'No'])->default('No');
            $table->enum('qc_applicable', ['Yes', 'No'])->default('No');
            $table->enum('test_certificate_applicable', ['Yes', 'No'])->default('No');
            $table->foreignId('unit_id')->constrained('units')->onDelete('restrict');
            $table->text('sop')->nullable();
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
        Schema::dropIfExists('raw_materials');
    }
}
