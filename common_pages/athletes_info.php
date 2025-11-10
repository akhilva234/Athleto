<?php

      require_once "../session_check.php";
      include_once "../nocache.php";
    include "../config.php";
    $user= $_SESSION['user'];
    $role=$_SESSION['role'];

    if(isset($_POST['update'])){

         require '../common_pages/update_athletes.php';
        
    }

    $message='';
    if(isset($_SESSION['athlete-msg'])){
        $message=$_SESSION['athlete-msg'];
        unset($_SESSION['athlete-msg']);
    }

    $currentYear=date('Y');

    $athletes=$pdo->prepare("SELECT 
    a.athlete_id,
    a.first_name,
    a.last_name,
    a.year,
    c.category_name,
    d.dept_name,
    p.meet_year
    FROM athletes a
    JOIN categories c ON a.category_id=c.category_id
    JOIN departments d ON a.dept_id=d.dept_id 
    JOIN participation p ON a.athlete_id=p.athlete_id
    WHERE p.meet_year=?
    ORDER BY a.athlete_id");

    $athletes->execute([$currentYear]);

    $athletes = $athletes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="../assets/js/toast.js"></script>
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/common_css/tables.css">
    <link rel="stylesheet" href="../assets/css/update_form.css">
    <link rel="stylesheet" href="../assets/css/athlete_info.css">
</head>
<body class="hide-events-filter" data-view="athletes" data-user="<?=$role?>">
    <h2>Athletes</h2>
    <br>
     <?php include_once "../common_pages/filter.php"?>
<div class="whole-blur-container"></div>
 <div class="participants-table-container table-whole-container">
        <table class="participants-table athletes-table">
            <thead>
            <tr>
            <th>SI.NO</th>
            <th>Chest Number</th>
            <th>Name</th>
            <th>Category</th>
            <th>Course</th>
            <th>Year</th>
             <?php if($role!=='captain'):?>
            <th>Update</th>
            <th>Delete</th>
            <?php endif; ?>
            </tr>
            </thead>
            <tbody>
                 <?php if (empty($athletes)): ?>
            <tr>
                <td colspan="8" style="text-align:center; font-weight:bold; color:#555;">
                    No athletes found.
                </td>
            </tr>
        <?php else: ?>
            <?php $count=1;?>
            <?php foreach($athletes as $athlete):?>
                <tr id="row-<?= $athlete['athlete_id']?>">
                    <td><?=htmlspecialchars($count++)?></td>
                     <td><span class="chest-no-tr">
                        <?=htmlspecialchars($athlete['athlete_id'])?>
                    </span></td>
                    <td><?=htmlspecialchars($athlete['first_name'])?><?=" "?><?=htmlspecialchars($athlete['last_name'])?></td>
                    <td><?=htmlspecialchars($athlete['category_name'])?></td>
                    <td><?=htmlspecialchars($athlete['dept_name'])?></td>
                    <td><?=htmlspecialchars($athlete['year'])?></td>
                      <?php if($role!=='captain'):?>
                    <td><button class="update-btn" data-athlete-id="<?=htmlspecialchars($athlete['athlete_id'])?>">
                        Update
                    </button></td>
                      <td><button class="delete-btn" data-athlete-id="<?=htmlspecialchars($athlete['athlete_id'])?>">
                        Delete
                    </button></td>
                <?php endif; ?>
                 </tr>   
            <?php endforeach ;?>
              <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($message)): ?>
    <script>
    <?php if (strpos($message, 'Failed') !== false || strpos($message, 'Invalid') !== false): ?>
        toastr.error(<?= json_encode($message) ?>);
    <?php else: ?>
        toastr.success(<?= json_encode($message) ?>);
    <?php endif; ?>
    </script>
<?php endif; ?>
    <div id="editAthleteModal">
    <div class="modal-content" id="editAthleteContent">
        
    </div>
</div>

<script src="../assets/js/pageReload.js"></script>
</body>
<script src="../assets/js/limitCheck.js" type="module"></script>
<script src="../assets/js/delete_whole.js" type="module"></script>
<script src="../assets/js/update_form.js" type="module"></script>
</html>