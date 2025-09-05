<?php
require_once "../config.php";

header("Content-Type: application/json");

if (isset($_GET['team_id'])) {
    $team_id = (int) $_GET['team_id'];

    try {
        $stmt = $pdo->prepare(" SELECT 
                a.athlete_id,
                CONCAT(a.first_name, ' ', a.last_name) AS athlete_name,
                d.dept_name,
                e.event_name,
                r.position,
                r.result_id
            FROM relay_team_members tm
            JOIN athletes a ON tm.athlete_id = a.athlete_id
            JOIN departments d ON a.dept_id = d.dept_id
            JOIN relay_teams rt ON tm.team_id = rt.team_id
            JOIN results r ON r.relay_team_id = rt.team_id
            JOIN events e ON r.event_id = e.event_id
            WHERE tm.team_id = ?
        ");
        $stmt->execute([$team_id]);
        $athletes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($athletes);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}
