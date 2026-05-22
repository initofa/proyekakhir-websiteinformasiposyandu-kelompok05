<?php
require_once __DIR__ . '/../config/database.php';

if (!isLoggedIn() || !hasRole('admin')) {
    header("Location: /posyandu/auth/login.php");
    exit();
}
?>