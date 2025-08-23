<?php
    require_once "../session_check.php";
   include_once "../nocache.php";
    include "../config.php";
     if(isset($_POST['resultadd'])){
                require '../common_pages/relayResultAdd.php';
            }
    $user= $_SESSION['user'];
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
     <link rel="stylesheet" href="../assets/css/result_form.css">
</head>
<body data-view="relays">
     <div class="whole-blur-container"></div>
     <h2>Relay Participants</h2>
        <?php  echo "user:".$user;
        
        $sql="SELECT 
        rt.team_id,
        e.event_name,
        e.event_id,
        d.dept_name,
        c.category_name,
        GROUP_CONCAT(CONCAT(a.first_name, ' ', a.last_name) SEPARATOR ', ') AS team_members
    FROM relay_teams rt
    JOIN events e ON rt.event_id = e.event_id
    JOIN departments d ON rt.dept_id = d.dept_id
    JOIN relay_team_members rtm ON rt.team_id = rtm.team_id
    JOIN athletes a ON rtm.athlete_id = a.athlete_id
    JOIN categories c ON rt.category_id=c.category_id
    WHERE e.is_relay = 1
    GROUP BY rt.team_id, e.event_name, d.dept_name
    ORDER BY e.event_id, rt.team_id";

    $relayParticipants=$pdo->query($sql);
?>
<br>
<?php $filter_type = 'relay'; ?>
<?php include_once '../common_pages/filter.php' ;?>
    <div class="participants-table-container table-whole-container">
        <table class="participants-table">
        <thead>
            <tr>
                <th>SI No.</th>
                <th>Team Id</th>
                <th>Event Name</th>
                <th>Category</th>
                <th>Department</th>
                <th>Team Members</th>
                <th>Result</th>
            </tr>
        </thead>
        <?php $count=1;?>
        <tbody>
            <?php foreach($relayParticipants as $relay) :?>
            <tr id="row-<?= $relay['team_id'] . '-' . $relay['event_id']?>">
                <td><?=htmlspecialchars($count++)?></td>
                <td><?=htmlspecialchars($relay['team_id'])?></td>
                 <td><?=htmlspecialchars($relay['event_name'])?></td>
                 <td><?=htmlspecialchars($relay['category_name'])?></td>
                 <td><?=htmlspecialchars($relay['dept_name'])?></td>
                 <td><?=htmlspecialchars($relay['team_members'])?></td>
                 <td><button class="result-entry-btn" data-team-id="<?=$relay['team_id']?>"
                  data-event-id="<?=$relay['event_id']?>">
                        Enter Result</button></td>
            </tr>
         <?php endforeach ;?>   
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
            <input type="number" class="team-id input-style" name="teamid">
            <input type="hidden" class="event-id " name="eventid">

            <input type="text" class="team-name input-style" name="team_name" readonly><br>
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
<script type="module" src="../assets/js/relayInfoFetch.js"></script>
</html>