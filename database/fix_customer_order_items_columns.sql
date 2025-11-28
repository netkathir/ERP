-- SQL Queries to Add Missing Columns to customer_order_items Table
-- Execute these queries if the migration doesn't work or you need to manually fix the database

-- Check if columns exist before adding (MySQL syntax)
-- For customer_order_items table:

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

-- Note: If your MySQL version doesn't support "IF NOT EXISTS" in ALTER TABLE,
-- use these queries instead (check first, then add):

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

