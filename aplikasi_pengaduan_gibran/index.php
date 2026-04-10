<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-ASPIRASI | Digital Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap');
        
        :root {
            --primary-green: #10b981;
            --dark-base: #0f172a;
            --soft-bg: #f1f5f9;
        }

        body { 
            font-family: 'Space Grotesk', sans-serif; 
            background-color: var(--soft-bg);
            color: var(--dark-base);
        }

        /* Layout Khusus: Sidebar Navigation (Desktop) */
        .side-nav {
            background: var(--dark-base);
            height: calc(100vh - 40px);
            width: 280px;
            position: fixed;
            left: 20px;
            top: 20px;
            border-radius: 24px;
            display: none;
            flex-direction: column;
            padding: 40px 20px;
            z-index: 100;
        }

        @media (min-width: 1024px) {
            .side-nav { display: flex; }
            .main-content { margin-left: 320px; }
        }

        /* Card Effects */
        .neo-card {
            background: white;
            border: 2px solid #e2e8f0;
            border-bottom: 6px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .neo-card:hover {
            transform: translateY(-4px);
            border-color: var(--primary-green);
            border-bottom-width: 6px;
        }

        .action-btn {
            background: var(--primary-green);
            color: white;
            border-bottom: 4px solid #065f46;
            transition: all 0.1s;
        }

        .action-btn:active {
            transform: translateY(2px);
            border-bottom-width: 0;
        }

        /* Mobile Menu */
        #mobile-box {
            display: none;
            position: fixed;
            inset: 0;
            background: var(--dark-base);
            z-index: 200;
            padding: 40px;
        }
    </style>
