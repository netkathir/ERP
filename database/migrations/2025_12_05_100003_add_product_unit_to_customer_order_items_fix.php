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
        // Check and add product_id
        if (!Schema::hasColumn('customer_order_items', 'product_id')) {
            Schema::table('customer_order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->nullable()->after('tender_item_id');
            });
            
            // Add foreign key constraint
            DB::statement('ALTER TABLE customer_order_items ADD CONSTRAINT customer_order_items_product_id_foreign FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE');
        }

        // Check and add unit_id
        if (!Schema::hasColumn('customer_order_items', 'unit_id')) {
            Schema::table('customer_order_items', function (Blueprint $table) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('product_id');
            });
            
            // Add foreign key constraint
            DB::statement('ALTER TABLE customer_order_items ADD CONSTRAINT customer_order_items_unit_id_foreign FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL');
        }

        // Make tender_item_id nullable if not already
        if (Schema::hasColumn('customer_order_items', 'tender_item_id')) {
            // Drop foreign key first
            try {
                DB::statement('ALTER TABLE customer_order_items DROP FOREIGN KEY customer_order_items_tender_item_id_foreign');
            } catch (\Exception $e) {
                // Foreign key might have different name or not exist
            }
            
            // Make nullable
            DB::statement('ALTER TABLE customer_order_items MODIFY tender_item_id BIGINT UNSIGNED NULL');
            
            // Re-add foreign key
            DB::statement('ALTER TABLE customer_order_items ADD CONSTRAINT customer_order_items_tender_item_id_foreign FOREIGN KEY (tender_item_id) REFERENCES tender_items(id) ON DELETE CASCADE');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('customer_order_items', 'product_id')) {
            DB::statement('ALTER TABLE customer_order_items DROP FOREIGN KEY customer_order_items_product_id_foreign');
            Schema::table('customer_order_items', function (Blueprint $table) {
                $table->dropColumn('product_id');
            });
        }

        if (Schema::hasColumn('customer_order_items', 'unit_id')) {
            DB::statement('ALTER TABLE customer_order_items DROP FOREIGN KEY customer_order_items_unit_id_foreign');
            Schema::table('customer_order_items', function (Blueprint $table) {
                $table->dropColumn('unit_id');
            });
        }
    }
};

