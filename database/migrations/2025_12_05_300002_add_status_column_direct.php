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
        // Directly execute SQL to add status column
        try {
            DB::statement("ALTER TABLE `customer_orders` ADD COLUMN `status` VARCHAR(255) NOT NULL DEFAULT 'Pending'");
        } catch (\Exception $e) {
            // Column might already exist, check and continue
            $columns = DB::select("SHOW COLUMNS FROM `customer_orders` LIKE 'status'");
            if (empty($columns)) {
                throw $e; // Re-throw if column doesn't exist and we couldn't add it
            }
        }

        // Add updated_by_id column
        try {
            DB::statement("ALTER TABLE `customer_orders` ADD COLUMN `updated_by_id` BIGINT UNSIGNED NULL");
        } catch (\Exception $e) {
            // Column might already exist, check and continue
            $columns = DB::select("SHOW COLUMNS FROM `customer_orders` LIKE 'updated_by_id'");
            if (empty($columns)) {
                throw $e; // Re-throw if column doesn't exist and we couldn't add it
            }
        }

        // Add foreign key for updated_by_id
        try {
            DB::statement("ALTER TABLE `customer_orders` ADD CONSTRAINT `customer_orders_updated_by_id_foreign` FOREIGN KEY (`updated_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL");
        } catch (\Exception $e) {
            // Foreign key might already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE `customer_orders` DROP FOREIGN KEY `customer_orders_updated_by_id_foreign`");
        } catch (\Exception $e) {
            // Ignore if doesn't exist
        }
        
        Schema::table('customer_orders', function (Blueprint $table) {
            if (Schema::hasColumn('customer_orders', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('customer_orders', 'updated_by_id')) {
                $table->dropColumn('updated_by_id');
            }
        });
    }
};

