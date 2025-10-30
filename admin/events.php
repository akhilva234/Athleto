<?php

    require_once "../session_check.php";
     include_once "../nocache.php";
    include "../config.php";
    if(isset($_POST['eventadd'])){
        require 'add_events.php';
    }

    $message='';
        if (isset($_SESSION['event-msg'])) {
            $message = $_SESSION['event-msg'];
            unset($_SESSION['event-msg']);
        }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="../assets/js/toast.js"></script>
    <link rel="stylesheet" href="../assets/css/common.css">
     <link rel="stylesheet" href="../assets/css/common_css/tables.css">
     <link rel="stylesheet" href="../assets/css/result_form.css">
     <link rel="stylesheet" href="../assets/css/add_event.css">
</head>
<body data-view="events">
    <div class="whole-blur-container"></div>
    <h2>Events</h2>
    <?php
    try{
            $events=$pdo->query("SELECT e.event_id,
                            e.event_name,
                            GROUP_CONCAT(c.category_name ORDER BY c.category_name SEPARATOR ',' ) AS categories
                            FROM events e
                            JOIN event_categories ac ON e.event_id=ac.event_id
                            JOIN  categories c ON ac.category_id = c.category_id
                            GROUP BY e.event_id ,e.event_name
                            ORDER BY event_id");
    }catch(PDOException $ex){
        echo "Query failed: " . $ex->getMessage();
        exit;
    }
    $events=$events->fetchAll();
    ?>
    <br>
    <button class="add-btn">
        <i class="fas fa-plus-circle"></i>
        Add event
    </button>
    <div class="participants-table-container table-whole-container">
        <table class="participants-table events-table">
            <thead>
            <tr>
            <th>SI.NO</th>
            <th>Event Id</th>
            <th>Name</th>
            <th>Categories</th>
            <th>Delete</th>
            </tr>
            </thead>
           
            <tbody>
                 <?php if (empty($events)): ?>
                <tr>
                <td colspan="8" style="text-align:center; font-weight:bold; color:#555;">
                    No Events found.
                </td>
            </tr>
        <?php else: ?>
             <?php $count=1;?>
            <?php foreach($events as $event):?>
                <tr id="row-<?=$event['event_id']?>">
                    <td><?=htmlspecialchars($count++)?></td>
                     <td><span class="chest-no-tr">
                        <?=htmlspecialchars($event['event_id'])?>
                    </span></td>
                      <td><?=htmlspecialchars($event['event_name'])?></td>
                    <td><?=htmlspecialchars($event['categories'])?></td>
                   <td><button class="delete-btn" data-event-id="<?=$event['event_id']?>">
                        Delete</button></td>  
                </tr>
            <?php endforeach ;?>
             <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
    $categories=$pdo->query("SELECT * FROM categories");
    ?>
      <div class="result-form-container modal eventmodal" required>
        <h3 class="result-form-head">Add Event</h3>
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
            <label>
            <input type="checkbox" name="is_relay" value="1"> Is Relay
            </label>
            <input type="submit" class="submit-btn btns" name="eventadd" value="Add">
            <button type="button" class="cancel-btn btns">Cancel</button>
        </form>
        </div>
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
</body>
<script src="../assets/js/addCommon.js"></script>
<script src="../assets/js/deleteCommon.js"></script>
</html>