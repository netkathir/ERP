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
        try {
            // Create approval_masters table if it doesn't exist
            if (!Schema::hasTable('approval_masters')) {
                DB::statement("
                    CREATE TABLE `approval_masters` (
                      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                      `form_name` varchar(255) NOT NULL,
                      `display_name` varchar(255) NOT NULL,
                      `description` text DEFAULT NULL,
                      `is_active` tinyint(1) NOT NULL DEFAULT 1,
                      `created_at` timestamp NULL DEFAULT NULL,
                      `updated_at` timestamp NULL DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      UNIQUE KEY `approval_masters_form_name_unique` (`form_name`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
            }

            // Create approval_mappings table if it doesn't exist
            if (!Schema::hasTable('approval_mappings')) {
                DB::statement("
                    CREATE TABLE `approval_mappings` (
                      `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                      `approval_master_id` bigint(20) unsigned NOT NULL,
                      `user_id` bigint(20) unsigned NOT NULL,
                      `approval_order` int(11) NOT NULL DEFAULT 1,
                      `is_active` tinyint(1) NOT NULL DEFAULT 1,
                      `created_at` timestamp NULL DEFAULT NULL,
                      `updated_at` timestamp NULL DEFAULT NULL,
                      PRIMARY KEY (`id`),
                      UNIQUE KEY `approval_mappings_approval_master_id_user_id_unique` (`approval_master_id`,`user_id`),
                      KEY `approval_mappings_approval_master_id_foreign` (`approval_master_id`),
                      KEY `approval_mappings_user_id_foreign` (`user_id`),
                      CONSTRAINT `approval_mappings_approval_master_id_foreign` FOREIGN KEY (`approval_master_id`) REFERENCES `approval_masters` (`id`) ON DELETE CASCADE,
                      CONSTRAINT `approval_mappings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ");
            }

            // Add approval fields to customer_orders if they don't exist
            if (Schema::hasTable('customer_orders')) {
                $columns = Schema::getColumnListing('customer_orders');
                
                if (!in_array('approval_status', $columns)) {
                    DB::statement("ALTER TABLE `customer_orders` ADD COLUMN `approval_status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' AFTER `net_amount`");
                }

                if (!in_array('approved_by', $columns)) {
                    DB::statement("ALTER TABLE `customer_orders` ADD COLUMN `approved_by` bigint(20) unsigned NULL AFTER `approval_status`");
                    // Add foreign key
                    try {
                        DB::statement("ALTER TABLE `customer_orders` ADD CONSTRAINT `customer_orders_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL");
                    } catch (\Exception $e) {
                        // Foreign key might already exist, ignore
                    }
                }

                if (!in_array('approved_at', $columns)) {
                    DB::statement("ALTER TABLE `customer_orders` ADD COLUMN `approved_at` timestamp NULL DEFAULT NULL AFTER `approved_by`");
                }

                if (!in_array('approval_remarks', $columns)) {
                    DB::statement("ALTER TABLE `customer_orders` ADD COLUMN `approval_remarks` text NULL AFTER `approved_at`");
                }

                if (!in_array('rejected_by', $columns)) {
                    DB::statement("ALTER TABLE `customer_orders` ADD COLUMN `rejected_by` bigint(20) unsigned NULL AFTER `approval_remarks`");
                    // Add foreign key
                    try {
                        DB::statement("ALTER TABLE `customer_orders` ADD CONSTRAINT `customer_orders_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL");
                    } catch (\Exception $e) {
                        // Foreign key might already exist, ignore
                    }
                }

                if (!in_array('rejected_at', $columns)) {
                    DB::statement("ALTER TABLE `customer_orders` ADD COLUMN `rejected_at` timestamp NULL DEFAULT NULL AFTER `rejected_by`");
                }

                if (!in_array('rejection_remarks', $columns)) {
                    DB::statement("ALTER TABLE `customer_orders` ADD COLUMN `rejection_remarks` text NULL AFTER `rejected_at`");
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail migration
            \Log::error('Error creating approval tables: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop tables in down() to avoid data loss
    }
};
