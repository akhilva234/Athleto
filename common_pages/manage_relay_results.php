<?php
    require_once "../session_check.php";
   include_once "../nocache.php";
    include_once "../config.php";
    $user=$_SESSION['user'];

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
    <title></title>
     <link rel="stylesheet" href="../assets/css/common.css">
      <link rel="stylesheet" href="../assets/css/common_css/tables.css">
</head>
<body data-view="relayResults">
    <h2>Relay Results</h2>
    <?php
    try{
         $results=$pdo->query("SELECT 
        rt.team_id,
        d.dept_name,
        c.category_name,
        e.event_name,
        e.event_id,
        GROUP_CONCAT(CONCAT(a.first_name,' ',a.last_name) SEPARATOR ', ') AS athletes,
        r.position,
        u.username,
        r.recorded_at
    FROM results r
    JOIN relay_teams rt ON rt.team_id = r.relay_team_id
    JOIN relay_team_members rtm ON rtm.team_id = rt.team_id
    JOIN athletes a ON a.athlete_id = rtm.athlete_id
    JOIN events e ON e.event_id = r.event_id
    JOIN categories c ON rt.category_id = c.category_id
    JOIN departments d ON rt.dept_id = d.dept_id
    JOIN users u ON u.user_id = r.added_by
        GROUP BY rt.team_id
    ");
    }catch(Exception $e){

        echo "Failed:".$e->getMessage();
    }  
    ?>
    <br>
    <?php $filter_type = 'relay'; ?>
    <?php include_once '../common_pages/filter.php' ;?>
    <div class="result-table-container table-whole-container">
        <table class="result-table">
            <thead>
            <tr>
            <th>SI.NO</th>
            <th>Chest Number</th>
            <th>Department</th>
            <th>Athletes</th>
            <th>Category</th>
            <th>Event</th>
            <th>Position</th>
            <th>Verified by</th>
            <th>Time</th>
            </tr>
            </thead>
             <?php $count=1;?>
            <tbody>
                <?php foreach($results as $result):?>
                      <tr id="row-<?= $result['team_id'] . '-' . $result['event_id']?>">
                    <td><?=htmlspecialchars($count++)?></td>
                    <td><span class="chest-no-tr">
                        <?=htmlspecialchars($result['team_id'])?>
                    </span></td>
                    <td><?=htmlspecialchars($result['dept_name'])?></td>
                    <td><?=htmlspecialchars($result['athletes'])?></td>
                    <td><?=htmlspecialchars($result['category_name'])?></td>
                    <td><?=htmlspecialchars($result['event_name'])?></td>
                    <td><?=htmlspecialchars($result['position'])?></td>
                    <td><?=htmlspecialchars($result['username'])?></td>
                    <td><?=htmlspecialchars($result['recorded_at'])?></td>
                </tr>    
            <?php endforeach ;?>
            </tbody>
        </table>
    </div>
    <script src="../assets/js/pageReload.js"></script>
</body>
<script src="../assets/js/infoFetch.js" type="module"></script>
</html>