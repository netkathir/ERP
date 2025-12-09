-- =====================================================
-- SQL Script to Remove process_id from bom_processes table
-- Run this manually if the migration doesn't work
-- =====================================================

-- Step 1: Drop foreign key (if exists)
-- First, find the foreign key name:
-- SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE 
-- WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bom_processes' 
-- AND COLUMN_NAME = 'process_id' AND REFERENCED_TABLE_NAME IS NOT NULL;

-- Then drop it (replace 'FK_NAME' with actual name):
-- ALTER TABLE `bom_processes` DROP FOREIGN KEY `FK_NAME`;

-- Step 2: Drop unique index that includes process_id
ALTER TABLE `bom_processes` DROP INDEX `bom_process_unique`;

-- Step 3: Drop the column
ALTER TABLE `bom_processes` DROP COLUMN `process_id`;

-- Step 4: (Optional) Recreate unique constraint without process_id if needed
-- ALTER TABLE `bom_processes` ADD UNIQUE KEY `bom_process_unique` (`product_id`, `branch_id`);

