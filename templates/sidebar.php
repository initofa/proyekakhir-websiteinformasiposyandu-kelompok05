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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPANDA - <?php echo $title ?? 'Sistem Informasi Posyandu'; ?></title>
    <link rel="icon" type="image/png" href="/posyandu/img/sipanda.png">
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart -->
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
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
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
    </style>
</head>

<body class="bg-gradient-to-br from-green-50 via-emerald-50 to-teal-50">

<div id="sidebar"
    class="fixed inset-y-0 left-0 overflow-y-auto bg-gradient-to-b from-green-800 via-green-700 to-green-600 shadow-xl w-20 p-4 rounded-r-2xl z-40 transition-sidebar">

    <!-- LOGO -->
<!-- LOGO -->
<div class="text-white text-center mb-4">
    <div class="w-14 h-14 mx-auto mb-2 bg-white bg-opacity-20 rounded-full flex items-center justify-center shadow-lg overflow-hidden">
        <img src="<?= $base_url ?>/img/sipanda.png" 
             alt="SIPANDA Logo" 
             class="w-full h-full object-cover">
    </div>

    <!-- Nama user login -->
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
    <ul class="space-y-0.5">

        <!-- ADMIN -->
        <?php if ($role == 'admin'): ?>

        <li>
            <a href="<?= $base_url ?>/admin/dashboard.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-home text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/admin/users/list_users.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-users text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Users</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/admin/vaksin/list_vaksin.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-syringe text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Vaksin</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/admin/jadwal/list_jadwal.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-calendar-alt text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Jadwal</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/admin/artikel/list_artikel.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-newspaper text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Artikel</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/admin/kategori/list_kategori.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-tags text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Kategori</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/admin/ibu_hamil/list_ibu_hamil.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-female text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Ibu Hamil</span>
            </a>
        </li>

        <!-- BIDAN -->
        <?php elseif ($role == 'bidan'): ?>

        <li>
            <a href="<?= $base_url ?>/bidan/dashboard.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-home text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/bidan/vaksin/list_vaksin.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-syringe text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Vaksin</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/bidan/jadwal/list_jadwal.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-calendar-alt text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Jadwal</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/bidan/imunisasi/list_pendaftaran.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-clipboard-list text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Imunisasi</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/bidan/ibu_hamil/list_ibu_hamil.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-female text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Ibu Hamil</span>
            </a>
        </li>

        <!-- IBU -->
        <?php elseif ($role == 'ibu'): ?>

        <li>
            <a href="<?= $base_url ?>/ibu/dashboard.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-home text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Dashboard</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/ibu/anak/list_anak.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-baby text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Anak</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/ibu/imunisasi/jadwal_imunisasi.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-calendar-plus text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Jadwal</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/ibu/imunisasi/riwayat_imunisasi.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-history text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Riwayat</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/ibu/perkembangan/detail_perkembangan.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-chart-line text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Perkembangan</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/ibu/artikel/list_artikel.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-book text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Artikel</span>
            </a>
        </li>

        <li>
            <a href="<?= $base_url ?>/ibu/kehamilan/riwayat_hamil.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-baby-carriage text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Kehamilan</span>
            </a>
        </li>

        <?php endif; ?>

        <!-- LOGOUT -->
        <li class="pt-1 mt-1 border-t border-white/20">
            <a href="<?= $base_url ?>/auth/logout.php"
                class="menu-item flex items-center text-white hover:bg-white hover:bg-opacity-20 py-2 px-3 rounded-xl transition">
                <i class="fas fa-sign-out-alt text-lg w-6"></i>
                <span class="ml-3 sidebar-text hidden">Logout</span>
            </a>
        </li>

    </ul>
</div>

<!-- MAIN CONTENT -->
<div id="mainContent" class="ml-24 p-6 transition-sidebar min-h-screen duration-300">