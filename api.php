<?php
session_start();
require_once 'includes/config.php';
header('Content-Type: application/json');

// Cek login untuk semua aksi kecuali login itu sendiri
$action = $_POST['action'] ?? $_GET['action'] ?? '';
if ($action !== 'login' && !isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesi habis, silakan login ulang.']);
    exit;
}

$db = getDB();

function tableHasColumn($db, $table, $column) {
    static $cache = [];
    $key = $table . '|' . $column;
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }
    // Use direct query instead of prepared statement for SHOW COLUMNS
    $result = $db->query("SHOW COLUMNS FROM `" . $db->real_escape_string($table) . "` LIKE '" . $db->real_escape_string($column) . "'");
    if (!$result) {
        $cache[$key] = false;
        return false;
    }
    $cache[$key] = ($result->num_rows > 0);
    return $cache[$key];
}

switch ($action) {

    // ── LOGIN ──────────────────────────────────────────
    case 'login':
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $stmt = $db->prepare("SELECT id, username, password, nama_lengkap FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['nama_lengkap'];
            echo json_encode(['success' => true, 'name' => $user['nama_lengkap']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Username atau password salah.']);
        }
        break;

    // ── LOGOUT ─────────────────────────────────────────
    case 'logout':
        session_destroy();
        echo json_encode(['success' => true]);
        break;

    // ── DASHBOARD STATS ────────────────────────────────
    case 'get_stats':
        $tables = array_keys(TABLES);
        $stats = [];
        $total = 0;
        foreach ($tables as $tbl) {
            $r = $db->query("SELECT COUNT(*) as c FROM `$tbl`")->fetch_assoc();
            $stats[$tbl] = (int)$r['c'];
            $total += (int)$r['c'];
        }
        // Status breakdown (hanya jika kolom status tersedia)
        $status_data = [];
        $status_options = defined('STATUS_OPTIONS') ? STATUS_OPTIONS : [];
        if ($status_options) {
            foreach ($status_options as $s) {
                $count = 0;
                foreach ($tables as $tbl) {
                    if (!tableHasColumn($db, $tbl, 'status')) {
                        continue;
                    }
                    $stmt = $db->prepare("SELECT COUNT(*) as c FROM `$tbl` WHERE status = ?");
                    if (!$stmt) {
                        continue;
                    }
                    $stmt->bind_param('s', $s);
                    $stmt->execute();
                    $count += (int)$stmt->get_result()->fetch_assoc()['c'];
                }
                $status_data[$s] = $count;
            }
        }
        // Monthly (6 bulan terakhir, hanya jika kolom created_at tersedia)
        $monthly = [];
        $tables_with_created = array_filter($tables, function ($tbl) use ($db) {
            return tableHasColumn($db, $tbl, 'created_at');
        });
        if ($tables_with_created) {
            for ($i = 5; $i >= 0; $i--) {
                $month_label = date('M Y', strtotime("-$i months"));
                $month_count = 0;
                foreach ($tables_with_created as $tbl) {
                    $year = date('Y', strtotime("-$i months"));
                    $month = date('n', strtotime("-$i months"));
                    $r = $db->query("SELECT COUNT(*) as c FROM `$tbl` WHERE YEAR(created_at)=$year AND MONTH(created_at)=$month")->fetch_assoc();
                    $month_count += (int)$r['c'];
                }
                $monthly[] = ['label' => $month_label, 'count' => $month_count];
            }
        }
        echo json_encode(['success' => true, 'table_counts' => $stats, 'total' => $total, 'status_data' => $status_data, 'monthly' => $monthly]);
        break;

    // ── GET DATA (list) ────────────────────────────────
    case 'get_data':
        $table = $_GET['table'] ?? '';
        if (!array_key_exists($table, TABLES)) { echo json_encode(['success'=>false,'message'=>'Tabel tidak valid']); break; }
        $search = trim($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $per_page = 15;
        $offset = ($page - 1) * $per_page;
        $where = [];
        $params = [];
        $types = '';
        if ($search) {
            $like = "%$search%";
            if (in_array($table, ['psat', 'izin_lainnya'])) {
                // PSAT dan Izin Lainnya bisa dicari dari nama, nama_usaha, email
                $where[] = "(nama LIKE ? OR nama_usaha LIKE ? OR email LIKE ? OR izin LIKE ?)";
                $params = array_merge($params, [$like, $like, $like, $like]);
                $types .= 'ssss';
            } else {
                $where[] = "(nama LIKE ? OR nama_usaha LIKE ? OR no_telefon LIKE ?)";
                $params = array_merge($params, [$like, $like, $like]);
                $types .= 'sss';
            }
        }
        $sql_where = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        // Count
        $count_stmt = $db->prepare("SELECT COUNT(*) as c FROM `$table` $sql_where");
        if ($params) $count_stmt->bind_param($types, ...$params);
        $count_stmt->execute();
        $total_rows = (int)$count_stmt->get_result()->fetch_assoc()['c'];
        // Data
        $stmt = $db->prepare("SELECT * FROM `$table` $sql_where ORDER BY nama ASC LIMIT ? OFFSET ?");
        $p2 = array_merge($params, [$per_page, $offset]);
        $t2 = $types . 'ii';
        $stmt->bind_param($t2, ...$p2);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['success'=>true,'data'=>$rows,'total'=>$total_rows,'page'=>$page,'per_page'=>$per_page,'total_pages'=>ceil($total_rows/$per_page)]);
        break;

    // ── GET SINGLE ROW ─────────────────────────────────
    case 'get_row':
        $table = $_GET['table'] ?? '';
        $id = (int)($_GET['id'] ?? 0);
        if (!array_key_exists($table, TABLES) || !$id) { echo json_encode(['success'=>false]); break; }
        $stmt = $db->prepare("SELECT * FROM `$table` WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        echo json_encode(['success'=>true,'row'=>$row]);
        break;

    // ── TAMBAH DATA ────────────────────────────────────
    case 'add_data':
        $table = $_POST['table'] ?? '';
        if (!array_key_exists($table, TABLES)) { echo json_encode(['success'=>false,'message'=>'Tabel tidak valid']); break; }
        $nama       = trim($_POST['nama'] ?? '');
        $nama_usaha = trim($_POST['nama_usaha'] ?? '');
        $alamat     = trim($_POST['alamat'] ?? '');
        $no_telefon = trim($_POST['no_telefon'] ?? '');
        if (!$nama) { echo json_encode(['success'=>false,'message'=>'Nama client wajib diisi.']); break; }
        
        // Tentukan kolom berdasarkan tabel
        if (in_array($table, ['psat', 'izin_lainnya'])) {
            // PSAT dan Izin Lainnya memiliki kolom khusus
            $izin          = trim($_POST['izin'] ?? '');
            $email         = trim($_POST['email'] ?? '');
            $link_gdrive   = trim($_POST['link_gdrive'] ?? '');
            $tanggal_terbit = trim($_POST['tanggal_terbit'] ?? '');
            $masa_berlaku  = trim($_POST['masa_berlaku'] ?? '');
            if (!$izin) { echo json_encode(['success'=>false,'message'=>'Jenis izin wajib diisi.']); break; }
            if (!$email) { echo json_encode(['success'=>false,'message'=>'Email wajib diisi.']); break; }
            if (!$tanggal_terbit) { echo json_encode(['success'=>false,'message'=>'Tanggal terbit wajib diisi.']); break; }
            if (!$masa_berlaku) { echo json_encode(['success'=>false,'message'=>'Masa berlaku wajib diisi.']); break; }
            $stmt = $db->prepare("INSERT INTO `$table` (nama,nama_usaha,izin,no_telefon,email,link_gdrive,tanggal_terbit,masa_berlaku) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->bind_param('ssssssss', $nama,$nama_usaha,$izin,$no_telefon,$email,$link_gdrive,$tanggal_terbit,$masa_berlaku);
        } else {
            // Tabel lain (nib, pirt, bpom, halal, merek, pt) - semua punya struktur sama
            $link_gdrive    = trim($_POST['link_gdrive'] ?? '');
            $tanggal_terbit = trim($_POST['tanggal_terbit'] ?? '');
            $masa_berlaku   = trim($_POST['masa_berlaku'] ?? '');
            $stmt = $db->prepare("INSERT INTO `$table` (nama,nama_usaha,alamat,no_telefon,link_gdrive,tanggal_terbit,masa_berlaku) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param('sssssss', $nama,$nama_usaha,$alamat,$no_telefon,$link_gdrive,$tanggal_terbit,$masa_berlaku);
        }
        if ($stmt->execute()) {
            echo json_encode(['success'=>true,'id'=>$db->insert_id,'message'=>'Data berhasil ditambahkan.']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Gagal menyimpan: '.$db->error]);
        }
        break;

    // ── EDIT DATA ──────────────────────────────────────
    case 'edit_data':
        $table = $_POST['table'] ?? '';
        $id    = (int)($_POST['id'] ?? 0);
        if (!array_key_exists($table, TABLES) || !$id) { echo json_encode(['success'=>false,'message'=>'Data tidak valid']); break; }
        $nama       = trim($_POST['nama'] ?? '');
        $nama_usaha = trim($_POST['nama_usaha'] ?? '');
        $alamat     = trim($_POST['alamat'] ?? '');
        $no_telefon = trim($_POST['no_telefon'] ?? '');
        if (!$nama) { echo json_encode(['success'=>false,'message'=>'Nama client wajib diisi.']); break; }
        
        // Tentukan kolom berdasarkan tabel
        if (in_array($table, ['psat', 'izin_lainnya'])) {
            // PSAT dan Izin Lainnya memiliki kolom khusus
            $izin          = trim($_POST['izin'] ?? '');
            $email         = trim($_POST['email'] ?? '');
            $link_gdrive   = trim($_POST['link_gdrive'] ?? '');
            $tanggal_terbit = trim($_POST['tanggal_terbit'] ?? '');
            $masa_berlaku  = trim($_POST['masa_berlaku'] ?? '');
            if (!$izin) { echo json_encode(['success'=>false,'message'=>'Jenis izin wajib diisi.']); break; }
            if (!$email) { echo json_encode(['success'=>false,'message'=>'Email wajib diisi.']); break; }
            if (!$tanggal_terbit) { echo json_encode(['success'=>false,'message'=>'Tanggal terbit wajib diisi.']); break; }
            if (!$masa_berlaku) { echo json_encode(['success'=>false,'message'=>'Masa berlaku wajib diisi.']); break; }
            $stmt = $db->prepare("UPDATE `$table` SET nama=?,nama_usaha=?,izin=?,no_telefon=?,email=?,link_gdrive=?,tanggal_terbit=?,masa_berlaku=? WHERE id=?");
            $stmt->bind_param('ssssssssi', $nama,$nama_usaha,$izin,$no_telefon,$email,$link_gdrive,$tanggal_terbit,$masa_berlaku,$id);
        } else {
            // Tabel lain (nib, pirt, bpom, halal, merek, pt) - semua punya struktur sama
            $link_gdrive    = trim($_POST['link_gdrive'] ?? '');
            $tanggal_terbit = trim($_POST['tanggal_terbit'] ?? '');
            $masa_berlaku   = trim($_POST['masa_berlaku'] ?? '');
            $stmt = $db->prepare("UPDATE `$table` SET nama=?,nama_usaha=?,alamat=?,no_telefon=?,link_gdrive=?,tanggal_terbit=?,masa_berlaku=? WHERE id=?");
            $stmt->bind_param('sssssssi', $nama,$nama_usaha,$alamat,$no_telefon,$link_gdrive,$tanggal_terbit,$masa_berlaku,$id);
        }
        if ($stmt->execute()) {
            echo json_encode(['success'=>true,'message'=>'Data berhasil diperbarui.']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Gagal update: '.$db->error]);
        }
        break;

    // ── HAPUS DATA ─────────────────────────────────────
    case 'delete_data':
        $table = $_POST['table'] ?? '';
        $id    = (int)($_POST['id'] ?? 0);
        if (!array_key_exists($table, TABLES) || !$id) { echo json_encode(['success'=>false,'message'=>'Data tidak valid']); break; }
        $stmt = $db->prepare("DELETE FROM `$table` WHERE id = ?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['success'=>true,'message'=>'Data berhasil dihapus.']);
        } else {
            echo json_encode(['success'=>false,'message'=>'Gagal hapus: '.$db->error]);
        }
        break;

    default:
        echo json_encode(['success'=>false,'message'=>'Aksi tidak dikenali.']);
}
?>
