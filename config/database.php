<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'db_posyandu';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Jakarta');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'http://localhost/db_posyandu/');
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/db_posyandu/uploads/');

// Buat folder uploads jika belum ada
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0777, true);
    mkdir(UPLOAD_PATH . 'artikel/', 0777, true);
}


function isLoggedIn() {
    return isset($_SESSION['nik']);
}

function getCurrentUser() {
    global $conn;
    if (isset($_SESSION['nik'])) {
        $nik = $_SESSION['nik'];
        $query = "SELECT * FROM users WHERE nik = '$nik'";
        $result = mysqli_query($conn, $query);
        return mysqli_fetch_assoc($result);
    }
    return null;
}

function hasRole($role) {
    $user = getCurrentUser();
    return $user && $user['role'] == $role;
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: /posyandu/auth/login.php");
        exit();
    }
}
function redirectIfRole($role) {
    $user = getCurrentUser();
    if ($user && $user['role'] == $role) {
        header("Location: /posyandu/dashboard.php");
        exit();
    }
}

function getUserName($nik) {
    global $conn;
    if (empty($nik)) return '-';
    $query = "SELECT nama_lengkap FROM users WHERE nik = '$nik'";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    return $data ? $data['nama_lengkap'] : '-';
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