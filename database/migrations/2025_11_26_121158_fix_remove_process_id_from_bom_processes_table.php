<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixRemoveProcessIdFromBomProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if column exists
        $columnExists = DB::select("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bom_processes' AND COLUMN_NAME = 'process_id'");
        
        if (empty($columnExists)) {
            // Column doesn't exist, we're done
            return;
        }
        
        // Get all foreign keys that reference process_id
        $foreignKeys = DB::select("SELECT DISTINCT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bom_processes' AND COLUMN_NAME = 'process_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
        
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `bom_processes` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Continue even if foreign key doesn't exist
            }
        }
        
        // Drop all indexes that include process_id
        $indexes = DB::select("SELECT DISTINCT INDEX_NAME FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bom_processes' AND COLUMN_NAME = 'process_id'");
        
        foreach ($indexes as $index) {
            try {
                if ($index->INDEX_NAME !== 'PRIMARY') {
                    DB::statement("ALTER TABLE `bom_processes` DROP INDEX `{$index->INDEX_NAME}`");
                }
            } catch (\Exception $e) {
                // Continue if index doesn't exist
            }
        }
        
        // Make it nullable first if it's not already
        try {
            DB::statement('ALTER TABLE `bom_processes` MODIFY COLUMN `process_id` BIGINT UNSIGNED NULL');
        } catch (\Exception $e) {
            // Might already be nullable
        }
        
        // Now drop the column - check if it still exists first
        $stillExists = DB::select("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bom_processes' AND COLUMN_NAME = 'process_id'");
        
        if (!empty($stillExists)) {
            try {
                DB::statement('ALTER TABLE `bom_processes` DROP COLUMN `process_id`');
            } catch (\Exception $e) {
                // If it fails, the column might be referenced elsewhere
                // Try to drop it using a different approach
                \Log::warning('Could not drop process_id column: ' . $e->getMessage());
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
        // Add process_id back if needed
        Schema::table('bom_processes', function (Blueprint $table) {
            if (!Schema::hasColumn('bom_processes', 'process_id')) {
                $table->foreignId('process_id')->nullable()->after('product_id')->constrained('processes')->onDelete('cascade');
            }
        });
    }
}
