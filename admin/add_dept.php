<?php

     require_once "../session_check.php";
     include_once "../nocache.php";
    include "../config.php";

    if($_SERVER['REQUEST_METHOD']=='POST'){

        $dept_name=ucwords(strtolower(trim($_POST['dept_name'])));

        if(empty($dept_name)){
             $_SESSION['dept-msg']="Failed: All fields are necessary";
             header("Location: adm_dashboard.php?page=departments&status=failure");
            exit;
        }

        try{

            $pdo->beginTransaction();

            $dept=$pdo->prepare("INSERT INTO headdepartment (hd_name) VALUES (?)");

            $dept->execute([$dept_name]);

            $pdo->commit();
            $_SESSION['dept-msg']="Department added Successfully";
          header("Location: adm_dashboard.php?page=departments&status=success");
          exit;

        }catch(PDOException $e){

            if (isset($e->errorInfo[1]) && $e->errorInfo[1]== 1062) { 

            $_SESSION['dept-msg']="Failed:Duplicate Department found. Insertion skipped";
        }else{
            $_SESSION['dept-msg']="Failed To add Event".$e->getMessage();
        }
    }
}

?>