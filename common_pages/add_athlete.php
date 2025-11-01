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
                    $pdo->beginTransaction();
                    try {
                                                // Step 1: Check if athlete exists (ignore year here)
                        $athleteCheck = $pdo->prepare("SELECT athlete_id FROM athletes WHERE first_name = :fname AND last_name = :lname AND dept_id = :dep LIMIT 1");
                        $athleteCheck->execute([
                            'fname' => $fname,
                            'lname' => $lname,
                            'dep'   => $dep_id
                        ]);

                        if ($athleteCheck->rowCount() > 0) {
                            $athleteId = $athleteCheck->fetchColumn(); // Use existing athlete

                            $updateYear = $pdo->prepare("
                                UPDATE athletes 
                                SET year = :yr 
                                WHERE athlete_id = :id
                            ");
                            $updateYear->execute([
                                'yr' => $year,  // from your form
                                'id' => $athleteId
                            ]);
                        } else {
                            // Insert new athlete
                            $athleteSql = $pdo->prepare("INSERT INTO athletes (first_name, last_name, category_id, dept_id, year) 
                                                        VALUES (:fname, :lname, :category, :department, :yr)");
                            $athleteSql->execute([
                                'fname' => $fname,
                                'lname' => $lname,
                                'category' => $cat_id,
                                'department' => $dep_id,
                                'yr' => $year
                            ]);
                            $athleteId = $pdo->lastInsertId();
                        }

                        $currentYear=date('Y');

                        $insertedCount = 0;

                       $alreadyRegisteredEvents = [];
                        $participationCheck = $pdo->prepare("
                            SELECT event_id 
                            FROM participation 
                            WHERE athlete_id = :athleteid AND meet_year = :meetyear AND event_id = :eventid
                        ");

                        $participationInsert = $pdo->prepare("
                            INSERT INTO participation (athlete_id, event_id)
                            VALUES (:athleteid, :eventid)
                        ");

                        foreach ($validEvents as $event_id) {
                            $participationCheck->execute([
                                'athleteid' => $athleteId,
                                'eventid'   => $event_id,
                                'meetyear'      => $currentYear
                            ]);
                            
                            if ($participationCheck->rowCount() > 0) {
                                // Already registered for this event in this year
                                $alreadyRegisteredEvents[] = $event_id;
                                continue; // Skip insertion
                            }
                            
                            $participationInsert->execute([
                                'athleteid' => $athleteId,
                                'eventid'   => $event_id
                            ]);

                              $insertedCount++;
                        }

                        if ($insertedCount > 0) {
                            $message .= "Athlete & $insertedCount event(s) successfully registered. ";
                        }

                        if (!empty($alreadyRegisteredEvents)) {
                            $eventNames = [];
                            // Fetch event names to display
                            $eventQuery = $pdo->prepare("SELECT event_name FROM events WHERE event_id = ?");
                            foreach ($alreadyRegisteredEvents as $eid) {
                                $eventQuery->execute([$eid]);
                                $eventNames[] = $eventQuery->fetchColumn();
                            }
                            $message .= "Already registered for these events in $currentYear: " . implode(", ", $eventNames) ."Other events added. ";
                        }

                        if ($insertedCount === 0 && !empty($alreadyRegisteredEvents)) {
                            $message = "Failed:No new participation was added.Already registered for all these events";
                        }



                    $relayEvents = $pdo->prepare("SELECT event_id FROM events WHERE is_relay = 1");
                    $relayEvents->execute();
                    $relayEventIds = array_column($relayEvents->fetchAll(PDO::FETCH_ASSOC), 'event_id');

                    foreach ($validEvents as $event_id) {
                        if (in_array($event_id, $relayEventIds)) {

                            $checkTeam = $pdo->prepare("SELECT team_id FROM relay_teams WHERE event_id = ? AND dept_id = ? AND category_id=? 
                            AND meet_year=?");
                            $checkTeam->execute([$event_id, $dep_id, $cat_id,$currentYear]);
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
                        // $completed=true;
                        $pdo->commit();
                        // if($completed){
                        //   $_SESSION['athlete-msg'] = "Athlete and participation successfully registered!";
                        //     header("Location: $redirectPage&status=success");
                        //     exit;
                        // }

                        $_SESSION['athlete-msg'] = $message;
                        header("Location: $redirectPage&status=success");
                        exit;
                    } catch (PDOException $e) {
                        $pdo->rollBack();
                        $message = "Failed: " . $e->getMessage();
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
         <div class="degree-container">
            Degree<br>
            <select name="degree_id" id="degree-select" required>
                <option value="">-- Select Degree --</option>
                <?php
                $degrees = $pdo->query("SELECT degree_id, degree_name FROM degree")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($degrees as $degree): ?>
                    <option value="<?= htmlspecialchars($degree['degree_id']) ?>" 
                    data-name="<?= htmlspecialchars(strtolower($degree['degree_name'])) ?>">
                <?= htmlspecialchars($degree['degree_name']) ?>
            </option>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

                <div class="head-department-container">
            Department<br>
            <select name="hd_id" id="hd-select" required>
                <option value="">-- Select Head Department --</option>
                <?php
                $headDepts = $pdo->query("SELECT hd_id, hd_name FROM headdepartment")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($headDepts as $hd): ?>
                    <option value="<?= htmlspecialchars($hd['hd_id']) ?>">
                        <?= htmlspecialchars($hd['hd_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

                <div class="department-container">
                    Course<br>
                    <select name="dep_id" id="dep-select" required>
                        <option value="">-- Select Department --</option>
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
                    <select name="year_select" id="year-select" required>
                        <option value="">-- Select Year --</option>
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

     <script>
        document.getElementById('hd-select').addEventListener('change', loadDepartments);
        document.getElementById('degree-select').addEventListener('change', loadDepartments);

    function loadDepartments() {

        const degreeId = document.getElementById('degree-select').value;
        const hdId = document.getElementById('hd-select').value;
        const depSelect = document.getElementById('dep-select');

        depSelect.innerHTML = '<option value="">-- Loading --</option>';

        if (!degreeId || !hdId) {
            depSelect.innerHTML = '<option value="">-- Select Department --</option>';
            return;
        }

        fetch(`../common_pages/get_departments.php?degree_id=${degreeId}&hd_id=${hdId}`)
            .then(res => res.json())
            .then(data => {
                depSelect.innerHTML = '<option value="">-- Select Department --</option>';
                if (data.length === 0) {
                    depSelect.innerHTML = '<option value="">-- No departments found --</option>';
                } else {
                    data.forEach(dep => {
                        const option = document.createElement('option');
                        option.value = dep.dept_id;
                        option.textContent = dep.dept_name;
                        depSelect.appendChild(option);
                    });
                }
            })
            .catch(err => {
                depSelect.innerHTML = '<option value="">-- Error loading departments --</option>';
                console.error(err);
            });
    }

    </script>
    <script src="../assets/js/yearSelection.js"></script>
    <script type="module" src="../assets/js/maxEventRestrict.js"></script>
    <script src="../assets/js/eventSearch.js"></script>
    <script src="../assets/js/pageReload.js"></script>
</body>
</html>

