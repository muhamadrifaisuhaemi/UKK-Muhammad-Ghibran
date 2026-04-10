<?php
require 'functions.php'; // Pastikan file ini memiliki koneksi ke database $conn

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_petugas']);
    $username = strtolower(stripslashes(mysqli_real_escape_string($conn, $_POST['username'])));
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // 1. Cek apakah username sudah ada
    $check_user = mysqli_query($conn, "SELECT username FROM admin WHERE username = '$username'");
    if (mysqli_fetch_assoc($check_user)) {
        $error_msg = "Username sudah terdaftar!";
    } 
    // 2. Cek konfirmasi password
    else if ($password !== $confirm) {
        $error_msg = "Konfirmasi password tidak sesuai!";
    } 
    else {
        // 3. Enkripsi Password (Hashing)
        $password_fix = password_hash($password, PASSWORD_DEFAULT);

        // 4. Insert ke Database
        $query = "INSERT INTO admin (username, password, nama_petugas) VALUES ('$username', '$password_fix', '$nama')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>
                    alert('Registrasi Admin Berhasil!');
                    document.location.href = 'login.php';
                  </script>";
        } else {
            $error_msg = "Gagal mendaftarkan akun!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Admin | E-ASPIRASI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0fdf4; /* Light Green Background */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .main-container {
            display: flex;
            background: white;
            width: 100%;
            max-width: 1000px;
            min-height: 650px;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(22, 101, 52, 0.1);
        }

        .info-side {
            flex: 1;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%); /* Green Gradient */
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
        }

        .login-side {
            flex: 1.2;
            padding: 40px 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .input-group {
            background: #f0fdf4;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .input-group:focus-within {
            border-color: #10b981;
            background: white;
            box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.1);
        }

        .btn-register {
            background: #059669;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background: #047857;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(5, 150, 105, 0.3);
        }

        .tips-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 768px) {
            .info-side { display: none; }
            .login-side { padding: 40px 20px; }
        }
    </style>
</head>
<body>

    <div class="main-container" data-aos="zoom-in" data-aos-duration="800">
        <div class="info-side">
            <div data-aos="fade-right" data-aos-delay="300">
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-8">
                    <i class="fas fa-user-plus text-white text-xl"></i>
                </div>
                <h1 class="text-4xl font-extrabold leading-tight mb-4">Gabung Sebagai<br>Petugas Admin</h1>
                <p class="text-white/80 leading-relaxed text-lg">
                    Daftarkan akun petugas baru untuk mulai mengelola sistem E-Aspirasi sekolah.
                </p>
            </div>

            <div class="tips-card" data-aos="fade-up" data-aos-delay="500">
                <span class="text-[10px] uppercase tracking-widest font-bold opacity-70">Verifikasi Data</span>
                <p class="text-sm mt-2 font-medium">
                    Pastikan nama yang didaftarkan sesuai dengan data kepegawaian untuk proses validasi.
                </p>
            </div>
        </div>

        <div class="login-side" data-aos="fade-left" data-aos-delay="300">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-slate-800">Daftar Akun</h2>
                <div class="w-12 h-1 bg-emerald-600 mt-2 mb-4"></div>
                <p class="text-slate-500 font-medium">Lengkapi formulir untuk registrasi petugas</p>
            </div>

            <?php if (isset($error_msg)) : ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-600 p-3 mb-6 text-sm flex items-center rounded-r-lg">
                    <i class="fas fa-circle-exclamation mr-2"></i>
                    <?= $error_msg; ?>
                </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-4">
                <div>
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap Petugas</label>
                    <div class="input-group flex items-center px-4 py-1 mt-1">
                        <i class="fas fa-id-card text-emerald-400 mr-3"></i>
                        <input type="text" name="nama_petugas" placeholder="Contoh: Budi Santoso" required
                            class="bg-transparent border-none outline-none w-full py-2 text-slate-700 font-medium">
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Username</label>
                    <div class="input-group flex items-center px-4 py-1 mt-1">
                        <i class="fas fa-at text-emerald-400 mr-3"></i>
                        <input type="text" name="username" placeholder="Buat username unik" required
                            class="bg-transparent border-none outline-none w-full py-2 text-slate-700 font-medium">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                        <div class="input-group flex items-center px-4 py-1 mt-1">
                            <input type="password" name="password" placeholder="••••••" required
                                class="bg-transparent border-none outline-none w-full py-2 text-slate-700 font-medium text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Konfirmasi</label>
                        <div class="input-group flex items-center px-4 py-1 mt-1">
                            <input type="password" name="confirm_password" placeholder="••••••" required
                                class="bg-transparent border-none outline-none w-full py-2 text-slate-700 font-medium text-sm">
                        </div>
                    </div>
                </div>

                <button type="submit" name="register" class="btn-register w-full text-white font-bold py-4 rounded-xl shadow-lg uppercase tracking-widest text-xs mt-4">
                    Daftar Sekarang
                </button>
            </form>
             <a href="admin.php" class="btn-back w-full text-slate-600 font-bold py-4 rounded-xl text-center uppercase tracking-widest text-xs">
                        Ke Dashboard Awal
                    </a>

            <div class="mt-8 text-center">
                <p class="text-slate-500 text-sm">Sudah memiliki akun? 
                    <a href="login.php" class="text-emerald-600 font-bold hover:underline">Login Admin</a>
                </p>
                <div class="mt-6">
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script> AOS.init({ once: true }); </script>
</body>
</html>