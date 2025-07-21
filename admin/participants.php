<?php
    require_once "../session_check.php";
     if(isset($_POST['resultadd'])){
                require '../common_pages/add_result.php';
            }
    include_once "../config.php";
    $user=$_SESSION['user'];
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false); // for IE
    header("Pragma: no-cache");
    header("Expires: 0");

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="../assets/css/common.css">
     <link rel="stylesheet" href="../assets/css/common_css/tables.css">
     <link rel="stylesheet" href="../assets/css/result_form.css">
     <link rel="stylesheet" href="../assets/css/common_css/message.css">
</head>
<body>
    <div class="whole-blur-container"></div>
    <h2>Participants</h2>
    <?php  echo "user:".$user;?>
    <?php
        $Participants=$pdo->query("SELECT  
    a.athlete_id,   
    a.first_name,
    a.last_name,
    a.year,
    d.dept_name,
    c.category_name,
    e.event_name,
    e.event_id
FROM 
    athletes a
JOIN 
    departments d ON a.dept_id = d.dept_id
JOIN 
    categories c ON a.category_id = c.category_id
JOIN 
    participation p ON a.athlete_id = p.athlete_id
JOIN 
    events e ON p.event_id = e.event_id
ORDER BY 
    a.athlete_id
");
    ?>
     <?php include_once "../common_pages/filter.php"?>
    <div class="participants-table-container table-whole-container">
        <table class="participants-table">
            <thead>
            <tr>
            <th>SI.NO</th>
            <th>Chest Number</th>
            <th>Name</th>
            <th>Category</th>
            <th>Event</th>
            <th>Department</th>
            <th>Year</th>
            <th>Result Entry</th>
            <th>Delete</th>
            </tr>
            </thead>
            <?php $count=1;?>
            <tbody>
            <?php foreach($Participants as $athlete):?>
                <tr id="row-<?= $athlete['athlete_id'] . '-' . $athlete['event_id']; ?>">
                    <td><?=htmlspecialchars($count++)?></td>
                     <td><span class="chest-no-tr">
                        <?=htmlspecialchars($athlete['athlete_id'])?>
                    </span></td>
                    <td><?=htmlspecialchars($athlete['first_name'])?><?=" "?><?=htmlspecialchars($athlete['last_name'])?></td>
                    <td><?=htmlspecialchars($athlete['category_name'])?></td>
                    <td><?=htmlspecialchars($athlete['event_name'])?></td>
                    <td><?=htmlspecialchars($athlete['dept_name'])?></td>
                    <td><?=htmlspecialchars($athlete['year'])?></td>
                    <td><button class="result-entry-btn" data-athlete-id="<?=$athlete['athlete_id']?>" data-event-id="<?=$athlete['event_id']?>">
                        Enter Result</button></td>
                     <td><button class="delete-btn" data-athlete-id="<?=$athlete['athlete_id']?>" data-event-id="<?=$athlete['event_id']?>">
                        Delete</button></td>   
                </tr>
            <?php endforeach ;?>
            </tbody>
        </table>
    </div>
            <?php if(isset($_SESSION['result-add-msg'])):?>
                <div class="success-msg"><?=htmlspecialchars($_SESSION['result-add-msg'])?>
            </div>
            <?php unset($_SESSION['result-add-msg'])?>
          <?php endif;?>  
    <div class="result-form-container modal">
        <h3 class="result-form-head">Mark Result</h3>
        <div class="modal-container">
        <form action="" class="result-form" method='post'>
            <input type="hidden" class="athlete-id" name="athleteid">
            <input type="hidden" class="event-id " name="eventid">

            <input type="text" class="athlete-name input-style" name="athelete_name" readonly><br>
            <input type="text" class="event-name input-style" name="event_name" readonly><br>

                <label>Position:</label>
                <select name="position" required>
                <option value="">-- Select Position --</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                </select><br>
                
            <input type="submit" class="submit-btn btns" name="resultadd">
            <button type="button" class="cancel-btn btns">Cancel</button>
        </form>
        </div>
        </div>
</body>
<script src="../assets/js/infoFetch.js"></script>
<script src="../assets/js/messagePopup.js"></script>
<script src="../assets/js/delete.js"></script>
</html>