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
            ORDER BY total_points DESC
        ";
        $stmt = $pdo->query($sql);
        $topAthlete = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            d.dept_name,
            SUM(CASE r.position
                WHEN 1 THEN 5
                WHEN 2 THEN 3
                WHEN 3 THEN 1
                ELSE 0
            END) AS total_points
        FROM results r
        JOIN athletes a ON r.athlete_id = a.athlete_id
        JOIN categories c ON a.category_id = c.category_id
        JOIN departments d ON a.dept_id=d.dept_id
        GROUP BY c.category_id, a.athlete_id
        ORDER BY c.category_id, total_points DESC
    ";
    $stmt = $pdo->query($sql);
    $categoryWise = $stmt->fetchAll();

            $maleChampions = [];
        $femaleChampions = [];

        foreach ($categoryWise as $cat) {
            $gender = strtolower(trim($cat['category_name']));
            if ($gender === 'men') {
                $maleChampions[] = $cat;
            } elseif ($gender === 'women') {
                $femaleChampions[] = $cat;
            }
        }
      
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
    <h3>Team Championship</h3>
    <div class="participants-table-container table-whole-container">
    <table  class="participants-table athletes-table department-table">
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
   <br>
   <h3>Category Championship</h3><br>
   <h4>Men Championship</h4>
    <div class="participants-table-container table-whole-container">
    <table  class="participants-table athletes-table category-table">
        <thead>
            <tr>
            <th>SI.No</th>
            <th>Category</th>
            <th>Name</th>
            <th>Department</th>
            <th>Total points</th>
            </tr>
        </thead>
         <?php $count=1;?>
        <tbody>
            <?php foreach($maleChampions as $cat): ?>
                <tr>
                     <td><?=htmlspecialchars($count++)?></td>
                     <td><?=htmlspecialchars($cat['category_name'])?></td>
                      <td><?=htmlspecialchars($cat['first_name'])?><?=" "?><?=htmlspecialchars($cat['last_name'])?></td>
                       <td><?=htmlspecialchars($cat['dept_name'])?></td>
                     <td><?=htmlspecialchars($cat['total_points'])?></td>
                </tr>
                <?php endforeach;?>
        </tbody>
    </table>
   </div> 
   <br>
   <h4>Women Championship</h4>
   <div class="participants-table-container table-whole-container">
    <table  class="participants-table athletes-table category-table">
        <thead>
            <tr>
            <th>SI.No</th>
            <th>Category</th>
            <th>Name</th>
            <th>Department</th>
            <th>Total points</th>
            </tr>
        </thead>
         <?php $count=1;?>
        <tbody>
            <?php foreach($femaleChampions as $cat): ?>
                <tr>
                     <td><?=htmlspecialchars($count++)?></td>
                     <td><?=htmlspecialchars($cat['category_name'])?></td>
                      <td><?=htmlspecialchars($cat['first_name'])?><?=" "?><?=htmlspecialchars($cat['last_name'])?></td>
                       <td><?=htmlspecialchars($cat['dept_name'])?></td>
                     <td><?=htmlspecialchars($cat['total_points'])?></td>
                </tr>
                <?php endforeach;?>
        </tbody>
    </table>
   </div> 
   <br>
    <h3>Individual Championship</h3>
    <div class="participants-table-container table-whole-container">
    <table  class="participants-table athletes-table ">
        <thead>
            <tr>
            <th>SI.No</th>
            <th>Chest No</th>
            <th>Name</th>
            <th>Department</th>
            <th>Total Points</th>
            </tr>
        </thead>
         <?php $count=1;?>
        <tbody>
            <?php foreach($topAthlete as $athlete): ?>
                <tr>
                     <td><?=htmlspecialchars($count++)?></td>
                      <td><?=htmlspecialchars($athlete['athlete_id'])?></td>
                       <td><?=htmlspecialchars($athlete['first_name'])?><?=" "?><?=htmlspecialchars($athlete['last_name'])?></td>
                     <td><?=htmlspecialchars($athlete['dept_name'])?></td>
                     <td><?=htmlspecialchars($athlete['total_points'])?></td>
                </tr>
                <?php endforeach;?>
        </tbody>
    </table>
   </div> 
</body>
</html>