<?php
    require_once "../session_check.php";
    include "../config.php";

    if(isset($_GET['team_id'],$_GET['event_id'])){

        $teamid=$_GET['team_id'];
        $eventid=$_GET['event_id'];

        $relayPart=$pdo->prepare("SELECT d.dept_name,e.event_name,c.category_name
        FROM relay_teams rt 
        JOIN events e  ON rt.event_id=e.event_id
        JOIN departments d ON rt.dept_id= d.dept_id
        JOIN categories c ON rt.category_id=c.category_id
        WHERE rt.team_id=? AND rt.event_id=?
        ");

        $relayPart->execute([$teamid,$eventid]);
        $data=$relayPart->fetch(PDO::FETCH_ASSOC);

        if($data){
            echo json_encode([
                'team_name' => $data['dept_name'],
                'event_name' => $data['event_name'],
                'category_name' => $data['category_name']
            ]);
        }
        else{
             echo json_encode(['error' => 'No data found']);
        }
    }else{
        echo json_encode(['error' => 'Missing parameters']);
}
?>