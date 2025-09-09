<?php
    require_once "../session_check.php";
    include_once "../nocache.php";
include "../config.php";

$message = '';

    if (isset($_GET['status']) && $_GET['status'] === 'success') {
        if (isset($_SESSION['athlete-msg'])) {
            $message = $_SESSION['athlete-msg'];
            unset($_SESSION['athlete-msg']);
        }
    }

    $role = $_SESSION['role'];

$redirects = [
    'admin'   => 'adm_dashboard.php?page=add_athlete',
    'faculty' => 'faculty_dashboard.php?page=add_athlete',
    'captain' => 'captain_dashboard.php?page=add_athlete'
];

$redirectPage = $redirects[$role] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fname = ucwords(strtolower(trim($_POST['firstname'])));
    $lname = ucwords(strtolower(trim($_POST['lastname'])));

    $dep_id = isset($_POST['dep_id']) ? (int)$_POST['dep_id'] : 0;
    $year = isset($_POST['year_select']) ? $_POST['year_select'] : 0;
    $cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;
    $event_ids = isset($_POST['events']) ? $_POST['events'] : []; 

    
    if (empty($fname) || empty($lname) || !$dep_id || !$cat_id || !$year || empty($event_ids)) {
        $message = "All fields are required.";
    } else {
        
        $depCheck = $pdo->prepare("SELECT dept_id FROM departments WHERE dept_id = ?");
        $depCheck->execute([$dep_id]);
        if (!$depCheck->fetch()) {
            $message = "Invalid Department selected.";
        } else {
            
            $catCheck = $pdo->prepare("SELECT category_id FROM categories WHERE category_id = ?");
            $catCheck->execute([$cat_id]);
            if (!$catCheck->fetch()) {
                $message = "Invalid Category selected.";
            } else {
                
                $validEvents = [];
                foreach ($event_ids as $eid) {
                    $eid = (int)$eid;
                    $eventCheck = $pdo->prepare("SELECT event_id FROM events WHERE event_id = ?");
                    $eventCheck->execute([$eid]);
                    if ($eventCheck->fetch()) {
                        $validEvents[] = $eid;
                    }
                }

                if (empty($validEvents)) {
                    $message = "No valid events selected.";
                } else {
                    try {
                        $pdo->beginTransaction();
                        $completed=false;
                        $athleteSql = $pdo->prepare("INSERT INTO athletes (first_name, last_name, category_id, dept_id,year) 
                                                    VALUES (:fname, :lname, :category, :department, :yr)");

                        $athleteSql->execute([
                            'fname' => $fname,
                            'lname' => $lname,
                            'category' => $cat_id,
                            'department' => $dep_id,
                            'yr' => $year
                        ]);

                        $athleteId = $pdo->lastInsertId();

                        $participation = $pdo->prepare("INSERT INTO participation (athlete_id, event_id) VALUES (:athleteid, :eventid)");

                        foreach ($validEvents as $event_id) {
                            $insert_Participant=$participation->execute([
                                'athleteid' => $athleteId,
                                'eventid' => $event_id
                            ]);
                        }

                    $relayEvents = $pdo->prepare("SELECT event_id FROM events WHERE is_relay = 1");
                    $relayEvents->execute();
                    $relayEventIds = array_column($relayEvents->fetchAll(PDO::FETCH_ASSOC), 'event_id');

                    foreach ($validEvents as $event_id) {
                        if (in_array($event_id, $relayEventIds)) {

                            $checkTeam = $pdo->prepare("SELECT team_id FROM relay_teams WHERE event_id = ? AND dept_id = ? AND category_id=?");
                            $checkTeam->execute([$event_id, $dep_id, $cat_id]);
                            $team = $checkTeam->fetch();

                            if ($team) {
                                $relayTeamId = $team['team_id'];
                            } else {
                                $createTeam = $pdo->prepare(
                                "INSERT INTO relay_teams (dept_id,event_id, category_id) VALUES (?, ?, ?)");
                            $createTeam->execute([$dep_id,$event_id, $cat_id]);
                            $relayTeamId = $pdo->lastInsertId();
                            }
                                $memberCount = $pdo->prepare("
                                SELECT COUNT(*) 
                                FROM relay_team_members
                                WHERE team_id = ?
                            ");
                            $memberCount->execute([$relayTeamId]);
                            $count = $memberCount->fetchColumn(); 
                            if ($count < 5) {
                                $addMember = $pdo->prepare("INSERT INTO relay_team_members (team_id, athlete_id) VALUES (?, ?)");
                                $addMember->execute([$relayTeamId, $athleteId]);
                            } else {
                                throw new Exception("Failed:This relay team already has 5 participants.");
                            }
                           
                        }
                    }
                        $completed=true;
                        $pdo->commit();
                        if($completed){
                          $_SESSION['athlete-msg'] = "Athlete and participation successfully registered!";
                            header("Location: $redirectPage&status=success");
                            exit;
                        }
                    } catch (PDOException $e) {
                        $pdo->rollBack();
                         if (isset($e->errorInfo[1]) && $e->errorInfo[1]== 1062) { 
                        $message="Duplicate athlete found. Insertion skipped.";
                    } else {
                        $message = "Failed: " . $e->getMessage();
                    }
                }catch (Exception $e) {
                    $pdo->rollBack();
                    $message = $e->getMessage();
                }
            }
        }
    }
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Athlete</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="../assets/js/toast.js"></script>
    <link rel="stylesheet" href="../assets/css/add_athlete.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/common_css/form_common.css">
</head>
<body>
    <h2>Add Athlete</h2>

    <div class="insert-container">
        <div class="form-container">
            <form action="" method="post" class="form">

                <div class="name-container">
                    Firstname<br>
                    <input type="text" name="firstname" placeholder="Firstname" required>
                </div>

                <div class="name-container">
                    Lastname<br>
                    <input type="text" name="lastname" placeholder="Lastname" required>
                </div>

                <div class="department-container">
                    Department<br>
                    <select name="dep_id" required>
                        <option value="">-- Select Department --</option>
                        <?php
                        $departments = $pdo->query("SELECT dept_id, dept_name FROM departments");
                        foreach ($departments as $department): ?>
                            <option value="<?= htmlspecialchars($department['dept_id']) ?>" class="department">
                                <?= htmlspecialchars($department['dept_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="category-container">
                    Category<br>
                    <select name="cat_id"  id="category-check" required>
                        <option value="">-- Select Category --</option>
                        <?php
                        $categories = $pdo->query("SELECT category_id, category_name FROM categories");
                        foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['category_id']) ?>" class="category">
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="events-whole-container">
                    Individual Events (Max 3)<br>
                    <input type="text" class="event-search" data-target="individual-event" placeholder="Search Individual Events" name="search">
                    <div class="no-events-message" style="display: none; color: red; font-style: italic;">No events found</div>

                    <div class="events-container scrollable" id="individual-events-box">
                        <?php
                        $events = $pdo->query("SELECT event_id, event_name FROM events WHERE is_relay=0")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($events as $event): ?>
                            <label>
                                <input type="checkbox" 
                                    name="events[]" 
                                    value="<?= htmlspecialchars($event['event_id']) ?>" 
                                    class="events individual-event"> 
                                <?= htmlspecialchars($event['event_name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="events-whole-container">
                    Relay Events<br>
                    <input type="text" class="event-search" data-target="relay-events" placeholder="Search Relay Events"  name="search">
                    <div class="no-events-message" style="display: none; color: red; font-style: italic;">No events found</div>

                    <div class="events-container scrollable" id="relay-events-box">
                        <?php
                        $relayEvents = $pdo->query("SELECT event_id, event_name FROM events WHERE is_relay=1")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($relayEvents as $event): ?>
                            <label>
                                <input type="checkbox" 
                                    name="events[]" 
                                    value="<?= htmlspecialchars($event['event_id']) ?>" 
                                    class="events relay-events"  
                                    data-event-id="<?= htmlspecialchars($event['event_id']) ?>"> 
                                <?= htmlspecialchars($event['event_name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="year-container">
                    Year<br>
                    <select name="year_select" required>
                        <option value="">-- Select Year --</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </div>

                <br>
                <div>
                    <input type="submit" value="Add" name="submit" class="add-btn athlete-add-btn">
                </div>
            </form>
        </div>
    </div>
        <?php if (!empty($message)): ?>
    <script>
        <?php if (strpos($message, 'Failed') !== false || strpos($message, 'Invalid') !== false): ?>
            toastr.error(<?= json_encode($message) ?>);
        <?php else: ?>
            toastr.success(<?= json_encode($message) ?>);
        <?php endif; ?>
    </script>
    <?php endif; ?>

    <script type="module" src="../assets/js/maxEventRestrict.js"></script>
    <script src="../assets/js/eventSearch.js"></script>
    <script src="../assets/js/pageReload.js"></script>
</body>
</html>

