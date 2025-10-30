<?php
require_once "../session_check.php";
include_once "../config.php";

$user = $_SESSION['user'];
$role = $_SESSION['role'];

$redirects = [
    'admin'   => 'adm_dashboard.php?page=participants',
    'faculty' => 'faculty_dashboard.php?page=participants',
    'captain' => 'captain_dashboard.php?page=participants'
];

$redirectPage = $redirects[$role] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $athlete_id = (int)$_POST['athleteid'];
    $event_id   = (int)$_POST['eventid'];
    $position   = (int)$_POST['position'];

    try {
        $pdo->beginTransaction();

        $catStmt = $pdo->prepare("SELECT category_id FROM athletes WHERE athlete_id = ?");
        $catStmt->execute([$athlete_id]);
        $athleteCategory = $catStmt->fetchColumn();

        $ifExists = $pdo->prepare("SELECT 1 FROM results WHERE athlete_id = ? AND event_id = ?");
        $ifExists->execute([$athlete_id, $event_id]);
        if ($ifExists->fetch()) {
            $_SESSION['result-add-msg'] = "Failed: Position for event already secured by this athlete.";
            header("Location: $redirectPage&status=error");
            exit;
        }

        // $positionExists = $pdo->prepare("
        //     SELECT r.result_id 
        //     FROM results r
        //     INNER JOIN athletes a ON r.athlete_id = a.athlete_id
        //     WHERE r.event_id = ? AND r.position = ? AND a.category_id = ?
        // ");
        // $positionExists->execute([$event_id, $position, $athleteCategory]);

        // if ($positionExists->fetch()) {
        //     $_SESSION['result-add-msg'] = "Failed: Position already taken for this event in the selected category.";
        //     header("Location: $redirectPage&status=error");
        //     exit;
        // }

        $result_add = $pdo->prepare("INSERT INTO results(event_id, athlete_id, position, added_by)
            VALUES(:eventid, :athleteid, :position, :added)");

        $success = $result_add->execute([
            'eventid'   => $event_id,
            'athleteid' => $athlete_id,
            'position'  => $position,
            'added'     => $user
        ]);

        if ($success) {
            $pdo->commit();
            $_SESSION['result-add-msg'] = "Result added successfully";
            header("Location: $redirectPage&status=success");
            exit;
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "user:".$user;
        echo "Failed: ".$e->getMessage();
    }
}
?>
