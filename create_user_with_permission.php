<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\User;
use App\Role;
use App\Permission;
use Illuminate\Support\Facades\Hash;

echo "=== Setting up Gymie User with Permissions ===\n\n";

// Step 1: Create/Get the permission
echo "Step 1: Checking permission...\n";
$permissionName = 'manage-gymie';
$permission = Permission::where('name', $permissionName)->first();

if (!$permission) {
    echo "Creating permission '$permissionName'...\n";
    $permission = new Permission();
    $permission->name = $permissionName;
    $permission->display_name = 'Manage Gymie';
    $permission->description = 'Permission to manage Gymie application';
    $permission->save();
    echo "✅ Permission created: $permissionName (ID: {$permission->id})\n";
} else {
    echo "✅ Permission already exists: $permissionName (ID: {$permission->id})\n";
}

// Step 2: Get/Update the Gymie role with permission
echo "\nStep 2: Checking Gymie role...\n";
$role = Role::where('name', 'Gymie')->first();

if (!$role) {
    echo "ERROR: Role 'Gymie' not found!\n";
    echo "Creating Gymie role...\n";
    $role = new Role();
    $role->name = 'Gymie';
    $role->display_name = 'Gymie User';
    $role->description = 'Gymie application user role';
    $role->save();
    echo "✅ Role created: Gymie (ID: {$role->id})\n";
} else {
    echo "✅ Found role: {$role->name} (ID: {$role->id})\n";
}

// Check if role already has this permission
$hasPermission = $role->perms()->where('name', $permissionName)->exists();
if ($hasPermission) {
    echo "Role 'Gymie' already has permission '$permissionName'.\n";
} else {
    // Attach permission to role
    $role->attachPermission($permission);
    echo "✅ Permission '$permissionName' attached to role 'Gymie'!\n";
}

// Display all permissions for this role
echo "\nRole 'Gymie' permissions:\n";
$rolePermissions = $role->perms()->get();
foreach ($rolePermissions as $perm) {
    echo "  - " . $perm->name . "\n";
}

// Step 3: Create/Update user
echo "\nStep 3: Setting up user...\n";
$email = 'urashid957@gmail.com';
$password = 'password';
$name = 'Gymie User';

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
echo "✅ User created/updated successfully!\n";
echo "User ID: {$user->id}\n";

// Step 4: Attach role to user
echo "\nStep 4: Assigning role to user...\n";
$hasRole = $user->hasRole($role->name);
if ($hasRole) {
    echo "User already has role 'Gymie' assigned.\n";
} else {
    $user->attachRole($role);
    echo "✅ Role 'Gymie' attached to user successfully!\n";
}

// Final verification
echo "\n=== VERIFICATION ===\n";
echo "\nUser roles:\n";
$userRoles = $user->roles()->get();
foreach ($userRoles as $r) {
    echo "  - " . $r->name . "\n";
}

echo "\nUser permissions (via roles):\n";
// Get permissions through roles instead
foreach ($userRoles as $r) {
    $rolePerms = $r->perms()->get();
    foreach ($rolePerms as $perm) {
        echo "  - " . $perm->name . " (via role: {$r->name})\n";
    }
}

// Alternative: Check if user has specific permission
echo "\nChecking specific permission:\n";
if ($user->can('manage-gymie')) {
    echo "  ✅ User CAN 'manage-gymie'\n";
} else {
    echo "  ❌ User CANNOT 'manage-gymie'\n";
}

echo "\n=== ✅ SETUP COMPLETE! ===\n";
echo "You can now login with:\n";
echo "  Email: $email\n";
echo "  Password: $password\n";
echo "\nUser has role 'Gymie' with permission 'manage-gymie'\n";
