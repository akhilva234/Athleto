<?php
require_once "../session_check.php";
include_once "../nocache.php";
include "../config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['template_name']) || empty($_FILES['template_image']['name'])) {
        $_SESSION['message'] = "Failed: Required fields cannot be empty.";
        header("Location: adm_dashboard.php?page=add_template&status=failure");
        exit();
    }

    if ($_FILES['template_image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = __DIR__ . "/../uploads/templates/";
        $dbPath    = "uploads/templates/";

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName       = time() . "_" . basename($_FILES['template_image']['name']);
        $targetFilePath = $targetDir . $fileName;
        $dbFilePath     = $dbPath . $fileName;

        $fileType     = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['template_image']['tmp_name'], $targetFilePath)) {
                $stmt = $pdo->prepare("INSERT INTO certificate_templates (template_name, file_path) VALUES (?, ?)");
                $stmt->execute([$_POST['template_name'], $dbFilePath]);

                $_SESSION['message'] = "Template uploaded successfully!";
                header("Location: adm_dashboard.php?page=add_template&status=success");
                exit();
            } else {
                $_SESSION['message'] = "Error uploading the file.";
                header("Location: adm_dashboard.php?page=add_template&status=failure");
                exit();
            }
        } else {
            $_SESSION['message'] = "Only JPG, JPEG, PNG files are allowed.";
            header("Location: adm_dashboard.php?page=add_template&status=failure");
            exit();
        }
    } else {
        $_SESSION['message'] = "Please select a valid file.";
        header("Location: adm_dashboard.php?page=add_template&status=failure");
        exit();
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

          <?php if (isset($_SESSION['message'])): ?>
        <p class="message <?= ($_GET['status'] ?? '') === 'success' ? 'success' : 'error' ?>">
            <?= htmlspecialchars($_SESSION['message']); ?>
        </p>
        <?php unset($_SESSION['message']); ?>
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

