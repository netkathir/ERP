<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProcessIdToBomProcessItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bom_process_items', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique('bom_item_unique');
            
            // Add process_id column
            $table->foreignId('process_id')->nullable()->after('bom_process_id')->constrained('processes')->onDelete('cascade');
            
            // Create new unique constraint with process_id
            $table->unique(['bom_process_id', 'raw_material_id', 'process_id'], 'bom_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bom_process_items', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('bom_item_unique');
            
            // Drop process_id column
            $table->dropForeign(['process_id']);
            $table->dropColumn('process_id');
            
            // Restore old unique constraint
            $table->unique(['bom_process_id', 'raw_material_id'], 'bom_item_unique');
        });
    }
}
