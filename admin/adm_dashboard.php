
<?php
    include "../config.php";

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
              <span class="generte-certificate-container"><a href="?page=generate_certificate">
                <i class="fas fa-scroll"></i>
                Certificates
            </a></span>
        </div>
        <div class="right-body content" >
        <?php

            if(isset($_GET['page'])){

                $page=$_GET['page'];

                $allowed=['adm_home','add_user','add_athlete','add_event','add_department','manage_results','participants','generate_certificate'];

                if(in_array($page,$allowed)&& file_exists($page.".php")){
                    include $page.".php";
                }
                else{

                    $_SESSION['page_error']="page not found";

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