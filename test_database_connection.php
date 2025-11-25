<?php
/**
 * Database Connection Test Script
 * Run: php test_database_connection.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "==========================================\n";
echo "Database Connection Test\n";
echo "==========================================\n\n";

try {
    // Get database configuration
    $config = config('database.connections.mysql');
    
    echo "Configuration:\n";
    echo "  Host: " . ($config['host'] ?? 'N/A') . "\n";
    echo "  Port: " . ($config['port'] ?? 'N/A') . "\n";
    echo "  Database: " . ($config['database'] ?? 'N/A') . "\n";
    echo "  Username: " . ($config['username'] ?? 'N/A') . "\n";
    echo "  Password: " . (empty($config['password']) ? '(empty)' : '***') . "\n\n";
    
    // Test connection
    echo "Testing connection...\n";
    $pdo = DB::connection()->getPdo();
    
    echo "✅ Connection successful!\n\n";
    
    // Check if database exists
    echo "Checking if database exists...\n";
    $databaseName = $config['database'] ?? 'basic_template';
    
    try {
        $result = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
        
        if (count($result) > 0) {
            echo "✅ Database '{$databaseName}' exists!\n\n";
            
            // Check tables
            echo "Checking tables...\n";
            $tables = DB::select("SHOW TABLES");
            $tableCount = count($tables);
            
            if ($tableCount > 0) {
                echo "✅ Found {$tableCount} table(s)\n";
                foreach ($tables as $table) {
                    $tableName = array_values((array)$table)[0];
                    echo "   - {$tableName}\n";
                }
            } else {
                echo "⚠️  No tables found. Run: php artisan migrate\n";
            }
        } else {
            echo "❌ Database '{$databaseName}' does NOT exist!\n";
            echo "\nTo create it, run:\n";
            echo "  php artisan db:setup\n";
            echo "  OR\n";
            echo "  mysql -u root -p -e \"CREATE DATABASE {$databaseName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\"\n";
        }
    } catch (Exception $e) {
        echo "⚠️  Could not check database existence: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Connection failed!\n\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Please check:\n";
    echo "1. MySQL service is running\n";
    echo "2. .env file has correct database credentials\n";
    echo "3. Database exists (run: php artisan db:setup)\n";
    exit(1);
}

echo "\n==========================================\n";
echo "Test completed!\n";
echo "==========================================\n";

