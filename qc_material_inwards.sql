-- SQL Queries for QC Material Inward Tables
-- Execute these queries on your live database

-- Table: qc_material_inwards
CREATE TABLE `qc_material_inwards` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `qc_material_no` varchar(255) NOT NULL,
  `material_inward_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `branch_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `qc_material_inwards_qc_material_no_unique` (`qc_material_no`),
  KEY `qc_material_inwards_material_inward_id_foreign` (`material_inward_id`),
  KEY `qc_material_inwards_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `qc_material_inwards_supplier_id_foreign` (`supplier_id`),
  KEY `qc_material_inwards_branch_id_foreign` (`branch_id`),
  KEY `qc_material_inwards_created_by_id_foreign` (`created_by_id`),
  KEY `qc_material_inwards_updated_by_id_foreign` (`updated_by_id`),
  CONSTRAINT `qc_material_inwards_material_inward_id_foreign` FOREIGN KEY (`material_inward_id`) REFERENCES `material_inwards` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `qc_material_inwards_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `qc_material_inwards_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `qc_material_inwards_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  CONSTRAINT `qc_material_inwards_created_by_id_foreign` FOREIGN KEY (`created_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `qc_material_inwards_updated_by_id_foreign` FOREIGN KEY (`updated_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: qc_material_inward_items
CREATE TABLE `qc_material_inward_items` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `qc_material_inward_id` bigint(20) UNSIGNED NOT NULL,
  `material_inward_item_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_order_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `raw_material_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_description` text DEFAULT NULL,
  `received_qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `received_qty_in_kg` decimal(15,3) DEFAULT NULL,
  `unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `batch_no` varchar(255) DEFAULT NULL,
  `supplier_invoice_no` varchar(255) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `given_qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `accepted_qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `rejected_qty` decimal(15,3) NOT NULL DEFAULT 0.000,
  `rejection_reason` text DEFAULT NULL,
  `qc_completed` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `qc_material_inward_items_qc_material_inward_id_foreign` (`qc_material_inward_id`),
  KEY `qc_material_inward_items_material_inward_item_id_foreign` (`material_inward_item_id`),
  KEY `qc_material_inward_items_purchase_order_item_id_foreign` (`purchase_order_item_id`),
  KEY `qc_material_inward_items_raw_material_id_foreign` (`raw_material_id`),
  KEY `qc_material_inward_items_unit_id_foreign` (`unit_id`),
  CONSTRAINT `qc_material_inward_items_qc_material_inward_id_foreign` FOREIGN KEY (`qc_material_inward_id`) REFERENCES `qc_material_inwards` (`id`) ON DELETE CASCADE,
  CONSTRAINT `qc_material_inward_items_material_inward_item_id_foreign` FOREIGN KEY (`material_inward_item_id`) REFERENCES `material_inward_items` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `qc_material_inward_items_purchase_order_item_id_foreign` FOREIGN KEY (`purchase_order_item_id`) REFERENCES `purchase_order_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `qc_material_inward_items_raw_material_id_foreign` FOREIGN KEY (`raw_material_id`) REFERENCES `raw_materials` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `qc_material_inward_items_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

