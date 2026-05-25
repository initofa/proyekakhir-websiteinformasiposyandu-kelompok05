<!-- templates/sidebar.php -->
<?php 
$role = $_SESSION['role'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

$base_url = "/posyandu";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>SIPANDA - <?php echo $title ?? 'Sistem Informasi Posyandu'; ?></title>
    <link rel="icon" type="image/png" href="/posyandu/img/sipanda.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .transition-sidebar {
            transition: all 0.35s ease-in-out;
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

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @media (min-width: 1024px) {
            body {
                position: relative;
            }
            
            #sidebar {
                position: fixed;
                left: 0;
                top: 0;
                width: 80px;
                height: 100vh;
                z-index: 40;
                transition: width 0.35s ease-in-out;
                border-radius: 0 20px 20px 0;
                overflow-y: auto;
                overflow-x: hidden;
            }
            
            #sidebar:hover {
                width: 280px !important;
            }
            
            .menu-item {
                justify-content: center !important;
            }

            #sidebar:hover .menu-item {
                justify-content: flex-start !important;
                padding-left: 1.25rem !important;
            }
            
            .sidebar-text {
                display: none !important;
                white-space: nowrap;
            }
            
            #sidebar:hover .sidebar-text {
                display: inline-block !important;
                margin-left: 12px;
            }
            
            #mainContent {
                margin-left: 80px !important;
                transition: margin-left 0.35s ease-in-out;
                width: calc(100% - 80px);
            }
            
            #sidebar:hover ~ #mainContent {
                margin-left: 280px !important;
                width: calc(100% - 280px);
            }
            
            .desktop-wrapper {
                display: flex;
                min-height: 100vh;
            }
            
            #mobileMenuBtn, #overlay {
                display: none !important;
            }
        }

        @media (max-width: 1023px) {
            #sidebar {
                position: fixed;
                left: 0;
                top: 0;
                width: 200px !important;
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                z-index: 50;
                border-radius: 0 !important;
            }
            
            #sidebar.mobile-open {
                transform: translateX(0) !important;
            }
            
            .sidebar-text {
                display: inline-block !important;
                margin-left: 12px;
            }
            
            .menu-item {
                width: 100%;
                justify-content: flex-start !important;
            }
            
            .menu-item i {
                width: 24px;
                text-align: center;
            }
            
            #overlay {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 45;
                transition: all 0.3s ease;
            }
            
            #overlay.hidden {
                display: none;
            }
            
            #overlay:not(.hidden) {
                display: block !important;
            }
            
            #mainContent {
                margin-left: 0 !important;
                padding-top: 70px !important;
                width: 100%;
            }
            
            #mobileMenuBtn {
                display: flex !important;
                position: fixed;
                top: 16px;
                left: 16px;
                z-index: 55;
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50 overflow-x-hidden">
<div id="overlay" class="fixed inset-0 bg-black/50 z-45 hidden"></div>

<button id="mobileMenuBtn"
    class="fixed top-4 left-4 z-55 hidden bg-gradient-to-r from-green-600 to-green-500 text-white w-12 h-12 rounded-xl shadow-lg flex items-center justify-center hover:scale-105 transition-transform">
    <i class="fas fa-bars text-xl"></i>
</button>

<div class="desktop-wrapper" style="display: flex; min-height: 100vh;">
    <div id="sidebar"
        class="bg-gradient-to-b from-green-800 via-green-700 to-green-600 shadow-xl"
        style="width: 80px; border-radius: 0 20px 20px 0;">

        <div class="text-white text-center mb-3 mt-4 px-2">
            <div class="w-14 h-14 mx-auto mb-2 bg-white bg-opacity-20 rounded-full flex items-center justify-center shadow-lg overflow-hidden">
                <img src="<?= $base_url ?>/img/sipanda.png" 
                     alt="SIPANDA Logo" 
                     class="w-full h-full object-cover">
            </div>

            <h2 class="text-sm font-bold sidebar-text hidden">
                <?php 
                    if ($role == 'admin') {
                        echo 'Admin';
                    } elseif ($role == 'bidan') {
                        echo 'Bidan ' . ($_SESSION['nama_lengkap'] ?? '');
                    } elseif ($role == 'ibu') {
                        echo 'Ibu ' . ($_SESSION['nama_lengkap'] ?? '');
                    } else {
                        echo ucfirst($role);
                    }
                ?>
            </h2>
        </div>

        <!-- MENU -->
        <ul class="space-y-1 px-2">
            <!-- ADMIN -->
            <?php if ($role == 'admin'): ?>

            <li>
                <a href="<?= $base_url ?>/admin/dashboard.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-home text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Dashboard</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/admin/ibu_hamil/list_ibu_hamil.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-female text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Ibu Hamil</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/admin/anak/list_anak.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-baby text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Anak</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/admin/vaksin/list_vaksin.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-syringe text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Vaksin</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/admin/jadwal/list_jadwal.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-calendar-alt text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Jadwal</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/admin/artikel/list_artikel.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-newspaper text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Artikel</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/admin/kategori/list_kategori.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-tags text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Kategori</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/admin/users/list_users.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-users text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Users</span>
                </a>
            </li>

            <!-- BIDAN -->
            <?php elseif ($role == 'bidan'): ?>

            <li>
                <a href="<?= $base_url ?>/bidan/dashboard.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-home text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Dashboard</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/bidan/ibu_hamil/list_ibu_hamil.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-female text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Ibu Hamil</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/bidan/anak/list_anak.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-baby text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Anak</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/bidan/vaksin/list_vaksin.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-syringe text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Vaksin</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/bidan/imunisasi/list_pendaftaran.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-clipboard-list text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Imunisasi</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/bidan/artikel/list_artikel.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-book text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Artikel</span>
                </a>
            </li>

            <!-- IBU -->
            <?php elseif ($role == 'ibu'): ?>

            <li>
                <a href="<?= $base_url ?>/ibu/dashboard.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-home text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Dashboard</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/ibu/kehamilan/riwayat_hamil.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-baby-carriage text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Kehamilan</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/ibu/anak/list_anak.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-baby text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Anak</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/ibu/imunisasi/jadwal_imunisasi.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-calendar-plus text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Jadwal</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/ibu/imunisasi/riwayat_imunisasi.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-history text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Riwayat</span>
                </a>
            </li>

            <li>
                <a href="<?= $base_url ?>/ibu/perkembangan/detail_perkembangan.php"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-chart-line text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Perkembangan</span>
                </a>
            </li>

            <?php endif; ?>

            <!-- LOGOUT -->
            <li class="pt-1 mt-1 border-t border-white/20">
                <a href="javascript:void(0)" id="logoutBtn"
                    class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                    <i class="fas fa-sign-out-alt text-lg w-6 text-center"></i>
                    <span class="sidebar-text hidden">Logout</span>
                </a>
            </li>

        </ul>
    </div>

    <!-- MAIN CONTENT -->
    <div id="mainContent" class="p-4 lg:p-6 transition-sidebar min-h-screen" style="flex: 1;">