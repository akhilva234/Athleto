<?php
    require_once "../session_check.php";
    include "../config.php";

    if($_SERVER['REQUEST_METHOD']=='POST'){

        $team_id=(int)$_POST['teamid'];
        $event_id=(int)$_POST['eventid'];
        $position=(int)$_POST['position'];

        try{

            $pdo->beginTransaction();
            
        }catch(PDOException $e){

        }


    }
?>