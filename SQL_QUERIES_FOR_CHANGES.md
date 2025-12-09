# SQL Queries for Database Changes

## 1. Create `user_role` Table

This table was created to support many-to-many relationship between users and roles.

### SQL Query (Actual Executed):
```sql
CREATE TABLE `user_role` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_role_unique` (`user_id`,`role_id`),
  KEY `user_role_role_id_foreign` (`role_id`),
  CONSTRAINT `user_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_role_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### To Check if Table Exists (Before Creating):
```sql
SELECT COUNT(*) as table_exists 
FROM information_schema.tables 
WHERE table_schema = 'basic_template' 
AND table_name = 'user_role';
```

### To Drop Table (if needed):
```sql
DROP TABLE IF EXISTS `user_role`;
```

---

## How to Get SQL Queries from Laravel Migrations

### Method 1: Using `--pretend` flag
```bash
php artisan migrate --pretend
```
This shows the SQL queries without executing them.

### Method 2: Enable Query Log
Add this to your migration file temporarily:
```php
DB::enableQueryLog();
// Your migration code
dd(DB::getQueryLog());
```

### Method 3: Check Laravel Logs
After running migrations, check:
```bash
tail -f storage/logs/laravel.log
```

### Method 4: Use Database Tool
Connect to your database and check the migration history table:
```sql
SELECT * FROM migrations ORDER BY id DESC;
```

---

## Future Changes - SQL Queries Template

Whenever I make changes to tables or columns, I'll provide the SQL queries here:

### Adding a Column:
```sql
ALTER TABLE `table_name` 
ADD COLUMN `column_name` VARCHAR(255) NULL AFTER `existing_column`;
```

### Modifying a Column:
```sql
ALTER TABLE `table_name` 
MODIFY COLUMN `column_name` VARCHAR(255) NOT NULL;
```

### Dropping a Column:
```sql
ALTER TABLE `table_name` 
DROP COLUMN `column_name`;
```

### Adding an Index:
```sql
ALTER TABLE `table_name` 
ADD INDEX `index_name` (`column_name`);
```

### Adding a Foreign Key:
```sql
ALTER TABLE `table_name` 
ADD CONSTRAINT `foreign_key_name` 
FOREIGN KEY (`column_name`) REFERENCES `referenced_table` (`id`) 
ON DELETE CASCADE;
```

---

## Current Database Schema Changes Made

### 1. user_role Table (Created: 2025-11-27)
- **Purpose**: Pivot table for many-to-many relationship between users and roles
- **Columns**:
  - `id` (BIGINT, Primary Key, Auto Increment)
  - `user_id` (BIGINT, Foreign Key to users.id)
  - `role_id` (BIGINT, Foreign Key to roles.id)
  - `created_at` (TIMESTAMP)
  - `updated_at` (TIMESTAMP)
- **Constraints**:
  - Unique constraint on (`user_id`, `role_id`) to prevent duplicate assignments
  - Foreign key constraints with CASCADE delete

---

## Useful SQL Queries for Verification

### Check if user_role table exists:
```sql
SHOW TABLES LIKE 'user_role';
```

### View user_role table structure:
```sql
DESCRIBE user_role;
-- OR
SHOW CREATE TABLE user_role;
```

### Check existing user-role assignments:
```sql
SELECT 
    u.id as user_id,
    u.name as user_name,
    u.email,
    r.id as role_id,
    r.name as role_name,
    ur.created_at
FROM user_role ur
JOIN users u ON ur.user_id = u.id
JOIN roles r ON ur.role_id = r.id
ORDER BY u.name, r.name;
```

### Count roles per user:
```sql
SELECT 
    u.id,
    u.name,
    u.email,
    COUNT(ur.role_id) as role_count
FROM users u
LEFT JOIN user_role ur ON u.id = ur.user_id
GROUP BY u.id, u.name, u.email
ORDER BY role_count DESC;
```

---

## Notes

- All foreign keys use `ON DELETE CASCADE` - if a user or role is deleted, the relationship is automatically removed
- The unique constraint prevents assigning the same role to the same user twice
- The table uses InnoDB engine for better performance and foreign key support

