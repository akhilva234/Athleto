
<?php
     require_once "../session_check.php";
    include "../config.php";
    $user= $_SESSION['user'];

    $page_error='';

?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athleto</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/header_common.css">
     <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="dashboard-header">
        <div class="logo">
            <img src="../assets/images/sports-mode-svgrepo-com.svg" alt="" class="logo-img">
        </div>
        <h1 class="title">Athleto</h1>
       
    </header>
    <div class="main-body">
        <div class="left-body">
           <a href="?page=adm_home"><span class="home-container">
             <i class="fas fa-home"></i>
             Home
        </span></a> 

         <span class="display-athletes-container">
            <a href="?page=athletes_info">
                <i class="fa-solid fa-ribbon"></i>
                Athletes
            </a>
            </span>

            <div class="select-container">
                 <p class="add-phrase">
                    <i class="fas fa-plus-circle"></i>
                    Add
                </p><select name="Add-container" class="action-select" value="" >
                    <option value="">-- Select Action --</option>
                <option value="?page=add_user" class="option-adduser">
                    <span class="add-user-container">User</span>
                </option>
                <option value="?page=add_athlete" class="option-addathlete">
                     <span class="add-athlete-container">Athlete</span>
                </option>
                <option value="?page=add_event" class="option-addevent">
                     <span class="add-events-container">Event</span>
                </option>
                <option value="?page=add_department" class="option-add-department">
                    <span class="add-department-container">Department</span>
                </option>
                <option value="?page=add_template" class="option-add-template">
                    <span class="certificate-template-container.php">Certificate Template</span>
                </option>
            </select>
            </div>
            <span class="manage-results-container"><a href="?page=manage_results">
                <i class="fas fa-medal"></i>
                Results</a>
            </span>
             <span class="view-atheletes-container"><a href="?page=participants">
                <i class="fas fa-running"></i>
                Participants</a>
            </span>
              <span class="Championships-container"><a href="?page=championships">
               <i class="fas fa-trophy"></i>
                Championships
            </a></span>
        </div>
        <div class="right-body content" >
        <?php

               $allowed = [
                'adm_home' => 'adm_home.php',
                'add_user' => 'add_user.php',
                'add_athlete' => 'add_athlete.php',
                'add_event' => 'add_event.php',
                'add_department' => 'add_department.php',
                'athletes_info' => '../common_pages/athletes_info.php',
                'manage_results' => '../common_pages/manage_results.php',
                'participants' => 'participants.php',
                'championships' => '../common_pages/championships.php'
            ];
            if (isset($_GET['page'])) {
            $page = $_GET['page'];

            if (array_key_exists($page, $allowed) && file_exists($allowed[$page])) {
                include $allowed[$page];
            } else {
                $_SESSION['page_error'] = "Page not found";
            }
        }

              if(isset($_SESSION['page_error'])){
                $page_error=$_SESSION['page_error'];
                unset($_SESSION['page_error']);
             }
        ?>
        <h2 class="page-error"><?=$page_error?></h2>
        </div>
    </div>
     <script src="../assets/js/pageLoader.js"></script>
</body>
 
</html>