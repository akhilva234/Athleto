<?php
    include_once "../config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="../assets/css/common.css">
</head>
<body>
    <h2>Participants</h2>
    <?php
        $Participants=$pdo->query("SELECT part_id,
        athletes.first_name,
        athletes.last_name,
        athletes.athlete_id,
        categories.category_name,
        events.event_name,
        departments.dept_name,
        athletes.year
        FROM participation
        JOIN athletes ON participation.athlete_id=athletes.athlete_id
        JOIN categories ON athletes.category_id=categories.category_id
        JOIN events ON participation.event_id=events.event_id
        JOIN departments ON athletes.dept_id=departments.dept_id
        ORDER BY part_id");
    ?>
    <div class="participants-table-container">
        <table class="participants-table">
            <tr>
            <th>SI.NO</th>
            <th>Chest Number</th>
            <th>Name</th>
            <th>Category</th>
            <th>Event</th>
            <th>Department</th>
            <th>Year</th>
            </tr>
            <?php foreach($Participants as $athlete):?>
                <tr>
                    <td><?=htmlspecialchars($athlete['part_id'])?></td>
                    <td><?=htmlspecialchars($athlete['athlete_id'])?></td>
                    <td><?=htmlspecialchars($athlete['first_name'])?><?=htmlspecialchars($athlete['last_name'])?></td>
                    <td><?=htmlspecialchars($athlete['category_name'])?></td>
                    <td><?=htmlspecialchars($athlete['event_name'])?></td>
                    <td><?=htmlspecialchars($athlete['dept_name'])?></td>
                    <td><?=htmlspecialchars($athlete['year'])?></td>
                </tr>
            <?php endforeach ;?>
        </table>
    </div>
</body>
</html>