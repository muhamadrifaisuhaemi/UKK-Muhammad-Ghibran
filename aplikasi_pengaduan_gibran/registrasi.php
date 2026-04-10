<?php
session_start();
require 'functions.php';

if (isset($_POST['register'])) {
    $res = registrasi($_POST);
    if ($res > 0) {
        echo "<script>
                alert('Pendaftaran Berhasil! Silahkan Login.');
                window.location='login_siswa.php';
              </script>";
    } else {
        echo "<script>alert('Gagal! NIS mungkin sudah terdaftar atau data tidak valid.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa | E-Aspirasi Emerald</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f4fff9;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* Animated Background Shapes (Green Theme) */
        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.2) 100%);
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
            box-shadow: 0 40px 100px -20px rgba(16, 100, 70, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        @media (min-width: 768px) {
            .login-container { grid-template-columns: 1fr 1.1fr; }
        }

        .side-info {
            background: linear-gradient(160deg, #059669 0%, #10b981 100%);
            padding: 60px;
            display: none;
            flex-direction: column;
            justify-content: center;
            color: white;
            position: relative;
        }

        @media (min-width: 768px) { .side-info { display: flex; } }

        .form-section { padding: 50px 50px; background: white; }

        .input-wrapper {
            position: relative;
            border: 2px solid #f1f9f5;
            border-radius: 18px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f8fafc;
        }

        .input-wrapper:focus-within {
            border-color: #10b981;
            background: white;
            box-shadow: 0 12px 24px -8px rgba(16, 185, 129, 0.15);
        }

        .btn-emerald {
            background: #10b981;
            color: white;
            font-weight: 700;
            transition: all 0.4s;
            box-shadow: 0 15px 30px -10px rgba(16, 185, 129, 0.3);
        }

        .btn-emerald:hover {
            background: #059669;
            transform: translateY(-3px);
            box-shadow: 0 20px 40px -10px rgba(16, 185, 129, 0.4);
        }

        .btn-emerald:active { transform: translateY(0); }

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
                    <i class="fas fa-user-plus text-xl"></i>
                </div>
                <h1 class="text-4xl font-extrabold mb-6">Mulai <br>Langkahmu.</h1>
                <p class="text-emerald-50/80 text-lg leading-relaxed max-w-xs">
                    Buat akun sekarang dan berkontribusi langsung untuk kemajuan sarana sekolah kita.
                </p>
            </div>
            
            <div class="mt-auto pt-10">
                <div class="p-5 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/10">
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-200 mb-2">Pemberitahuan</p>
                    <p class="text-sm">Gunakan NIS resmi yang terdaftar di sekolah untuk validasi data otomatis.</p>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="mb-10">
                <h2 class="text-3xl font-black text-slate-900 mb-2">Registrasi</h2>
                <div class="h-1.5 w-12 bg-emerald-500 rounded-full mb-4"></div>
                <p class="text-slate-400 font-medium">Lengkapi formulir pendaftaran di bawah ini</p>
            </div>

            <form action="" method="post" id="regForm" class="space-y-4">
                <input type="hidden" name="role" value="siswa">
                
                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Nomor Induk Siswa</label>
                    <div class="input-wrapper flex items-center px-5">
                        <i class="fas fa-id-card text-slate-300 mr-4"></i>
                        <input type="text" name="username" placeholder="Contoh: 12345" required 
                        class="w-full py-3.5 bg-transparent outline-none text-slate-700 font-bold placeholder:text-slate-300 placeholder:font-normal">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Nama Lengkap</label>
                    <div class="input-wrapper flex items-center px-5">
                        <i class="fas fa-user text-slate-300 mr-4"></i>
                        <input type="text" name="nama" placeholder="Masukkan nama lengkap" required 
                        class="w-full py-3.5 bg-transparent outline-none text-slate-700 font-bold placeholder:text-slate-300 placeholder:font-normal">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Kelas</label>
                    <div class="input-wrapper flex items-center px-5">
                        <i class="fas fa-graduation-cap text-slate-300 mr-4"></i>
                        <input type="text" name="kelas" placeholder="Contoh: 5A" required 
                        class="w-full py-3.5 bg-transparent outline-none text-slate-700 font-bold placeholder:text-slate-300 placeholder:font-normal">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-extrabold text-slate-400 uppercase tracking-[0.2em] mb-2 ml-1">Buat Password</label>
                    <div class="input-wrapper flex items-center px-5">
                        <i class="fas fa-lock text-slate-300 mr-4"></i>
                        <input type="password" id="passInput" name="password" placeholder="••••••••" required 
                        class="w-full py-3.5 bg-transparent outline-none text-slate-700 font-bold placeholder:text-slate-300 placeholder:font-normal">
                        <button type="button" onclick="togglePass()" class="text-slate-300 hover:text-emerald-500 transition">
                            <i id="eyeIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" name="register" class="btn-emerald w-full py-4 rounded-2xl flex items-center justify-center gap-3 mt-4">
                    <span id="btnText">DAFTAR SEKARANG</span>
                    <div id="btnLoader" class="loader"></div>
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-slate-400 text-sm mb-4">
                    Sudah punya akun? 
                    <a href="login_siswa.php" class="text-emerald-600 font-bold hover:text-emerald-800 underline decoration-2 underline-offset-4">Masuk di sini</a>
                </p>
                <a href="index.php" class="text-slate-300 hover:text-emerald-500 text-[10px] font-black uppercase tracking-[0.3em] transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-chevron-left text-[8px]"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>

    <script>
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

        document.getElementById('regForm').onsubmit = function() {
            document.getElementById('btnText').style.display = 'none';
            document.getElementById('btnLoader').style.display = 'block';
        };
    </script>
</body>
</html>