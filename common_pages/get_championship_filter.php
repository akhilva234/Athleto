<?php
require_once "../session_check.php";
include "../config.php";

$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

try {
    // --- Team Championship ---
    $sql = "
        SELECT 
            d.dept_name,
            r.meet_year,
            SUM(CASE r.position
                WHEN 1 THEN 5
                WHEN 2 THEN 3
                WHEN 3 THEN 1
                ELSE 0
            END) AS total_points
        FROM results r
        JOIN athletes a ON r.athlete_id = a.athlete_id
        JOIN departments d ON a.dept_id = d.dept_id
        WHERE r.meet_year = ?
        GROUP BY d.dept_id
        ORDER BY total_points DESC LIMIT 3
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$year]);
    $deptChampions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // --- Category Championship ---
    $sql = " SELECT *
    FROM (
        SELECT 
            c.category_name,
            c.category_id,
            a.athlete_id,
            a.first_name,
            a.last_name,
            d.dept_name,
            r.meet_year,
            SUM(CASE r.position
                WHEN 1 THEN 5
                WHEN 2 THEN 3
                WHEN 3 THEN 1
                ELSE 0
            END) AS total_points,
            ROW_NUMBER() OVER (PARTITION BY c.category_id ORDER BY SUM(CASE r.position
                WHEN 1 THEN 5
                WHEN 2 THEN 3
                WHEN 3 THEN 1 ELSE 0 END) DESC) AS rn
        FROM results r
        JOIN athletes a ON r.athlete_id = a.athlete_id
        JOIN categories c ON a.category_id = c.category_id
        JOIN departments d ON a.dept_id = d.dept_id
        WHERE r.meet_year = ?
        GROUP BY c.category_id, a.athlete_id
    ) sub
    WHERE rn <= 3
    ORDER BY category_id, total_points DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$year]);
    $categoryWise = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $maleChampions = [];
    $femaleChampions = [];
    foreach ($categoryWise as $cat) {
        if ($cat['category_id'] == 1) {
            $maleChampions[] = $cat;
        } elseif ($cat['category_id'] == 2) {
            $femaleChampions[] = $cat;
        }
    }

    // --- Individual Championship ---
    $sql = "
        SELECT 
                a.athlete_id,
                a.first_name,
                a.last_name,
                d.dept_name,
                r.meet_year,
                SUM(CASE r.position
                    WHEN 1 THEN 5
                    WHEN 2 THEN 3
                    WHEN 3 THEN 1
                    ELSE 0
                END) AS total_points
            FROM results r
            JOIN athletes a ON r.athlete_id = a.athlete_id
            JOIN departments d ON a.dept_id=d.dept_id
            WHERE r.meet_year = ?
            GROUP BY a.athlete_id
            ORDER BY total_points DESC LIMIT 3
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$year]);
    $topAthlete = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output JSON
    echo json_encode([
        'deptChampions' => $deptChampions,
        'maleChampions' => $maleChampions,
        'femaleChampions' => $femaleChampions,
        'topAthlete' => $topAthlete
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
