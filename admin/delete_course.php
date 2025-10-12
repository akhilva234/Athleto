<?php
require_once "../session_check.php";
include_once "../nocache.php";
include "../config.php";

// Set JSON header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dept_id'])) {
    $dept_id = (int)$_POST['dept_id'];

    try {
        $pdo->beginTransaction();

        
        $athleteCheck = $pdo->prepare("SELECT COUNT(*) FROM athletes WHERE dept_id = ?");
        $athleteCheck->execute([$dept_id]);
        $athleteCount = $athleteCheck->fetchColumn();

        if ($athleteCount > 0) {
            $pdo->rollBack();
            echo json_encode([
                "status" => "error",
                "message" => "Cannot delete this course — athletes are registered under it."
            ]);
            exit;
        }

        
        $delRelay = $pdo->prepare("DELETE FROM relay_teams WHERE dept_id = ?");
        $delRelay->execute([$dept_id]);

        
        $delCourse = $pdo->prepare("DELETE FROM departments WHERE dept_id = ?");
        $delCourse->execute([$dept_id]);

        $pdo->commit();

        echo json_encode([
            "status" => "success",
            "message" => "Course deleted successfully."
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();

        $errorMsg = "Course deletion failed.";
        if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
            $errorMsg = "Cannot delete this course — it is linked to other records.";
        }

        echo json_encode([
            "status" => "error",
            "message" => $errorMsg,
            "details" => $e->getMessage() 
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request."
    ]);
}
?>
