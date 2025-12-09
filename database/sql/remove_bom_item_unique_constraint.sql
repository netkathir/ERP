-- =====================================================
-- SQL Script to Remove Unique Constraint from bom_process_items
-- This allows duplicate raw material entries in BOM Process items
-- =====================================================

-- Step 1: Find the foreign key on bom_process_id
-- SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
-- WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bom_process_items' 
-- AND COLUMN_NAME = 'bom_process_id' AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Step 2: Drop the foreign key (replace 'FK_NAME' with actual name)
-- ALTER TABLE `bom_process_items` DROP FOREIGN KEY `FK_NAME`;

-- Step 3: Drop the unique constraint
ALTER TABLE `bom_process_items` DROP INDEX `bom_item_unique`;

-- Step 4: Recreate the foreign key without the unique constraint
-- ALTER TABLE `bom_process_items` ADD CONSTRAINT `bom_process_items_bom_process_id_foreign` 
-- FOREIGN KEY (`bom_process_id`) REFERENCES `bom_processes` (`id`) ON DELETE CASCADE;

-- =====================================================
-- Result:
-- - Multiple rows with same raw_material_id and process_id are now allowed
-- - Duplicate entries can be added to the child table
-- =====================================================

