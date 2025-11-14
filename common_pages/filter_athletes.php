<?php
require_once "../session_check.php";
include "../config.php";

$user = $_SESSION['user'];

//$view = $_GET['view'] ?? 'participants';
$dept   = isset($_GET['dept'])      && $_GET['dept']      !== '' ? explode(',', $_GET['dept'])      : [];
$events = isset($_GET['event'])     && $_GET['event']     !== '' ? explode(',', $_GET['event'])     : [];
$cat    = isset($_GET['category'])  && $_GET['category']  !== '' ? explode(',', $_GET['category'])  : [];
$year     = isset($_GET['year'])      && $_GET['year']      !== '' ? explode(',', $_GET['year'])      : [];
$chest_no = isset($_GET['chest_no']) && $_GET['chest_no'] !== '' ? trim($_GET['chest_no']) : '';
$view = isset($_GET['view']) ? $_GET['view'] : '';

$where  = [];
$params = [];


try{
        if ($view === 'athletes') {
    // Show ALL athletes (individual + relay)

    if (!empty($dept)) {
        $in = implode(',', array_fill(0, count($dept), '?'));
        $where[] = "d.dept_id IN ($in)";
        $params = array_merge($params, $dept);
    }

    if (!empty($cat)) {
        $in = implode(',', array_fill(0, count($cat), '?'));
        $where[] = "c.category_id IN ($in)";
        $params = array_merge($params, $cat);
    }

    
    if (!empty($year)) {
        $in = implode(',', array_fill(0, count($year), '?'));
        $where[] = "combined.meet_year IN ($in)";
        $params = array_merge($params, $year);
    }
    if ($chest_no !== '') {
        $where[] = "a.athlete_id LIKE ?";
        $params[] = "%$chest_no%";
    }

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $sql = "
    SELECT DISTINCT
        a.athlete_id,
        a.first_name,
        a.last_name,
        a.year,
        d.dept_name,
        c.category_name,
        combined.meet_year,
        CASE 
            WHEN p.athlete_id IS NOT NULL AND rt.team_id IS NOT NULL THEN 'Both'
            WHEN p.athlete_id IS NOT NULL THEN 'Individual'
            WHEN rt.team_id IS NOT NULL THEN 'Relay'
            ELSE 'None'
        END AS participation_type
    FROM athletes a
    JOIN departments d ON a.dept_id = d.dept_id
    JOIN categories c ON a.category_id = c.category_id

    LEFT JOIN participation p ON p.athlete_id = a.athlete_id
    LEFT JOIN relay_team_members rtm ON rtm.athlete_id = a.athlete_id
    LEFT JOIN relay_teams rt ON rt.team_id = rtm.team_id

    LEFT JOIN (
        SELECT athlete_id, meet_year FROM participation
        UNION
        SELECT rtm.athlete_id, rt.meet_year
        FROM relay_teams rt
        JOIN relay_team_members rtm ON rtm.team_id = rt.team_id
    ) AS combined ON combined.athlete_id = a.athlete_id

    $whereSql
    ORDER BY a.athlete_id
";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} elseif ($view === 'participants') {
    // Show ONLY individual event participants (no relay)

    if (!empty($dept)) {
        $in = implode(',', array_fill(0, count($dept), '?'));
        $where[] = "d.dept_id IN ($in)";
        $params = array_merge($params, $dept);
    }

    if (!empty($cat)) {
        $in = implode(',', array_fill(0, count($cat), '?'));
        $where[] = "c.category_id IN ($in)";
        $params = array_merge($params, $cat);
    }
    if (!empty($events)) {
        $in = implode(',', array_fill(0, count($events), '?'));
        $where[] = "e.event_id IN ($in)";
        $params = array_merge($params, $events);
    }

    if (!empty($year)) {
        $in = implode(',', array_fill(0, count($year), '?'));
        $where[] = "p.meet_year IN ($in)";
        $params = array_merge($params, $year);
    }

    if ($chest_no !== '') {
        $where[] = "a.athlete_id LIKE ?";
        $params[] = "%$chest_no%";
    }

    $where[] = "e.is_relay = 0";
    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $sql = "
        SELECT DISTINCT
            a.athlete_id,
            a.first_name,
            a.last_name,
            a.year,
            d.dept_name,
            c.category_name,
            e.event_name,
            p.meet_year,
            e.event_id
        FROM participation p
        JOIN athletes a ON a.athlete_id = p.athlete_id
        JOIN events e ON e.event_id = p.event_id
        JOIN departments d ON a.dept_id = d.dept_id
        JOIN categories c ON a.category_id = c.category_id
        $whereSql
        ORDER BY a.athlete_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    
} elseif($view === 'relays'){
    if (!empty($dept)) {
        $in = implode(',', array_fill(0, count($dept), '?'));
        $where[] = "d.dept_id IN ($in)";
        $params = array_merge($params, $dept);
    }
    if (!empty($events)) {
        $in = implode(',', array_fill(0, count($events), '?'));
        $where[] = "e.event_id IN ($in)";
        $params = array_merge($params, $events);
    }
    if (!empty($cat)) {
    $in = implode(',', array_fill(0, count($cat), '?'));
    $where[] = "c.category_id IN ($in)";
    $params = array_merge($params, $cat);
    }
    if ($chest_no !== '') {
        $where[] = "rt.team_id LIKE ?";
        $params[] = "%$chest_no%";
    }


     if (!empty($year)) {
         $in = implode(',', array_fill(0, count($year), '?'));
         $where[] = "rt.meet_year IN ($in)";
         $params = array_merge($params, $year);
     }

    $where[] = "e.is_relay = 1";

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $sql = "
    SELECT 
        rt.team_id,
        e.event_name,
        e.event_id,
        GROUP_CONCAT(a.first_name, ' ', a.last_name) as team_members,
        d.dept_name,
        c.category_name,
        rt.meet_year
    FROM relay_teams rt
    JOIN events e ON e.event_id = rt.event_id
    JOIN relay_team_members rtm ON rt.team_id = rtm.team_id
    JOIN athletes a ON a.athlete_id = rtm.athlete_id
    JOIN departments d ON a.dept_id = d.dept_id
    JOIN categories c ON rt.category_id=c.category_id
    $whereSql
    GROUP BY rt.team_id
    ORDER BY rt.team_id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
else{

    echo json_encode("Invalid view selected..");
}
    
}catch(Exception $e){

    echo json_encode($e->getMessage());

}

