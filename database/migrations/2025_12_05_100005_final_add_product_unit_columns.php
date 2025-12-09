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
        // Add product_id column if it doesn't exist
        if (!Schema::hasColumn('customer_order_items', 'product_id')) {
            DB::statement('ALTER TABLE customer_order_items ADD COLUMN product_id BIGINT UNSIGNED NULL AFTER tender_item_id');
        }
        
        // Add foreign key for product_id (check if it exists first)
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'customer_order_items' 
            AND CONSTRAINT_NAME = 'customer_order_items_product_id_foreign'
        ");
        
        if (empty($foreignKeys)) {
            try {
                DB::statement('ALTER TABLE customer_order_items ADD CONSTRAINT customer_order_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE');
            } catch (\Exception $e) {
                // Ignore if constraint already exists
            }
        }

        // Add unit_id column if it doesn't exist
        if (!Schema::hasColumn('customer_order_items', 'unit_id')) {
            DB::statement('ALTER TABLE customer_order_items ADD COLUMN unit_id BIGINT UNSIGNED NULL AFTER product_id');
        }
        
        // Add foreign key for unit_id (check if it exists first)
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'customer_order_items' 
            AND CONSTRAINT_NAME = 'customer_order_items_unit_id_foreign'
        ");
        
        if (empty($foreignKeys)) {
            try {
                DB::statement('ALTER TABLE customer_order_items ADD CONSTRAINT customer_order_items_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL');
            } catch (\Exception $e) {
                // Ignore if constraint already exists
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('customer_order_items', 'product_id')) {
            try {
                DB::statement('ALTER TABLE customer_order_items DROP FOREIGN KEY customer_order_items_product_id_foreign');
            } catch (\Exception $e) {
                // Ignore
            }
            DB::statement('ALTER TABLE customer_order_items DROP COLUMN product_id');
        }

        if (Schema::hasColumn('customer_order_items', 'unit_id')) {
            try {
                DB::statement('ALTER TABLE customer_order_items DROP FOREIGN KEY customer_order_items_unit_id_foreign');
            } catch (\Exception $e) {
                // Ignore
            }
            DB::statement('ALTER TABLE customer_order_items DROP COLUMN unit_id');
        }
    }
};

