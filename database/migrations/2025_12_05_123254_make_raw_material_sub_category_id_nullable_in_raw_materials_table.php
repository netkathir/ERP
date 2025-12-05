<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeRawMaterialSubCategoryIdNullableInRawMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop the foreign key constraint first
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropForeign(['raw_material_sub_category_id']);
        });

        // Modify the column to allow NULL
        DB::statement('ALTER TABLE `raw_materials` MODIFY COLUMN `raw_material_sub_category_id` BIGINT UNSIGNED NULL');

        // Re-add the foreign key constraint (nullable)
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->foreign('raw_material_sub_category_id')
                  ->references('id')
                  ->on('raw_material_sub_categories')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the foreign key constraint
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropForeign(['raw_material_sub_category_id']);
        });

        // Make the column NOT NULL again
        DB::statement('ALTER TABLE `raw_materials` MODIFY COLUMN `raw_material_sub_category_id` BIGINT UNSIGNED NOT NULL');

        // Re-add the foreign key constraint (not nullable)
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->foreign('raw_material_sub_category_id')
                  ->references('id')
                  ->on('raw_material_sub_categories')
                  ->onDelete('cascade');
        });
    }
}
