<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use App\Role;
use Illuminate\Support\Facades\Hash;

// User details
$email = 'urashid957@gmail.com';
$password = 'password';
$name = 'Gymie User';

// Check if user already exists
$existingUser = User::where('email', $email)->first();

if ($existingUser) {
    echo "User already exists with email: $email\n";
    echo "Updating user...\n";
    $user = $existingUser;
} else {
    echo "Creating new user...\n";
    $user = new User();
}

// Set user properties
$user->name = $name;
$user->email = $email;
$user->password = Hash::make($password);
$user->status = 1; // Active
$user->save();

echo "User created/updated successfully!\n";
echo "User ID: " . $user->id . "\n";
echo "Email: " . $user->email . "\n";

// Get the Gymie role
$role = Role::where('name', 'Gymie')->first();

if (!$role) {
    echo "ERROR: Role 'Gymie' not found!\n";
    exit(1);
}

echo "Found role: " . $role->name . " (ID: " . $role->id . ")\n";

// Check if user already has this role
$hasRole = $user->hasRole($role->name);

if ($hasRole) {
    echo "User already has role 'Gymie' assigned.\n";
} else {
    // Attach role to user (using Entrust)
    $user->attachRole($role);
    echo "Role 'Gymie' attached to user successfully!\n";
}

// Verify
$userRoles = $user->roles()->get();
echo "\nUser roles:\n";
foreach ($userRoles as $r) {
    echo "  - " . $r->name . "\n";
}

echo "\n✅ User setup complete!\n";
echo "You can now login with:\n";
echo "  Email: $email\n";
echo "  Password: $password\n";

