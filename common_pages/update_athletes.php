<?php

    require_once "../session_check.php";
    include "../config.php";
    $user= $_SESSION['user'];

    if($_SERVER['REQUEST_METHOD']=='POST'){

    $athleteId=(int)$_POST['athlete_id'];
    $fname = ucwords(strtolower(trim($_POST['first_name'])));
    $lname = ucwords(strtolower(trim($_POST['last_name'])));
    $year=(int)$_POST['year'];

    $depId=(int)$_POST['dept_id'];
    $categoryId=(int)$_POST['category_id'];

    $eventIds=$_POST['event_ids'];

    $relayEventIds=$_POST['relay_event_ids'];

    if(empty($athleteId)||empty($fname)||empty($lname)||empty($year)||
        empty($depId)||empty($categoryId)||empty($eventIds)){

            $_SESSION['athlete-msg']="All fields are required";
            exit;
            } else {
                
                $validEvents = [];
                foreach ($eventIds as $eid) {
                    $eid = (int)$eid;
                    $eventCheck = $pdo->prepare("SELECT event_id FROM events WHERE event_id = ?");
                    $eventCheck->execute([$eid]);
                    if ($eventCheck->fetch()) {
                        $validEvents[] = $eid;
                    }
                }

                 foreach ($relayEventIds as $eid) {
                    $eid = (int)$eid;
                    $eventCheck = $pdo->prepare("SELECT event_id FROM events WHERE event_id = ?");
                    $eventCheck->execute([$eid]);
                    if ($eventCheck->fetch()) {
                        $validEvents[] = $eid;
                    }
                }

                if (empty($validEvents)) {
                    $_SESSION['athlete-msg'] = "No valid events selected.";
                } else {
                    try {
                        $pdo->beginTransaction();

                        $athleteSql = $pdo->prepare("UPDATE athletes SET first_name=?,last_name=?,category_id=?,
                        dept_id=?,year=? WHERE athlete_id=?");

                        $athleteSql->execute([
                            $fname,
                            $lname,
                            $categoryId,
                            $depId,
                            $year,
                            $athleteId
                        ]);

                        $delete = $pdo->prepare("DELETE FROM participation WHERE athlete_id = ?");
                        $delete->execute([$athleteId]);

                        $insert = $pdo->prepare("INSERT INTO participation (athlete_id, event_id) VALUES (?, ?)");
                        foreach ($validEvents as $event_id) {
                            $insert->execute([$athleteId, $event_id]);
                        }

                        $pdo->commit();
                        if($insert){

                            
                          $_SESSION['result-add-msg'] = "Athlete and participation updated successfully";
                            header("Location: adm_dashboard.php?page=athletes_info&status=success");
                            exit;
                        }
                    } catch (PDOException $e) {
                        $pdo->rollBack();
                        $message = "Failed: " . $e->getMessage();
                }
            }
        }
}
   
?>