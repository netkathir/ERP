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
        // Use raw SQL to ensure columns are added
        try {
            // Add product_id column
            if (!Schema::hasColumn('customer_order_items', 'product_id')) {
                DB::statement('ALTER TABLE customer_order_items ADD COLUMN product_id BIGINT UNSIGNED NULL AFTER tender_item_id');
                DB::statement('ALTER TABLE customer_order_items ADD CONSTRAINT customer_order_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE');
            }
        } catch (\Exception $e) {
            // Column might already exist or constraint might exist
            if (strpos($e->getMessage(), 'Duplicate column name') === false && strpos($e->getMessage(), 'Duplicate key name') === false) {
                throw $e;
            }
        }

        try {
            // Add unit_id column
            if (!Schema::hasColumn('customer_order_items', 'unit_id')) {
                DB::statement('ALTER TABLE customer_order_items ADD COLUMN unit_id BIGINT UNSIGNED NULL AFTER product_id');
                DB::statement('ALTER TABLE customer_order_items ADD CONSTRAINT customer_order_items_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL');
            }
        } catch (\Exception $e) {
            // Column might already exist or constraint might exist
            if (strpos($e->getMessage(), 'Duplicate column name') === false && strpos($e->getMessage(), 'Duplicate key name') === false) {
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            if (Schema::hasColumn('customer_order_items', 'product_id')) {
                DB::statement('ALTER TABLE customer_order_items DROP FOREIGN KEY customer_order_items_product_id_foreign');
                DB::statement('ALTER TABLE customer_order_items DROP COLUMN product_id');
            }
        } catch (\Exception $e) {
            // Ignore if column doesn't exist
        }

        try {
            if (Schema::hasColumn('customer_order_items', 'unit_id')) {
                DB::statement('ALTER TABLE customer_order_items DROP FOREIGN KEY customer_order_items_unit_id_foreign');
                DB::statement('ALTER TABLE customer_order_items DROP COLUMN unit_id');
            }
        } catch (\Exception $e) {
            // Ignore if column doesn't exist
        }
    }
};

