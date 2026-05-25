<?php
/**
 * DEBUGGING & FIX - Login Password Issue Analyzer
 * Akses: http://localhost/jabalega-admin/fix_passwords.php
 */

require_once 'includes/config.php';
header('Content-Type: text/html; charset=utf-8');

$db = getDB();

// Styling
$style = "
<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
.container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
h2 { color: #0a2d5e; border-bottom: 2px solid #1a5fad; padding-bottom: 10px; }
h3 { color: #1a5fad; margin-top: 20px; }
table { width: 100%; border-collapse: collapse; margin: 15px 0; }
th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
th { background: #0a2d5e; color: white; font-weight: bold; }
tr:nth-child(even) { background: #f9f9f9; }
.status-before { color: #d32f2f; font-weight: bold; }
.status-after { color: #388e3c; font-weight: bold; }
.code { background: #272822; color: #f8f8f2; padding: 10px; border-radius: 4px; font-family: monospace; overflow-x: auto; }
.success { color: #388e3c; }
.error { color: #d32f2f; }
.info { color: #1976d2; }
.warning { color: #f57c00; }
.box { border-left: 4px solid #1a5fad; padding: 15px; margin: 15px 0; background: #f0f7ff; }
</style>
";

echo $style;
echo "<div class='container'>";
echo "<h2>🔍 Analisis & Fix - Login Password Issue</h2>";

// ========== STEP 1: Cek Data di Database ==========
echo "<h3>STEP 1: Data User di Database</h3>";
$users = $db->query("SELECT id, username, password FROM users");
if (!$users) {
    die("❌ Query error: " . $db->error);
}

echo "<table>";
echo "<tr><th>ID</th><th>Username</th><th>Password di Database</th><th>Panjang</th><th>Status</th></tr>";
while ($row = $users->fetch_assoc()) {
    $pwd = $row['password'];
    $len = strlen($pwd);
    // Check if it's a bcrypt hash (starts with $2y$ and is 60 chars)
    $is_hash = (substr($pwd, 0, 4) === '$2y$' && $len === 60);
    $status = $is_hash ? '<span class="success">✓ HASHED</span>' : '<span class="error">❌ PLAINTEXT</span>';
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['username']}</td>";
    echo "<td><code style='background:#f0f0f0;padding:5px;'>" . substr($pwd, 0, 50) . "...</code></td>";
    echo "<td>{$len} karakter</td>";
    echo "<td>{$status}</td>";
    echo "</tr>";
}
echo "</table>";
// ========== STEP 2: Test password_verify dengan hash lama ==========
echo "<h3>STEP 2: Test password_verify() dengan Hash LAMA dari Database</h3>";

$admin_user = $db->query("SELECT id, username, password FROM users WHERE username = 'admin'")->fetch_assoc();

if ($admin_user) {
    $current_hash = $admin_user['password'];
    $test_password = 'admin123';
    
    echo "<div class='box'>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Hash di Database:</strong><br><code>$current_hash</code></p>";
    echo "<p><strong>Password yang ditest:</strong> $test_password</p>";
    
    $verify_result = password_verify($test_password, $current_hash);
    $result_text = $verify_result ? "✓ COCOK (TRUE)" : "❌ TIDAK COCOK (FALSE)";
    $result_class = $verify_result ? "success" : "error";
    echo "<p><strong>Hasil password_verify():</strong> <span class='$result_class'>$result_text</span></p>";
    echo "</div>";
    
    // ========== STEP 3: Generate Hash Baru ==========
    echo "<h3>STEP 3: Generate Hash BARU untuk 'admin123'</h3>";
    
    $new_hash = password_hash($test_password, PASSWORD_BCRYPT);
    
    echo "<div class='box'>";
    echo "<p><strong>Hash BARU yang dibuat sekarang:</strong><br><code>$new_hash</code></p>";
    
    // Test dengan hash baru
    $verify_new = password_verify($test_password, $new_hash);
    $new_result_text = $verify_new ? "✓ COCOK (TRUE)" : "❌ TIDAK COCOK (FALSE)";
    $new_result_class = $verify_new ? "success" : "error";
    echo "<p><strong>Hasil password_verify() dengan hash BARU:</strong> <span class='$new_result_class'>$new_result_text</span></p>";
    echo "</div>";
    
    // ========== STEP 4: Perbandingan ==========
    echo "<h3>STEP 4: Perbandingan Hash</h3>";
    echo "<table>";
    echo "<tr><th>Deskripsi</th><th>Hash Lama (dari DB)</th><th>Hash Baru (generated)</th></tr>";
    echo "<tr>";
    echo "<td><strong>Hash</strong></td>";
    echo "<td><code style='word-break:break-all;'>$current_hash</code></td>";
    echo "<td><code style='word-break:break-all;'>$new_hash</code></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><strong>password_verify() cocok?</strong></td>";
    echo "<td class='" . ($verify_result ? "success" : "error") . "'>" . ($verify_result ? "✓ YA" : "❌ TIDAK") . "</td>";
    echo "<td class='" . ($verify_new ? "success" : "error") . "'>" . ($verify_new ? "✓ YA" : "❌ TIDAK") . "</td>";
    echo "</tr>";
    echo "</table>";
    
    // ========== STEP 5: Eksekusi Update ==========
    echo "<h3>STEP 5: Update Database dengan Hash Baru</h3>";
    
    if (!$verify_result && $verify_new) {
        // Hash lama tidak cocok tapi hash baru cocok = harus di-update
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
        $stmt->bind_param('s', $new_hash);
        
        if ($stmt->execute()) {
            echo "<div class='box' style='border-left-color: #388e3c;'>";
            echo "<p class='success'><strong>✓ BERHASIL!</strong> Password admin telah diupdate dengan hash yang benar.</p>";
            echo "</div>";
            
            // Verifikasi hasil update
            echo "<h3>STEP 6: Verifikasi Hasil Update</h3>";
            $verify_user = $db->query("SELECT password FROM users WHERE username = 'admin'")->fetch_assoc();
            $verify_final = password_verify($test_password, $verify_user['password']);
            echo "<div class='box'>";
            echo "<p><strong>Test login dengan username 'admin' dan password 'admin123':</strong></p>";
            echo "<p class='" . ($verify_final ? "success" : "error") . "'>" . ($verify_final ? "<strong>✓ BERHASIL! Anda sekarang bisa login.</strong>" : "<strong>❌ MASIH GAGAL</strong>") . "</p>";
            echo "</div>";
        } else {
            echo "<div class='box' style='border-left-color: #d32f2f;'>";
            echo "<p class='error'><strong>❌ UPDATE GAGAL:</strong> " . $db->error . "</p>";
            echo "</div>";
        }
    } elseif ($verify_result) {
        echo "<div class='box' style='border-left-color: #388e3c;'>";
        echo "<p class='success'><strong>✓ Hash sudah BENAR!</strong> Tidak perlu update database.</p>";
        echo "<p>Masalahnya mungkin ada di tempat lain. Periksa:</p>";
        echo "<ul>";
        echo "<li>Apakah JavaScript mengirim data dengan benar?</li>";
        echo "<li>Apakah ada trimming/whitespace pada input?</li>";
        echo "<li>Apakah ada error di frontend?</li>";
        echo "</ul>";
        echo "</div>";
    }
    
} else {
    echo "<div class='box' style='border-left-color: #d32f2f;'>";
    echo "<p class='error'><strong>❌ ERROR:</strong> User 'admin' tidak ditemukan di database!</p>";
    echo "</div>";
}

// ========== AUTO FIX: Cari semua user dengan plaintext password ==========
echo "<h3>STEP 7: Fix ALL Plaintext Passwords</h3>";

$all_users = $db->query("SELECT id, username, password FROM users");
$needs_fix = [];

while ($row = $all_users->fetch_assoc()) {
    $pwd = $row['password'];
    // Check if it's NOT a bcrypt hash
    $is_hash = (substr($pwd, 0, 4) === '$2y$' && strlen($pwd) === 60);
    if (!$is_hash) {
        $needs_fix[] = $row;
    }
}

if (count($needs_fix) > 0) {
    echo "<p class='warning'><strong>⚠️ Ditemukan " . count($needs_fix) . " user dengan plaintext password!</strong></p>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Username</th><th>Plaintext Password</th><th>Action</th></tr>";
    
    foreach ($needs_fix as $user) {
        $plaintext_pwd = $user['password'];
        $new_hash = password_hash($plaintext_pwd, PASSWORD_BCRYPT);
        
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param('si', $new_hash, $user['id']);
        $success = $stmt->execute();
        
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td><code>{$plaintext_pwd}</code></td>";
        echo "<td>" . ($success ? "<span class='success'>✓ HASHED</span>" : "<span class='error'>❌ FAILED</span>") . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<div class='box' style='border-left-color: #388e3c;'>";
    echo "<p class='success'><strong>✓ Semua plaintext password telah di-hash!</strong></p>";
    echo "<p>Sekarang Anda bisa login dengan username dan plaintext password.</p>";
    echo "</div>";
} else {
    echo "<div class='box' style='border-left-color: #388e3c;'>";
    echo "<p class='success'><strong>✓ Semua password sudah di-hash!</strong> Tidak ada yang perlu di-fix.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p style='text-align: center; color: #666;'>";
echo "<a href='index.php' style='color: #1a5fad; text-decoration: none;'>&larr; Kembali ke Login</a>";
echo "</p>";
echo "</div>";
?>