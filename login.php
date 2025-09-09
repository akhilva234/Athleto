
<?php
   require_once "session_check.php";
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    include "config.php";

    $msg=" ";

    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athleto</title>
    <link rel="stylesheet" href="assets/css/homepage.css">
    <link rel="stylesheet" href="assets/css/logininfo.css">
    <link rel="stylesheet" href="assets/css/header_common.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
    <?php

    if(isset($_POST['submit'])){

        $uname=$_POST['username'];
        $password=$_POST['password'];

        $sql=$pdo->prepare("select * from users where username=:username");

        $sql->execute(['username'=>$uname]);

         $user=$sql->fetch(PDO::FETCH_ASSOC);
         
         if($user && password_verify($password,$user['password'])){

            $_SESSION['role']=$user['role'];
            $_SESSION['user']=$user['user_id'];
            $_SESSION['username']=$user['username'];

             header('Location: dashboard.php');
             exit;
         }
         else{
              $_SESSION['msg'] = "Invalid Username or Password";
         }

          header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    if(isset($_SESSION['msg'])){
        $msg=$_SESSION['msg'];
        unset($_SESSION['msg']);
    }
?>
    <header class="header">
        <div class="logo">
            <span><img src="assets/images/sports-mode-svgrepo-com.svg" alt="" class="logo-img"></span>
        </div>
            <h1 class="title">Athleto</h1>
        <div class="auth-btn-container">
           <button class="auth-btn"><a href="#signin" class="signin">Log in</a></button>
        </div>
    </header>
    <section class="hero">
            <img src="assets/images/steven-lelham-atSaEOeE8Nk-unsplash.jpg" alt="" class="hero-image">
        <div class="hero-overlay"></div>
    </section>
    <div class="cards-container">
    <div class="cards-grid">
         <div class="feature-card" onclick="window.location.href='#signin'">
                <h3>Result Tracking</h3>
                <p>Real-time tracking of competition results with automatic position calculations.</p>
            </div>
            
            <div class="feature-card" onclick="window.location.href='#signin'">
                <h3>Event Management</h3>
                <p>Create and schedule events with participant registration and category management.</p>
            </div>
            
            <div class="feature-card" onclick="window.location.href='#signin'">
                <h3>Certificate Generation</h3>
                <p>Automatically generate and distribute digital certificates for winners.</p>
            </div>
    </div>
  </div>
    <section class="content">
        <h2>Streamline Your Sports Management</h2>
        <p>Athleto provides an all-in-one platform for organizing competitions, tracking results, and rewarding participants with seamless certificate generation.</p>
    </section>
    
    <div class="sign-in-container" id="signin">
    <h2 class="sign-in-title">Log in</h2>
    <form action="" method="post">
        <div class="username-container">
            <input type="text" placeholder="Username" name="username" class="username-box box-style" required>
        </div>
        <div class="password-container" style="position: relative;">   
            <input type="password" placeholder="password" name="password" class="password-box box-style" id="password" style="padding-right: 30px;">
            <i id="togglePassword" class="fa fa-eye" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"></i>
        </div>
        <div class="submit-btn-container" name="submit">
            <input type="submit" class="sign-in-button" name="submit">
        </div>
    </form> 
    <h4 class="login-info"><?=$msg?></h4>
</div>
  
 <footer>
        <p>&copy; 2023 Athleto Sports Management. All rights reserved.</p>
    </footer>
    <script src="./assets/js/pageReload.js"></script>
</body>
<script>
if (window.history && window.history.pushState) {
    window.history.pushState(null, "", window.location.href);
    window.onpopstate = function () {
        window.history.pushState(null, "", window.location.href);
    };
}
</script>
<script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');

    togglePassword.addEventListener('click', () => {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        togglePassword.classList.toggle('fa-eye');
        togglePassword.classList.toggle('fa-eye-slash');
    });
</script>

</html>    