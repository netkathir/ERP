-- =====================================================
-- SQL Query for Production Departments Table
-- Created: 2025-11-26
-- Description: Creates the production_departments table
-- =====================================================

CREATE TABLE `production_departments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `branch_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `production_departments_name_unique` (`name`),
  KEY `production_departments_branch_id_foreign` (`branch_id`),
  CONSTRAINT `production_departments_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

