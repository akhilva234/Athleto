<?php

    require_once "../session_check.php";
    include_once "../nocache.php";
    include "../config.php";

    if(isset($_POST['eventadd'])){
        $event=ucwords(strtolower(trim($_POST['event_name'])));
        $cats=isset($_POST['cat_ids']) ? $_POST['cat_ids'] : [];
        $isRelay = isset($_POST['is_relay']) ? 1 : 0;


        if(empty($event)|| empty($cats)){
            $_SESSION['event-msg']="Failed:All fields are necessary";
            header("Location: adm_dashboard.php?page=events&status=failure");
            exit;
        }
        try{
            
            $pdo->beginTransaction();

            $stmt=$pdo->prepare("INSERT INTO events(event_name,is_relay) VALUES(?,?)");
            $stmt->execute([$event,$isRelay]);

            $eventId=$pdo->lastInsertId();

            $catstmt=$pdo->prepare("INSERT INTO event_categories VALUES(?,?)");
            foreach($cats as $cat){
                $catId=(int)$cat;
                $catstmt->execute([$eventId,$catId]);
            }
          $pdo->commit();
          $_SESSION['event-msg']="Events added Successfully";
          header("Location: adm_dashboard.php?page=events&status=success");
          exit;


        }catch(PDOException $e){
            if (isset($e->errorInfo[1]) && $e->errorInfo[1]== 1062) { 

            $_SESSION['event-msg']="Failed:Duplicate event found. Insertion skipped";
        }else{
            $_SESSION['event-msg']="Failed To add Event".$e->getMessage();
        }

    }
}
?>