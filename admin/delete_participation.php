<?php
require_once "../session_check.php";
include_once "../config.php";
header('Content-Type: application/json');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

if (isset($_GET['athlete_id']) && isset($_GET['event_id'])) {
    $athlete_id = (int) $_GET['athlete_id'];
    $event_id   = (int) $_GET['event_id'];

    $deletion = $pdo->prepare("
        DELETE FROM participation
        WHERE athlete_id = :athlete_id AND event_id = :event_id
    ");

    $success = $deletion->execute([
        'athlete_id' => $athlete_id,
        'event_id'   => $event_id
    ]);

    echo json_encode(['success' => $success]);
    exit;
}
?>
