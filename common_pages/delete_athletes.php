<?php
 require_once "../session_check.php";
require "../config.php";

if (isset($_GET['athleteid'])) {
    $athlete_id = intval($_GET['athleteid']);

    try {
        
        $pdo->beginTransaction();

         $stmt = $pdo->prepare("DELETE FROM results WHERE athlete_id = ?");
        $stmt->execute([$athlete_id]);

        $stmt = $pdo->prepare("DELETE FROM relay_team_members WHERE athlete_id = ?");
        $stmt->execute([$athlete_id]);

        $stmt = $pdo->prepare("DELETE FROM participation WHERE athlete_id = ?");
        $stmt->execute([$athlete_id]);

        $stmt = $pdo->prepare("DELETE FROM athletes WHERE athlete_id = ?");
        $stmt->execute([$athlete_id]);
        
        $pdo->commit();

        echo json_encode(["success" => true, "message" => "Athlete deleted successfully."]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No athlete_id provided."]);
}
?>
