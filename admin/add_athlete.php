<?php
session_start();
include "../config.php";

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fname = trim($_POST['firstname']);
    $lname = trim($_POST['lastname']);

    $dep_id = isset($_POST['dep_id']) ? (int)$_POST['dep_id'] : 0;
    $year = isset($_POST['year_select']) ? (int)$_POST['year_select'] : 0;
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

                        $athleteSql = $pdo->prepare("INSERT INTO athletes (first_name, last_name, category_id, dept_id, year) 
                                                    VALUES (:fname, :lname, :category, :department, :year)");

                        $athleteSql->execute([
                            'fname' => $fname,
                            'lname' => $lname,
                            'category' => $cat_id,
                            'department' => $dep_id,
                            'year' => $year
                        ]);

                        $athleteId = $pdo->lastInsertId();

                        $participation = $pdo->prepare("INSERT INTO participation (athlete_id, event_id) VALUES (:athleteid, :eventid)");

                        foreach ($validEvents as $event_id) {
                            $participation->execute([
                                'athleteid' => $athleteId,
                                'eventid' => $event_id
                            ]);
                        }

                        $pdo->commit();
                        $message = "Athlete and participation successfully registered!";
                    } catch (Exception $e) {
                        $pdo->rollBack();
                        $message = "Failed: " . $e->getMessage();
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
</head>
<body>
    <h1>Add Athlete</h1>

    <div class="insert-container">
        <div class="form-container">
            <form action="" method="post" class="form">

                <div>
                    Firstname<br>
                    <input type="text" name="firstname" placeholder="Firstname" required>
                </div>

                <div>
                    Lastname<br>
                    <input type="text" name="lastname" placeholder="Lastname" required>
                </div>

                <div>
                    Category<br>
                    <select name="cat_id" required>
                        <option value="">-- Select Category --</option>
                        <?php
                        $category = $pdo->query("SELECT category_id, category_name FROM categories");
                        foreach ($category as $cat) {
                            echo '<option value="' . htmlspecialchars($cat['category_id']) . '">' . htmlspecialchars($cat['category_name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div>
                    Events (Max 3)<br>
                    <?php
                    $events = $pdo->query("SELECT event_id, event_name FROM events")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($events as $event) {
                        echo '<input type="checkbox" name="events[]" value="' . htmlspecialchars($event['event_id']) . '"> ' . htmlspecialchars($event['event_name']) . '<br>';
                    }
                    ?>
                </div>

                <div>
                    Department<br>
                    <select name="dep_id" required>
                        <option value="">-- Select Department --</option>
                        <?php
                        $departments = $pdo->query("SELECT dept_id, dept_name FROM departments");
                        foreach ($departments as $department) {
                            echo '<option value="' . htmlspecialchars($department['dept_id']) . '">' . htmlspecialchars($department['dept_name']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div>
                    Year<br>
                    <select name="year_select" required>
                        <option value="">-- Select Year --</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </div>

                <div>
                    <input type="submit" value="Add" name="submit">
                </div>

            </form>
             <?php if (!empty($message)): ?>
        <div class="message <?= strpos($message, 'Failed') !== false || strpos($message, 'Invalid') !== false ? 'error' : '' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
        </div>
    </div>
</body>
</html>
