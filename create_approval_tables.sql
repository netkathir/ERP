-- Create approval_masters table
CREATE TABLE IF NOT EXISTS `approval_masters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `form_name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `approval_masters_form_name_unique` (`form_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create approval_mappings table
CREATE TABLE IF NOT EXISTS `approval_mappings` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add approval columns to customer_orders if they don't exist
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `approval_status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending' AFTER `net_amount`,
ADD COLUMN IF NOT EXISTS `approved_by` bigint(20) unsigned NULL AFTER `approval_status`,
ADD COLUMN IF NOT EXISTS `approved_at` timestamp NULL DEFAULT NULL AFTER `approved_by`,
ADD COLUMN IF NOT EXISTS `approval_remarks` text NULL AFTER `approved_at`,
ADD COLUMN IF NOT EXISTS `rejected_by` bigint(20) unsigned NULL AFTER `approval_remarks`,
ADD COLUMN IF NOT EXISTS `rejected_at` timestamp NULL DEFAULT NULL AFTER `rejected_by`,
ADD COLUMN IF NOT EXISTS `rejection_remarks` text NULL AFTER `rejected_at`;

-- Add foreign keys if they don't exist
SET @dbname = DATABASE();
SET @tablename = "customer_orders";
SET @columnname = "approved_by";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
    AND REFERENCED_TABLE_NAME IS NOT NULL
  ) > 0,
  "SELECT 'Foreign key already exists' AS result",
  CONCAT("ALTER TABLE ", @tablename, " ADD CONSTRAINT customer_orders_approved_by_foreign FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @columnname = "rejected_by";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = @dbname
    AND TABLE_NAME = @tablename
    AND COLUMN_NAME = @columnname
    AND REFERENCED_TABLE_NAME IS NOT NULL
  ) > 0,
  "SELECT 'Foreign key already exists' AS result",
  CONCAT("ALTER TABLE ", @tablename, " ADD CONSTRAINT customer_orders_rejected_by_foreign FOREIGN KEY (rejected_by) REFERENCES users(id) ON DELETE SET NULL")
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

