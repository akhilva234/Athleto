<?php
      require_once "../session_check.php";
        include "../config.php";
        $user= $_SESSION['user'];

        //$pointsMap = [1 => 5, 2 => 3, 3 => 1];

        $sql = "
            SELECT 
                a.athlete_id,
                a.first_name,
                a.last_name,
                SUM(CASE r.position
                    WHEN 1 THEN 5
                    WHEN 2 THEN 3
                    WHEN 3 THEN 1
                    ELSE 0
                END) AS total_points
            FROM results r
            JOIN athletes a ON r.athlete_id = a.athlete_id
            GROUP BY a.athlete_id
            ORDER BY total_points DESC
            LIMIT 1
        ";
        $stmt = $pdo->query($sql);
        $topAthlete = $stmt->fetch();

            $sql = "
        SELECT 
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
        ORDER BY total_points DESC
    ";
    $stmt = $pdo->query($sql);
    $deptChampions = $stmt->fetchAll();


        $sql = "
        SELECT 
            c.category_name,
            a.athlete_id,
            a.first_name,
            a.last_name,
            SUM(CASE r.position
                WHEN 1 THEN 5
                WHEN 2 THEN 3
                WHEN 3 THEN 1
                ELSE 0
            END) AS total_points
        FROM results r
        JOIN athletes a ON r.athlete_id = a.athlete_id
        JOIN categories c ON a.category_id = c.category_id
        GROUP BY c.category_id, a.athlete_id
        ORDER BY c.category_id, total_points DESC
    ";
    $stmt = $pdo->query($sql);
    $categoryWise = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/common_css/tables.css">
</head>
<body>
    <h2>Championships</h2><br>
    <h4>Team Championship</h4>
    <div class="participants-table-container table-whole-container">
    <table  class="participants-table athletes-table">
        <thead>
            <tr>
            <th>SI.No</th>
            <th>Department</th>
            <th>Total Points</th>
            </tr>
        </thead>
         <?php $count=1;?>
        <tbody>
            <?php foreach($deptChampions as $dept): ?>
                <tr>
                     <td><?=htmlspecialchars($count++)?></td>
                     <td><?=htmlspecialchars($dept['dept_name'])?></td>
                     <td><?=htmlspecialchars($dept['total_points'])?></td>
                </tr>
                <?php endforeach;?>
        </tbody>
    </table>
   </div> 
</body>
</html>