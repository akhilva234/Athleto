<?php
     require_once "../session_check.php";
    include_once "../nocache.php";
    include "../config.php";
try{

    $results=$pdo->query("SELECT 
    r.position,
    e.event_name,
    a.athlete_id,
    GROUP_CONCAT(CONCAT(a.first_name,'',a.last_name) SEPARATOR ',') AS athlete_name
    FROM results r 
    JOIN athletes a ON r.athlete_id= a.athlete_id
    JOIN events e ON r.event_id=e.event_id
     GROUP BY r.position, e.event_name, a.athlete_id
    ORDER BY r.result_id DESC LIMIT 4");
}catch(PDOException $e ){
    echo "Failed:".$e->getMessage();
}
    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/admin_home.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    <title>Athleto</title>
</head>
<body>
    <h2>Welcome <?=htmlspecialchars($_SESSION['username'])?></h2>
<div class="results-container">
    <h3>Most Recent Results</h3>
    <table>
        <thead>
            <tr>
                <th>Chest No.</th>
                <th>Name</th>
                <th>Event</th>
                <th>Position</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($results as $res): ?>
            <tr>
                <td><?=htmlspecialchars($res['athlete_id'])?></td>
                 <td><?=htmlspecialchars($res['athlete_name'])?></td>
                 <td><?=htmlspecialchars($res['event_name'])?></td>
                 <td><?=htmlspecialchars($res['position'])?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="../assets/js/pageReload.js"></script>
</body>
</html>