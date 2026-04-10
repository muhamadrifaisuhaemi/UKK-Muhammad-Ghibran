<?php
session_start();
require 'functions.php';

// Proteksi Halaman
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: index.php");
    exit;
}

$nis = $_SESSION['id'];
$nama_user = $_SESSION['nama'];
global $conn;

$page = $_GET['page'] ?? 'dashboard';

// --- LOGIKA INPUT ASPIRASI ---
if (isset($_POST["kirim"])) {
    $isi_laporan = htmlspecialchars($_POST["isi_laporan"]);
    $id_kategori = $_POST["id_kategori"];
    $lokasi = htmlspecialchars($_POST["lokasi"]);
    $mode_waktu = $_POST["mode_waktu"];
    $tanggal_final = ($mode_waktu == "otomatis") ? date("Y-m-d H:i:s") : $_POST["tanggal_kejadian"] . " " . $_POST["jam_kejadian"] . ":00";

    $nama_baru = "";
    if ($_FILES['foto']['error'] === 0) {
        $nama_foto = $_FILES['foto']['name'];
        $ukuran_foto = $_FILES['foto']['size'];
        $tmp_name = $_FILES['foto']['tmp_name'];
        $ekstensi_valid = ['jpg', 'jpeg', 'png'];
        $ekstensi = strtolower(pathinfo($nama_foto, PATHINFO_EXTENSION));

        if (in_array($ekstensi, $ekstensi_valid) && $ukuran_foto < 2000000) {
            $nama_baru = uniqid() . "." . $ekstensi;
            if (!is_dir('./assets/img/')) mkdir('./assets/img/', 0777, true);
            move_uploaded_file($tmp_name, './assets/img/' . $nama_baru);
        }
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO aspirasi (nis, id_kategori, lokasi, keterangan, foto, tanggal, status, feedback) VALUES (?, ?, ?, ?, ?, ?, 'Menunggu', '')");
    mysqli_stmt_bind_param($stmt, "sissss", $nis, $id_kategori, $lokasi, $isi_laporan, $nama_baru, $tanggal_final);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Aspirasi Berhasil Terkirim!'); document.location.href = 'siswa.php?page=riwayat';</script>";
    }
    mysqli_stmt_close($stmt);
}

// --- LOGIKA HAPUS MASAL ---
if (isset($_POST["hapus_masal"]) && !empty($_POST['id_laporan'])) {
    $ids = $_POST['id_laporan'];
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = mysqli_prepare($conn, "DELETE FROM aspirasi WHERE id_aspirasi IN ($placeholders) AND nis = ?");
    $types = str_repeat('i', count($ids)) . 's';
    $params = array_merge($ids, [$nis]);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    header("Location: siswa.php?page=riwayat");
}

// Statistik
$count_total = query("SELECT COUNT(*) as jml FROM aspirasi WHERE nis = '$nis'")[0]['jml'];
$count_proses = query("SELECT COUNT(*) as jml FROM aspirasi WHERE nis = '$nis' AND status = 'Proses'")[0]['jml'];
$count_selesai = query("SELECT COUNT(*) as jml FROM aspirasi WHERE nis = '$nis' AND status = 'Selesai'")[0]['jml'];

// Riwayat
$filter_tgl = $_GET['tgl'] ?? '';
$query_riwayat = "SELECT aspirasi.*, kategori.nama_kategori FROM aspirasi JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori WHERE nis = '$nis'";
if ($filter_tgl) $query_riwayat .= " AND DATE(aspirasi.tanggal) = '$filter_tgl'";
$query_riwayat .= " ORDER BY tanggal DESC";

