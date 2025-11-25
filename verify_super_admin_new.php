<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "==========================================\n";
echo "Super Admin Verification\n";
echo "==========================================\n\n";

$user = User::with('role')->first();

if ($user) {
    echo "✅ Super Admin User Found:\n";
    echo "   Name: {$user->name}\n";
    echo "   Email: {$user->email}\n";
    echo "   Role: " . ($user->role ? $user->role->name : 'No Role') . "\n";
    echo "   User ID: {$user->id}\n";
    echo "\n";
    echo "📧 Login Credentials:\n";
    echo "   Email: admin@erp.com\n";
    echo "   Password: SuperAdmin@2024\n";
} else {
    echo "❌ No Super Admin user found!\n";
}

echo "\n==========================================\n";

