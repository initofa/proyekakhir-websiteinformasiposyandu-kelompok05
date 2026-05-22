<?php
require_once __DIR__ . '/../config/database.php';

if (!isLoggedIn() || !hasRole('bidan')) {
    header("Location: /posyandu/auth/login.php");
    exit();
}
?>