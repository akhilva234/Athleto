<?php

    require_once "../session_check.php";
     include_once "../nocache.php";
    include "../config.php";

$eventId = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;


try{
    $pdo->beginTransaction();

    $delCat=$pdo->prepare("DELETE FROM event_categories WHERE event_id=?");

    $delCat->execute([$eventId]);

     $delEvent=$pdo->prepare("DELETE FROM events WHERE event_id=?");
     
     $delEvent->execute([$eventId]);

     $pdo->commit();

     echo json_encode(["success" => true, "message" => "Event deleted sucessfully"]);

}catch(PDOException $e){

    echo json_encode(["success" => false , "messsage" => "Event deletion failed"]);
}

?>