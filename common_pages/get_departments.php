<?php
include "../config.php";

if (isset($_GET['hd_id'])) {
    $hd_id = (int)$_GET['hd_id'];
    $stmt = $pdo->prepare("SELECT dept_id, dept_name FROM departments WHERE hd_id = ?");
    $stmt->execute([$hd_id]);
    $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($departments);
}
?>
