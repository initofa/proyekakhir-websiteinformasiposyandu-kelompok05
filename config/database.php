<?php
// config/database.php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_posyandu";

// Menggunakan mysqli object oriented
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// CONSTANTS
// ============================================
define('BASE_URL', 'http://localhost/db_posyandu/');
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/db_posyandu/uploads/');

// Buat folder uploads jika belum ada
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0777, true);
    mkdir(UPLOAD_PATH . 'artikel/', 0777, true);
}

// ============================================
// AUTH FUNCTIONS
// ============================================
function isLoggedIn() {
    return isset($_SESSION['nik']);
}

function getCurrentUser() {
    global $conn;

    if (!isset($_SESSION['nik'])) {
        return null;
    }

    $nik = mysqli_real_escape_string($conn, $_SESSION['nik']);
    $query = mysqli_query($conn, "SELECT * FROM users WHERE nik = '$nik'");
    
    if ($query && mysqli_num_rows($query) > 0) {
        return mysqli_fetch_assoc($query);
    }
    
    return null;
}

function hasRole($role) {
    $user = getCurrentUser();
    return $user && $user['role'] == $role;
}

function redirectIfNotLoggedIn() {
    // Halaman yang boleh diakses tanpa login
    $allowed_pages = ['login.php', 'register.php', 'index.php', 'artikel.php', 'artikel_detail.php'];
    $current_page = basename($_SERVER['PHP_SELF']);
    
    // Jika di halaman yang diperbolehkan, jangan redirect
    if (in_array($current_page, $allowed_pages)) {
        return;
    }
    
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "auth/login.php");
        exit();
    }
}

function redirectIfRole($role) {
    $user = getCurrentUser();
    $current_page = basename($_SERVER['PHP_SELF']);
    
    if ($user && $user['role'] == $role) {
        // Hindari redirect loop dengan mengecek halaman tujuan
        $dashboard_pages = ['dashboard.php', 'index.php'];
        if (!in_array($current_page, $dashboard_pages)) {
            if ($role == 'admin') {
                header("Location: " . BASE_URL . "admin/dashboard.php");
            } elseif ($role == 'bidan') {
                header("Location: " . BASE_URL . "bidan/dashboard.php");
            } elseif ($role == 'ibu') {
                header("Location: " . BASE_URL . "ibu/dashboard.php");
            }
            exit();
        }
    }
}

function getUserName($nik) {
    global $conn;
    if (empty($nik)) return '-';
    
    $nik = mysqli_real_escape_string($conn, $nik);
    $query = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE nik = '$nik'");
    
    if ($query && mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        return $data['nama_lengkap'];
    }
    
    return '-';
}

function paginate($current_page, $total_pages, $url) {
    $html = '<div class="flex justify-between items-center mt-4 px-4 py-3 border-t border-gray-200">';
    $html .= '<div class="text-sm text-gray-600">Halaman ' . $current_page . ' dari ' . $total_pages . '</div>';
    $html .= '<div class="flex gap-2">';
    
    if ($current_page > 1) {
        $html .= '<a href="' . $url . '&page=' . ($current_page - 1) . '" class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300 transition">« Prev</a>';
    }
    
    for ($i = max(1, $current_page - 2); $i <= min($total_pages, $current_page + 2); $i++) {
        $active = ($i == $current_page) ? 'bg-green-600 text-white' : 'bg-gray-200 hover:bg-gray-300';
        $html .= '<a href="' . $url . '&page=' . $i . '" class="px-3 py-1 ' . $active . ' rounded-lg transition">' . $i . '</a>';
    }
    
    if ($current_page < $total_pages) {
        $html .= '<a href="' . $url . '&page=' . ($current_page + 1) . '" class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Next »</a>';
    }
    
    $html .= '</div></div>';
    return $html;
}
?>