<?php
ob_clean();

require_once "../config.php";

if (!isset($_GET['result_id'])) {
    http_response_code(400);
    exit("Missing result_id");
}

$resultId = (int)$_GET['result_id'];

// Example: fetch athlete details based on result_id
$stmt = $pdo->prepare(" SELECT 
    CONCAT(a.first_name,'',a.last_name)AS athlete_name,
     d.dept_name, e.event_name, r.position
    FROM results r
    JOIN athletes a ON r.athlete_id = a.athlete_id
    JOIN departments d ON a.dept_id = d.dept_id
    JOIN events e ON r.event_id = e.event_id
    WHERE r.result_id = ?
");
$stmt->execute([$resultId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(404);
    exit("Result not found");
}

// Assign variables
$athleteName = $row['athlete_name'];
$department  = $row['dept_name'];
$event       = $row['event_name'];
$position    = $row['position'];

// Fetch latest certificate template
$stmt = $pdo->query("SELECT file_path FROM certificate_templates ORDER BY template_id DESC LIMIT 1");
$template = $stmt->fetchColumn();

if (!$template) {
    http_response_code(500);
    exit("No template found");
}

// Load certificate template
$image = imagecreatefrompng("../" . $template);
$black = imagecolorallocate($image, 0, 0, 0);
$fontPath = __DIR__ . "/../assets/fonts/arial.ttf";

// Add text (coordinates matched to template)
imagettftext($image, 28, 0, 650, 450, $black, $fontPath, $athleteName);
imagettftext($image, 19, 0, 600, 510, $black, $fontPath, $department);
imagettftext($image, 22, 0, 1090, 510, $black, $fontPath, $position);
imagettftext($image, 22, 0, 300, 580, $black, $fontPath, $event);

// Output certificate
header("Content-Type: image/png");
header("Content-Disposition: attachment; filename=\"" . str_replace(' ', '_', $athleteName) . "_certificate.png\"");
imagepng($image);
imagedestroy($image);
?>
