<?php
session_start();
session_destroy();
header("Location: /posyandu/index.php");
exit();
?>