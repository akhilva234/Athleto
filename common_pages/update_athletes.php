<?php
require_once "../session_check.php";
include "../config.php";
$user = $_SESSION['user'];
$role = $_SESSION['role'];

$redirects = [
    'admin'   => 'adm_dashboard.php?page=athletes_info',
    'faculty' => 'faculty_dashboard.php?page=athletes_info',
    'captain' => 'captain_dashboard.php?page=athletes_info'
];

$redirectPage = $redirects[$role] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $athleteId = (int)$_POST['athlete_id'];
    $fname = ucwords(strtolower(trim($_POST['first_name'])));
    $lname = ucwords(strtolower(trim($_POST['last_name'])));
    $year = (int)$_POST['year'];
    $depId = (int)$_POST['dept_id'];
    $categoryId = (int)$_POST['category_id'];

    $eventIds = $_POST['event_ids'] ?? [];
    $relayEventIds = $_POST['relay_event_ids'] ?? [];

    if (empty($athleteId) || empty($fname) || empty($lname) || empty($year) ||
        empty($depId) || empty($categoryId)) {
        $_SESSION['athlete-msg'] = "Failed:All fields are required";
        header("Location: $redirectPage&status=error");
        exit;
    } else {
        $validEvents = [];

        // Validate individual event IDs
        foreach ($eventIds as $eid) {
            $eid = (int)$eid;
            $eventCheck = $pdo->prepare("SELECT event_id FROM events WHERE event_id = ?");
            $eventCheck->execute([$eid]);
            if ($eventCheck->fetch()) {
                $validEvents[] = $eid;
            }
        }

        // Validate relay event IDs
        foreach ($relayEventIds as $eid) {
            $eid = (int)$eid;
            $eventCheck = $pdo->prepare("SELECT event_id FROM events WHERE event_id = ?");
            $eventCheck->execute([$eid]);
            if ($eventCheck->fetch()) {
                $validEvents[] = $eid;
            }
        }

        if (empty($validEvents)) {
            $_SESSION['athlete-msg'] = "Failed:No valid events selected.";
            header("Location: $redirectPage&status=error");
            exit;

        } else {
            try {
                $pdo->beginTransaction();

                // Update athlete info
                $athleteSql = $pdo->prepare("UPDATE athletes SET first_name=?, last_name=?, category_id=?, dept_id=?, year=? WHERE athlete_id=?");
                $athleteSql->execute([
                    $fname,
                    $lname,
                    $categoryId,
                    $depId,
                    $year,
                    $athleteId
                ]);

                // Delete previous participation and relay team members
                $deleteParticipation = $pdo->prepare("DELETE FROM participation WHERE athlete_id = ?");
                $deleteParticipation->execute([$athleteId]);

                $deleteRelay = $pdo->prepare("DELETE FROM relay_team_members WHERE athlete_id = ?");
                $deleteRelay->execute([$athleteId]);

                // Insert new participation records
                $insertParticipation = $pdo->prepare("INSERT INTO participation (athlete_id, event_id) VALUES (?, ?)");
                foreach ($validEvents as $event_id) {
                    $insertParticipation->execute([$athleteId, $event_id]);
                }

                // Handle relay teams
                foreach ($relayEventIds as $event_id) {
                    $event_id = (int)$event_id;

                    // Check or create team
                   
                     $checkTeam = $pdo->prepare("SELECT team_id FROM relay_teams WHERE event_id = ? AND dept_id = ? AND category_id=?");
                        $checkTeam->execute([$event_id, $depId, $categoryId]);
                        $team = $checkTeam->fetch();

                    if ($team) {
                        $relayTeamId = $team['team_id'];
                    } else {
                         $createTeam = $pdo->prepare( "INSERT INTO relay_teams (dept_id,event_id, category_id) VALUES (?, ?, ?)");
                        $createTeam->execute([$depId,$event_id, $categoryId]);
                        $relayTeamId = $pdo->lastInsertId();
                    }

                    // Check gender-based team limit
                     $memberCount = $pdo->prepare("
                                SELECT COUNT(*) 
                                FROM relay_team_members
                                WHERE team_id = ?
                            ");
                    $memberCount->execute([$relayTeamId]);
                    $count = $memberCount->fetchColumn();

                    if ($count < 5) {
                        // Add to relay team
                        $addMember = $pdo->prepare("INSERT INTO relay_team_members (team_id, athlete_id) VALUES (?, ?)");
                        $addMember->execute([$relayTeamId, $athleteId]);
                    } else {
                        throw new Exception("Failed:Relay team for event ID $event_id already has 5  participants.");
                    }
                }

                $pdo->commit();

                $_SESSION['athlete-msg'] = "Athlete and participation updated successfully";
                header("Location: $redirectPage&status=success");
                exit;

            } catch (PDOException $e) {
                $pdo->rollBack();
                $_SESSION['athlete-msg'] = "Failed: " . $e->getMessage();
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['athlete-msg'] = $e->getMessage();
            }
        }
    }
}
?>
