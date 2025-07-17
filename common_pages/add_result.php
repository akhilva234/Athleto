<?php
    require_once "../session_check.php";
    $user=$_SESSION['user'];
include_once "../config.php";

    if(isset($_SERVER['REQUEST_METHOD'])=='POST'){

        $athlete_id=(int)$_POST['athleteid'];
        $event_id=(int)$_POST['eventid'];
        $position=(int)$_POST['position'];

        try{
            $pdo->beginTransaction();

            $ifExists = $pdo->prepare("SELECT * FROM results WHERE athlete_id = ? AND event_id = ?");
            $ifExists->execute([$athlete_id, $event_id]);
            $exists = $ifExists->fetch(PDO::FETCH_ASSOC);
                if($exists) {
                    $_SESSION['result-add-msg'] = "Position for event already secured";
                    header("Location: adm_dashboard.php?page=participants&status=success");
                    exit;
                }
             $positionExists = $pdo->prepare("SELECT * FROM results WHERE event_id = ? 
             AND position = ?");
            $positionExists->execute([$event_id, $position]);
            $positionTaken = $positionExists->fetch(PDO::FETCH_ASSOC);

            if ($positionTaken) {
                $_SESSION['result-add-msg'] = "That position is already taken for this event.";
                header("Location: adm_dashboard.php?page=participants&status=error");
                exit;
            }
   

            $result_add=$pdo->prepare("INSERT INTO results(event_id,athlete_id,position,added_by)
             VALUES(:eventid,:athleteid,:position,:added)");

            $success= $result_add->execute([
                'eventid'=>$event_id,
                'athleteid'=>$athlete_id,
                'position'=>$position,
                'added'=>$user
             ]);

             if($success){
                $pdo->commit();
                $_SESSION['result-add-msg']="Result added Successfully";
                header("Location: adm_dashboard.php?page=participants&status=success");
                exit;
             }
        }catch(Exception $e){

            $pdo->rollBack();
             echo "user:".$user;

            echo "Failed".$e->getMessage();
        }
    }
?>