<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to modify the column to be nullable
        try {
            DB::statement("ALTER TABLE `purchase_indent_items` MODIFY COLUMN `special_instructions` TEXT NULL");
        } catch (\Exception $e) {
            // If error, try alternative approach
            try {
                DB::statement("ALTER TABLE `purchase_indent_items` CHANGE COLUMN `special_instructions` `special_instructions` TEXT NULL");
            } catch (\Exception $e2) {
                // Log error but don't fail
                \Log::error('Failed to make special_instructions nullable: ' . $e2->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to NOT NULL (but this might fail if there are NULL values)
        try {
            DB::statement("UPDATE purchase_indent_items SET special_instructions = '' WHERE special_instructions IS NULL");
        } catch (\Exception $e) {
            // Ignore if update fails
        }
        
        Schema::table('purchase_indent_items', function (Blueprint $table) {
            $table->text('special_instructions')->nullable(false)->change();
        });
    }
};

