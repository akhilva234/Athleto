<?php
require_once "../session_check.php";
include_once "../nocache.php";
include "../config.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hd_id = isset($_POST['hd_id']) ? (int)$_POST['hd_id'] : 0;
   $dept_name = isset($_POST['dept_name']) ? ucwords(strtolower(trim($_POST['dept_name']))) : '';

    if ($hd_id <= 0 || $dept_name === '') {
        echo json_encode(["status" => "error", "message" => "Invalid input."]);
        exit;
    }

    try {
        // Check duplicate
        $check = $pdo->prepare("SELECT COUNT(*) FROM departments WHERE hd_id = ? AND dept_name = ?");
        $check->execute([$hd_id, $dept_name]);
        if ($check->fetchColumn() > 0) {
            echo json_encode(["status" => "error", "message" => "Course already exists."]);
            exit;
        }

        // Insert
        $stmt = $pdo->prepare("INSERT INTO departments (hd_id, dept_name) VALUES (?, ?)");
        $stmt->execute([$hd_id, $dept_name]);

        echo json_encode(["status" => "success", "message" => "Course added successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
