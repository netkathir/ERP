-- =====================================================
-- SQL Queries for BOM Process Tables Updates
-- Created: 2025-11-26
-- Description: Remove process_id from bom_processes table and remove unique constraint from bom_process_items
-- =====================================================

-- =====================================================
-- 1. Remove process_id from bom_processes table
-- =====================================================

-- Step 1: Drop foreign key constraint (get actual name first)
-- Run this query to find the foreign key name:
-- SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
-- WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bom_processes' 
-- AND COLUMN_NAME = 'process_id' AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Then drop it (replace 'FK_NAME' with actual name):
-- ALTER TABLE `bom_processes` DROP FOREIGN KEY `FK_NAME`;

-- Step 2: Drop unique constraint
ALTER TABLE `bom_processes` DROP INDEX `bom_process_unique`;

-- Step 3: Drop process_id column
ALTER TABLE `bom_processes` DROP COLUMN `process_id`;

-- Note: No new unique constraint is added - duplicates are now allowed

-- =====================================================
-- 2. Remove unique constraint from bom_process_items table
-- =====================================================

-- Drop the unique constraint to allow duplicate entries
ALTER TABLE `bom_process_items` DROP INDEX `bom_item_unique`;

-- =====================================================
-- Notes:
-- 
-- After these changes:
-- 1. bom_processes table no longer has process_id column
-- 2. bom_process_items table allows duplicate raw material + process combinations
-- 3. Multiple BOMs can be created for the same product
-- 4. Each item in the child table has its own process_id
-- =====================================================

