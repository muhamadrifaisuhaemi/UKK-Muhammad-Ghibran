<?php
session_start();
require 'functions.php';

if (isset($_SESSION['login']) && $_SESSION['role'] === 'admin') {
    header("Location: admin.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM admin WHERE username = '$username'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['id'] = $row['id_admin'];
            $_SESSION['nama'] = $row['nama_petugas'];
            $_SESSION['role'] = 'admin';

            header("Location: admin.php");
            exit;
        }
    }
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | E-ASPIRASI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #eef2f7;
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
            min-height: 600px;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        }

        .info-side {
            flex: 1;
            background: linear-gradient(135deg, #0061ff 0%, #00c6ff 100%);
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
        }

        .login-side {
            flex: 1.2;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: white;
        }

        .input-group {
            background: #f0f5ff;
            border-radius: 12px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .input-group:focus-within {
            border-color: #0061ff;
            background: white;
            box-shadow: 0 10px 15px -3px rgba(0, 97, 255, 0.1);
        }

        .btn-login {
            background: #0056ff;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #0041c2;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 86, 255, 0.3);
        }

        .btn-back {
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .tips-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 768px) {
            .main-container { flex-direction: column; }
            .info-side { display: none; }
            .login-side { padding: 40px 20px; }
        }
    </style>
</head>
<body>

    <div class="main-container" data-aos="zoom-in" data-aos-duration="1000">
        <div class="info-side">
            <div data-aos="fade-right" data-aos-delay="400">
                <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mb-10">
                    <i class="fas fa-shield-halved text-white text-xl"></i>
                </div>
                <h1 class="text-4xl font-extrabold leading-tight mb-4">Portal<br>Petugas Admin</h1>
                <p class="text-white/80 leading-relaxed text-lg">
                    Akses dashboard khusus petugas untuk meninjau laporan dan mengelola infrastruktur sekolah.
                </p>
            </div>

            <div class="tips-card" data-aos="fade-up" data-aos-delay="600">
                <span class="text-[10px] uppercase tracking-widest font-bold opacity-60">Tips Keamanan</span>
                <p class="text-sm mt-2 font-medium">
                    Jangan pernah membagikan akses login admin Anda kepada siapapun untuk menjaga integritas data.
                </p>
            </div>
        </div>

        <div class="login-side" data-aos="fade-left" data-aos-delay="400">
            <div class="mb-10">
                <h2 class="text-3xl font-bold text-slate-800">Login Admin</h2>
                <div class="w-12 h-1 bg-blue-600 mt-2 mb-4"></div>
                <p class="text-slate-500 font-medium">Masuk untuk mengelola dashboard admin Anda</p>
            </div>

            <?php if (isset($error)) : ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-600 p-4 mb-6 text-sm flex items-center rounded-r-lg animate-bounce">
                    <i class="fas fa-exclamation-circle mr-3"></i>
                    Kredensial admin tidak valid!
                </div>
            <?php endif; ?>

            <form action="" method="post" class="space-y-5">
                <div>
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Username</label>
                    <div class="input-group flex items-center px-4 py-2 mt-2">
                        <i class="fas fa-user-tie text-slate-400 mr-3"></i>
                        <input type="text" name="username" placeholder="Masukkan username admin" required
                            class="bg-transparent border-none outline-none w-full py-2 text-slate-700 font-medium">
                    </div>
                </div>

                <div>
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Password</label>
                    <div class="input-group flex items-center px-4 py-2 mt-2">
                        <i class="fas fa-lock text-slate-400 mr-3"></i>
                        <input type="password" name="password" placeholder="••••••" required
                            class="bg-transparent border-none outline-none w-full py-2 text-slate-700 font-medium">
                    </div>
                </div>

                <div class="flex flex-col gap-3 pt-2">
                    <button type="submit" name="login" class="btn-login w-full text-white font-bold py-4 rounded-xl shadow-lg uppercase tracking-widest text-xs">
                        Masuk Portal Admin
                    </button>
                    
                    <a href="admin.php" class="btn-back w-full text-slate-600 font-bold py-4 rounded-xl text-center uppercase tracking-widest text-xs">
                        Ke Dashboard Awal
                    </a>
                </div>
            </form>

            <div class="mt-8 text-center">
                <p class="text-slate-500 text-sm">Belum punya akun? 
                    <a href="registrasi_admin.php" class="text-blue-600 font-bold hover:underline">Daftar Akun</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
      AOS.init({
        once: true // Animasi hanya berjalan sekali saat di-load
      });
    </script>
</body>
</html>