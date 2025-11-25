-- ============================================
-- ERP System Database Setup Script
-- ============================================
-- Run this script in MySQL to create the database
-- Usage: mysql -u root -p < database/create_database_complete.sql
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS `basic_template` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Use the database
USE `basic_template`;

-- Show success message
SELECT 'Database basic_template created successfully!' AS Status;

