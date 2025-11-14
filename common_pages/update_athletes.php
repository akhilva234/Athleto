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
        
            // Validate individual event IDs
$validIndividualEvents = [];
foreach ($eventIds as $eid) {
    $eid = (int)$eid;
    $eventCheck = $pdo->prepare("SELECT event_id FROM events WHERE event_id = ? AND is_relay = 0");
    $eventCheck->execute([$eid]);
    if ($eventCheck->fetch()) {
        $validIndividualEvents[] = $eid;
    }
}

// Validate relay event IDs
$validRelayEvents = [];
foreach ($relayEventIds as $eid) {
    $eid = (int)$eid;
    $eventCheck = $pdo->prepare("SELECT event_id FROM events WHERE event_id = ? AND is_relay = 1");
    $eventCheck->execute([$eid]);
    if ($eventCheck->fetch()) {
        $validRelayEvents[] = $eid;
    }
}

// If both are empty
if (empty($validIndividualEvents) && empty($validRelayEvents)) {
    $_SESSION['athlete-msg'] = "Failed: No valid events selected.";
    header("Location: $redirectPage&status=error");
    exit;
}

try {
    $pdo->beginTransaction();

    $currentYear=date('Y');

    // Update athlete info
    $athleteSql = $pdo->prepare("UPDATE athletes SET first_name=?, last_name=?, category_id=?, dept_id=?, year=? WHERE athlete_id=?");
    $athleteSql->execute([$fname, $lname, $categoryId, $depId, $year, $athleteId]);

    // Delete old participation and relay entries
    $pdo->prepare("DELETE FROM participation WHERE athlete_id = ? AND meet_year=?")->execute([$athleteId,$currentYear]);

    // Delete old relay team members for this athlete for current meet year
    $deleteRelay = $pdo->prepare("
        DELETE rtm FROM relay_team_members rtm
        INNER JOIN relay_teams rt ON rt.team_id = rtm.team_id
        WHERE rtm.athlete_id = ? AND rt.meet_year = ?
    ");
    $deleteRelay->execute([$athleteId, $currentYear]);




    // Insert only INDIVIDUAL events into participation
    $insertParticipation = $pdo->prepare("INSERT INTO participation (athlete_id, event_id) VALUES (?, ?)");
    foreach ($validIndividualEvents as $event_id) {
        $insertParticipation->execute([$athleteId, $event_id]);
    }

    // Handle RELAY events separately
    foreach ($validRelayEvents as $event_id) {
        // Find or create team
        $checkTeam = $pdo->prepare("SELECT team_id FROM relay_teams WHERE event_id = ? AND dept_id = ? AND category_id = ? AND meet_year=?");
        $checkTeam->execute([$event_id, $depId, $categoryId,$currentYear]);
        $team = $checkTeam->fetch();

        if ($team) {
            $relayTeamId = $team['team_id'];
        } else {
            $createTeam = $pdo->prepare("INSERT INTO relay_teams (dept_id, event_id, category_id) VALUES (?, ?, ?)");
            $createTeam->execute([$depId, $event_id, $categoryId]);
            $relayTeamId = $pdo->lastInsertId();
        }

        $alreadyMember = $pdo->prepare("SELECT COUNT(*) FROM relay_team_members WHERE team_id = ? AND athlete_id = ?");
        $alreadyMember->execute([$relayTeamId, $athleteId]);
        $isMember = $alreadyMember->fetchColumn();

    if ($isMember==0) {

        $memberCount = $pdo->prepare("SELECT COUNT(*) FROM relay_team_members WHERE team_id = ?");
        $memberCount->execute([$relayTeamId]);
        $count = $memberCount->fetchColumn();

        if ($count < 5) {
            $addMember = $pdo->prepare("INSERT INTO relay_team_members (team_id, athlete_id) VALUES (?, ?)");
            $addMember->execute([$relayTeamId, $athleteId]);
        } else {
            throw new Exception("Failed: Relay team for event ID $event_id already has 5 participants.");
        }
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

?>
