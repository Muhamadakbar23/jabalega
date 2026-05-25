<?php
/**
 * DEBUG - Database & Login Check
 * Akses: https://jabalega-admin.xo.je/debug.php
 */

require_once 'includes/config.php';
header('Content-Type: text/html; charset=utf-8');

$style = "
<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
.section { margin: 20px 0; padding: 15px; border-left: 4px solid #1a5fad; background: #f0f7ff; }
.success { color: #388e3c; font-weight: bold; }
.error { color: #d32f2f; font-weight: bold; }
.code { background: #f5f5f5; padding: 10px; border-radius: 4px; font-family: monospace; overflow-x: auto; }
table { width: 100%; border-collapse: collapse; margin: 10px 0; }
th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
th { background: #0a2d5e; color: white; }
h2 { color: #0a2d5e; border-bottom: 2px solid #1a5fad; padding-bottom: 10px; }
</style>
";

echo $style;
echo "<div class='container'>";
echo "<h2>🔍 DEBUG - Database & Login Connection</h2>";

// ========== TEST 1: Database Connection ==========
echo "<div class='section'>";
echo "<h3>TEST 1: Database Connection</h3>";

try {
    $db = getDB();
    echo "<p class='success'>✓ Database connected successfully!</p>";
    echo "<p>Host: " . DB_HOST . "</p>";
    echo "<p>Database: " . DB_NAME . "</p>";
    echo "<p>User: " . DB_USER . "</p>";
    echo "<p>Charset: " . $db->character_set_name() . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Connection failed: " . $e->getMessage() . "</p>";
    die("</div></div>");
}

// ========== TEST 2: Check Users Table ==========
echo "</div>";
echo "<div class='section'>";
echo "<h3>TEST 2: Users Table Structure</h3>";

$result = $db->query("SHOW COLUMNS FROM users");
if ($result) {
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p class='success'>✓ Users table structure is correct</p>";
} else {
    echo "<p class='error'>✗ Cannot read users table: " . $db->error . "</p>";
}

// ========== TEST 3: Count Users ==========
echo "</div>";
echo "<div class='section'>";
echo "<h3>TEST 3: Users Count</h3>";

$count_result = $db->query("SELECT COUNT(*) as total FROM users");
$count_row = $count_result->fetch_assoc();
$total_users = $count_row['total'];

echo "<p>Total users in database: <strong>$total_users</strong></p>";

if ($total_users > 0) {
    echo "<p class='success'>✓ Users found!</p>";
} else {
    echo "<p class='error'>✗ No users found in database</p>";
}

// ========== TEST 4: List All Users ==========
echo "</div>";
echo "<div class='section'>";
echo "<h3>TEST 4: All Users</h3>";

$users_result = $db->query("SELECT id, username, nama_lengkap, password FROM users ORDER BY id");
if ($users_result && $users_result->num_rows > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Username</th><th>Nama Lengkap</th><th>Password Hash</th><th>Status</th></tr>";
    while ($user = $users_result->fetch_assoc()) {
        $pwd = $user['password'];
        $is_hash = (substr($pwd, 0, 4) === '$2y$' && strlen($pwd) === 60);
        $status = $is_hash ? '<span class="success">✓ HASHED</span>' : '<span class="error">❌ PLAINTEXT</span>';
        echo "<tr>";
        echo "<td>" . $user['id'] . "</td>";
        echo "<td>" . $user['username'] . "</td>";
        echo "<td>" . $user['nama_lengkap'] . "</td>";
        echo "<td><code style='font-size:11px;'>" . substr($pwd, 0, 40) . "...</code></td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p class='error'>✗ Failed to fetch users: " . $db->error . "</p>";
}

// ========== TEST 5: Test Login Manually ==========
echo "</div>";
echo "<div class='section'>";
echo "<h3>TEST 5: Manual Login Test (admin / admin123)</h3>";

$test_username = 'admin';
$test_password = 'admin123';

$stmt = $db->prepare("SELECT id, username, password, nama_lengkap FROM users WHERE username = ?");
if ($stmt) {
    $stmt->bind_param('s', $test_username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if ($user) {
        echo "<p>Username found: <strong>" . $user['username'] . "</strong></p>";
        echo "<p>Nama Lengkap: <strong>" . $user['nama_lengkap'] . "</strong></p>";
        
        $password_verify_result = password_verify($test_password, $user['password']);
        
        if ($password_verify_result) {
            echo "<p class='success'>✓ Password verification SUCCESS!</p>";
            echo "<p>Login should work for user: <strong>$test_username</strong></p>";
        } else {
            echo "<p class='error'>✗ Password verification FAILED</p>";
            echo "<p>Stored password hash: <code>" . $user['password'] . "</code></p>";
            
            // Suggest creating new hash
            $new_hash = password_hash($test_password, PASSWORD_BCRYPT);
            echo "<p>Correct hash for '$test_password':</p>";
            echo "<div class='code'>" . $new_hash . "</div>";
            echo "<p style='color: #f57c00;'><strong>⚠️ Run fix_passwords.php to update</strong></p>";
        }
    } else {
        echo "<p class='error'>✗ Username '$test_username' not found in database</p>";
    }
    $stmt->close();
} else {
    echo "<p class='error'>✗ Prepare statement failed: " . $db->error . "</p>";
}

// ========== TEST 6: Check All Tabel ==========
echo "</div>";
echo "<div class='section'>";
echo "<h3>TEST 6: All Database Tables</h3>";

$tables_result = $db->query("SHOW TABLES");
if ($tables_result) {
    $tables = [];
    while ($row = $tables_result->fetch_row()) {
        $tables[] = $row[0];
    }
    echo "<p>Tables: <strong>" . implode(", ", $tables) . "</strong></p>";
    echo "<p class='success'>✓ Database has " . count($tables) . " tables</p>";
} else {
    echo "<p class='error'>✗ Cannot list tables: " . $db->error . "</p>";
}

// ========== Test 7: Session Test ==========
echo "</div>";
echo "<div class='section'>";
echo "<h3>TEST 7: Session Configuration</h3>";

echo "<p>Session save path: <code>" . ini_get('session.save_path') . "</code></p>";
echo "<p>Session name: <code>" . ini_get('session.name') . "</code></p>";
echo "<p>Max lifetime: <code>" . ini_get('session.gc_maxlifetime') . " seconds</code></p>";

session_start();
$_SESSION['test'] = 'working';
if (isset($_SESSION['test'])) {
    echo "<p class='success'>✓ Session working correctly</p>";
} else {
    echo "<p class='error'>✗ Session not working</p>";
}

echo "</div>";
echo "<hr>";
echo "<p style='text-align: center; color: #666;'>";
echo "<a href='index.php' style='color: #1a5fad; text-decoration: none;'>&larr; Back to Login</a> | ";
echo "<a href='fix_passwords.php' style='color: #1a5fad; text-decoration: none;'>Fix Passwords →</a>";
echo "</p>";
echo "</div>";
?>
