<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../config.php';

if (isset($_GET['athlete_id'], $_GET['event_id'])) {
    $athlete_id = (int) $_GET['athlete_id'];
    $event_id = (int) $_GET['event_id'];
    $stmt = $pdo->prepare("SELECT a.first_name, a.last_name, e.event_name
    FROM participation p
    JOIN athletes a ON a.athlete_id = p.athlete_id
    JOIN events e ON e.event_id = p.event_id
    WHERE p.athlete_id = :athlete_id AND p.event_id = :event_id");
    $stmt->execute(['athlete_id' => $athlete_id, 'event_id' => $event_id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        echo json_encode([
            'athlete_name' => $data['first_name'] . ' ' . $data['last_name'],
            'event_name' => $data['event_name']
        ]);
    } else {
        echo json_encode(['error' => 'No data found']);
    }
} else {
    echo json_encode(['error' => 'Missing parameters']);
}
?>