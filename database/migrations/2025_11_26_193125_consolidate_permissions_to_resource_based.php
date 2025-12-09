<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConsolidatePermissionsToResourceBased extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration consolidates action-based permissions into resource-based permissions.
     * 
     * Example:
     * OLD: "Branches - View", "Branches - Create", "Branches - Edit", "Branches - Delete"
     * NEW: "Branches" (single entry with Read/Write/Delete flags in pivot table)
     *
     * To run the consolidation, execute:
     * php artisan db:seed --class=ConsolidatePermissionsSeeder
     *
     * @return void
     */
    public function up()
    {
        // This is a data migration handled by ConsolidatePermissionsSeeder
        // No schema changes needed - the pivot table already has read/write/delete columns
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Note: This is a one-way data consolidation
        // Rollback would require restoring old permission structure
    }
}
