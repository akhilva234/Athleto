<?php 
require_once "../session_check.php";
include "../config.php";
header('Content-Type: application/json');

try{

    $athlete_id = $_GET['athleteId'] ?? null;

    if (!$athlete_id) {
        echo json_encode([
            "status" => "error",
            "message" => "athlete_id parameter is required"
        ]);
        exit;
    }

    $sql = "
    SELECT MAX(latest_year) AS max_meet_year
    FROM (
        SELECT meet_year AS latest_year
        FROM participation
        WHERE athlete_id = :athlete_id
        
        UNION ALL
        
        SELECT rt.meet_year AS latest_year
        FROM relay_teams rt
        INNER JOIN relay_team_members rtm ON rt.team_id = rtm.team_id
        WHERE rtm.athlete_id = :athlete_id
    ) AS combined;
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['athlete_id' => $athlete_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['max_meet_year']) {
        echo json_encode([
            "status" => "success",
            "athlete_id" => $athlete_id,
            "max_meet_year" => $result['max_meet_year']
        ]);
    } else {
        echo json_encode([
            "status" => "success",
            "athlete_id" => $athlete_id,
            "max_meet_year" => null,
            "message" => "No participation record found for this athlete"
        ]);
    }

}catch(PDOException $e){
     echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);

}


?>