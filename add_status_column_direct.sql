-- Add status column to customer_orders table
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `status` VARCHAR(255) NOT NULL DEFAULT 'Pending' AFTER `net_amount`;

-- Add updated_by_id column
ALTER TABLE `customer_orders` 
ADD COLUMN IF NOT EXISTS `updated_by_id` BIGINT UNSIGNED NULL AFTER `status`;

-- Add foreign key for updated_by_id
ALTER TABLE `customer_orders` 
ADD CONSTRAINT `customer_orders_updated_by_id_foreign` 
FOREIGN KEY (`updated_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

