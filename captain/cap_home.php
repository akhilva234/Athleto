<?php
     require_once "../session_check.php";
    include_once "../nocache.php";
    include "../config.php";

    if(isset($_POST['update'])){
        require './update_user.php';
    }
       $message = '';

    if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
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
    ORDER BY r.result_id DESC LIMIT 3");

    $standings=$pdo->query(" SELECT 
            d.dept_name,
            SUM(CASE r.position
                WHEN 1 THEN 5
                WHEN 2 THEN 3
                WHEN 3 THEN 1
                ELSE 0
            END) AS total_points
        FROM results r
        JOIN athletes a ON r.athlete_id = a.athlete_id
        JOIN departments d ON a.dept_id = d.dept_id
        GROUP BY d.dept_id
        ORDER BY total_points DESC LIMIT 3
    ");
    $ranks=$standings->fetchAll();

    $sql=$pdo->query("SELECT 
                 a.athlete_id,
                CONCAT(a.first_name,'',a.last_name) AS athlete_name,
                d.dept_name,
                SUM(CASE r.position
                    WHEN 1 THEN 5
                    WHEN 2 THEN 3
                    WHEN 3 THEN 1
                    ELSE 0
                END) AS total_points
            FROM results r
            JOIN athletes a ON r.athlete_id = a.athlete_id
            JOIN departments d ON a.dept_id=d.dept_id
            GROUP BY a.athlete_id
            ORDER BY total_points DESC LIMIT 3
    ");

    $athletes=$sql->fetchAll();

    $users=$pdo->query("SELECT * FROM users WHERE role!='admin'");

}catch(PDOException $e ){
    echo "Failed:".$e->getMessage();
}
    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="../assets/js/toast.js"></script>
    <link rel="stylesheet" href="../assets/css/admin_home.css">
    <link rel="stylesheet" href="../assets/css/update_user_form.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    <title>Athleto</title>
</head>
<body>
    <h2>Welcome</h2>
    <div class="container-wrapper">
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
    <div class="results-container standings-container">
    <h3>Current Standings</h3>
    <table>
        <thead>
            <tr>
                <th>SI.No</th>
                <th>Department</th>
                <th>Points</th>
            </tr>
        </thead>
        <?php $count=1;?>
        <tbody>
            <?php foreach($ranks as $rank): ?>
            <tr>
                <td><?=htmlspecialchars($count++)?></td>
                <td><?=htmlspecialchars($rank['dept_name'])?></td>
                <td><?=htmlspecialchars($rank['total_points'])?></td>
            </tr>
          <?php endforeach;?>  
        </tbody>
    </table>
</div>
  <div class="athletes-container results-container">
    <h3>Current Athlete Standings</h3>
    <table>
        <thead>
            <tr>
                <th>SI.No</th>
                <th>Athlete</th>
                <th>Department Name</th>
                <th>Points</th>
            </tr>
        </thead>
        <?php $count=1;?>
        <tbody>
            <?php foreach($athletes as $athlete): ?>
            <tr>
                <td><?=htmlspecialchars($count++)?></td>
                <td><?=htmlspecialchars($athlete['athlete_name'])?></td>
                <td><?=htmlspecialchars($athlete['dept_name'])?></td>
                <td><?=htmlspecialchars($athlete['total_points'])?></td>
            </tr>
          <?php endforeach;?>  
        </tbody>
    </table>
</div>
    </div>
  <div id="editUserModal" class="modal"></div>
<script src="../assets/js/pageReload.js"></script>
<script src="../assets/js/deleteuser.js"></script>
</body>
<script src="../assets/js/userinfoFetch.js"></script>
</html>