-- Make special_instructions nullable in purchase_indent_items table
ALTER TABLE `purchase_indent_items` MODIFY COLUMN `special_instructions` TEXT NULL;

