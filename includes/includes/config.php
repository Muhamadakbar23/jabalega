<?php
// =============================================
// KONFIGURASI DATABASE - Sesuaikan jika perlu
// =============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'jabalega');

define('SITE_NAME', 'Jabalega Admin');
define('SITE_VERSION', '1.0');

// Koneksi database
function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die(json_encode(['error' => 'Koneksi database gagal: ' . $conn->connect_error]));
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

// Daftar tabel & labelnya
define('TABLES', [
    'pt'    => ['label' => 'Pendirian PT',    'icon' => 'building', 'color' => '#1a5fad'],
    'nib'   => ['label' => 'NIB / Perizinan', 'icon' => 'file-text','color' => '#0f6e56'],
    'pirt'  => ['label' => 'PIRT',            'icon' => 'package',  'color' => '#854f0b'],
    'bpom'  => ['label' => 'BPOM',            'icon' => 'shield',   'color' => '#993556'],
    'halal' => ['label' => 'Sertifikasi Halal','icon' => 'check-circle','color' => '#3b6d11'],
    'merek' => ['label' => 'HAKI / Merek',    'icon' => 'award',    'color' => '#533ab7'],
]);

// Status default (dipakai jika kolom status tersedia di tabel)
define('STATUS_OPTIONS', ['Proses', 'Selesai', 'Pending', 'Dibatalkan']);

// Kolom standar tiap tabel
define('TABLE_COLUMNS', [
    'nama'        => 'Nama Client',
    'nama_usaha'  => 'Nama Usaha',
    'alamat'      => 'Alamat',
    'no_telefon'  => 'No. Telepon',
    'link_gdrive' => 'Link Google Drive',
]);
?>
