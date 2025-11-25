<?php
/**
 * Direct Database Creation Script
 * This script creates the database directly using PDO
 * Run: php create_database_direct.php
 */

require __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get database configuration from .env
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$database = $_ENV['DB_DATABASE'] ?? 'basic_template';
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';

echo "==========================================\n";
echo "Direct Database Creation\n";
echo "==========================================\n\n";
echo "Configuration:\n";
echo "  Host: {$host}\n";
echo "  Port: {$port}\n";
echo "  Database: {$database}\n";
echo "  Username: {$username}\n";
echo "  Password: " . (empty($password) ? '(empty)' : '***') . "\n\n";

try {
    // Connect to MySQL without selecting a database
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    echo "✅ Connected to MySQL server successfully!\n\n";
    
    // Create database
    echo "Creating database '{$database}'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    echo "✅ Database '{$database}' created successfully!\n\n";
    
    // Verify database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '{$database}'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "✅ Verified: Database '{$database}' exists!\n\n";
        echo "Next steps:\n";
        echo "1. Run: php artisan migrate\n";
        echo "2. Run: php artisan db:seed\n";
        echo "\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting:\n";
    echo "1. Check if MySQL service is running\n";
    echo "2. Verify username and password in .env file\n";
    echo "3. Make sure user has CREATE DATABASE privilege\n";
    echo "\n";
    echo "Manual creation:\n";
    echo "mysql -u {$username} -p\n";
    echo "CREATE DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
    exit(1);
}

echo "==========================================\n";
echo "Done!\n";
echo "==========================================\n";