</head>
<body class="p-5 lg:p-0">

    <div class="lg:hidden flex justify-between items-center mb-8 bg-white p-4 rounded-2xl border-2 border-slate-200">
        <div class="flex items-center gap-3">
            <div class="bg-emerald-500 w-10 h-10 rounded-lg flex items-center justify-center text-white">
                <i class="fas fa-bolt"></i>
            </div>
            <span class="font-bold text-xl">E-ASPIRASI</span>
        </div>
        <button id="open-mobile" class="text-2xl"><i class="fas fa-align-right"></i></button>
    </div>

    <nav class="side-nav shadow-2xl">
        <div class="flex items-center gap-3 mb-16 px-4">
            <div class="bg-emerald-500 w-12 h-12 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-900/50">
                <i class="fas fa-bolt text-xl"></i>
            </div>
            <div class="text-white">
                <h1 class="font-bold text-xl leading-none">ASPIRASI</h1>
                <p class="text-[10px] text-emerald-400 font-bold tracking-widest uppercase">Digital Hub v2</p>
            </div>
        </div>

        <div class="flex flex-col gap-4">
            <a href="index.php" class="bg-emerald-500/10 text-emerald-400 p-4 rounded-2xl flex items-center gap-4 border border-emerald-500/20">
                <i class="fas fa-home"></i> <span class="font-semibold">Beranda</span>
            </a>
            <a href="login_siswa.php" class="text-slate-400 p-4 rounded-2xl flex items-center gap-4 hover:bg-slate-800 transition">
                <i class="fas fa-users"></i> <span class="font-semibold">Portal Siswa</span>
            </a>
            <hr class="border-slate-800 my-2">
            <a href="login.php" class="text-red-400 p-4 rounded-2xl flex items-center gap-4 hover:bg-red-500/10 transition">
                <i class="fas fa-lock"></i> <span class="font-semibold">Admin Area</span>
            </a>
        </div>

        <div class="mt-auto">
            <div class="bg-slate-800 p-5 rounded-3xl">
                <p class="text-slate-400 text-xs mb-3 font-medium">Butuh Bantuan?</p>
                <a href="https://wa.me/xxx" class="text-white text-sm font-bold flex items-center gap-2">
                    <i class="fab fa-whatsapp text-emerald-500"></i> Hubungi Kami
                </a>
            </div>
        </div>
    </nav>

    <main class="main-content min-h-screen lg:py-10 lg:pr-10">
        
        <div class="grid lg:grid-cols-12 gap-8 items-stretch mb-12">
            <div class="lg:col-span-8 bg-white border-2 border-slate-200 rounded-[40px] p-8 md:p-16 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-50 rounded-full -mr-20 -mt-20 z-0"></div>
                
                <div class="relative z-10">
                    <span class="bg-slate-900 text-white px-4 py-2 rounded-full text-xs font-bold uppercase tracking-tighter mb-6 inline-block">
                        Sekolah Digital Platform
                    </span>
                    <h2 class="text-5xl md:text-7xl font-bold text-slate-900 mb-8 leading-tight">
                        Ubah Keluhan <br> Menjadi <span class="text-emerald-500 underline decoration-8 decoration-emerald-100">Solusi.</span>
                    </h2>
                    <p class="text-slate-500 text-lg md:text-xl max-w-xl mb-10 leading-relaxed">
                        Sampaikan aspirasi Anda secara digital. Kami mendengar, memproses, dan mewujudkan perubahan untuk sekolah.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="registrasi.php" class="action-btn px-10 py-5 rounded-2xl font-bold text-lg inline-flex items-center gap-3">
                            Mulai Lapor <i class="fas fa-plus-circle"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-4 flex flex-col gap-6">
                <div class="bg-emerald-500 rounded-[40px] p-8 text-white flex-1 flex flex-col justify-between">
                    <i class="fas fa-rocket text-4xl opacity-50"></i>
                    <div>
                        <h3 class="text-2xl font-bold mb-2">Pantau Status</h3>
                        <p class="text-emerald-100 mb-6 text-sm">Sudah kirim laporan? Cek perkembangannya di sini.</p>
                        <a href="login_siswa.php" class="bg-white text-emerald-600 w-full py-4 rounded-xl font-bold block text-center">Lihat Dashboard</a>
                    </div>
                </div>
                <div class="bg-white border-2 border-slate-200 rounded-[40px] p-8 flex-1 flex flex-col justify-between">
                    <i class="fas fa-shield-alt text-4xl text-slate-200"></i>
                    <div>
                        <h3 class="text-2xl font-bold mb-2 text-slate-900">Keamanan</h3>
                        <p class="text-slate-400 text-sm mb-6">Data Anda terlindungi dan dienkripsi oleh sistem.</p>
                        <div class="flex -space-x-3">
                            <div class="w-10 h-10 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center"><i class="fas fa-lock text-xs"></i></div>
                            <div class="w-10 h-10 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center"><i class="fas fa-user-secret text-xs"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <a href="login_siswa.php" class="neo-card p-10 rounded-[40px] group">
                <div class="flex justify-between items-start mb-12">
                    <div class="bg-slate-100 w-16 h-16 rounded-2xl flex items-center justify-center text-2xl group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <i class="fas fa-arrow-right text-slate-300 group-hover:text-emerald-500 transition-colors"></i>
                </div>
                <h4 class="text-3xl font-bold mb-2">Portal Siswa</h4>
                <p class="text-slate-400 font-medium leading-relaxed">Login untuk mengirim aspirasi baru dan melihat respon dari Admin sekolah.</p>
            </a>

            <a href="login.php" class="neo-card p-10 rounded-[40px] group border-slate-900">
                <div class="flex justify-between items-start mb-12">
                    <div class="bg-slate-900 w-16 h-16 rounded-2xl flex items-center justify-center text-2xl text-white">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <i class="fas fa-arrow-right text-slate-300 group-hover:text-slate-900 transition-colors"></i>
                </div>
                <h4 class="text-3xl font-bold mb-2 text-slate-900">Portal Admin</h4>
                <p class="text-slate-400 font-medium leading-relaxed">Khusus staf dan guru untuk mengelola laporan yang masuk dari seluruh siswa.</p>
            </a>
        </div>

        <footer class="mt-20 border-t-2 border-slate-200 pt-12 flex flex-col md:flex-row justify-between items-center gap-8">
            <p class="font-bold text-slate-400 text-sm tracking-widest uppercase">
                © 2026 E-ASPIRASI • Made with Heart
            </p>
            <div class="flex gap-10">
                <a href="#" class="text-slate-400 hover:text-emerald-500 font-bold">Instagram</a>
                <a href="#" class="text-slate-400 hover:text-emerald-500 font-bold">Twitter</a>
                <a href="#" class="text-slate-400 hover:text-emerald-500 font-bold">Privacy</a>
            </div>
        </footer>
    </main>

    <div id="mobile-box">
        <div class="flex justify-between items-center mb-16">
            <span class="text-emerald-500 font-black text-2xl">MENU</span>
            <button id="close-mobile" class="text-white text-3xl"><i class="fas fa-times"></i></button>
        </div>
        <div class="flex flex-col gap-8 text-white">
            <a href="index.php" class="text-4xl font-bold">Beranda</a>
            <a href="login_siswa.php" class="text-4xl font-bold">Portal Siswa</a>
            <a href="login.php" class="text-4xl font-bold text-red-500">Admin Area</a>
            <hr class="border-slate-800">
            <a href="registrasi.php" class="text-2xl text-emerald-400 font-bold italic underline">Kirim Aspirasi Sekarang →</a>
        </div>
    </div>

    <script>
        // Logic Mobile Menu
        const openBtn = document.getElementById('open-mobile');
        const closeBtn = document.getElementById('close-mobile');
        const mobileBox = document.getElementById('mobile-box');

        openBtn.addEventListener('click', () => {
            mobileBox.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });

        closeBtn.addEventListener('click', () => {
            mobileBox.style.display = 'none';
            document.body.style.overflow = 'auto';
        });

        // Close menu on link click
        const mobileLinks = mobileBox.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileBox.style.display = 'none';
                document.body.style.overflow = 'auto';
            });
        });
    </script>
</body>
</html>