$riwayat = query($query_riwayat);
$kategori = query("SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Aspirasi | Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .sidebar-glass { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); }
        .nav-active { background: linear-gradient(to right, #eff6ff, transparent); color: #2563eb; border-left: 4px solid #2563eb; }
        .custom-gradient { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
        
        /* Mobile Sidebar Animation */
        #sidebar { transition: transform 0.3s ease-in-out; }
        @media (max-width: 1023px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
        }
    </style>
</head>
<body class="min-h-screen">

    <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 z-[40] hidden lg:hidden backdrop-blur-sm"></div>

    <aside id="sidebar" class="w-72 h-screen sidebar-glass border-r border-slate-200 fixed flex flex-col z-[50]">
        <div class="p-8 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 custom-gradient rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <span class="font-bold text-lg tracking-tight text-blue-600">E-Aspirasi</span>
            </div>
            <button onclick="toggleSidebar()" class="lg:hidden text-slate-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <nav class="flex-1 px-4 space-y-1">
            <p class="px-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Menu Utama</p>
            <a href="?page=dashboard" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition <?= ($page == 'dashboard') ? 'nav-active' : 'text-slate-500 hover:bg-slate-50'; ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                Dashboard
            </a>
            <a href="?page=kirim" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition <?= ($page == 'kirim') ? 'nav-active' : 'text-slate-500 hover:bg-slate-50'; ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Kirim Laporan
            </a>
            <a href="?page=riwayat" class="flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-semibold transition <?= ($page == 'riwayat') ? 'nav-active' : 'text-slate-500 hover:bg-slate-50'; ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Riwayat Saya
            </a>
        </nav>

        <div class="p-4 m-4 bg-slate-50 border border-slate-100 rounded-2xl">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">
                    <?= substr($nama_user, 0, 1); ?>
                </div>
                <div class="overflow-hidden">
                    <p class="text-xs font-bold text-slate-800 truncate"><?= $nama_user; ?></p>
                    <p class="text-[10px] text-slate-400"><?= $nis; ?></p>
                </div>
            </div>
            <a href="logout.php" class="block w-full text-center py-2 bg-rose-50 rounded-xl text-xs font-bold text-rose-600 border border-rose-100">LOGOUT</a>
        </div>
    </aside>

    <main class="lg:ml-72 p-4 md:p-10">
        <div class="lg:hidden flex justify-between items-center mb-6 bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
            <button onclick="toggleSidebar()" class="p-2 bg-slate-50 rounded-lg text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <span class="font-black text-blue-600 text-sm tracking-tighter">E-ASPIRASI</span>
        </div>

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-10">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">
                    <?php 
                        if($page == 'dashboard') echo "Ringkasan Laporan";
                        elseif($page == 'kirim') echo "Buat Laporan";
                        else echo "Riwayat Aspirasi";
                    ?>
                </h1>
                <p class="text-slate-500 text-sm mt-1">Halo, Selamat Datang di Panel Siswa.</p>
            </div>
            <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100 w-full md:w-auto text-center md:text-right">
                <p class="text-[10px] font-bold text-blue-600 uppercase tracking-widest"><?= date('d M Y'); ?></p>
                <p id="clock-digital" class="text-xl font-black text-slate-700 leading-tight">00:00</p>
            </div>
        </header>

        <?php if($page == 'dashboard') : ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-8 mb-10">
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
                    <p class="text-slate-400 text-[10px] font-bold uppercase mb-2">Total</p>
                    <h3 class="text-3xl font-black text-slate-800"><?= $count_total; ?></h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
                    <p class="text-slate-400 text-[10px] font-bold uppercase mb-2">Proses</p>
                    <h3 class="text-3xl font-black text-amber-500"><?= $count_proses; ?></h3>
                </div>
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden group">
                    <p class="text-slate-400 text-[10px] font-bold uppercase mb-2">Selesai</p>
                    <h3 class="text-3xl font-black text-emerald-500"><?= $count_selesai; ?></h3>
                </div>
            </div>

            <div class="custom-gradient rounded-[2rem] p-8 md:p-12 text-white relative overflow-hidden shadow-2xl shadow-blue-200">
                <div class="relative z-10 max-w-xl">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Sampaikan Aspirasimu!</h2>
                    <p class="text-blue-100 text-base md:text-lg opacity-90 mb-8">Setiap laporan Anda membantu sekolah kami menjadi lebih baik.</p>
                    <a href="?page=kirim" class="inline-block px-8 py-4 bg-white text-blue-600 rounded-2xl font-bold text-sm shadow-xl transition-transform hover:-translate-y-1">Buat Laporan Sekarang</a>
                </div>
            </div>

        <?php elseif($page == 'kirim') : ?>
            <div class="max-w-4xl bg-white rounded-[2rem] border border-slate-100 shadow-xl overflow-hidden">
                <form action="" method="post" enctype="multipart/form-data" class="p-6 md:p-10 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-3">Waktu Laporan</label>
                            <div class="flex flex-wrap gap-2 p-1.5 bg-slate-100 rounded-2xl w-fit">
                                <label class="cursor-pointer">
                                    <input type="radio" name="mode_waktu" value="otomatis" class="hidden peer" checked onchange="toggleWaktu(this.value)">
                                    <div class="px-6 py-2.5 rounded-xl text-xs font-bold peer-checked:bg-white peer-checked:text-blue-600 shadow-sm text-slate-500 transition">Otomatis</div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="mode_waktu" value="manual" class="hidden peer" onchange="toggleWaktu(this.value)">
                                    <div class="px-6 py-2.5 rounded-xl text-xs font-bold peer-checked:bg-white peer-checked:text-blue-600 text-slate-500 transition">Manual</div>
                                </label>
                            </div>
                        </div>

                        <div id="manualInput" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 animate-fadeIn">
                            <input type="date" name="tanggal_kejadian" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                            <input type="time" name="jam_kejadian" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Kategori</label>
                            <select name="id_kategori" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                                <?php foreach($kategori as $k) : ?>
                                    <option value="<?= $k['id_kategori']; ?>"><?= $k['nama_kategori']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Lokasi</label>
                            <input type="text" name="lokasi" required placeholder="Contoh: Kelas 5B" class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Detail Aduan</label>
                        <textarea name="isi_laporan" rows="4" required class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none"></textarea>
                    </div>

                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 text-center bg-slate-50">
                        <input type="file" name="foto" id="foto-input" class="hidden" accept="image/*" onchange="previewImage(this)">
                        <label for="foto-input" class="cursor-pointer block">
                            <div class="mb-2 mx-auto w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <span class="text-xs font-bold text-blue-600">Klik untuk Upload Foto (Opsional)</span>
                        </label>
                        <img id="img-preview" class="hidden mt-4 mx-auto max-h-40 rounded-lg shadow-md">
                    </div>

                    <button type="submit" name="kirim" class="w-full py-4 custom-gradient text-white font-bold rounded-xl shadow-lg shadow-blue-100 transition-transform active:scale-95">Kirim Aspirasi</button>
                </form>
            </div>

        <?php elseif($page == 'riwayat') : ?>
            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row gap-4 justify-between items-center">
                    <form action="" method="get" class="flex gap-2 w-full md:w-auto">
                        <input type="hidden" name="page" value="riwayat">
                        <input type="date" name="tgl" value="<?= $filter_tgl ?>" class="px-4 py-2 bg-slate-50 border rounded-xl text-xs font-semibold outline-none">
                        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold">Filter</button>
                    </form>
                    <button type="submit" form="formHapus" name="hapus_masal" onclick="return confirm('Hapus terpilih?')" class="w-full md:w-auto px-4 py-2 bg-rose-50 text-rose-600 rounded-xl text-xs font-bold">Hapus Terpilih</button>
                </div>

                <form action="" method="post" id="formHapus" class="overflow-x-auto">
                    <table class="w-full text-left min-w-[600px]">
                        <thead>
                            <tr class="text-[10px] uppercase font-black text-slate-400 border-b border-slate-50 bg-slate-50/50">
                                <th class="p-6 text-center w-12">#</th>
                                <th class="p-6">Informasi</th>
                                <th class="p-6">Status</th>
                                <th class="p-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach($riwayat as $row) : ?>
                            <tr class="group hover:bg-slate-50 transition-colors">
                                <td class="p-6 text-center">
                                    <input type="checkbox" name="id_laporan[]" value="<?= $row['id_aspirasi'] ?>" class="rounded text-blue-600">
                                </td>
                                <td class="p-6">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-[9px] font-black px-2 py-0.5 bg-blue-100 text-blue-600 rounded uppercase tracking-tighter"><?= $row['nama_kategori'] ?></span>
                                            <span class="text-[10px] text-slate-400"><?= date('d/m/y H:i', strtotime($row['tanggal'])) ?></span>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700 leading-relaxed">"<?= $row['keterangan'] ?>"</p>
                                        <p class="text-[11px] text-slate-400 italic flex items-center gap-1 mt-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                            <?= $row['lokasi'] ?>
                                        </p>
                                        <?php if($row['feedback']) : ?>
                                            <div class="mt-3 p-3 bg-blue-600 text-white rounded-xl text-[11px] shadow-lg shadow-blue-100">
                                                <span class="font-black block mb-1">RESON ADMIN:</span>
                                                <?= $row['feedback'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span class="text-[9px] font-black px-3 py-1 rounded-full border 
                                        <?= $row['status'] == 'Menunggu' ? 'bg-slate-100 text-slate-500' : ($row['status'] == 'Proses' ? 'bg-amber-100 text-amber-600 border-amber-200' : 'bg-emerald-100 text-emerald-600 border-emerald-200') ?>">
                                        <?= strtoupper($row['status']) ?>
                                    </span>
                                </td>
                                <td class="p-6 text-right">
                                    <div class="flex justify-end gap-2 opacity-100 md:opacity-0 group-hover:opacity-100 transition-opacity">
                                        <?php if($row['status'] == 'Menunggu') : ?>
                                            <a href="edit_aspirasi.php?id=<?= $row['id_aspirasi'] ?>" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg></a>
                                        <?php endif; ?>
                                        <a href="hapus_aspirasi.php?id=<?= $row['id_aspirasi'] ?>" onclick="return confirm('Hapus?')" class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </form>
            </div>
        <?php endif; ?>
    </main>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('overlay').classList.toggle('hidden');
        }

        function toggleWaktu(val) {
            const area = document.getElementById('manualInput');
            if(val === 'manual') {
                area.classList.remove('hidden');
                area.classList.add('grid');
            } else {
                area.classList.add('hidden');
                area.classList.remove('grid');
            }
        }

        function previewImage(input) {
            const preview = document.getElementById('img-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function runClock() {
            const now = new Date();
            document.getElementById('clock-digital').innerText = now.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'}).replace(/\./g, ':');
        }
        setInterval(runClock, 1000); runClock();
    </script>
</body>
</html>