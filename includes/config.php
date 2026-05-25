<?php
// =============================================
// KONFIGURASI DATABASE - Multi Environment Support
// =============================================

// Load .env file if exists (untuk local development)
if (file_exists(__DIR__ . '/../.env')) {
    $env_file = parse_ini_file(__DIR__ . '/../.env');
    if ($env_file !== false) {
        foreach ($env_file as $key => $value) {
            if (!getenv($key)) {
                putenv("$key=$value");
            }
        }
    }
}

// Auto-detect environment based on hostname & environment variables
$is_localhost = (
    $_SERVER['HTTP_HOST'] ?? '' === 'localhost' || 
    $_SERVER['HTTP_HOST'] ?? '' === '127.0.0.1' ||
    strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false ||
    php_uname('n') === 'LAPTOP' ||
    defined('LOCALHOST') ||
    getenv('ENVIRONMENT') === 'DEVELOPMENT'
);

// Get database config from environment or use defaults
$db_config = [
    'host' => getenv('DB_HOST') ?: ($is_localhost ? 'localhost' : 'sql204.infinityfree.com'),
    'user' => getenv('DB_USER') ?: ($is_localhost ? 'root' : 'inf_41939326'),
    'pass' => getenv('DB_PASS') ?: ($is_localhost ? '' : 'TcSzIcSQxSb'),
    'name' => getenv('DB_NAME') ?: ($is_localhost ? 'jabalega' : 'inf_41939326_jabalega'),
    'port' => getenv('DB_PORT') ?: 3306,
];

define('DB_HOST', $db_config['host']);
define('DB_USER', $db_config['user']);
define('DB_PASS', $db_config['pass']);
define('DB_NAME', $db_config['name']);
define('DB_PORT', $db_config['port']);
define('ENVIRONMENT', $is_localhost ? 'DEVELOPMENT' : 'PRODUCTION');

define('SITE_NAME', 'Jabalega Admin');
define('SITE_VERSION', '1.0');

// Koneksi database dengan error handling lebih baik
function getDB() {
    static $conn = null;
    if ($conn === null) {
        // Try with different ports for InfinityFree (sometimes uses port 3306 or 3307)
        $ports = [DB_PORT, 3306, 3307];
        $host = DB_HOST;
        $last_error = '';
        
        foreach ($ports as $port) {
            $conn = new mysqli(
                $host,
                DB_USER,
                DB_PASS,
                DB_NAME,
                $port
            );
            
            if (!$conn->connect_error) {
                // Connection successful
                $conn->set_charset('utf8mb4');
                return $conn;
            }
            
            $last_error = $conn->connect_error;
            $conn = null;
        }
        
        // All connection attempts failed
        http_response_code(500);
        $response = [
            'success' => false, 
            'message' => 'Database connection failed',
            'error' => $last_error,
            'config' => [
                'host' => DB_HOST,
                'port' => DB_PORT,
                'user' => DB_USER,
                'database' => DB_NAME,
                'environment' => ENVIRONMENT
            ]
        ];
        
        header('Content-Type: application/json');
        die(json_encode($response));
    }
    return $conn;
}

// Daftar tabel & labelnya
define('TABLES', [
    'pt'            => ['label' => 'Pendirian PT',         'icon' => 'building',      'color' => '#1a5fad'],
    'nib'           => ['label' => 'NIB / Perizinan',      'icon' => 'file-text',     'color' => '#0f6e56'],
    'pirt'          => ['label' => 'PIRT',                 'icon' => 'package',       'color' => '#854f0b'],
    'bpom'          => ['label' => 'BPOM',                 'icon' => 'shield',        'color' => '#993556'],
    'halal'         => ['label' => 'Sertifikasi Halal',    'icon' => 'check-circle',  'color' => '#3b6d11'],
    'merek'         => ['label' => 'HAKI / Merek',         'icon' => 'award',         'color' => '#533ab7'],
    'psat'          => ['label' => 'Izin PSAT',            'icon' => 'file-check',    'color' => '#0066cc'],
    'izin_lainnya'  => ['label' => 'Izin Lainnya',         'icon' => 'briefcase',     'color' => '#ff6b35'],
]);

// Status default (dipakai jika kolom status tersedia di tabel)
define('STATUS_OPTIONS', ['Proses', 'Selesai', 'Pending', 'Dibatalkan']);

// Kolom standar tiap tabel
define('TABLE_COLUMNS', [
    'nama'           => 'Nama Client',
    'nama_usaha'     => 'Nama Usaha',
    'alamat'         => 'Alamat',
    'no_telefon'     => 'No. Telepon',
    'tanggal_terbit' => 'Tanggal Terbit',
    'masa_berlaku'   => 'Masa Berlaku',
    'link_gdrive'    => 'Link Google Drive',
    'email'          => 'Email',
    'izin'           => 'Jenis Izin',
]);
?>
