
<?php
require_once "../session_check.php";
include "../config.php";

$user = $_SESSION['user'];

$dept     = isset($_GET['dept'])      && $_GET['dept']      !== '' ? explode(',', $_GET['dept'])      : [];
$events   = isset($_GET['event'])     && $_GET['event']     !== '' ? explode(',', $_GET['event'])     : [];
$cat      = isset($_GET['category'])  && $_GET['category']  !== '' ? explode(',', $_GET['category'])  : [];
$chest_no = isset($_GET['chest_no'])  && $_GET['chest_no']  !== '' ? trim($_GET['chest_no'])          : '';

$where  = [];
$params = [];

try {
    // Filters
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
        $where[] = "a.athlete_id LIKE ?";
        $params[] = "%$chest_no%";
    }

    $where[] = "e.is_relay = 0";

    $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $sql = "
    SELECT 
        r.result_id,
        r.position, 
        r.recorded_at,
        a.athlete_id,
        a.first_name,
        a.last_name,
        a.year,
        d.dept_name,
        c.category_name,
        e.event_id,
        e.event_name,
        u.username
    FROM results r
    JOIN athletes a ON a.athlete_id = r.athlete_id
    JOIN events e   ON e.event_id   = r.event_id
    JOIN departments d ON a.dept_id = d.dept_id
    JOIN categories c  ON a.category_id = c.category_id
    JOIN users u ON u.user_id=r.added_by
    $whereSql
    ORDER BY a.athlete_id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

} catch (Exception $e) {
    echo json_encode($e->getMessage());
}

?>