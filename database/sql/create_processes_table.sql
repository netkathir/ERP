-- =====================================================
-- SQL Query for Processes Table
-- Created: 2025-11-26
-- Description: Master table for storing process information
-- =====================================================

-- Create processes table
CREATE TABLE `processes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `branch_id` BIGINT UNSIGNED NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `processes_name_unique` (`name`),
  KEY `processes_branch_id_foreign` (`branch_id`),
  CONSTRAINT `processes_branch_id_foreign` 
    FOREIGN KEY (`branch_id`) 
    REFERENCES `branches` (`id`) 
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Notes:
-- 1. The 'name' field is unique to prevent duplicate process names
-- 2. The 'description' field is nullable (optional)
-- 3. The 'branch_id' is nullable and has a foreign key constraint
--    that sets the value to NULL if the referenced branch is deleted
-- 4. Timestamps are automatically managed by Laravel
-- =====================================================

