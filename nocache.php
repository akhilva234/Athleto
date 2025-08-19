<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: 0");
header("Pragma: no-cache");
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}      
?>
