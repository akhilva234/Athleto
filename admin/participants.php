<?php
    include_once "../config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="../assets/css/common.css">
     <link rel="stylesheet" href="../assets/css/participants.css">
</head>
<body>
    <h2>Participants</h2>
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
    <div class="participants-table-container">
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
            </tr>
            </thead>
            <?php $count=1;?>
            <tbody>
            <?php foreach($Participants as $athlete):?>
                <tr>
                    <td><?=htmlspecialchars($count++)?></td>
                    <td><?=htmlspecialchars($athlete['athlete_id'])?></td>
                    <td><?=htmlspecialchars($athlete['first_name'])?><?=" "?><?=htmlspecialchars($athlete['last_name'])?></td>
                    <td><?=htmlspecialchars($athlete['category_name'])?></td>
                    <td><?=htmlspecialchars($athlete['event_name'])?></td>
                    <td><?=htmlspecialchars($athlete['dept_name'])?></td>
                    <td><?=htmlspecialchars($athlete['year'])?></td>
                    <td><button class="result-entry-btn" data-athlete-id="<?=$athlete['athlete_id']?>" data-event-id="<?=$athlete['event_id']?>">
                        Enter Result</button></td>
                </tr>
            <?php endforeach ;?>
            </tbody>
        </table>
    </div>
    <div class="result-form-container">
        <form action="add_result.php" class="result-form">
            <input type="number" class="athlete-id" name="athleteid">
            <input type="number" class="event-id" name="eventid">

            <input type="text" class="athlete-name" name="athelete_name" readonly><br>
            <input type="text" class="event-name" name="event_name" readonly><br>

                <label>Position:</label>
                <select name="position">
                <option value="">-- Select Position --</option>
                <option value="1">1st</option>
                <option value="2">2nd</option>
                <option value="3">3rd</option>
                </select><br>
                
            <button type="submit" class="submit-btn">Submit</button>
            <button type="button" class="cancel-btn">Cancel</button>
        </form>
    </div>
</body>
<script src="../assets/js/infoFetch.js"></script>
</html>