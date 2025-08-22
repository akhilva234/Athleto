<?php

    require_once "../session_check.php";
     include_once "../nocache.php";
    include "../config.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="../assets/css/common.css">
     <link rel="stylesheet" href="../assets/css/common_css/tables.css">
     <link rel="stylesheet" href="../assets/css/result_form.css">
     <link rel="stylesheet" href="../assets/css/common_css/message.css">
     <link rel="stylesheet" href="../assets/css/add_depts.css">
</head>
<body data-view="departments">
    <div class="whole-blur-container"></div>
    <h2>Departments</h2>
    
    <?php
    try{
        $depts=$pdo->query("SELECT * FROM departments ORDER BY dept_id");
    }catch(PDOException $e){
        echo "Query failed".$e->getMessage();
        exit;
    }
    ?>
      <br>
    <button class="add-btn">
        <i class="fas fa-plus-circle"></i>
        Add Department
    </button>
        <div class="participants-table-container table-whole-container">
        <table class="participants-table departments-table">
            <thead>
            <tr>
            <th>SI.NO</th>
            <th>Department Id</th>
            <th>Name</th>
            <th>Delete</th>
            </tr>
            </thead>
            <?php $count=1;?>
            <tbody>
            <?php foreach($depts as $dep):?>
                <tr id="row-<?=$dep['dept_id']?>">
                    <td><?=htmlspecialchars($count++)?></td>
                     <td><span class="chest-no-tr">
                        <?=htmlspecialchars($dep['dept_id'])?>
                    </span></td>
                      <td><?=htmlspecialchars($dep['dept_name'])?></td>
                   <td><button class="delete-btn" data-dept-id="<?=$dep['dept_id']?>">
                        Delete</button></td>  
                </tr>
            <?php endforeach ;?>
            </tbody>
        </table>
    </div>
    <div class="result-form-container modal deptmodal">
        <h3 class="result-form-head">Add Department</h3>
        <div class="modal-container">
        <form action="" class="result-form" method='post'>
            <input type="text" class="event-name input-style" name="event_name"><br>
            <div class="category-container">
                <?php foreach($categories as $cat): ?>
                    <br><label>
                        <input type="checkbox"
                        name="cat_ids[]"
                        value="<?=$cat['category_id']?>"><?=htmlspecialchars($cat['category_name'])?>
                    </label>
                <?php endforeach;?>    
            </div>
            <br>
            <input type="submit" class="submit-btn btns" name="eventadd" value="Add">
            <button type="button" class="cancel-btn btns">Cancel</button>
        </form>
        </div>
        </div>
</body>
<script src="../assets/js/addCommon.js"></script>
<script src="../assets/js/deleteCommon.js"></script>
</html>