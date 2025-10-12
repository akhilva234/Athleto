<?php

     require_once "../session_check.php";
     include_once "../nocache.php";
    include "../config.php";


    $dept_id=isset($_GET['dept_id']) ? (int)$_GET['dept_id'] : 0;

    try{

        $pdo->beginTransaction();

            $delRelayTeams = $pdo->prepare("DELETE FROM relay_teams WHERE dept_id IN (SELECT dept_id FROM departments WHERE hd_id = ?)");
            $delRelayTeams->execute([$dept_id]);

        $deldept=$pdo->prepare("DELETE FROM departments WHERE hd_id=?");

        $deldept->execute([$dept_id]);

        $delhead=$pdo->prepare("DELETE FROM headdepartment WHERE hd_id=?");

        $delhead->execute([$dept_id]);

        $pdo->commit();

        echo json_encode(["success" => true, "message" => "Department  deleted sucessfully"]);
    }catch(PDOException $e){

    echo json_encode(["success" => false , "messsage" => "Department deletion failed","error"=>$e->getMessage()]);

    }
?>