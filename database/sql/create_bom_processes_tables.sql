-- =====================================================
-- SQL Queries for BOM Process Tables (Updated Structure)
-- Created: 2025-11-26
-- Updated: 2025-11-26 (Removed process_id from bom_processes, added to bom_process_items)
-- Description: Tables for storing Bill of Materials (BOM) Process information
-- =====================================================

-- =====================================================
-- 1. Create bom_processes table (Header/Parent Table)
-- Note: process_id has been removed from this table
-- =====================================================
CREATE TABLE IF NOT EXISTS `bom_processes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bom_processes_product_id_foreign` (`product_id`),
  KEY `bom_processes_branch_id_foreign` (`branch_id`),
  CONSTRAINT `bom_processes_product_id_foreign` 
    FOREIGN KEY (`product_id`) 
    REFERENCES `products` (`id`) 
    ON DELETE CASCADE,
  CONSTRAINT `bom_processes_branch_id_foreign` 
    FOREIGN KEY (`branch_id`) 
    REFERENCES `branches` (`id`) 
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 2. Create bom_process_items table (Child/Detail Table)
-- Note: process_id has been added to this table (nullable)
-- Note: Unique constraint has been removed to allow duplicate entries
-- =====================================================
CREATE TABLE IF NOT EXISTS `bom_process_items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bom_process_id` bigint(20) UNSIGNED NOT NULL,
  `process_id` bigint(20) UNSIGNED DEFAULT NULL,
  `raw_material_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(15,4) NOT NULL DEFAULT 0.0000,
  `unit_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bom_process_items_bom_process_id_foreign` (`bom_process_id`),
  KEY `bom_process_items_process_id_foreign` (`process_id`),
  KEY `bom_process_items_raw_material_id_foreign` (`raw_material_id`),
  KEY `bom_process_items_unit_id_foreign` (`unit_id`),
  CONSTRAINT `bom_process_items_bom_process_id_foreign` 
    FOREIGN KEY (`bom_process_id`) 
    REFERENCES `bom_processes` (`id`) 
    ON DELETE CASCADE,
  CONSTRAINT `bom_process_items_process_id_foreign` 
    FOREIGN KEY (`process_id`) 
    REFERENCES `processes` (`id`) 
    ON DELETE CASCADE,
  CONSTRAINT `bom_process_items_raw_material_id_foreign` 
    FOREIGN KEY (`raw_material_id`) 
    REFERENCES `raw_materials` (`id`) 
    ON DELETE CASCADE,
  CONSTRAINT `bom_process_items_unit_id_foreign` 
    FOREIGN KEY (`unit_id`) 
    REFERENCES `units` (`id`) 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Notes:
-- 
-- bom_processes table:
-- 1. Links a Product with a BOM (one BOM per Product)
-- 2. process_id has been removed - process is now at item level
-- 3. No unique constraint - allows multiple BOMs for same product
-- 4. Foreign keys cascade delete to maintain referential integrity
-- 5. branch_id is nullable for multi-branch support
--
-- bom_process_items table:
-- 1. Stores raw materials with quantities, UOM, and process for each BOM item
-- 2. process_id is nullable - each item can have its own process
-- 3. No unique constraint - allows duplicate raw material entries
-- 4. quantity is stored as DECIMAL(15,4) for precision (supports up to 4 decimal places)
-- 5. Foreign keys cascade delete items when BOM is deleted
-- 6. unit_id uses RESTRICT to prevent deletion of units in use
-- =====================================================
