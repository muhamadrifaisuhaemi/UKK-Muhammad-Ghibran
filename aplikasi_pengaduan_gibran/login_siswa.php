<?php
session_start();
require 'functions.php';

// Jika sudah login, langsung lempar ke dashboard
if (isset($_SESSION['login'])) {
    if ($_SESSION['role'] === 'siswa') {
        header("Location: siswa.php");
        exit;
    } elseif ($_SESSION['role'] === 'admin') {
        header("Location: admin.php");
        exit;
    }
}

if (isset($_POST['login'])) {
    $nis = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['login'] = true;
            $_SESSION['id'] = $row['nis'];      
            $_SESSION['nama'] = $row['nama']; 
            $_SESSION['role'] = 'siswa';
            $_SESSION['kelas'] = $row['kelas'];

            header("Location: siswa.php");
            exit;
        } else {
            echo "<script>alert('Login Gagal! Password salah.');</script>";
        }
    } else {
        echo "<script>alert('Login Gagal! NIS tidak terdaftar.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa Login | Azure Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4f9ff;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Animated Background Shapes */
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, rgba(0, 98, 255, 0.2) 0%, rgba(0, 212, 255, 0.2) 100%);
            filter: blur(80px);
            border-radius: 50%;
            z-index: 1;
            animation: move 20s infinite alternate;
        }

        @keyframes move {
            from { transform: translate(-10%, -10%) rotate(0deg); }
            to { transform: translate(20%, 20%) rotate(360deg); }
        }

        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 950px;
            display: grid;
            grid-template-columns: 1fr;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            margin: 20px;
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 40px 100px -20px rgba(0, 40, 100, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        @media (min-width: 768px) {
            .login-container { grid-template-columns: 1fr 1.1fr; }
        }

        .side-info {
            background: linear-gradient(160deg, #0052d9 0%, #00d4ff 100%);
            padding: 60px;
            display: none;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
        }

        @media (min-width: 768px) { .side-info { display: flex; } }

        .form-section { padding: 60px 50px; background: white; }

        .input-wrapper {
            position: relative;
            border: 2px solid #f1f5f9;
            border-radius: 18px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f8fafc;
        }

        .input-wrapper:focus-within {
            border-color: #0062ff;
            background: white;
            box-shadow: 0 12px 24px -8px rgba(0, 98, 255, 0.15);
        }

        .btn-azure {
            background: #0062ff;
            color: white;
            font-weight: 700;
            transition: all 0.4s;
            box-shadow: 0 15px 30px -10px rgba(0, 98, 255, 0.3);
        }

        .btn-azure:hover {
            background: #004ecc;
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -10px rgba(0, 98, 255, 0.4);
        }

        .btn-azure:active { transform: translateY(0); }

        .loader {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid #ffffff;
            border-bottom-color: transparent;
            border-radius: 50%;
            animation: rotation 1s linear infinite;
        }

        @keyframes rotation {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <div class="blob" style="top:-10%; left:-10%;"></div>
    <div class="blob" style="bottom:-10%; right:-10%; animation-delay: -5s;"></div>

    <div class="login-container">
        <div class="side-info">
            <div class="mb-auto">
                <div class="bg-white/20 w-12 h-12 rounded-full flex items-center justify-center backdrop-blur-md mb-8">
                    <i class="fas fa-shield-alt text-xl"></i>
                </div>
                <h1 class="text-4xl font-extrabold mb-6">Portal <br>Aspirasi Siswa</h1>
                <p class="text-blue-50/80 text-lg leading-relaxed max-w-xs">
                    Suarakan ide dan saranmu dengan aman untuk fasilitas sekolah yang lebih baik.
                </p>
            </div>
            
            <div class="mt-auto pt-10">
                <div class="p-5 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/10">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-200 mb-2">Tips Keamanan</p>
                    <p class="text-sm">Jangan bagikan password NIS Anda kepada siapapun untuk menjaga privasi data.</p>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="mb-12">
                <h2 class="text-3xl font-black text-slate-900 mb-2">Login Akun</h2>
                <div class="h-1.5 w-12 bg-blue-600 rounded-full mb-4"></div>
                <p class="text-slate-400 font-medium">Masuk menggunakan kredensial siswa Anda</p>
            </div>

            <form action="" method="post" id="loginForm" class="space-y-6">
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Nomor Induk Siswa</label>
                    <div class="input-wrapper flex items-center px-5">
                        <i class="fas fa-id-card text-slate-300 mr-4"></i>
                        <input type="text" name="username" placeholder="Masukkan NIS Anda" required 
                        class="w-full py-4 bg-transparent outline-none text-slate-700 font-bold placeholder:text-slate-300 placeholder:font-normal">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Kata Sandi</label>
                    <div class="input-wrapper flex items-center px-5">
                        <i class="fas fa-lock text-slate-300 mr-4"></i>
                        <input type="password" id="passInput" name="password" placeholder="••••••••" required 
                        class="w-full py-4 bg-transparent outline-none text-slate-700 font-bold placeholder:text-slate-300 placeholder:font-normal">
                        <button type="button" onclick="togglePass()" class="text-slate-300 hover:text-blue-500 transition">
                            <i id="eyeIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" name="login" class="btn-azure w-full py-4 rounded-2xl flex items-center justify-center gap-3">
                    <span id="btnText">MASUK PORTAL</span>
                    <div id="btnLoader" class="loader"></div>
                </button>
            </form>

            <div class="mt-16 text-center">
                <p class="text-slate-400 text-sm mb-6">
                    Belum punya akses? 
                    <a href="registrasi.php" class="text-blue-600 font-bold hover:text-blue-800 underline decoration-2 underline-offset-4">Daftar Akun</a>
                </p>
                <a href="index.php" class="text-slate-300 hover:text-slate-500 text-[10px] font-black uppercase tracking-[0.3em] transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-chevron-left text-[8px]"></i> Beranda Utama
                </a>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePass() {
            const input = document.getElementById('passInput');
            const icon = document.getElementById('eyeIcon');
            if (input.type === "password") {
                input.type = "text";
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Loading Animation on Submit
        document.getElementById('loginForm').onsubmit = function() {
            document.getElementById('btnText').style.display = 'none';
            document.getElementById('btnLoader').style.display = 'block';
        };
    </script>
</body>
</html>