<?php
session_start();
require 'functions.php';

// 1. Proteksi Admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

global $conn;

// Ambil halaman aktif
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Hitung Notifikasi
$query_notif = mysqli_query($conn, "SELECT COUNT(*) as jml FROM aspirasi WHERE status = 'Menunggu'");
$notif_data = mysqli_fetch_assoc($query_notif);
$jml_notif = $notif_data['jml'] ?? 0;

// --- LOGIKA KELOLA ADMIN & HISTORY (Tetap Sama) ---
if (isset($_POST['tambah_admin'])) {
    $username = htmlspecialchars($_POST['username']);
    $nama = htmlspecialchars($_POST['nama_petugas']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $cek = mysqli_query($conn, "SELECT username FROM admin WHERE username = '$username'");
    if(mysqli_fetch_assoc($cek)) {
        echo "<script>alert('Username sudah digunakan!');</script>";
    } else {
        $query_tambah = "INSERT INTO admin (username, password, nama_petugas) VALUES ('$username', '$password', '$nama')";
        mysqli_query($conn, $query_tambah);
        echo "<script>alert('Admin baru berhasil ditambahkan!'); window.location='admin.php?page=kelola_admin';</script>";
    }
}

if (isset($_GET['hapus_admin'])) {
    $id_hapus = $_GET['hapus_admin'];
    if ($id_hapus == $_SESSION['id']) {
        echo "<script>alert('Anda tidak bisa menghapus akun sendiri!'); window.location='admin.php?page=kelola_admin';</script>";
    } else {
        mysqli_query($conn, "DELETE FROM admin WHERE id_admin = $id_hapus");
        echo "<script>alert('Admin berhasil dihapus!'); window.location='admin.php?page=kelola_admin';</script>";
    }
}

if (isset($_POST['delete_history'])) {
    if (!empty($_POST['selected_ids'])) {
        $ids = implode(',', array_map('intval', $_POST['selected_ids']));
        mysqli_query($conn, "DELETE FROM tanggapan WHERE id_tanggapan IN ($ids)");
        echo "<script>alert('Riwayat terpilih berhasil dihapus!'); window.location='admin.php?page=history';</script>";
    }
}

// 3. Query Data Utama
$query_base = "SELECT aspirasi.*, siswa.nama, siswa.kelas, kategori.nama_kategori 
                FROM aspirasi 
                JOIN siswa ON aspirasi.nis = siswa.nis 
                JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori";
$laporan = query($query_base . " ORDER BY tanggal DESC");

$total = count($laporan);
$pending = count(array_filter($laporan, function($item) { return $item['status'] == 'Menunggu'; }));
$proses = count(array_filter($laporan, function($item) { return $item['status'] == 'Proses'; }));
$selesai = count(array_filter($laporan, function($item) { return $item['status'] == 'Selesai'; }));

$list_admin = query("SELECT * FROM admin ORDER BY id_admin DESC");
$riwayat = query("SELECT tanggapan.*, aspirasi.keterangan as aduan, admin.nama_petugas 
                  FROM tanggapan 
                  JOIN aspirasi ON tanggapan.id_aspirasi = aspirasi.id_aspirasi
                  JOIN admin ON tanggapan.id_admin = admin.id_admin
                  ORDER BY id_tanggapan DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin | E-Aspirasi SD AMYN</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root { --primary: #4f46e5; --sidebar-bg: #ffffff; --main-bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--main-bg); overflow-x: hidden; }

        /* Sidebar Responsive Logic */
        .sidebar { 
            width: 280px; position: fixed; height: 100vh; background: var(--sidebar-bg); 
            border-right: 1px solid #e2e8f0; z-index: 1001; transition: all 0.4s ease; left: 0;
        }
        .main-content { 
            margin-left: 280px; min-height: 100vh; transition: all 0.4s ease; width: calc(100% - 280px);
        }

        @media (max-width: 1024px) {
            .sidebar { left: -280px; }
            .main-content { margin-left: 0; width: 100%; }
            .sidebar.active { left: 0; box-shadow: 20px 0 50px rgba(0,0,0,0.1); }
            .overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; }
            .overlay.active { display: block; }
        }

        .nav-link-custom { 
            display: flex; align-items: center; padding: 12px 20px; border-radius: 14px; 
            color: #64748b; font-weight: 600; text-decoration: none !important; transition: 0.3s; margin-bottom: 5px;
        }
        .nav-link-custom:hover { background: #f1f5f9; color: var(--primary); }
        .nav-link-custom.active { background: var(--primary); color: white; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); }
        
        .glass-card { background: white; border-radius: 24px; border: 1px solid #eef2ff; transition: 0.3s; }
        .table-responsive { border-radius: 15px; overflow-x: auto; }
    </style>
</head>
<body>

    <div class="overlay" id="overlay" onclick="toggleSidebar()"></div>

    <aside class="sidebar p-4 no-print" id="sidebar">
        <div class="flex items-center gap-3 mb-10 px-2">
            <div class="bg-indigo-600 rounded-xl text-white flex items-center justify-center font-bold shadow-lg shadow-indigo-200" style="width: 45px; height: 45px;">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <h1 class="text-lg font-black text-slate-800 tracking-tighter leading-none">E-ASPIRASI</h1>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Admin Panel</span>
            </div>
        </div>
        
        <nav>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[2px] mb-4 px-2">Menu Utama</p>
            <a href="admin.php?page=dashboard" class="nav-link-custom <?= $page == 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-th-large mr-3"></i> Dashboard
            </a>
            <a href="admin.php?page=tanggapi" class="nav-link-custom <?= $page == 'tanggapi' ? 'active' : '' ?>">
                <i class="fas fa-comment-dots mr-3"></i> Tanggapi
            </a>
            <a href="admin.php?page=history" class="nav-link-custom <?= $page == 'history' ? 'active' : '' ?>">
                <i class="fas fa-history mr-3"></i> Riwayat
            </a>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[2px] mb-4 mt-8 px-2">Pengaturan</p>
            <a href="admin.php?page=kelola_admin" class="nav-link-custom <?= $page == 'kelola_admin' ? 'active' : '' ?>">
                <i class="fas fa-user-shield mr-3"></i> Administrator
            </a>
        </nav>

        <div class="absolute bottom-8 left-0 w-full px-4">
            <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')" class="flex items-center gap-3 text-rose-500 font-bold text-sm px-4 py-3 rounded-xl hover:bg-rose-50 transition-all no-underline">
                <i class="fas fa-sign-out-alt"></i> Keluar Sistem
            </a>
        </div>
    </aside>

    <main class="main-content">
        <header class="h-20 bg-white/80 backdrop-blur-md border-b flex items-center justify-between px-6 md:px-10 sticky top-0 z-[99]">
            <div class="flex items-center gap-4">
                <button class="text-slate-500 p-2 hover:bg-slate-100 rounded-lg lg:hidden" onclick="toggleSidebar()">
                    <i class="fas fa-bars-staggered text-xl"></i>
                </button>
                <h2 class="text-xs font-bold text-slate-500 capitalize hidden sm:block">Halaman / <?= str_replace('_', ' ', $page) ?></h2>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <p class="text-xs font-black text-slate-800 leading-none mb-1"><?= $_SESSION['nama'] ?></p>
                    <span class="text-[10px] text-emerald-500 font-bold uppercase">Online</span>
                </div>
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border-2 border-white shadow-sm">
                    <?= strtoupper(substr($_SESSION['nama'], 0, 1)) ?>
                </div>
            </div>
        </header>

        <div class="p-5 md:p-10">
            
            <?php if ($page == 'dashboard') : ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="glass-card p-6 border-l-4 border-indigo-500 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Total Aduan</p>
                        <h3 class="text-3xl font-black text-slate-800"><?= $total ?></h3>
                    </div>
                    <div class="glass-card p-6 border-l-4 border-rose-500 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Menunggu</p>
                        <h3 class="text-3xl font-black text-slate-800"><?= $pending ?></h3>
                    </div>
                    <div class="glass-card p-6 border-l-4 border-amber-500 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Proses</p>
                        <h3 class="text-3xl font-black text-slate-800"><?= $proses ?></h3>
                    </div>
                    <div class="glass-card p-6 border-l-4 border-emerald-500 shadow-sm">
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Selesai</p>
                        <h3 class="text-3xl font-black text-slate-800"><?= $selesai ?></h3>
                    </div>
                </div>

                <div class="glass-card p-6 lg:p-8 shadow-sm mb-8">
                    <h3 class="text-lg font-bold text-slate-800 mb-6">Tren Laporan</h3>
                    <div class="h-[300px]">
                        <canvas id="chartLaporan"></canvas>
                    </div>
                </div>

            <?php elseif ($page == 'tanggapi') : ?>
                <div class="mb-6">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Menunggu Respon</h2>
                    <p class="text-slate-400 text-sm">Aspirasi yang memerlukan tindakan segera.</p>
                </div>
                <div class="glass-card overflow-hidden shadow-sm">
                    <div class="table-responsive">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 border-b border-slate-100">
                                <tr>
                                    <th class="p-4 text-[10px] font-black text-slate-400 uppercase">Pengirim</th>
                                    <th class="p-4 text-[10px] font-black text-slate-400 uppercase">Aduan</th>
                                    <th class="p-4 text-[10px] font-black text-slate-400 uppercase text-center">Status</th>
                                    <th class="p-4 text-[10px] font-black text-slate-400 uppercase text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php foreach($laporan as $row) : ?>
                                <tr>
                                    <td class="p-4">
                                        <p class="text-sm font-bold text-slate-800 mb-0"><?= $row['nama'] ?></p>
                                        <p class="text-[10px] text-indigo-500 font-black uppercase"><?= $row['kelas'] ?></p>
                                    </td>
                                    <td class="p-4">
                                        <p class="text-xs text-slate-500 italic truncate max-w-[150px] sm:max-w-xs">"<?= $row['keterangan'] ?>"</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        <?php 
                                            $c = $row['status'] == 'Selesai' ? 'bg-emerald-100 text-emerald-600' : ($row['status'] == 'Proses' ? 'bg-amber-100 text-amber-600' : 'bg-rose-100 text-rose-600');
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase <?= $c ?>"><?= $row['status'] ?></span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="proses_laporan.php?id=<?= $row['id_aspirasi']; ?>" class="bg-slate-900 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-indigo-600 transition-all no-underline inline-block">
                                            Respon
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'history') : ?>
                <div class="mb-6 flex flex-col sm:flex-row justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-slate-800 tracking-tight">Riwayat</h2>
                        <p class="text-slate-400 text-sm">Arsip tanggapan petugas.</p>
                    </div>
                    <button onclick="window.print()" class="bg-white border text-slate-600 px-4 py-2 rounded-xl font-bold text-xs uppercase hover:bg-slate-50">
                        <i class="fas fa-print mr-2"></i> Cetak
                    </button>
                </div>
                <div class="glass-card shadow-sm overflow-hidden">
                    <div class="table-responsive">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50">
                                <tr class="text-[10px] font-black text-slate-400 uppercase">
                                    <th class="p-4">Waktu</th>
                                    <th class="p-4">Tanggapan</th>
                                    <th class="p-4">Petugas</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-slate-50">
                                <?php foreach($riwayat as $r) : ?>
                                <tr>
                                    <td class="p-4 text-slate-400 font-bold"><?= date('d/m/y', strtotime($r['tgl_tanggapan'])) ?></td>
                                    <td class="p-4 text-slate-700 font-medium"><?= $r['tanggapan'] ?></td>
                                    <td class="p-4 font-bold text-indigo-600"><?= $r['nama_petugas'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <?php elseif ($page == 'kelola_admin') : ?>
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-4">
                        <div class="glass-card p-6 shadow-sm">
                            <h3 class="font-black text-slate-800 mb-4">Tambah Petugas</h3>
                            <form method="post" class="space-y-4">
                                <input type="text" name="nama_petugas" required class="w-full p-3 bg-slate-50 border-0 rounded-xl text-sm" placeholder="Nama Lengkap">
                                <input type="text" name="username" required class="w-full p-3 bg-slate-50 border-0 rounded-xl text-sm" placeholder="Username">
                                <input type="password" name="password" required class="w-full p-3 bg-slate-50 border-0 rounded-xl text-sm" placeholder="Password">
                                <button type="submit" name="tambah_admin" class="w-full bg-indigo-600 text-white p-3 rounded-xl font-black text-xs uppercase shadow-lg shadow-indigo-100">Simpan Petugas</button>
                            </form>
                        </div>
                    </div>
                    <div class="lg:col-span-8">
                        <div class="glass-card shadow-sm overflow-hidden">
                            <table class="w-full text-left divide-y divide-slate-50">
                                <tbody class="text-sm">
                                    <?php foreach($list_admin as $adm) : ?>
                                    <tr>
                                        <td class="p-4 font-bold text-slate-800"><?= $adm['nama_petugas'] ?> <br> <span class="text-[10px] text-slate-400">@<?= $adm['username'] ?></span></td>
                                        <td class="p-4 text-right">
                                            <?php if($adm['id_admin'] != $_SESSION['id']): ?>
                                                <a href="admin.php?page=kelola_admin&hapus_admin=<?= $adm['id_admin']; ?>" class="text-rose-500 bg-rose-50 px-3 py-1.5 rounded-lg font-black text-[10px] uppercase no-underline">Hapus</a>
                                            <?php else: ?>
                                                <span class="text-emerald-500 bg-emerald-50 px-3 py-1.5 rounded-lg font-black text-[10px] uppercase">Anda</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            if (window.innerWidth <= 1024) {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            } else {
                if (sidebar.style.left === '-280px') {
                    sidebar.style.left = '0';
                    document.querySelector('.main-content').style.marginLeft = '280px';
                    document.querySelector('.main-content').style.width = 'calc(100% - 280px)';
                } else {
                    sidebar.style.left = '-280px';
                    document.querySelector('.main-content').style.marginLeft = '0';
                    document.querySelector('.main-content').style.width = '100%';
                }
            }
        }

        <?php if($page == 'dashboard'): ?>
        new Chart(document.getElementById('chartLaporan'), {
            type: 'bar',
            data: {
                labels: ['Menunggu', 'Proses', 'Selesai'],
                datasets: [{
                    data: [<?= $pending ?>, <?= $proses ?>, <?= $selesai ?>],
                    backgroundColor: ['#f43f5e', '#f59e0b', '#10b981'],
                    borderRadius: 10
                }]
            },
            options: { maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
        <?php endif; ?>
    </script>
</body>
</html>