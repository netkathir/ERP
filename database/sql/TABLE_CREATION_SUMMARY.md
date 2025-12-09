# Table Creation SQL Queries Summary

This document contains SQL queries for creating all tables related to:
1. BOM Process (with child table)
2. Production Department
3. Employee
4. Billing Address

## Files Available

### 1. Complete SQL File (All Tables)
**File:** `database/sql/create_all_tables_complete.sql`
- Contains all 5 tables in one file
- Ready to run in sequence

### 2. Individual SQL Files

#### BOM Process Tables
**File:** `database/sql/create_bom_processes_tables.sql`
- `bom_processes` (Parent table)
- `bom_process_items` (Child table)

#### Production Department
**File:** `database/sql/create_production_departments_table.sql`
- `production_departments` table

#### Employee
**File:** `database/sql/create_employees_table.sql`
- `employees` table

#### Billing Address
**File:** `database/sql/create_billing_addresses_table.sql`
- `billing_addresses` table

## Table Structures

### 1. bom_processes
- **id** (Primary Key)
- **product_id** (Foreign Key → products)
- **branch_id** (Foreign Key → branches, nullable)
- **created_at**, **updated_at**

**Notes:**
- No `process_id` column (removed)
- No unique constraint (allows duplicates)
- Process is now stored at item level in `bom_process_items`

### 2. bom_process_items
- **id** (Primary Key)
- **bom_process_id** (Foreign Key → bom_processes)
- **process_id** (Foreign Key → processes, nullable)
- **raw_material_id** (Foreign Key → raw_materials)
- **quantity** (DECIMAL 15,4)
- **unit_id** (Foreign Key → units)
- **created_at**, **updated_at**

**Notes:**
- `process_id` added to item level
- No unique constraint (allows duplicate raw materials)
- Each item can have its own process

### 3. production_departments
- **id** (Primary Key)
- **name** (Unique)
- **description** (nullable)
- **branch_id** (Foreign Key → branches, nullable)
- **created_at**, **updated_at**

### 4. employees
- **id** (Primary Key)
- **employee_code** (Unique)
- **name**
- **department_id** (Foreign Key → departments)
- **designation_id** (Foreign Key → designations)
- **date_of_birth**
- **email** (Unique)
- **mobile_no**
- **active** (Enum: Yes/No, default: Yes)
- **address_line_1** (nullable)
- **address_line_2** (nullable)
- **city**
- **state**
- **country** (default: 'India')
- **pincode** (nullable)
- **emergency_contact_no** (nullable)
- **branch_id** (Foreign Key → branches, nullable)
- **created_at**, **updated_at**

### 5. billing_addresses
- **id** (Primary Key)
- **company_name**
- **address_line_1**
- **address_line_2**
- **city**
- **state**
- **pincode**
- **email**
- **contact_no**
- **gst_no**
- **branch_id** (Foreign Key → branches, nullable)
- **created_at**, **updated_at**

## Foreign Key Dependencies

Before running these SQL queries, ensure the following tables exist:
- `products`
- `processes`
- `raw_materials`
- `units`
- `departments`
- `designations`
- `branches`

## Usage

### Option 1: Run Complete File
```sql
SOURCE database/sql/create_all_tables_complete.sql;
```

### Option 2: Run Individual Files
```sql
SOURCE database/sql/create_bom_processes_tables.sql;
SOURCE database/sql/create_production_departments_table.sql;
SOURCE database/sql/create_employees_table.sql;
SOURCE database/sql/create_billing_addresses_table.sql;
```

## Important Notes

1. **BOM Process Structure:**
   - Process is stored at item level, not at BOM level
   - Duplicate entries are allowed (no unique constraints)
   - Each item can have its own process

2. **Foreign Key Constraints:**
   - Most foreign keys use `ON DELETE CASCADE` for automatic cleanup
   - `unit_id` uses `ON DELETE RESTRICT` to prevent deletion of units in use
   - `branch_id` uses `ON DELETE SET NULL` to preserve records when branch is deleted

3. **Unique Constraints:**
   - `production_departments.name` - unique
   - `employees.employee_code` - unique
   - `employees.email` - unique

4. **Data Types:**
   - `quantity` in `bom_process_items` uses DECIMAL(15,4) for precision
   - `date_of_birth` in `employees` uses DATE type
   - `active` in `employees` uses ENUM('Yes','No')

