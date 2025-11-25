<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakeOrganizationIdNullableInBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Get the actual foreign key constraint name
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'branches' 
            AND COLUMN_NAME = 'organization_id' 
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        // Drop foreign key if it exists
        if (!empty($foreignKeys)) {
            $fkName = $foreignKeys[0]->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE `branches` DROP FOREIGN KEY `{$fkName}`");
        }
        
        // Drop the unique constraint if it exists
        try {
            Schema::table('branches', function (Blueprint $table) {
                $table->dropUnique(['organization_id', 'code']);
            });
        } catch (\Exception $e) {
            // Constraint might not exist, continue
        }
        
        // Modify the column to be nullable using raw SQL
        DB::statement('ALTER TABLE `branches` MODIFY `organization_id` BIGINT UNSIGNED NULL');
        
        // Re-add foreign key with nullable (optional, since we're not using organizations)
        Schema::table('branches', function (Blueprint $table) {
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('set null');
        });
        
        // Make code unique globally (since no organization dependency)
        Schema::table('branches', function (Blueprint $table) {
            $table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branches', function (Blueprint $table) {
            // Drop the unique constraint on code
            $table->dropUnique(['code']);
            
            // Drop the foreign key
            $table->dropForeign(['organization_id']);
        });
        
        // Make organization_id required again using raw SQL
        DB::statement('ALTER TABLE `branches` MODIFY `organization_id` BIGINT UNSIGNED NOT NULL');
        
        Schema::table('branches', function (Blueprint $table) {
            // Re-add foreign key
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            
            // Re-add composite unique constraint
            $table->unique(['organization_id', 'code']);
        });
    }
}
