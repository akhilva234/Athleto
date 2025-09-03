<?php

     require_once "../session_check.php";
    include_once "../nocache.php";
    include "../config.php";

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['template_image']) && $_FILES['template_image']['error'] === UPLOAD_ERR_OK) {
        
        // Path relative to this Athleto/Athleto/ folder
        $targetDir = __DIR__ . "/../uploads/templates/";  // absolute path on server
        $dbPath    = "uploads/templates/";             // relative path for DB
        
        // Ensure folder exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // Unique file name
        $fileName = time() . "_" . basename($_FILES['template_image']['name']);
        $targetFilePath = $targetDir . $fileName;  // full server path
        $dbFilePath     = $dbPath . $fileName;     // relative path stored in DB

        // Allowed extensions
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['template_image']['tmp_name'], $targetFilePath)) {
                // Save relative path in DB
                $stmt = $pdo->prepare("INSERT INTO certificate_templates (template_name, file_path) VALUES (?, ?)");
                $stmt->execute([$_POST['template_name'], $dbFilePath]);
                $message = "Template uploaded successfully!";
            } else {
                $message = "Error uploading the file.";
            }
        } else {
            $message = "Only JPG, JPEG, PNG files are allowed.";
        }
    } else {
        $message = "Please select a file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Certificate Template</title>
    <link rel="stylesheet" href="../assets/css/upload.css">
</head>
<body>

<div class="upload-form">
    <h2>Upload Certificate Template</h2>

    <?php if ($message): ?>
        <p class="message <?= strpos(strtolower($message), 'success') !== false ? 'success' : 'error' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <label for="template_name">Template Name:</label>
        <input type="text" id="template_name" name="template_name" required>

        <label for="template_image">Select Template Image:</label>
        <input type="file" id="template_image" name="template_image" accept=".jpg,.jpeg,.png" required>

        <button type="submit">Upload</button>
    </form>
</div>

</body>
</html>

