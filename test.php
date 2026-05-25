<?php
/**
 * SIMPLE CONNECTION TEST
 * Access: https://jabalega-admin.xo.je/test.php
 */

header('Content-Type: text/plain; charset=utf-8');

echo "=== JABALEGA DATABASE CONNECTION TEST ===\n\n";

// Test 1: Check PHP version
echo "1. PHP Version: " . phpversion() . "\n";

// Test 2: Check if mysqli is available
echo "2. MySQLi Extension: " . (extension_loaded('mysqli') ? "✓ Available" : "✗ NOT AVAILABLE") . "\n";

// Test 3: Check hostname
echo "3. Current Hostname: " . ($_SERVER['HTTP_HOST'] ?? 'UNKNOWN') . "\n";
echo "   PHP Hostname: " . php_uname('n') . "\n";

// Test 4: Load config
echo "\n4. Loading Configuration...\n";
require_once 'includes/config.php';
echo "   Environment: " . ENVIRONMENT . "\n";
echo "   DB Host: " . DB_HOST . "\n";
echo "   DB User: " . DB_USER . "\n";
echo "   DB Name: " . DB_NAME . "\n";

// Test 5: Try to connect
echo "\n5. Attempting Database Connection...\n";

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    echo "✗ CONNECTION FAILED\n";
    echo "   Error: " . $conn->connect_error . "\n";
    echo "   errno: " . $conn->connect_errno . "\n";
    exit(1);
} else {
    echo "✓ CONNECTION SUCCESS\n";
    echo "   Server Version: " . $conn->server_version . "\n";
    echo "   Client Version: " . $conn->client_version . "\n";
}

// Test 6: Check users table
echo "\n6. Checking Users Table...\n";
$result = $conn->query("SELECT COUNT(*) as cnt FROM users");
if (!$result) {
    echo "✗ Query failed: " . $conn->error . "\n";
    exit(1);
}
$row = $result->fetch_assoc();
echo "✓ Users in database: " . $row['cnt'] . "\n";

// Test 7: List users
echo "\n7. Users List:\n";
$users = $conn->query("SELECT id, username, nama_lengkap FROM users");
while ($user = $users->fetch_assoc()) {
    echo "   - ID " . $user['id'] . ": " . $user['username'] . " (" . $user['nama_lengkap'] . ")\n";
}

// Test 8: Test login function
echo "\n8. Testing Login with 'admin' / 'admin123'...\n";
$stmt = $conn->prepare("SELECT id, username, password, nama_lengkap FROM users WHERE username = ?");
if (!$stmt) {
    echo "✗ Prepare failed: " . $conn->error . "\n";
} else {
    $test_user = 'admin';
    $stmt->bind_param('s', $test_user);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if ($user) {
        $verify = password_verify('admin123', $user['password']);
        if ($verify) {
            echo "✓ Login verification SUCCESS\n";
            echo "   User: " . $user['username'] . "\n";
            echo "   Name: " . $user['nama_lengkap'] . "\n";
        } else {
            echo "✗ Password verification FAILED\n";
            echo "   Stored hash: " . substr($user['password'], 0, 30) . "...\n";
            echo "   Run fix_passwords.php to fix this\n";
        }
    } else {
        echo "✗ User 'admin' not found\n";
    }
    $stmt->close();
}

echo "\n=== TEST COMPLETE ===\n";
$conn->close();
?>
