
<?php
     session_start();
    include "config.php";

    $msg=" ";

    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athleto</title>
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="assets/css/login.css">
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

            $msg="admin found";
         }
         else{
            $msg= "Invalid Username or Password";
         }
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
     <form action="" method="post">
    <div class="sign-in-container" id="signin">
       <h2 class="sign-in-title">Sign in</h2>
        <div class="username-container">
            <input type="text" placeholder="Username" name="username" class="username-box box-style">
         </div>
         <div class="password-container">   
            <input type="password" placeholder="password" name="password" class="password-box box-style">
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
 <script>
</script>
</body>
</html>    