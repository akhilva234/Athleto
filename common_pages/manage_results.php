<?php
    require_once "../session_check.php";
    include_once "../nocache.php";
    include_once "../config.php";
    $user=$_SESSION['user'];
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
<body data-view="results">
    <h2>Individual Results</h2>
    <?php
    try{
         $results=$pdo->query("SELECT  
    a.athlete_id,   
    a.first_name,
    a.last_name,
    a.year,
    d.dept_name,
    c.category_name,
    e.event_name,
    e.event_id,
    position,
    u.username,
    recorded_at,
    result_id
    FROM results r
    JOIN athletes a ON a.athlete_id=r.athlete_id
    JOIN events e ON e.event_id=r.event_id
    JOIN categories c ON a.category_id=c.category_id
    JOIN departments d ON a.dept_id=d.dept_id
    JOIN users u ON u.user_id=r.added_by");
    }catch(Exception $e){

        echo "Failed:".$e->getMessage();
    }  
    ?>
    <br>
    <?php $filter_type = 'individual'; ?>
     <?php include_once "../common_pages/filter.php";?>
    <div class="result-table-container table-whole-container">
        <table class="result-table">
            <thead>
            <tr>
            <th>SI.NO</th>
            <th>Chest Number</th>
            <th>Name</th>
            <th>Category</th>
            <th>Event</th>
            <th>Position</th>
            <th>Department</th>
            <th>Year</th>
            <th>Verified by</th>
            <th>Time</th>
            <th>Certificate</th>
            </tr>
            </thead>
             <?php $count=1;?>
            <tbody>
                <?php foreach($results as $result):?>
                <tr id="row-<?= $result['athlete_id'] . '-' . $result['event_id']?>">
                    <td><?=htmlspecialchars($count++)?></td>
                    <td><span class="chest-no-tr">
                        <?=htmlspecialchars($result['athlete_id'])?>
                    </span></td>
                    <td><?=htmlspecialchars($result['first_name'])?><?=" "?>
                     <?=htmlspecialchars($result['last_name'])?>
                    </td>
                    <td><?=htmlspecialchars($result['category_name'])?></td>
                    <td><?=htmlspecialchars($result['event_name'])?></td>
                    <td><?=htmlspecialchars($result['position'])?></td>
                    <td><?=htmlspecialchars($result['dept_name'])?></td>
                    <td><?=htmlspecialchars($result['year'])?></td>
                    <td><?=htmlspecialchars($result['username'])?></td>
                    <td><?=htmlspecialchars($result['recorded_at'])?></td>
                     <td><button class="result-entry-btn dwnld-btn" data-result-id="<?=$result['result_id']?>"
                      data-athlete-id="<?=$result['athlete_id']?>">
                        Download</button></td>
                </tr>    
            <?php endforeach ;?>
            </tbody>
        </table>
    </div>
    <script src="../assets/js/pageReload.js"></script>
    <script src="../assets/js/certificate.js"></script>
</body>
</html>