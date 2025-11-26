<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveUniqueConstraintFromBomProcessItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Use raw SQL to drop the unique constraint
        // The unique index might be used by MySQL internally, but we can still drop it
        // Foreign keys on individual columns don't require the unique index
        try {
            DB::statement('ALTER TABLE `bom_process_items` DROP INDEX `bom_item_unique`');
        } catch (\Exception $e) {
            // If it fails, check if the index actually exists
            $indexExists = DB::select("SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bom_process_items' AND INDEX_NAME = 'bom_item_unique'");
            if (!empty($indexExists)) {
                // Index exists but can't be dropped - this shouldn't happen, but if it does, we'll log it
                \Log::warning('Could not drop bom_item_unique index: ' . $e->getMessage());
            }
            // If index doesn't exist, that's fine - our goal is achieved
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bom_process_items', function (Blueprint $table) {
            // Restore the unique constraint
            $table->unique(['bom_process_id', 'raw_material_id', 'process_id'], 'bom_item_unique');
        });
    }
}
