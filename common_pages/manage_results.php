<?php
    require_once "../session_check.php";
    include_once "../nocache.php";
    include_once "../config.php";
    $user=$_SESSION['user'];
     $role=$_SESSION['role'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
     <link rel="stylesheet" href="../assets/css/common.css">
      <link rel="stylesheet" href="../assets/css/common_css/tables.css">
      <link rel="stylesheet" href="../assets/css/printReport.css">
</head>
<body data-view="results"  data-user="<?=$role?>">
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
    $results=$results->fetchAll();
    ?>
    <br>
     <div class="filter-print-container">
    <?php 
        $filter_type = "individual"; // or "relay" depending on page
        include "../common_pages/filter.php"; 
    ?>
    <button id="print-btn">🖨️ Print List</button>
    </div>

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
            <th>Course</th>
            <th>Year</th>
            <th class="print-exclude">Verified by</th>
            <th class="print-exclude">Time</th>
            <th class="print-exclude">Certificate</th>
            </tr>
            </thead>
             
            <tbody>
                <?php if (empty($results)): ?>
                <tr>
                <td colspan="8" style="text-align:center; font-weight:bold; color:#555;">
                    No Results found.
                </td>
            </tr>
        <?php else: ?>
                <?php $count=1;?>
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
                    <td class="print-exclude"><?=htmlspecialchars($result['username'])?></td>
                    <td class="print-exclude"><?=htmlspecialchars($result['recorded_at'])?></td>
                     <td class="print-exclude"><button class="result-entry-btn dwnld-btn" data-result-id="<?=$result['result_id']?>"
                      data-athlete-id="<?=$result['athlete_id']?>">
                        Download</button></td>
                </tr>    
            <?php endforeach ;?>
             <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="../assets/js/pageReload.js"></script>
    <script src="../assets/js/certificate.js"></script>
    <script src="../assets/js/printTable.js"></script>
</body>
</html>