<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ForceRemoveBomItemUniqueConstraint extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if the unique index exists
        $indexExists = DB::select("SELECT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bom_process_items' AND INDEX_NAME = 'bom_item_unique'");
        
        if (!empty($indexExists)) {
            // The unique index is being used by a foreign key constraint
            // We need to find and drop the foreign key that references bom_process_id first
            
            // Get foreign key on bom_process_id
            $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bom_process_items' AND COLUMN_NAME = 'bom_process_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
            
            foreach ($foreignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE `bom_process_items` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
                } catch (\Exception $e) {
                    // Continue if foreign key doesn't exist
                }
            }
            
            // Now drop the unique index
            try {
                DB::statement('ALTER TABLE `bom_process_items` DROP INDEX `bom_item_unique`');
            } catch (\Exception $e) {
                \Log::error('Failed to drop bom_item_unique index: ' . $e->getMessage());
            }
            
            // Recreate the foreign key without the unique constraint
            foreach ($foreignKeys as $fk) {
                try {
                    DB::statement("ALTER TABLE `bom_process_items` ADD CONSTRAINT `{$fk->CONSTRAINT_NAME}` FOREIGN KEY (`bom_process_id`) REFERENCES `bom_processes` (`id`) ON DELETE CASCADE");
                } catch (\Exception $e) {
                    // Foreign key might already exist or have different name
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Restore the unique constraint if needed
        Schema::table('bom_process_items', function (Blueprint $table) {
            $table->unique(['bom_process_id', 'raw_material_id'], 'bom_item_unique');
        });
    }
}
