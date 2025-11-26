<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveProcessIdFromBomProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if process_id column exists
        $columnExists = DB::select("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bom_processes' AND COLUMN_NAME = 'process_id'");
        
        if (empty($columnExists)) {
            // Column doesn't exist, just drop unique constraint if it exists
            try {
                DB::statement('ALTER TABLE `bom_processes` DROP INDEX `bom_process_unique`');
            } catch (\Exception $e) {
                // Index might not exist
            }
            return;
        }
        
        // Get all foreign keys that reference process_id
        $foreignKeys = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bom_processes' AND COLUMN_NAME = 'process_id' AND REFERENCED_TABLE_NAME IS NOT NULL");
        
        foreach ($foreignKeys as $fk) {
            try {
                DB::statement("ALTER TABLE `bom_processes` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
        }
        
        // Now drop unique constraint
        try {
            DB::statement('ALTER TABLE `bom_processes` DROP INDEX `bom_process_unique`');
        } catch (\Exception $e) {
            // Index might not exist or already dropped
        }
        
        // Drop the column
        try {
            DB::statement('ALTER TABLE `bom_processes` DROP COLUMN `process_id`');
        } catch (\Exception $e) {
            // Column might not exist
        }
        
        // No unique constraint - allow multiple BOMs for same product (duplicates allowed)
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bom_processes', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('bom_process_unique');
            
            // Add process_id column back
            $table->foreignId('process_id')->after('product_id')->constrained('processes')->onDelete('cascade');
            
            // Restore old unique constraint
            $table->unique(['product_id', 'process_id', 'branch_id'], 'bom_process_unique');
        });
    }
}
