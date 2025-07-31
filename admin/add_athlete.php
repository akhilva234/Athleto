<?php
    require_once "../session_check.php";
    $user= $_SESSION['user'];
include "../config.php";

$message = '';

    if(isset($_SESSION['athlete-msg'])){

        $message=$_SESSION['athlete-msg'];

        unset($_SESSION['athlete-msg']);
    }

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
                } elseif (count($validEvents) > 3) {
                    $message = "You can select a maximum of 3 events.";
                } else {
                    try {
                        $pdo->beginTransaction();

                        $athleteSql = $pdo->prepare("INSERT INTO athletes (first_name, last_name, category_id, dept_id,year) 
                                                    VALUES (:fname, :lname, :category, :department, :yr)");

                        $athleteSql->execute([
                            'fname' => $fname,
                            'lname' => $lname,
                            'category' => $cat_id,
                            'department' => $dep_id,
                            'yr' => $year
                        ]);

                        if ($athleteSql->fetch()) {
                            echo "Athlete already exists.";
                            exit;
                        }

                        $athleteId = $pdo->lastInsertId();

                        $participation = $pdo->prepare("INSERT INTO participation (athlete_id, event_id) VALUES (:athleteid, :eventid)");

                        foreach ($validEvents as $event_id) {
                            $insert_Participant=$participation->execute([
                                'athleteid' => $athleteId,
                                'eventid' => $event_id
                            ]);
                        }
                        $pdo->commit();
                        if($insert_Participant){

                            
                          $_SESSION['athlete-msg'] = "Athlete and participation successfully registered!";
                            header("Location: adm_dashboard.php?page=add_athlete&status=success");
                            exit;
                        }
                    } catch (PDOException $e) {
                        $pdo->rollBack();
                         if (isset($e->errorInfo[1]) && $e->errorInfo[1]== 1062) { 
                        $message="Duplicate athlete found. Insertion skipped.";
                    } else {
                        $message = "Failed: " . $e->getMessage();
                    }
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
    <link rel="stylesheet" href="../assets/css/add_athlete.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/common_css/form_common.css">
</head>
<body>
    <h2>Add Athlete</h2>

    <p>User: <?= htmlspecialchars($user) ?></p>

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

                <div class="events-whole-container">
                    Individual Events (Max 3)<br>
                    <div class="events-container">
                        <?php
                        $events = $pdo->query("SELECT event_id, event_name FROM events WHERE is_relay=0")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($events as $event): ?>
                            <input type="checkbox" 
                                   name="events[]" 
                                   value="<?= htmlspecialchars($event['event_id']) ?>" 
                                   class="events individual-event"> 
                            <?= htmlspecialchars($event['event_name']) ?><br>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="events-whole-container">
                    Relay Events<br>
                    <div class="events-container">
                        <?php
                        $relayEvents = $pdo->query("SELECT event_id, event_name FROM events WHERE is_relay=1")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($relayEvents as $event): ?>
                            <input type="checkbox" 
                                   name="events[]" 
                                   value="<?= htmlspecialchars($event['event_id']) ?>" 
                                   class="events relay-events"  
                                   data-event-id="<?= htmlspecialchars($event['event_id']) ?>"> 
                            <?= htmlspecialchars($event['event_name']) ?><br>
                        <?php endforeach; ?>
                    </div>
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
                    <select name="cat_id" required>
                        <option value="">-- Select Category --</option>
                        <?php
                        $categories = $pdo->query("SELECT category_id, category_name FROM categories");
                        foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['category_id']) ?>" class="category" id="category-check">
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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

                <?php if (!empty($message)): ?>
                    <div class="success-message <?= (strpos($message, 'Failed') !== false || strpos($message, 'Invalid') !== false) ? 'error' : '' ?>">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

            </form>
        </div>
    </div>
    <script type="module" src="../assets/js/maxEventRestrict.js"></script>
</body>
</html>

