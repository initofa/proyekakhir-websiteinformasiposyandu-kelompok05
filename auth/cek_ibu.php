<?php
require_once __DIR__ . '/../config/database.php';

if (!isLoggedIn() || !hasRole('ibu')) {
    header("Location: /posyandu/auth/login.php");
    exit();
}
?>