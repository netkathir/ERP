-- =====================================================
-- SQL Queries for Customer Orders Database Changes
-- =====================================================
-- Execute these queries to add missing columns to customer_orders and customer_order_items tables
-- =====================================================

-- =====================================================
-- 1. Add missing columns to customer_order_items table
-- =====================================================

-- Add description column if it doesn't exist
ALTER TABLE `customer_order_items` 
ADD COLUMN IF NOT EXISTS `description` TEXT NULL AFTER `ordered_qty`;

-- Add pl_code column if it doesn't exist
ALTER TABLE `customer_order_items` 
ADD COLUMN IF NOT EXISTS `pl_code` VARCHAR(255) NULL AFTER `description`;

-- Add unit_price column if it doesn't exist
ALTER TABLE `customer_order_items` 
ADD COLUMN IF NOT EXISTS `unit_price` DECIMAL(15,4) DEFAULT 0 AFTER `pl_code`;

-- Add installation_charges column if it doesn't exist
ALTER TABLE `customer_order_items` 
ADD COLUMN IF NOT EXISTS `installation_charges` DECIMAL(15,2) DEFAULT 0 AFTER `unit_price`;

-- Add line_amount column if it doesn't exist
ALTER TABLE `customer_order_items` 
ADD COLUMN IF NOT EXISTS `line_amount` DECIMAL(15,2) DEFAULT 0 AFTER `installation_charges`;

-- =====================================================
-- 2. Add gst_amount column to customer_orders table
-- =====================================================

-- Add gst_amount column if it doesn't exist
-- Note: If gst_percent column exists, it will be added after it, otherwise at the end
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `gst_amount` DECIMAL(15,2) DEFAULT 0;

-- If you need to position it after gst_percent (and gst_percent exists), use:
-- ALTER TABLE `customer_orders` 
-- MODIFY COLUMN `gst_amount` DECIMAL(15,2) DEFAULT 0 AFTER `gst_percent`;

-- =====================================================
-- Alternative: Manual queries if IF NOT EXISTS is not supported
-- =====================================================

-- For customer_order_items table:
-- Check and add description
SET @dbname = DATABASE();
SET @tablename = 'customer_order_items';
SET @columnname = 'description';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' TEXT NULL AFTER `ordered_qty`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Check and add pl_code
SET @columnname = 'pl_code';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' VARCHAR(255) NULL AFTER `description`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Check and add unit_price
SET @columnname = 'unit_price';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' DECIMAL(15,4) DEFAULT 0 AFTER `pl_code`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Check and add installation_charges
SET @columnname = 'installation_charges';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' DECIMAL(15,2) DEFAULT 0 AFTER `unit_price`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Check and add line_amount
SET @columnname = 'line_amount';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' DECIMAL(15,2) DEFAULT 0 AFTER `installation_charges`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- For customer_orders table:
-- Check and add gst_amount
SET @tablename = 'customer_orders';
SET @columnname = 'gst_amount';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1',
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' DECIMAL(15,2) DEFAULT 0')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- Simple Direct Queries (if columns don't exist)
-- =====================================================
-- Use these if you're sure the columns don't exist:

-- ALTER TABLE `customer_order_items` 
-- ADD COLUMN `description` TEXT NULL AFTER `ordered_qty`,
-- ADD COLUMN `pl_code` VARCHAR(255) NULL AFTER `description`,
-- ADD COLUMN `unit_price` DECIMAL(15,4) DEFAULT 0 AFTER `pl_code`,
-- ADD COLUMN `installation_charges` DECIMAL(15,2) DEFAULT 0 AFTER `unit_price`,
-- ADD COLUMN `line_amount` DECIMAL(15,2) DEFAULT 0 AFTER `installation_charges`;

-- ALTER TABLE `customer_orders` 
-- ADD COLUMN `gst_amount` DECIMAL(15,2) DEFAULT 0;

-- =====================================================
-- Verify columns were added
-- =====================================================

-- Check customer_order_items columns
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'customer_order_items' 
  AND COLUMN_NAME IN ('description', 'pl_code', 'unit_price', 'installation_charges', 'line_amount')
ORDER BY ORDINAL_POSITION;

-- Check customer_orders gst_amount column
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'customer_orders' 
  AND COLUMN_NAME = 'gst_amount';

