-- =====================================================
-- SQL Queries to Add Tax and GST Fields to customer_orders Table
-- =====================================================
-- These queries add all the missing tax_type and GST calculation fields
-- =====================================================

-- Add tax_type column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `tax_type` ENUM('cgst_sgst', 'igst') DEFAULT 'cgst_sgst';

-- Add total_amount column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `total_amount` DECIMAL(15,2) DEFAULT 0;

-- Add gst_percent column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `gst_percent` DECIMAL(5,2) DEFAULT 0;

-- Add cgst_percent column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `cgst_percent` DECIMAL(5,2) DEFAULT 0;

-- Add cgst_amount column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `cgst_amount` DECIMAL(15,2) DEFAULT 0;

-- Add sgst_percent column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `sgst_percent` DECIMAL(5,2) DEFAULT 0;

-- Add sgst_amount column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `sgst_amount` DECIMAL(15,2) DEFAULT 0;

-- Add igst_percent column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `igst_percent` DECIMAL(5,2) DEFAULT 0;

-- Add igst_amount column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `igst_amount` DECIMAL(15,2) DEFAULT 0;

-- Add freight column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `freight` DECIMAL(15,2) DEFAULT 0;

-- Add inspection_charges column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `inspection_charges` DECIMAL(15,2) DEFAULT 0;

-- Add net_amount column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `net_amount` DECIMAL(15,2) DEFAULT 0;

-- Add amount_note column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `amount_note` TEXT NULL;

-- =====================================================
-- Alternative: Direct queries (if IF NOT EXISTS is not supported)
-- =====================================================

-- ALTER TABLE `customer_orders` 
-- ADD COLUMN `tax_type` ENUM('cgst_sgst', 'igst') DEFAULT 'cgst_sgst',
-- ADD COLUMN `total_amount` DECIMAL(15,2) DEFAULT 0,
-- ADD COLUMN `gst_percent` DECIMAL(5,2) DEFAULT 0,
-- ADD COLUMN `cgst_percent` DECIMAL(5,2) DEFAULT 0,
-- ADD COLUMN `cgst_amount` DECIMAL(15,2) DEFAULT 0,
-- ADD COLUMN `sgst_percent` DECIMAL(5,2) DEFAULT 0,
-- ADD COLUMN `sgst_amount` DECIMAL(15,2) DEFAULT 0,
-- ADD COLUMN `igst_percent` DECIMAL(5,2) DEFAULT 0,
-- ADD COLUMN `igst_amount` DECIMAL(15,2) DEFAULT 0,
-- ADD COLUMN `freight` DECIMAL(15,2) DEFAULT 0,
-- ADD COLUMN `inspection_charges` DECIMAL(15,2) DEFAULT 0,
-- ADD COLUMN `net_amount` DECIMAL(15,2) DEFAULT 0,
-- ADD COLUMN `amount_note` TEXT NULL;

-- =====================================================
-- Verify columns were added
-- =====================================================

SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'customer_orders' 
  AND COLUMN_NAME IN (
    'tax_type', 'total_amount', 'gst_percent', 'gst_amount',
    'cgst_percent', 'cgst_amount', 'sgst_percent', 'sgst_amount',
    'igst_percent', 'igst_amount', 'freight', 'inspection_charges',
    'net_amount', 'amount_note'
  )
ORDER BY ORDINAL_POSITION;

