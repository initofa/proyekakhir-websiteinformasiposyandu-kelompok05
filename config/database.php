<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "db_posyandu";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'http://localhost/posyandu/');
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/posyandu/uploads/');

// Buat folder uploads jika belum ada
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0777, true);
}

if (!file_exists(UPLOAD_PATH . 'artikel/')) {
    mkdir(UPLOAD_PATH . 'artikel/', 0777, true);
}

if (!file_exists(UPLOAD_PATH . 'berkas_anak/')) {
    mkdir(UPLOAD_PATH . 'berkas_anak/', 0777, true);
}

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
    
    return mysqli_fetch_assoc($query);
}

function hasRole($role) {
    $user = getCurrentUser();
    return $user && isset($user['ROLE']) && $user['ROLE'] == $role;
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "auth/login.php");
        exit();
    }
}

function redirectIfRole($role) {
    $user = getCurrentUser();
    $current_page = basename($_SERVER['PHP_SELF']);
    
    if ($user && isset($user['ROLE']) && $user['ROLE'] == $role) {
        if ($current_page != 'index.php' && !strpos($current_page, 'dashboard')) {
            if ($role == 'admin') {
                header("Location: " . BASE_URL . "admin/index.php");
            } elseif ($role == 'bidan') {
                header("Location: " . BASE_URL . "bidan/index.php");
            } elseif ($role == 'ibu') {
                header("Location: " . BASE_URL . "ibu/index.php");
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
    $data = mysqli_fetch_assoc($query);
    
    return $data ? $data['nama_lengkap'] : '-';
}

function getUserNameByNik($nik) {
    return getUserName($nik);
}

function getUserRole($nik) {
    global $conn;
    if (empty($nik)) return '-';
    
    $nik = mysqli_real_escape_string($conn, $nik);
    $query = mysqli_query($conn, "SELECT ROLE FROM users WHERE nik = '$nik'");
    $data = mysqli_fetch_assoc($query);
    
    return $data ? $data['ROLE'] : '-';
}

function getUserStatus($nik) {
    global $conn;
    if (empty($nik)) return '-';
    
    $nik = mysqli_real_escape_string($conn, $nik);
    $query = mysqli_query($conn, "SELECT STATUS FROM users WHERE nik = '$nik'");
    $data = mysqli_fetch_assoc($query);
    
    return $data ? $data['STATUS'] : '-';
}

function paginate($current_page, $total_pages, $base_url, $additional_params = []) {
    if ($total_pages <= 1) return '';

    $query_string = '';
    if (!empty($additional_params)) {
        foreach ($additional_params as $key => $value) {
            if ($value !== '') {
                $query_string .= '&' . urlencode($key) . '=' . urlencode($value);
            }
        }
    }

    $connector = (strpos($base_url, '?') === false) ? '?' : '&';

    $html = '<div class="flex justify-between items-center mt-4 px-4 py-3 border-t border-gray-200">';
    $html .= '<div class="text-sm text-gray-600">Halaman ' . $current_page . ' dari ' . $total_pages . '</div>';
    $html .= '<div class="flex gap-2">';
    
    if ($current_page > 1) {
        $prev_url = $base_url . $connector . 'page=' . ($current_page - 1) . $query_string;
        $html .= '<a href="' . $prev_url . '" class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300 transition text-sm font-medium text-gray-700">« Prev</a>';
    }
    
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $current_page) ? 'bg-green-600 text-white font-bold' : 'bg-gray-200 hover:bg-gray-300 text-gray-700';
        $page_url = $base_url . $connector . 'page=' . $i . $query_string;
        $html .= '<a href="' . $page_url . '" class="px-3 py-1 ' . $active . ' rounded-lg transition text-sm">' . $i . '</a>';
    }
    
    if ($current_page < $total_pages) {
        $next_url = $base_url . $connector . 'page=' . ($current_page + 1) . $query_string;
        $html .= '<a href="' . $next_url . '" class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300 transition text-sm font-medium text-gray-700">Next »</a>';
    }
    
    $html .= '</div></div>';
    return $html;
}


function formatTanggal($tanggal) {
    if (empty($tanggal) || $tanggal == '0000-00-00') return '-';
    $timestamp = strtotime($tanggal);
    return date('d/m/Y', $timestamp);
}

function formatTanggalIndonesia($tanggal) {
    if (empty($tanggal) || $tanggal == '0000-00-00') return '-';
    
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $t = strtotime($tanggal);
    $tanggal_num = date('d', $t);
    $bulan_num = date('n', $t);
    $tahun = date('Y', $t);
    
    return $tanggal_num . ' ' . $bulan[$bulan_num] . ' ' . $tahun;
}

function formatHPL($hpht) {
    if (empty($hpht) || $hpht == '0000-00-00') return '-';
    return date('d/m/Y', strtotime($hpht . ' + 280 days'));
}

function hitungUsiaKehamilan($hpht) {
    if (empty($hpht)) return 0;
    $today = new DateTime();
    $hpht_date = new DateTime($hpht);
    $diff = $today->diff($hpht_date);
    return floor($diff->days / 7);
}


function validateNIK($nik) {
    return preg_match('/^[0-9]{16}$/', $nik);
}

function validatePhoneNumber($phone) {
    return preg_match('/^[0-9]{10,15}$/', $phone);
}

?>