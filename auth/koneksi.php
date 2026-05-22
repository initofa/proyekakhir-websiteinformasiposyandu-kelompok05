<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'db_posyandu';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Base URL
define('BASE_URL', 'http://localhost/posyandu/');
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/posyandu/uploads/');

// Create upload directories if not exists
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0777, true);
    mkdir(UPLOAD_PATH . 'artikel/', 0777, true);
    mkdir(UPLOAD_PATH . 'anak/', 0777, true);
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
        header("Location: ../auth/login.php");
        exit();
    }
}

function redirectIfRole($role) {
    $user = getCurrentUser();
    if (!$user || $user['role'] != $role) {
        header("Location: ../index.php");
        exit();
    }
}

// Fungsi untuk mendapatkan nama user berdasarkan NIK
function getUserName($nik) {
    global $conn;
    $query = "SELECT nama_lengkap FROM users WHERE nik = '$nik'";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    return $data ? $data['nama_lengkap'] : '-';
}

// Fungsi untuk log activity
function logActivity($nik, $action, $table_name, $record_id) {
    global $conn;
    $ip = $_SERVER['REMOTE_ADDR'];
    $query = "INSERT INTO activity_logs (nik, action, table_name, record_id, ip_address) 
              VALUES ('$nik', '$action', '$table_name', '$record_id', '$ip')";
    mysqli_query($conn, $query);
}
?>