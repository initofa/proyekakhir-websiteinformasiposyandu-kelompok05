<?php
if (!isset($title)) {
    $title = 'SIPANDA';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIPANDA - Sistem Informasi Posyandu Anak dan Bunda. Pantau tumbuh kembang anak, imunisasi, dan kesehatan ibu hamil.">
    <meta name="keywords" content="posyandu, imunisasi, kehamilan, anak, kesehatan">
    <meta name="author" content="Posyandu Ceria">
    <title><?php echo $title; ?></title>
    <link rel="icon" type="image/png" href="img/sipanda.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap');
      * {
            font-family: 'Inter', sans-serif;
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hover-scale {
            transition: all 0.3s ease;
        }

        .hover-scale:hover {
            transform: translateY(-5px);
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-clamp: 2; 
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-clamp: 3; 
        }

        .line-clamp-4 {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-clamp: 4; 
        }

        html {
            scroll-behavior: smooth;
        }

        .bg-grid {
            background-image: radial-gradient(circle at 1px 1px, rgba(34, 197, 94, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .shadow-glow {
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.1);
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
        }
        
        .glass-navbar {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(50px);
            -webkit-backdrop-filter: blur(50px);
            border-bottom: 1px solid rgba(34, 197, 94, 0.15);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }
        
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, #16a34a, #22c55e);
            transition: all 0.3s ease;
            transform: translateX(-50%);
            border-radius: 2px;
        }
        
        .nav-link:hover::after {
            width: 70%;
        }
        
        .nav-link.active {
            color: #16a34a;
            font-weight: 600;
        }
        
        .nav-link.active::after {
            width: 70%;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.25);
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(34, 197, 94, 0.35);
        }
    </style>
</head>
<body class="bg-white">

<nav class="glass-navbar sticky top-0 z-50">
    <div class="container mx-auto px-4 md:px-8">
        <div class="flex justify-between items-center py-3 md:py-4">
            
            <a href="index.php" class="flex items-center space-x-3 group">
                <img src="img/sipanda.png" alt="SIPANDA" class="w-10 h-10 md:w-11 md:h-11 object-contain rounded-xl group-hover:scale-105 transition duration-300">
                <div>
                    <span class="text-lg md:text-xl font-bold bg-gradient-to-r from-green-700 to-green-500 bg-clip-text text-transparent">SIPANDA</span>
                </div>
            </a>
            
            <div class="hidden md:flex items-center space-x-8">
                <a href="index.php#profil" class="nav-link text-gray-700 hover:text-green-600 font-medium py-2 transition-all duration-200">
                    Profil
                </a>
                <a href="index.php#layanan" class="nav-link text-gray-700 hover:text-green-600 font-medium py-2 transition-all duration-200">
                    Layanan
                </a>
                <a href="artikel.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'artikel.php' ? 'active' : ''; ?> text-gray-700 hover:text-green-600 font-medium py-2 transition-all duration-200">
                    Artikel
                </a>
                <a href="index.php#kontak" class="nav-link text-gray-700 hover:text-green-600 font-medium py-2 transition-all duration-200">
                    Kontak
                </a>
                <a href="auth/login.php" class="btn-login text-white px-6 py-2 rounded-full font-semibold text-sm transition-all duration-200">
                    Login
                </a>
            </div>
            
            <button id="mobileMenuBtn" class="md:hidden w-10 h-10 rounded-xl bg-gray-100 text-gray-600 hover:bg-green-100 hover:text-green-600 transition-all duration-200 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
        </div>
        
        <div id="mobileMenu" class="hidden md:hidden pb-5 border-t border-gray-100">
            <div class="flex flex-col space-y-1 pt-3">
                <a href="index.php#profil" class="px-4 py-3 text-gray-700 hover:text-green-600 font-medium rounded-xl hover:bg-green-50 transition-all duration-200">
                    Profil
                </a>
                <a href="index.php#layanan" class="px-4 py-3 text-gray-700 hover:text-green-600 font-medium rounded-xl hover:bg-green-50 transition-all duration-200">
                    Layanan
                </a>
                <a href="artikel.php" class="px-4 py-3 text-gray-700 hover:text-green-600 font-medium rounded-xl hover:bg-green-50 transition-all duration-200 <?php echo basename($_SERVER['PHP_SELF']) == 'artikel.php' ? 'text-green-600 bg-green-50' : ''; ?>">
                    Artikel
                </a>
                <a href="index.php#kontak" class="px-4 py-3 text-gray-700 hover:text-green-600 font-medium rounded-xl hover:bg-green-50 transition-all duration-200">
                    Kontak
                </a>
                <a href="auth/login.php" class="mt-3 bg-gradient-to-r from-green-600 to-green-500 text-white text-center px-4 py-3 rounded-xl font-semibold">
                    Login
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    const mobileBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if(mobileBtn) {
        mobileBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        
        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });
    }
    
    window.addEventListener('scroll', () => {
        const sections = ['profil', 'layanan', 'kontak'];
        const scrollPos = window.scrollY + 100;
        
        sections.forEach(section => {
            const element = document.getElementById(section);
            if(element) {
                const offsetTop = element.offsetTop;
                const offsetHeight = element.offsetHeight;
                
                if(scrollPos >= offsetTop && scrollPos < offsetTop + offsetHeight) {
                    document.querySelectorAll('.nav-link').forEach(link => {
                        link.classList.remove('active');
                        if(link.getAttribute('href') === `index.php#${section}`) {
                            link.classList.add('active');
                        }
                    });
                }
            }
        });
    });
    
</script>