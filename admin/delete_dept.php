<?php

     require_once "../session_check.php";
     include_once "../nocache.php";
    include "../config.php";


    $dept_id=isset($_GET['dept_id']) ? (int)$_GET['dept_id'] : 0;

    try{

        $pdo->beginTransaction();

        $deldept=$pdo->prepare("DELETE FROM departments WHERE dept_id=?");

        $deldept->execute([$dept_id]);

        $pdo->commit();

        echo json_encode(["success" => true, "message" => "Department  deleted sucessfully"]);
    }catch(PDOException $e){

    echo json_encode(["success" => false , "messsage" => "Department deletion failed"]);

    }
?>