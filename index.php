<?php
session_start();
require_once 'includes/config.php';

$is_logged_in = isset($_SESSION['admin_id']);
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
$tables_config = TABLES;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Admin Panel — Jabalega.com</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet"/>
<style>
:root {
  --navy: #0a2d5e;
  --navy-light: #0f3d7a;
  --blue: #1a5fad;
  --blue-light: #3a7fd4;
  --bg: #f0f4f9;
  --sidebar-w: 240px;
  --white: #ffffff;
  --text: #1a2d45;
  --text-muted: #6b7f99;
  --border: #dce3ed;
  --radius: 10px;
  --shadow: 0 2px 12px rgba(10,45,94,0.08);
}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;}

/* ── TOAST ─────────────────────────────────── */
#toast{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;}
.toast-item{background:var(--white);border:0.5px solid var(--border);border-radius:8px;padding:12px 18px;font-size:13px;font-weight:500;box-shadow:0 4px 16px rgba(0,0,0,.1);display:flex;align-items:center;gap:8px;animation:slideIn .25s ease;max-width:320px;}
.toast-item.success{border-left:3px solid #3b6d11;color:#3b6d11;}
.toast-item.error{border-left:3px solid #a32d2d;color:#a32d2d;}
.toast-item.info{border-left:3px solid var(--blue);}
@keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}

/* ── LOGIN SCREEN ───────────────────────────── */
#login-screen{position:fixed;inset:0;background:var(--bg);display:flex;align-items:center;justify-content:center;z-index:1000;}
.login-box{background:var(--white);border-radius:16px;border:0.5px solid var(--border);box-shadow:var(--shadow);padding:2.5rem 2rem;width:100%;max-width:380px;}
.login-logo{text-align:center;margin-bottom:1.75rem;}
.login-logo .logo-badge{display:inline-block;background:var(--navy);color:white;font-family:'Sora',sans-serif;font-size:18px;letter-spacing:2px;padding:8px 20px;border-radius:8px;margin-bottom:10px;}
.login-logo p{font-size:13px;color:var(--text-muted);}
.login-box h2{font-size:18px;font-weight:700;color:var(--navy);margin-bottom:1.5rem;text-align:center;}
.form-group{margin-bottom:1rem;}
.form-group label{display:block;font-size:12px;font-weight:600;color:#2d4a6e;margin-bottom:5px;}
.form-group input{width:100%;height:42px;border:0.5px solid var(--border);border-radius:8px;padding:0 12px;font-size:14px;font-family:inherit;background:#f7f9fc;color:var(--text);outline:none;transition:border-color .2s,box-shadow .2s;}
.form-group input:focus{border-color:var(--blue);background:white;box-shadow:0 0 0 3px rgba(26,95,173,.1);}
.btn-primary{width:100%;height:44px;background:var(--navy);color:white;border:none;border-radius:8px;font-size:14px;font-weight:600;font-family:inherit;cursor:pointer;transition:background .2s;}
.btn-primary:hover{background:var(--blue);}
.btn-primary:disabled{opacity:.6;cursor:not-allowed;}

/* ── LAYOUT ─────────────────────────────────── */
#app{display:none;}
#app.visible{display:flex;min-height:100vh;}

/* ── SIDEBAR ────────────────────────────────── */
.sidebar{width:var(--sidebar-w);background:var(--navy);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100;overflow-y:auto;}
.sidebar-logo{padding:1.25rem 1.25rem 0.75rem;border-bottom:0.5px solid rgba(255,255,255,.1);}
.sidebar-logo .logo-text{font-family:'Sora',sans-serif;font-size:16px;font-weight:700;color:white;letter-spacing:1.5px;}
.sidebar-logo .logo-sub{font-size:10px;color:rgba(255,255,255,.45);margin-top:2px;}
.sidebar-nav{flex:1;padding:1rem 0;}
.nav-section{padding:0.5rem 1rem 0.25rem;font-size:10px;font-weight:700;color:rgba(255,255,255,.3);letter-spacing:1.5px;text-transform:uppercase;}
.nav-item{display:flex;align-items:center;gap:10px;padding:9px 1.25rem;font-size:13px;color:rgba(255,255,255,.65);cursor:pointer;transition:background .15s,color .15s;border-left:3px solid transparent;text-decoration:none;}
.nav-item:hover{background:rgba(255,255,255,.07);color:rgba(255,255,255,.9);}
.nav-item.active{background:rgba(255,255,255,.1);color:white;border-left-color:var(--blue-light);}
.nav-item .nav-icon{width:32px;height:32px;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;}
.nav-item .nav-badge{margin-left:auto;background:rgba(255,255,255,.15);color:rgba(255,255,255,.8);font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;min-width:20px;text-align:center;}
.sidebar-footer{padding:1rem 1.25rem;border-top:0.5px solid rgba(255,255,255,.1);}
.sidebar-user{display:flex;align-items:center;gap:10px;}
.user-avatar{width:34px;height:34px;border-radius:50%;background:var(--blue);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:white;flex-shrink:0;}
.user-info .user-name{font-size:12px;font-weight:600;color:white;}
.user-info .user-role{font-size:10px;color:rgba(255,255,255,.4);}
.btn-logout{background:none;border:0.5px solid rgba(255,255,255,.2);color:rgba(255,255,255,.6);font-size:11px;padding:4px 10px;border-radius:6px;cursor:pointer;font-family:inherit;margin-top:8px;width:100%;transition:all .2s;}
.btn-logout:hover{background:rgba(255,0,0,.15);color:#ff7070;border-color:rgba(255,0,0,.3);}

/* ── MAIN CONTENT ───────────────────────────── */
.main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;}
.topbar{background:white;border-bottom:0.5px solid var(--border);padding:0 1.5rem;height:56px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
.topbar-title{font-size:15px;font-weight:700;color:var(--navy);}
.topbar-right{display:flex;align-items:center;gap:10px;}
.content{padding:1.5rem;}

/* ── PAGE ───────────────────────────────────── */
.page{display:none;}
.page.active{display:block;}

/* ── STATS CARDS ────────────────────────────── */
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:1.5rem;}
.stat-card{background:white;border-radius:var(--radius);border:0.5px solid var(--border);padding:1rem 1.1rem;cursor:pointer;transition:box-shadow .2s,transform .15s;}
.stat-card:hover{box-shadow:var(--shadow);transform:translateY(-2px);}
.stat-card .sc-icon{width:36px;height:36px;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:10px;font-size:16px;}
.stat-card .sc-num{font-size:26px;font-weight:700;color:var(--navy);line-height:1;}
.stat-card .sc-label{font-size:11px;color:var(--text-muted);margin-top:3px;font-weight:500;}
.stat-total{background:var(--navy)!important;}
.stat-total .sc-num,.stat-total .sc-label{color:white!important;}
.stat-total .sc-label{color:rgba(255,255,255,.6)!important;}

/* ── CHARTS ROW ─────────────────────────────── */
.charts-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:1.5rem;}
.chart-card{background:white;border-radius:var(--radius);border:0.5px solid var(--border);padding:1.25rem;}
.chart-card h3{font-size:13px;font-weight:700;color:var(--navy);margin-bottom:1rem;}
.bar-chart{display:flex;flex-direction:column;gap:8px;}
.bar-item{display:flex;align-items:center;gap:10px;font-size:12px;}
.bar-item .bar-label{width:80px;color:var(--text-muted);font-weight:500;text-align:right;flex-shrink:0;}
.bar-track{flex:1;background:#f0f4f9;border-radius:4px;height:8px;overflow:hidden;}
.bar-fill{height:100%;border-radius:4px;transition:width .6s ease;}
.bar-item .bar-val{width:28px;font-weight:700;color:var(--text);font-size:11px;}
.donut-wrap{display:flex;align-items:center;gap:1rem;}
.donut-legend{display:flex;flex-direction:column;gap:6px;flex:1;}
.legend-item{display:flex;align-items:center;gap:7px;font-size:12px;color:var(--text-muted);}
.legend-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0;}
.legend-item strong{color:var(--text);margin-left:auto;font-weight:700;}

/* ── RECENT TABLE ───────────────────────────── */
.recent-card{background:white;border-radius:var(--radius);border:0.5px solid var(--border);padding:1.25rem;}
.recent-card h3{font-size:13px;font-weight:700;color:var(--navy);margin-bottom:1rem;}

/* ── DATA TABLE PAGE ────────────────────────── */
.table-header{display:flex;align-items:center;gap:10px;margin-bottom:1rem;flex-wrap:wrap;}
.search-wrap{position:relative;flex:1;min-width:200px;}
.search-wrap input{width:100%;height:38px;border:0.5px solid var(--border);border-radius:8px;padding:0 12px 0 36px;font-size:13px;font-family:inherit;background:white;outline:none;}
.search-wrap input:focus{border-color:var(--blue);box-shadow:0 0 0 3px rgba(26,95,173,.08);}
.search-wrap .si{position:absolute;left:11px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:var(--text-muted);}
select.filter-select{height:38px;border:0.5px solid var(--border);border-radius:8px;padding:0 10px;font-size:13px;font-family:inherit;background:white;color:var(--text);outline:none;cursor:pointer;}
.btn-add{height:38px;background:var(--navy);color:white;border:none;border-radius:8px;padding:0 16px;font-size:13px;font-weight:600;font-family:inherit;cursor:pointer;display:flex;align-items:center;gap:6px;white-space:nowrap;transition:background .2s;}
.btn-add:hover{background:var(--blue);}

.table-wrap{background:white;border-radius:var(--radius);border:0.5px solid var(--border);overflow:hidden;}
table{width:100%;border-collapse:collapse;font-size:13px;}
thead th{background:#f7f9fc;padding:10px 14px;text-align:left;font-weight:700;font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;border-bottom:0.5px solid var(--border);white-space:nowrap;}
tbody td{padding:11px 14px;border-bottom:0.5px solid #f0f4f9;color:var(--text);vertical-align:middle;}
tbody tr:last-child td{border-bottom:none;}
tbody tr:hover{background:#fafbfd;}
.cell-name{font-weight:600;color:var(--navy);}
.cell-usaha{color:var(--text-muted);font-size:12px;}
.cell-address{color:var(--text-muted);font-size:12px;}
.cell-phone a{color:var(--blue);text-decoration:none;font-size:12px;}
.cell-drive a{color:var(--blue);text-decoration:none;font-size:12px;display:inline-flex;align-items:center;gap:4px;}
.cell-drive a:hover{text-decoration:underline;}

/* STATUS BADGES */
.badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}
.badge-proses{background:#e6f1fb;color:#185fa5;}
.badge-selesai{background:#eaf3de;color:#3b6d11;}
.badge-pending{background:#faeeda;color:#854f0b;}
.badge-dibatalkan{background:#fcebeb;color:#a32d2d;}
.badge::before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor;}

.cell-actions{display:flex;gap:6px;}
.btn-edit,.btn-del{width:30px;height:30px;border-radius:7px;border:0.5px solid var(--border);background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s;}
.btn-edit:hover{background:#e6f1fb;border-color:#185fa5;color:#185fa5;}
.btn-del:hover{background:#fcebeb;border-color:#a32d2d;color:#a32d2d;}
.btn-edit svg,.btn-del svg{width:14px;height:14px;}

.pagination{display:flex;align-items:center;gap:6px;padding:12px 16px;border-top:0.5px solid var(--border);background:white;border-radius:0 0 var(--radius) var(--radius);}
.page-info{font-size:12px;color:var(--text-muted);margin-right:auto;}
.page-btn{width:30px;height:30px;border:0.5px solid var(--border);border-radius:6px;background:white;cursor:pointer;font-size:12px;font-weight:600;display:flex;align-items:center;justify-content:center;transition:all .15s;}
.page-btn:hover{background:#e6f1fb;border-color:var(--blue);}
.page-btn.active{background:var(--navy);color:white;border-color:var(--navy);}
.page-btn:disabled{opacity:.4;cursor:not-allowed;}
.empty-state{text-align:center;padding:3rem 1rem;color:var(--text-muted);}
.empty-state .es-icon{font-size:40px;margin-bottom:10px;}
.empty-state p{font-size:14px;}

/* ── MODAL ───────────────────────────────────── */
.modal-overlay{position:fixed;inset:0;background:rgba(10,45,94,.4);z-index:200;display:flex;align-items:center;justify-content:center;padding:1rem;opacity:0;pointer-events:none;transition:opacity .2s;}
.modal-overlay.open{opacity:1;pointer-events:all;}
.modal{background:white;border-radius:14px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);transform:translateY(20px);transition:transform .2s;}
.modal-overlay.open .modal{transform:translateY(0);}
.modal-header{padding:1.25rem 1.5rem;border-bottom:0.5px solid var(--border);display:flex;align-items:center;justify-content:space-between;}
.modal-header h3{font-size:15px;font-weight:700;color:var(--navy);}
.modal-close{width:30px;height:30px;border:0.5px solid var(--border);border-radius:7px;background:none;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;color:var(--text-muted);transition:all .15s;}
.modal-close:hover{background:#fcebeb;color:#a32d2d;}
.modal-body{padding:1.5rem;}
.modal-footer{padding:1rem 1.5rem;border-top:0.5px solid var(--border);display:flex;justify-content:flex-end;gap:10px;}
.mf-group{margin-bottom:1.1rem;}
.mf-group label{display:block;font-size:12px;font-weight:700;color:#2d4a6e;margin-bottom:5px;}
.mf-group input,.mf-group select,.mf-group textarea{width:100%;border:0.5px solid var(--border);border-radius:8px;padding:9px 12px;font-size:13px;font-family:inherit;background:#f7f9fc;color:var(--text);outline:none;transition:border-color .2s;}
.mf-group textarea{min-height:72px;resize:vertical;}
.mf-group input:focus,.mf-group select:focus,.mf-group textarea:focus{border-color:var(--blue);background:white;box-shadow:0 0 0 3px rgba(26,95,173,.08);}
.mf-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.btn-cancel{height:38px;background:white;color:var(--text);border:0.5px solid var(--border);border-radius:8px;padding:0 18px;font-size:13px;font-weight:600;font-family:inherit;cursor:pointer;transition:background .15s;}
.btn-cancel:hover{background:#f7f9fc;}
.btn-save{height:38px;background:var(--navy);color:white;border:none;border-radius:8px;padding:0 18px;font-size:13px;font-weight:600;font-family:inherit;cursor:pointer;transition:background .2s;}
.btn-save:hover{background:var(--blue);}
.btn-save:disabled{opacity:.6;cursor:not-allowed;}

/* ── DELETE CONFIRM ──────────────────────────── */
.confirm-overlay{position:fixed;inset:0;background:rgba(10,45,94,.4);z-index:300;display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:opacity .2s;}
.confirm-overlay.open{opacity:1;pointer-events:all;}
.confirm-box{background:white;border-radius:14px;padding:1.75rem;max-width:380px;width:100%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.2);}
.confirm-box .confirm-icon{font-size:36px;margin-bottom:12px;}
.confirm-box h3{font-size:15px;font-weight:700;color:var(--text);margin-bottom:6px;}
.confirm-box p{font-size:13px;color:var(--text-muted);margin-bottom:1.5rem;}
.confirm-box .confirm-btns{display:flex;gap:10px;justify-content:center;}
.btn-danger{height:38px;background:#a32d2d;color:white;border:none;border-radius:8px;padding:0 20px;font-size:13px;font-weight:600;font-family:inherit;cursor:pointer;}
.btn-danger:hover{background:#791f1f;}
</style>
</head>
<body>

<div id="toast"></div>

<!-- ═══════════════════════════════════════════ -->
<!--  LOGIN SCREEN                               -->
<!-- ═══════════════════════════════════════════ -->
<div id="login-screen" <?= $is_logged_in ? 'style="display:none"' : '' ?>>
  <div class="login-box">
    <div class="login-logo">
      <div class="logo-badge">JABALEGA</div>
      <p>Panel Admin — Jasa Bantuan Legalitas</p>
    </div>
    <h2>Masuk ke Dashboard</h2>
    <div class="form-group">
      <label>Username</label>
      <input type="text" id="l-user" placeholder="admin" autocomplete="username"/>
    </div>
    <div class="form-group">
      <label>Password</label>
      <input type="password" id="l-pass" placeholder="••••••••" autocomplete="current-password"/>
    </div>
    <button class="btn-primary" id="btn-login" onclick="doLogin()">Masuk</button>
    <p style="font-size:11px;color:var(--text-muted);text-align:center;margin-top:14px;">Default: admin / password</p>
  </div>
</div>

<!-- ═══════════════════════════════════════════ -->
<!--  APP SHELL                                  -->
<!-- ═══════════════════════════════════════════ -->
<div id="app" class="<?= $is_logged_in ? 'visible' : '' ?>">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-text">JABALEGA</div>
      <div class="logo-sub">Admin Panel</div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">Utama</div>
      <div class="nav-item active" data-page="dashboard" onclick="switchPage('dashboard',this)">
        <div class="nav-icon">■</div> Dashboard
      </div>
      <div class="nav-section" style="margin-top:8px;">Data Client</div>
      <?php foreach($tables_config as $key => $cfg): ?>
      <div class="nav-item" data-page="tabel-<?=$key?>" onclick="switchPage('tabel-<?=$key?>',this,'<?=$key?>')">
        <?=$cfg['label']?>
        <span class="nav-badge" id="badge-<?=$key?>">-</span>
      </div>
      <?php endforeach; ?>
    </nav>
    <div class="sidebar-footer">
      <div class="sidebar-user">
        <div class="user-avatar"><?=strtoupper(substr($admin_name,0,1))?></div>
        <div class="user-info">
          <div class="user-name"><?=htmlspecialchars($admin_name)?></div>
          <div class="user-role">Administrator</div>
        </div>
      </div>
      <button class="btn-logout" onclick="doLogout()">Keluar</button>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main">
    <div class="topbar">
      <span class="topbar-title" id="page-title">Dashboard</span>
      <div class="topbar-right">
        <span style="font-size:12px;color:var(--text-muted);"><?=date('d M Y')?></span>
      </div>
    </div>
    <div class="content">

      <!-- ─── DASHBOARD PAGE ─────────────────── -->
      <div id="page-dashboard" class="page active">
        <div class="stats-grid" id="stats-grid">
          <div class="stat-card stat-total">
            <div class="sc-num" id="stat-total">—</div>
            <div class="sc-label">Total Semua Client</div>
          </div>
          <?php foreach($tables_config as $key => $cfg): ?>
          <div class="stat-card" onclick="switchPage('tabel-<?=$key?>',document.querySelector('[data-page=tabel-<?=$key?>]'),'<?=$key?>')" style="cursor:pointer;">
            <div class="sc-num" id="stat-<?=$key?>">—</div>
            <div class="sc-label"><?=$cfg['label']?></div>
          </div>
          <?php endforeach; ?>
        </div>

        <div class="charts-row">
          <div class="chart-card">
            <h3>Client per Layanan</h3>
            <div class="bar-chart" id="chart-services"></div>
          </div>
        </div>

        <div class="recent-card">
          <h3>Client Terbaru (semua layanan)</h3>
          <div style="margin-top:.75rem;">
            <table>
              <thead><tr><th>Nama</th><th>Nama Usaha</th><th>Layanan</th></tr></thead>
              <tbody id="recent-tbody"><tr><td colspan="3" class="empty-state">Memuat...</td></tr></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ─── TABLE PAGES (generated per table) ─ -->
      <?php foreach($tables_config as $key => $cfg): ?>
      <div id="page-tabel-<?=$key?>" class="page" data-table="<?=$key?>">
        <div class="table-header">
          <div class="search-wrap">
            <svg class="si" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" placeholder="Cari nama, usaha, telepon..." oninput="debounceSearch('<?=$key?>')" id="search-<?=$key?>"/>
          </div>

          <button class="btn-add" onclick="openModal('<?=$key?>',null)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Client
          </button>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th><th>Nama Client</th><th>Nama Usaha</th>
                <?php if(in_array($key, ['psat','izin_lainnya'])): ?>
                  <th>Jenis Izin</th><th>No. Telepon</th><th>Email</th><th>Tgl Terbit</th><th>Masa Berlaku</th>
                <?php else: ?>
                  <th>Alamat</th><th>No. Telepon</th><th>Tgl Terbit</th><th>Masa Berlaku</th><th>Google Drive</th>
                <?php endif; ?>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody id="tbody-<?=$key?>">
              <tr><td colspan="<?=in_array($key, ['psat','izin_lainnya']) ? 9 : 9?>" class="empty-state"><div class="es-icon">∙∙∙</div><p>Memuat data...</p></td></tr>
            </tbody>
          </table>
          <div class="pagination" id="pagination-<?=$key?>"></div>
        </div>
      </div>
      <?php endforeach; ?>

    </div><!-- .content -->
  </main>
</div><!-- #app -->

<!-- ═══════════════════════════════════════════ -->
<!--  MODAL FORM                                 -->
<!-- ═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modal-overlay" onclick="closeModalOutside(event)">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modal-title">Tambah Client</h3>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="modal-table"/>
      <input type="hidden" id="modal-id"/>
      <div class="mf-row">
        <div class="mf-group">
          <label>Nama Client *</label>
          <input type="text" id="f-nama" placeholder="Nama lengkap"/>
        </div>
        <div class="mf-group">
          <label>Nama Usaha</label>
          <input type="text" id="f-nama_usaha" placeholder="Nama toko / perusahaan"/>
        </div>
      </div>
      
      <!-- Fields untuk PSAT & Izin Lainnya -->
      <div id="section-psat-fields" style="display:none;">
        <div class="mf-row">
          <div class="mf-group">
            <label>Jenis Izin/Sertifikat *</label>
            <input type="text" id="f-izin" placeholder="Contoh: Izin PSAT"/>
          </div>
          <div class="mf-group">
            <label>No. Telepon</label>
            <input type="text" id="f-no_telefon" placeholder="08xxxxxxxxxx"/>
          </div>
          <div class="mf-group">
            <label>Email *</label>
            <input type="email" id="f-email" placeholder="email@example.com"/>
          </div>
        </div>
        <div class="mf-row">
          <div class="mf-group">
            <label>Tanggal Terbit *</label>
            <input type="date" id="f-tanggal_terbit"/>
          </div>
          <div class="mf-group">
            <label>Masa Berlaku Sertifikat *</label>
            <input type="date" id="f-masa_berlaku"/>
          </div>
        </div>
        <div class="mf-group">
          <label>Link Google Drive</label>
          <input type="url" id="f-link_gdrive" placeholder="https://drive.google.com/..." />
        </div>
      </div>
      
      <!-- Fields untuk tabel lain -->
      <div id="section-standard-fields">
        <div class="mf-group">
          <label>Alamat</label>
          <textarea id="f-alamat" placeholder="Alamat lengkap"></textarea>
        </div>
        <div class="mf-row">
          <div class="mf-group">
            <label>No. Telepon</label>
            <input type="text" id="f-no_telefon" placeholder="08xxxxxxxxxx"/>
          </div>
        </div>
        <div class="mf-row">
          <div class="mf-group">
            <label>Tanggal Terbit Izin/Sertifikat</label>
            <input type="date" id="f-tanggal_terbit"/>
          </div>
          <div class="mf-group">
            <label>Masa Berlaku Sertifikat</label>
            <input type="date" id="f-masa_berlaku"/>
          </div>
        </div>
        <div id="section-gdrive" class="mf-group">
          <label>Link Google Drive</label>
          <input type="url" id="f-link_gdrive" placeholder="https://drive.google.com/..." />
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-cancel" onclick="closeModal()">Batal</button>
      <button class="btn-save" id="btn-save" onclick="saveData()">Simpan</button>
    </div>
  </div>
</div>

<!-- DELETE CONFIRM -->
<div class="confirm-overlay" id="confirm-overlay">
  <div class="confirm-box">
    <div class="confirm-icon">⚠</div>
    <h3>Hapus Data Client?</h3>
    <p id="confirm-msg">Data ini akan dihapus permanen dan tidak bisa dikembalikan.</p>
    <div class="confirm-btns">
      <button class="btn-cancel" onclick="closeConfirm()">Batal</button>
      <button class="btn-danger" id="btn-confirm-del" onclick="confirmDelete()">Ya, Hapus</button>
    </div>
  </div>
</div>

<script>
// ═══════════════════════════════════════════════
//  STATE
// ═══════════════════════════════════════════════
const state = {
  currentPage: {},   // page number per table
  deleteTarget: null // {table, id, name}
};

// ═══════════════════════════════════════════════
//  INIT
// ═══════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
  const loggedIn = <?= $is_logged_in ? 'true' : 'false' ?>;
  if (loggedIn) {
    loadDashboard();
  }
  // Enter key on login
  document.getElementById('l-pass')?.addEventListener('keydown', e => {
    if (e.key === 'Enter') doLogin();
  });
});

// ═══════════════════════════════════════════════
//  AUTH
// ═══════════════════════════════════════════════
async function doLogin() {
  const btn = document.getElementById('btn-login');
  const u = document.getElementById('l-user').value.trim();
  const p = document.getElementById('l-pass').value;
  if (!u || !p) { toast('Isi username dan password!', 'error'); return; }
  btn.disabled = true; btn.textContent = 'Memproses...';
  const res = await api({action:'login', username:u, password:p});
  if (res.success) {
    document.getElementById('login-screen').style.display = 'none';
    document.getElementById('app').classList.add('visible');
    toast(`Selamat datang, ${res.name}!`, 'success');
    loadDashboard();
  } else {
    toast(res.message || 'Login gagal', 'error');
  }
  btn.disabled = false; btn.textContent = 'Masuk';
}

async function doLogout() {
  await api({action:'logout'});
  location.reload();
}

// ═══════════════════════════════════════════════
//  NAVIGATION
// ═══════════════════════════════════════════════
function switchPage(pageId, el, tableKey) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  const p = document.getElementById('page-' + pageId);
  if (p) p.classList.add('active');
  if (el) el.classList.add('active');
  const titles = {dashboard:'Dashboard'};
  <?php foreach($tables_config as $key => $cfg): ?>
  titles['tabel-<?=$key?>'] = '<?=$cfg['label']?>';
  <?php endforeach; ?>
  document.getElementById('page-title').textContent = titles[pageId] || pageId;
  if (tableKey) loadTableData(tableKey);
}

// ═══════════════════════════════════════════════
//  DASHBOARD
// ═══════════════════════════════════════════════
async function loadDashboard() {
  const res = await apiGet({action:'get_stats'});
  if (!res.success) return;
  // stat cards
  document.getElementById('stat-total').textContent = res.total;
  const tables = <?=json_encode(array_keys($tables_config))?>;
  tables.forEach(t => {
    const el = document.getElementById('stat-' + t);
    if (el) el.textContent = res.table_counts[t] ?? 0;
    const badge = document.getElementById('badge-' + t);
    if (badge) badge.textContent = res.table_counts[t] ?? 0;
  });
  // bar chart
  const colors = <?=json_encode(array_column($tables_config,'color'))?>;
  const labels = <?=json_encode(array_column($tables_config,'label'))?>;
  const max = Math.max(...tables.map(t => res.table_counts[t] || 0), 1);
  const bc = document.getElementById('chart-services');
  bc.innerHTML = tables.map((t,i) => {
    const v = res.table_counts[t] || 0;
    const pct = Math.round((v/max)*100);
    return `<div class="bar-item">
      <div class="bar-label">${labels[i].split(' ').slice(-1)[0]}</div>
      <div class="bar-track"><div class="bar-fill" style="width:${pct}%;background:${colors[i]}"></div></div>
      <div class="bar-val">${v}</div>
    </div>`;
  }).join('');
  // recent
  loadRecent(res.table_counts);
}

/* Status donut chart removed - no longer applicable */

async function loadRecent(counts) {
  const tables = <?=json_encode(array_keys($tables_config))?>;
  const labels = <?=json_encode(array_combine(array_keys($tables_config), array_column($tables_config,'label')))?>;
  const tbody = document.getElementById('recent-tbody');
  let rows = [];
  for (const t of tables) {
    if ((counts[t] || 0) === 0) continue;
    const res = await apiGet({action:'get_data', table:t, page:1});
    if (res.success && res.data) {
      res.data.slice(0,3).forEach(r => rows.push({...r, _table:t}));
    }
  }
  rows = rows.slice(0,10);
  if (!rows.length) {
    tbody.innerHTML = '<tr><td colspan="3" class="empty-state"><div class="es-icon">—</div><p>Belum ada data client</p></td></tr>';
    return;
  }
  tbody.innerHTML = rows.map(r => `
    <tr>
      <td class="cell-name">${esc(r.nama)}</td>
      <td class="cell-usaha">${esc(r.nama_usaha||'—')}</td>
      <td><span style="font-size:12px;background:#f0f4f9;padding:3px 8px;border-radius:6px;font-weight:600;">${labels[r._table]||r._table}</span></td>
    </tr>`).join('');
}

// ═══════════════════════════════════════════════
//  TABLE DATA
// ═══════════════════════════════════════════════
const searchTimers = {};
function debounceSearch(table) {
  clearTimeout(searchTimers[table]);
  searchTimers[table] = setTimeout(() => loadTableData(table), 350);
}

async function loadTableData(table, page) {
  page = page || state.currentPage[table] || 1;
  state.currentPage[table] = page;
  const tbody = document.getElementById('tbody-' + table);
  const search = document.getElementById('search-' + table)?.value || '';
  const colSpan = table === 'psat' || table === 'izin_lainnya' ? 8 : 7;
  tbody.innerHTML = `<tr><td colspan="${colSpan}" class="empty-state"><div class="es-icon">∙∙∙</div><p>Memuat...</p></td></tr>`;
  const res = await apiGet({action:'get_data', table, search, page});
  if (!res.success) { toast('Gagal memuat data', 'error'); return; }
  if (!res.data.length) {
    tbody.innerHTML = `<tr><td colspan="${colSpan}" class="empty-state"><div class="es-icon">—</div><p>Tidak ada data ditemukan</p></td></tr>`;
    renderPagination(table, 0, 1, 1);
    return;
  }
  const offset = (page-1) * res.per_page;
  
  // Render untuk PSAT dan Izin Lainnya
  if (table === 'psat' || table === 'izin_lainnya') {
    tbody.innerHTML = res.data.map((r, i) => `
      <tr>
        <td style="color:var(--text-muted);font-size:12px">${offset+i+1}</td>
        <td>
          <div class="cell-name">${esc(r.nama)}</div>
        </td>
        <td class="cell-usaha">${esc(r.nama_usaha||'—')}</td>
        <td style="font-size:12px">${esc(r.izin||'—')}</td>
        <td class="cell-phone"><a href="tel:${esc(r.no_telefon)}">${esc(r.no_telefon||'—')}</a></td>
        <td style="font-size:12px"><a href="mailto:${esc(r.email)}">${esc(r.email||'—')}</a></td>
        <td style="font-size:12px">${formatDate(r.tanggal_terbit)}</td>
        <td style="font-size:12px;font-weight:600">${formatDate(r.masa_berlaku)}</td>
        <td>
          <div class="cell-actions">
            <button class="btn-edit" onclick="openModal('${table}',${r.id})" title="Edit">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
            <button class="btn-del" onclick="askDelete('${table}',${r.id},'${esc(r.nama)}')" title="Hapus">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
            </button>
          </div>
        </td>
      </tr>`).join('');
  } else {
    // Render untuk tabel standard
    tbody.innerHTML = res.data.map((r, i) => `
      <tr>
        <td style="color:var(--text-muted);font-size:12px">${offset+i+1}</td>
        <td>
          <div class="cell-name">${esc(r.nama)}</div>
        </td>
        <td class="cell-usaha">${esc(r.nama_usaha||'—')}</td>
        <td class="cell-address">${esc(r.alamat||'—')}</td>
        <td class="cell-phone"><a href="tel:${esc(r.no_telefon)}">${esc(r.no_telefon||'—')}</a></td>
        <td style="font-size:12px">${formatDate(r.tanggal_terbit)}</td>
        <td style="font-size:12px;font-weight:600">${formatDate(r.masa_berlaku)}</td>
        <td class="cell-drive">${r.link_gdrive ? `<a href="${esc(r.link_gdrive)}" target="_blank">Buka Drive</a>` : '<span style="color:var(--text-muted)">—</span>'}</td>
        <td>
          <div class="cell-actions">
            <button class="btn-edit" onclick="openModal('${table}',${r.id})" title="Edit">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </button>
            <button class="btn-del" onclick="askDelete('${table}',${r.id},'${esc(r.nama)}')" title="Hapus">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
            </button>
          </div>
        </td>
      </tr>`).join('');
  }
  
  renderPagination(table, res.total, page, res.total_pages);
  // update badge
  const badge = document.getElementById('badge-' + table);
  if (badge) badge.textContent = res.total;
}

function renderPagination(table, total, current, totalPages) {
  const el = document.getElementById('pagination-' + table);
  if (!el) return;
  let html = `<span class="page-info">Total ${total} data</span>`;
  html += `<button class="page-btn" onclick="loadTableData('${table}',${current-1})" ${current<=1?'disabled':''}>‹</button>`;
  let start = Math.max(1, current-2), end = Math.min(totalPages, start+4);
  if (end-start < 4) start = Math.max(1, end-4);
  for (let i=start;i<=end;i++) {
    html += `<button class="page-btn ${i===current?'active':''}" onclick="loadTableData('${table}',${i})">${i}</button>`;
  }
  html += `<button class="page-btn" onclick="loadTableData('${table}',${current+1})" ${current>=totalPages?'disabled':''}>›</button>`;
  el.innerHTML = html;
}

// ═══════════════════════════════════════════════
//  MODAL
// ═══════════════════════════════════════════════
async function openModal(table, id) {
  document.getElementById('modal-table').value = table;
  document.getElementById('modal-id').value = id || '';
  const tableLabels = <?=json_encode(array_column($tables_config,'label',''))?>;
  const isPsatTable = table === 'psat' || table === 'izin_lainnya';
  
  // Show/hide sections based on table type
  document.getElementById('section-psat-fields').style.display = isPsatTable ? 'block' : 'none';
  document.getElementById('section-standard-fields').style.display = isPsatTable ? 'none' : 'block';
  
  // Clear all fields
  ['nama','nama_usaha','alamat','no_telefon','link_gdrive','izin','email','tanggal_terbit','masa_berlaku'].forEach(f => {
    const el = document.getElementById('f-'+f);
    if (el) el.value = '';
  });

  if (id) {
    document.getElementById('modal-title').textContent = 'Edit Data Client';
    document.getElementById('btn-save').textContent = 'Perbarui';
    const res = await apiGet({action:'get_row', table, id});
    if (res.success && res.row) {
      const r = res.row;
      document.getElementById('f-nama').value = r.nama || '';
      document.getElementById('f-nama_usaha').value = r.nama_usaha || '';
      
      if (isPsatTable) {
        document.getElementById('f-izin').value = r.izin || '';
        document.getElementById('f-email').value = r.email || '';
        document.getElementById('f-link_gdrive').value = r.link_gdrive || '';
        document.getElementById('f-tanggal_terbit').value = r.tanggal_terbit || '';
        document.getElementById('f-masa_berlaku').value = r.masa_berlaku || '';
      } else {
        document.getElementById('f-alamat').value = r.alamat || '';
        document.getElementById('f-no_telefon').value = r.no_telefon || '';
        document.getElementById('f-link_gdrive').value = r.link_gdrive || '';
      }
    }
  } else {
    document.getElementById('modal-title').textContent = 'Tambah Client Baru';
    document.getElementById('btn-save').textContent = 'Simpan';
  }
  document.getElementById('modal-overlay').classList.add('open');
  setTimeout(() => document.getElementById('f-nama').focus(), 200);
}

function closeModal() {
  document.getElementById('modal-overlay').classList.remove('open');
}

function closeModalOutside(e) {
  if (e.target === document.getElementById('modal-overlay')) closeModal();
}

async function saveData() {
  const table = document.getElementById('modal-table').value;
  const id = document.getElementById('modal-id').value;
  const nama = document.getElementById('f-nama').value.trim();
  if (!nama) { toast('Nama client wajib diisi!', 'error'); document.getElementById('f-nama').focus(); return; }
  
  const isPsatTable = table === 'psat' || table === 'izin_lainnya';
  const btn = document.getElementById('btn-save');
  btn.disabled = true; btn.textContent = 'Menyimpan...';
  
  const body = {
    action: id ? 'edit_data' : 'add_data',
    table, id,
    nama,
    nama_usaha: document.getElementById('f-nama_usaha').value,
  };
  
  if (isPsatTable) {
    body.izin = document.getElementById('f-izin').value.trim();
    body.email = document.getElementById('f-email').value.trim();
    body.link_gdrive = document.getElementById('f-link_gdrive').value.trim();
    body.tanggal_terbit = document.getElementById('f-tanggal_terbit').value;
    body.masa_berlaku = document.getElementById('f-masa_berlaku').value;
  } else {
    body.alamat = document.getElementById('f-alamat').value;
    body.no_telefon = document.getElementById('f-no_telefon').value;
    body.link_gdrive = document.getElementById('f-link_gdrive').value;
  }
  
  const res = await api(body);
  btn.disabled = false; btn.textContent = (id ? 'Perbarui' : 'Simpan');
  if (res.success) {
    toast(res.message, 'success');
    closeModal();
    loadTableData(table);
    loadDashboard();
  } else {
    toast(res.message || 'Gagal menyimpan', 'error');
  }
}

// ═══════════════════════════════════════════════
//  DELETE
// ═══════════════════════════════════════════════
function askDelete(table, id, name) {
  state.deleteTarget = {table, id};
  document.getElementById('confirm-msg').textContent = `"${name}" akan dihapus permanen.`;
  document.getElementById('confirm-overlay').classList.add('open');
}

function closeConfirm() {
  document.getElementById('confirm-overlay').classList.remove('open');
  state.deleteTarget = null;
}

async function confirmDelete() {
  if (!state.deleteTarget) return;
  const {table, id} = state.deleteTarget;
  const res = await api({action:'delete_data', table, id});
  if (res.success) {
    toast(res.message, 'success');
    closeConfirm();
    loadTableData(table);
    loadDashboard();
  } else {
    toast(res.message || 'Gagal hapus', 'error');
  }
}

// ═══════════════════════════════════════════════
//  HELPERS
// ═══════════════════════════════════════════════
async function api(data) {
  const fd = new FormData();
  Object.entries(data).forEach(([k,v]) => fd.append(k, v ?? ''));
  try {
    const res = await fetch('api.php', {method:'POST', body:fd});
    return await res.json();
  } catch(e) { return {success:false, message:'Koneksi error'}; }
}

async function apiGet(params) {
  const qs = new URLSearchParams(params).toString();
  try {
    const res = await fetch('api.php?' + qs);
    return await res.json();
  } catch(e) { return {success:false}; }
}

function toast(msg, type='info') {
  const el = document.createElement('div');
  el.className = `toast-item ${type}`;
  el.textContent = msg;
  document.getElementById('toast').appendChild(el);
  setTimeout(() => el.remove(), 3500);
}

function esc(str) {
  if (!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function statusBadge(status) {
  const map = {
    'Proses':'badge-proses','Selesai':'badge-selesai',
    'Pending':'badge-pending','Dibatalkan':'badge-dibatalkan'
  };
  return `<span class="badge ${map[status]||''}">${status||'—'}</span>`;
}

function formatDate(dt) {
  if (!dt) return '—';
  return new Date(dt).toLocaleDateString('id-ID', {day:'2-digit',month:'short',year:'numeric'});
}

// Close modal on Escape
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') { closeModal(); closeConfirm(); }
});
</script>

</body>
</html>

