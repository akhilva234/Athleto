
<?php
ob_start();
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@800&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
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
           <a href="?page=adm_home"><span class="home-container font">
             <i class="fas fa-home"></i>
             Home
        </span></a> 

         <span class="display-athletes-container font">
            <a href="?page=athletes_info">
                <i class="fa-solid fa-ribbon"></i>
                Athletes
            </a>
            </span>

            <div class="select-container">
                 <p class="add-phrase">
                    <i class="fas fa-plus-circle"></i>
                    Add
                </p><br><select name="Add-container" class="action-select" value="" >
                    <option value="">-- Select Action --</option>
                <option value="?page=add_user" class="option-adduser">
                    <span class="add-user-container">User</span>
                </option>
                <option value="?page=add_athlete" class="option-addathlete">
                     <span class="add-athlete-container">Athlete</span>
                </option>
                <option value="?page=events" class="option-addevent">
                     <span class="add-events-container">Event</span>
                </option>
                <option value="?page=departments" class="option-add-department">
                    <span class="add-department-container">Department</span>
                </option>
                <option value="?page=add_template" class="option-add-template">
                    <span class="certificate-template-container.php">Certificate Template</span>
                </option>
            </select>
            </div>
             <div class="view-atheletes-container select-container font">
                <i class="fas fa-running"></i>
                Participants
                <select name="participants-container" class="action-select" value="" >
                <option value="">-- Select List --</option>
                <option value="?page=participants" class="option-inparticipants">
                    <span class="participation-list">Individual Participants</span>
                </option>
                <option value="?page=relay" class="option-relayparticipants">
                    <span class="participation-list">Relay Participants</span>
                </option>
                </select>
            </div>
              <div class="manage-results-container select-container font">
        <i class="fas fa-medal"></i>
        Results
        <select name="results-container" class="action-select">
            <option value="">-- Select List --</option>
            <option value="?page=manage_results">Individual Results</option>
            <option value="?page=manage_relay_results">Relay Results</option>
        </select>
        </div>
              <span class="Championships-container font"><a href="?page=championships">
               <i class="fas fa-trophy"></i>
                Championships
            </a></span>
            <span class="logout-container font">
                <a href="?page=logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </span>
        </div>
        <div class="right-body content" >
        <?php

               $allowed = [
                'adm_home' => 'adm_home.php',
                'add_user' => 'add_user.php',
                'add_athlete' => 'add_athlete.php',
                'events' => 'events.php',
                'departments' => 'departments.php',
                'athletes_info' => '../common_pages/athletes_info.php',
                'manage_results' => '../common_pages/manage_results.php',
                'manage_relay_results' => '../common_pages/manage_relay_results.php',
                'participants' => 'participants.php',
                'relay' => '../common_pages/relay.php',
                'championships' => '../common_pages/championships.php',
                'logout' => '../logout.php'  
            ];
            
            $page = $_GET['page'] ?? 'adm_home';

            if (array_key_exists($page, $allowed) && file_exists($allowed[$page])) {
                include $allowed[$page];
            } else {
                $_SESSION['page_error'] = "Page not found";
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
     <script src="../assets/js/pageReload.js"></script>
</body>
 <?php
ob_end_flush(); 
?>
</html>