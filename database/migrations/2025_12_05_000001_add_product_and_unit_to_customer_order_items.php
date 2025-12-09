<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customer_order_items', function (Blueprint $table) {
            // Drop foreign key constraint if it exists
            $table->dropForeign(['tender_item_id']);
            
            // Make tender_item_id nullable (for backward compatibility)
            $table->unsignedBigInteger('tender_item_id')->nullable()->change();
            
            // Re-add foreign key constraint (nullable)
            $table->foreign('tender_item_id')->references('id')->on('tender_items')->onDelete('cascade');
        });

        Schema::table('customer_order_items', function (Blueprint $table) {
            // Add product_id and unit_id
            if (!Schema::hasColumn('customer_order_items', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->after('tender_item_id');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            }
            if (!Schema::hasColumn('customer_order_items', 'unit_id')) {
                $table->unsignedBigInteger('unit_id')->nullable()->after('product_id');
                $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('customer_order_items', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
            if (Schema::hasColumn('customer_order_items', 'unit_id')) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            }
            // Revert tender_item_id to not nullable if needed
            $table->foreignId('tender_item_id')->nullable(false)->change();
        });
    }
};

