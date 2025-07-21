<?php

      require_once "../session_check.php";
    include "../config.php";
    $user= $_SESSION['user'];

    $athletes=$pdo->query("SELECT 
    a.athlete_id,
    a.first_name,
    a.last_name,
    a.year,
    c.category_name,
    d.dept_name
    FROM athletes a
    JOIN categories c ON a.category_id=c.category_id
    JOIN departments d ON a.dept_id=d.dept_id 
    ORDER BY a.athlete_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/common_css/tables.css">
</head>
<body>
 <div class="participants-table-container table-whole-container">
        <table class="participants-table">
            <thead>
            <tr>
            <th>SI.NO</th>
            <th>Chest Number</th>
            <th>Name</th>
            <th>Category</th>
            <th>Department</th>
            <th>Year</th>
            </tr>
            </thead>
            <?php $count=1;?>
            <tbody>
            <?php foreach($athletes as $athlete):?>
                <tr>
                    <td><?=htmlspecialchars($count++)?></td>
                     <td><span class="chest-no-tr">
                        <?=htmlspecialchars($athlete['athlete_id'])?>
                    </span></td>
                    <td><?=htmlspecialchars($athlete['first_name'])?><?=" "?><?=htmlspecialchars($athlete['last_name'])?></td>
                    <td><?=htmlspecialchars($athlete['category_name'])?></td>
                    <td><?=htmlspecialchars($athlete['dept_name'])?></td>
                    <td><?=htmlspecialchars($athlete['year'])?></td>
                 </tr>   
            <?php endforeach ;?>
            </tbody>
        </table>
    </div>
</body>
</html>