<?php
       require_once "../session_check.php";
    include "../config.php";
    $user= $_SESSION['user'];

    $departments=$pdo->query("SELECT * FROM departments");
    $categories=$pdo->query("SELECT * FROM categories");
    $events=$pdo->query("SELECT * FROM events");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/common_css/filter.css">
</head>
<body>
    <span class="filter-icon">
        <i class="fas fa-filter"></i>
    </span>
    <div class="filter-section">
     <div class="dropdown-checkbox" id="deptDropdown">
        <button type="button" class="dropdown-btn">Departments ▼</button>
        <div class="dropdown-content">
             <input type="text" class="dropdown-search" placeholder="Search departments...">
            <?php foreach ($departments as $dept): ?>
                <label>
                    <input type="checkbox" class="dept-checkbox" value="<?= htmlspecialchars($dept['dept_id']) ?>">
                    <?= htmlspecialchars($dept['dept_name']) ?>
                </label><br>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="dropdown-checkbox" id="eventDropdown">
        <button type="button" class="dropdown-btn">Events ▼</button>
        <div class="dropdown-content event-content">
             <input type="text" class="dropdown-search" placeholder="Search events...">
            <?php foreach ($events as $event): ?>
                <label>
                    <input type="checkbox" class="event-checkbox" value="<?= htmlspecialchars($event['event_id']) ?>">
                    <?= htmlspecialchars($event['event_name']) ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="dropdown-checkbox" id="catDropdown">
        <button type="button" class="dropdown-btn">Categories ▼</button>
        <div class="dropdown-content">
             <input type="text" class="dropdown-search" placeholder="Search categories...">
            <?php foreach ($categories as $cat): ?>
                <label>
                    <input type="checkbox" class="cat-checkbox" value="<?= htmlspecialchars($cat['category_id']) ?>">
                    <?= htmlspecialchars($cat['category_name']) ?>
                </label><br>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="search-box">
        <label>
            <input type="text" placeholder="Enter chest no.">
        </label>
    </div>
 </div>
</body>
<script src="../assets/js/filter.js" type="module"></script>
</html>