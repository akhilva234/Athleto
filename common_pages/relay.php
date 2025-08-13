<?php
    require_once "../session_check.php";
    include "../config.php";
    $user= $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="../assets/css/common.css">
     <link rel="stylesheet" href="../assets/css/common_css/tables.css">
     <link rel="stylesheet" href="../assets/css/result_form.css">
     <link rel="stylesheet" href="../assets/css/common_css/message.css">
</head>
<body>
     <h2>Relay Participants</h2>
        <?php  echo "user:".$user;
        
        $sql="SELECT 
        rt.team_id,
        e.event_name,
        d.dept_name,
        GROUP_CONCAT(CONCAT(a.first_name, ' ', a.last_name) SEPARATOR ', ') AS team_members
    FROM relay_teams rt
    JOIN events e ON rt.event_id = e.event_id
    JOIN departments d ON rt.dept_id = d.dept_id
    JOIN relay_team_members rtm ON rt.team_id = rtm.team_id
    JOIN athletes a ON rtm.athlete_id = a.athlete_id
    WHERE e.is_relay = 1
    GROUP BY rt.team_id, e.event_name, d.dept_name
    ORDER BY e.event_id, rt.team_id";

    $relayParticipants=$pdo->query($sql);
?>
    <div class="participants-table-container table-whole-container">
        <table class="participants-table">
        <thead>
            <tr>
                <th>Team Id</th>
                <th>Event Name</th>
                <th>Department</th>
                <th>Team Members</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($relayParticipants as $relay) :?>
            <tr>
                <td><?=htmlspecialchars($relay['team_id'])?></td>
                 <td><?=htmlspecialchars($relay['event_name'])?></td>
                 <td><?=htmlspecialchars($relay['dept_name'])?></td>
                 <td><?=htmlspecialchars($relay['team_members'])?></td>
                 <td><button class="result-entry-btn">
                        Enter Result</button></td>
            </tr>
         <?php endforeach ;?>   
        </tbody>
    </table>
    </div>
</body>
</html>