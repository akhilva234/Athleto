<?php
include "../config.php";

$degree_id = isset($_GET['degree_id']) ? (int)$_GET['degree_id'] : 0;
$hd_id = isset($_GET['hd_id']) ? (int)$_GET['hd_id'] : 0;

if (!$degree_id || !$hd_id) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT dept_id, dept_name 
                       FROM departments 
                       WHERE hd_id = ? AND degree_id = ?");
$stmt->execute([$hd_id, $degree_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>

