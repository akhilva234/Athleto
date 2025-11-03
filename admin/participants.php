<?php
    require_once "../session_check.php";
   include_once "../nocache.php";
     if(isset($_POST['resultadd'])){
                require '../common_pages/add_result.php';
            }
    include_once "../config.php";
    $user=$_SESSION['user'];
    $role=$_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="../assets/js/toast.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="../assets/css/common.css">
     <link rel="stylesheet" href="../assets/css/common_css/tables.css">
     <link rel="stylesheet" href="../assets/css/result_form.css">
     <link rel="stylesheet" href="../assets/css/printReport.css">
</head>
<body data-view="participants" data-user="<?=$role?>">
    <div class="whole-blur-container"></div>
    <h2>Individual Participants</h2>
    <?php
        $Participants=$pdo->query("SELECT  
    a.athlete_id,   
    a.first_name,
    a.last_name,
    a.year,
    d.dept_name,
    c.category_name,
    e.event_name,
    e.event_id
FROM 
    athletes a
JOIN 
    departments d ON a.dept_id = d.dept_id
JOIN 
    categories c ON a.category_id = c.category_id
JOIN 
    participation p ON a.athlete_id = p.athlete_id
JOIN 
    events e ON p.event_id = e.event_id
 WHERE e.is_relay=0   
ORDER BY 
    a.athlete_id
");

$Participants=$Participants->fetchAll();
    ?>
    <br>
   <div class="filter-print-container">
    <?php 
        $filter_type = "individual"; // or "relay" depending on page
        include "../common_pages/filter.php"; 
    ?>
    <?php if($role!=='captain'):?>
    <button id="print-btn">🖨️ Print List</button>
    <?php endif;?>
    </div>

    <div class="participants-table-container table-whole-container">
        <table class="participants-table">
            <thead>
            <tr>
            <th>SI.NO</th>
            <th>Chest Number</th>
            <th>Name</th>
            <th>Category</th>
            <th>Event</th>
            <th>Course</th>
            <th>Year</th>
            <?php if($role!=='captain'):?>  
            <th class="print-exclude">Result Entry</th>
              <?php endif;?>
            </tr>
            </thead>
            <tbody>
                <?php if (empty($Participants)): ?>
                <tr>
                <td colspan="8" style="text-align:center; font-weight:bold; color:#555;">
                    No Participants found.
                </td>
            </tr>
        <?php else: ?>
            <?php $count=1;?>
            <?php foreach($Participants as $athlete):?>
                <tr id="row-<?= $athlete['athlete_id'] . '-' . $athlete['event_id']?>">
                    <td><?=htmlspecialchars($count++)?></td>
                     <td><span class="chest-no-tr">
                        <?=htmlspecialchars($athlete['athlete_id'])?>
                    </span></td>
                    <td><?=htmlspecialchars($athlete['first_name'])?><?=" "?><?=htmlspecialchars($athlete['last_name'])?></td>
                    <td><?=htmlspecialchars($athlete['category_name'])?></td>
                    <td><?=htmlspecialchars($athlete['event_name'])?></td>
                    <td><?=htmlspecialchars($athlete['dept_name'])?></td>
                    <td><?=htmlspecialchars($athlete['year'])?></td>
                  <?php if($role!=='captain'):?>  
                    <td class="print-exclude"><button class="result-entry-btn" data-athlete-id="<?=$athlete['athlete_id']?>" data-event-id="<?=$athlete['event_id']?>">
                        Enter Result</button></td>  
                  <?php endif;?>       
                </tr>
            <?php endforeach ;?>
             <?php endif;?> 
            </tbody>
        </table>
    </div>
    <script>
          <?php if(isset($_SESSION['result-add-msg'])):?>
            <?php if (strpos($_SESSION['result-add-msg'], 'Failed') !== false || strpos($_SESSION['result-add-msg'], 'Invalid') !== false): ?>
        toastr.error(<?= json_encode($_SESSION['result-add-msg']) ?>);
    <?php else: ?>
        toastr.success(<?= json_encode($_SESSION['result-add-msg']) ?>);
    <?php endif; ?>
    <?php unset($_SESSION['result-add-msg']);?>
          <?php endif;?>
    </script>  
    <div class="result-form-container modal">
        <h3 class="result-form-head">Mark Result</h3>
        <div class="modal-container">
        <form action="" class="result-form" method='post'>
            <input type="number" class="athlete-id input-style" name="athleteid" readonly>
            <input type="hidden" class="event-id " name="eventid">

            <input type="text" class="athlete-name input-style" name="athelete_name" readonly><br>
            <input type="text" class="event-name input-style" name="event_name" readonly><br>
            <input type="text" class="category-name input-style" name="category_name" readonly><br>

                <label>Position:</label>
                <select name="position" required>
                <option value="">-- Select Position --</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                </select><br>
                
            <input type="submit" class="submit-btn btns" name="resultadd">
            <button type="button" class="cancel-btn btns">Cancel</button>
        </form>
        </div>
        </div>
        <script src="../assets/js/pageReload.js"></script>
</body>
<script src="../assets/js/infoFetch.js" type="module"></script>
<script src="../assets/js/messagePopup.js"></script>
<script src="../assets/js/delete.js" type="module"></script>
<script src="../assets/js/printTable.js"></script>
</html>