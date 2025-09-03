<?php

  session_start();
    require_once "config.php";
    
    $user=$_SESSION['role'];

    if($user=='admin'){

      header('Location: admin/adm_dashboard.php');
      exit;
    }
    else if($user=='faculty'){

      header('Location: faculty/faculty_dashboard.php');
      exit;

    }

  ?>  