<?php
    require_once "../session_check.php";
    $user=$_SESSION['user'];
include_once "../config.php";

    if(isset($_SERVER['REQUEST_METHOD'])=='POST'){

        $athlete_id=$_POST['athleteid'];
        $event_id=$_POST['eventid'];
        $position=(int)$_POST['position'];

        try{

            $result_add=$pdo->prepare("INSERT INTO results(event_id,athlete_id,position,added_by)
             VALUES(:eventid,:athleteid,:position,:added)");

            $success= $result_add->execute([
                'eventid'=>$event_id,
                'athleteid'=>$athlete_id,
                'position'=>$position,
                'added'=>$user
             ]);

             if($success){
                $_SESSION['result-add-msg']="Result added Successfully";
                header("Location: adm_dashboard.php?page=participants&status=success");
                exit;
             }
        }catch(Exception $e){

             echo "user:".$user;

            echo "Failed".$e->getMessage();
        }
    }
?>