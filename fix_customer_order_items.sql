-- Add product_id column
ALTER TABLE customer_order_items 
ADD COLUMN product_id BIGINT UNSIGNED NULL AFTER tender_item_id;

-- Add foreign key for product_id
ALTER TABLE customer_order_items 
ADD CONSTRAINT customer_order_items_product_id_foreign 
FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE;

-- Add unit_id column
ALTER TABLE customer_order_items 
ADD COLUMN unit_id BIGINT UNSIGNED NULL AFTER product_id;

-- Add foreign key for unit_id
ALTER TABLE customer_order_items 
ADD CONSTRAINT customer_order_items_unit_id_foreign 
FOREIGN KEY (unit_id) REFERENCES units(id) ON DELETE SET NULL;

-- Make tender_item_id nullable (if not already)
ALTER TABLE customer_order_items 
MODIFY tender_item_id BIGINT UNSIGNED NULL;

