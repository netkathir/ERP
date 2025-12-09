<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddFormNameToPermissionsTableIfNotExists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if form_name column exists using raw SQL
        $columns = DB::select("SHOW COLUMNS FROM permissions LIKE 'form_name'");
        
        if (empty($columns)) {
            // Add form_name column
            DB::statement("ALTER TABLE permissions ADD COLUMN form_name VARCHAR(255) NULL AFTER name");
            
            // Copy name to form_name for existing records
            DB::statement("UPDATE permissions SET form_name = name WHERE form_name IS NULL");
            
            // Make form_name unique and not null
            DB::statement("ALTER TABLE permissions MODIFY COLUMN form_name VARCHAR(255) NOT NULL");
            DB::statement("ALTER TABLE permissions ADD UNIQUE KEY permissions_form_name_unique (form_name)");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Check if form_name column exists before dropping
        $columns = DB::select("SHOW COLUMNS FROM permissions LIKE 'form_name'");
        
        if (!empty($columns)) {
            // Drop unique constraint first
            try {
                DB::statement("ALTER TABLE permissions DROP INDEX permissions_form_name_unique");
            } catch (\Exception $e) {
                // Index might not exist
            }
            
            // Drop column
            DB::statement("ALTER TABLE permissions DROP COLUMN form_name");
        }
    }
}
