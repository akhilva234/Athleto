<?php
require_once "../session_check.php";
include_once "../nocache.php";
include "../config.php";
$user= $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/css/common_css/tables.css">
<link rel="stylesheet" href="../assets/css/championship.css">
<title>Championships</title>
</head>
<body class="meet-year-filter">

<h2 class="page-heading">Championships</h2><br>

<!-- Year Dropdown -->
<div class="dropdown-checkbox" id="yearDropdown">
    <button type="button" class="dropdown-btn">Select Meet Year ▼</button>
    <div class="dropdown-content">
        <?php
        $startYear = 2023;
        $currentYear = (int)date('Y');
        for ($y = $startYear; $y <= $currentYear; $y++) {
            $display = $y . '-' . ($y + 1);
            $checked = ($y === $currentYear) ? 'checked' : '';
            echo "<label>
                    <input type='radio' name='champYear' class='year-radio' value='$y' $checked> $display
                  </label><br>";
        }
        ?>
    </div>
</div>

<div class="print-controls">
    <button id="print-btn-championship" class="print-btn">🖨️ Print</button>
</div>
<br><br>
<!-- Team Championship -->
<h3>Team Championship</h3>
<div class="participants-table-container table-whole-container">
<table class="participants-table athletes-table department-table">
    <thead>
        <tr>
            <th>SI.No</th>
            <th>Department</th>
            <th>Total Points</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
</div>

<!-- Category Championship -->
<h3>Category Championship</h3><br>

<h4>Men Championship</h4>
<div class="participants-table-container table-whole-container">
<table class="participants-table athletes-table category-table male">
    <thead>
        <tr>
            <th>SI.No</th>
            <th>Category</th>
            <th>Name</th>
            <th>Department</th>
            <th>Total points</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
</div>

<h4>Women Championship</h4>
<div class="participants-table-container table-whole-container">
<table class="participants-table athletes-table category-table female">
    <thead>
        <tr>
            <th>SI.No</th>
            <th>Category</th>
            <th>Name</th>
            <th>Department</th>
            <th>Total points</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
</div>

<!-- Individual Championship -->
<h3>Individual Championship</h3>
<div class="participants-table-container table-whole-container">
<table class="participants-table athletes-table individual">
    <thead>
        <tr>
            <th>SI.No</th>
            <th>Chest No</th>
            <th>Name</th>
            <th>Department</th>
            <th>Total Points</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
</div>

<script src="../assets/js/pageReload.js"></script>
<script src="../assets/js/printTable.js"></script>
<script src="../assets/js/championshipFiltering.js"></script>
</body>
</html>
