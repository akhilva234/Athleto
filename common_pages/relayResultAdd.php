<?php
    require_once "../session_check.php";
    include "../config.php";
    $user=$_SESSION['user'];
    $role = $_SESSION['role'];

$redirects = [
    'admin'   => 'adm_dashboard.php?page=relay',
    'faculty' => 'faculty_dashboard.php?page=relay',
    'captain' => 'captain_dashboard.php?page=relay'
];

$redirectPage = $redirects[$role] ?? 'index.php';
    if($_SERVER['REQUEST_METHOD']=='POST'){

        $team_id=(int)$_POST['teamid'];
        $event_id=(int)$_POST['eventid'];
        $position=(int)$_POST['position'];

        try{

            $pdo->beginTransaction();

            $ifExists = $pdo->prepare("SELECT 1 FROM results WHERE relay_team_id = ? ");
            $ifExists->execute([$team_id]);
            if ($ifExists->fetch()) {
                    $_SESSION['result-add-msg'] = "Failed: Position for event already secured by this team.";
                    header("Location: $redirectPage&status=error");
                    exit;
                }
            
                $positionExists = $pdo->prepare("
                        SELECT r.result_id 
                        FROM results r
                         JOIN relay_teams rt ON rt.team_id=r.relay_team_id
                        WHERE r.event_id = ? AND r.position = ? AND r.relay_team_id = ?
                    ");
                    $positionExists->execute([$event_id, $position, $team_id]);


                if ($positionExists->fetch()) {
                        $_SESSION['result-add-msg'] = "Failed: That position is already taken for this event.";
                        header("Location: $redirectPage&status=error");
                        exit;
                    }  
                    
                  $result=$pdo->prepare("INSERT INTO results(event_id,relay_team_id,position,added_by)VALUES(?,?,?,?)");
                  
                  $success=$result->execute([
                    $event_id,
                    $team_id,
                    $position,
                    $user
                  ]);

                   if($success){
                $pdo->commit();
                $_SESSION['result-add-msg']="Result added Successfully";
                header("Location: $redirectPage&status=success");
                exit;
             }

        }catch(PDOException $e){

            $pdo->rollBack();

            echo "Failed".$e->getMessage();

        }


    }
?>