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
        // Use raw SQL to ensure the column is added
        if (!Schema::hasColumn('customer_orders', 'status')) {
            DB::statement("ALTER TABLE `customer_orders` ADD COLUMN `status` VARCHAR(255) NOT NULL DEFAULT 'Pending' AFTER `net_amount`");
        }

        if (!Schema::hasColumn('customer_orders', 'updated_by_id')) {
            // First add the column
            DB::statement("ALTER TABLE `customer_orders` ADD COLUMN `updated_by_id` BIGINT UNSIGNED NULL AFTER `status`");
            
            // Then add the foreign key
            try {
                DB::statement("ALTER TABLE `customer_orders` ADD CONSTRAINT `customer_orders_updated_by_id_foreign` FOREIGN KEY (`updated_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL");
            } catch (\Exception $e) {
                // Foreign key might already exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('customer_orders', 'status')) {
            Schema::table('customer_orders', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
        
        if (Schema::hasColumn('customer_orders', 'updated_by_id')) {
            Schema::table('customer_orders', function (Blueprint $table) {
                $table->dropForeign(['updated_by_id']);
                $table->dropColumn('updated_by_id');
            });
        }
    }
};

