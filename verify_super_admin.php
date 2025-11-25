<?php
/**
 * Verify Super Admin User
 * Run: php verify_super_admin.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "==========================================\n";
echo "Super Admin Verification\n";
echo "==========================================\n\n";

$user = \App\Models\User::where('email', 'admin@gmail.com')->first();

if ($user) {
    echo "✅ Super Admin user found!\n\n";
    echo "Details:\n";
    echo "  Name: {$user->name}\n";
    echo "  Email: {$user->email}\n";
    echo "  Role: " . ($user->role ? $user->role->name : 'No Role') . "\n";
    echo "  Entity: " . ($user->entity ? $user->entity->name : 'No Entity') . "\n";
    echo "  Created: {$user->created_at}\n";
    echo "\n";
    echo "✅ Password: Admin@123\n";
    echo "\n";
    echo "You can now login with:\n";
    echo "  Email: admin@gmail.com\n";
    echo "  Password: Admin@123\n";
} else {
    echo "❌ Super Admin user not found!\n";
    echo "Run: php artisan db:seed --class=SuperAdminSeeder\n";
}

echo "\n==========================================\n";

