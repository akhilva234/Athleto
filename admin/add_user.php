
<?php
  session_start();
    include "../config.php";

    $message = " ";

    if (isset($_SESSION['add_user_msg'])) {
    $message = $_SESSION['add_user_msg'];
    unset($_SESSION['add_user_msg']);
}


?> 
<?php
    
        if (isset($_GET['depstatus']) && $_GET['depstatus'] == 'failure') {
             $message = "Please select a valid department.";
        }

        if (isset($_GET['passstatus']) && $_GET['passstatus'] == 'failure') {

            $message ="Passwords do not match.";
            
        }

    if($_SERVER["REQUEST_METHOD"] == "POST"){

            $uname=$_POST['username'];
             $plainpassword=$_POST['password'];
            $cpassword=$_POST['cpassword'];
            $email=$_POST['email'];
            $role=$_POST['role-select'];
            $dep_id=(int)$_POST['dep-select'];
            if (empty($dep_id)) {
                 header("Location: adm_dashboard.php?page=add_user&depstatus=failure");
                exit;
                
            }


            if ($plainpassword !== $cpassword) {
                 header("Location:  adm_dashboard.php?page=add_user&passstatus=failure");
                exit;
            }
             $password=password_hash($_POST['password'],PASSWORD_DEFAULT);
             $depCheck = $pdo->prepare("SELECT dept_id FROM departments WHERE dept_id = ?");
            $depCheck->execute([$dep_id]);
        if (!$depCheck->fetch()) {
            $message = "Invalid Department selected.";
        }
        else{
            
                try{

                    $usersql=$pdo->prepare("insert into users(username,password,email,role,dept_id) 
                    values(:uname,:password,:email,:role,:dep_id)");

                    $success=$usersql->execute([
                        'uname' => $uname,
                        'password' => $password,
                        'email' => $email,
                        'role' => $role,
                        'dep_id' => $dep_id
                    ]);
                    if($success){

                        $_SESSION['add_user_msg'] = "User added successfully!\n Username: {$uname}
                        \n Password: {$plainpassword} \n Please Note this username and Password";

                         header("Location: adm_dashboard.php?page=add_user&status=success");
                            exit;
                    }       

                    else{

                        echo "failed to add user";
                    }

                }catch(PDOException $e){

                    echo "Transaction failed: " . $e->getMessage();
                }

        }
    }
    
?>    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../assets/css/user_add.css">
    <link rel="stylesheet" href="../assets/css/common_css/form_common.css">
</head>
<body>
    <h2 class="add-heading">Add Users</h2>
    <div class="insert-container">
        <div class="form-container">
            <form action="" method="post" class="form">
            <div class="username">
                Username<br>
                <input type="text" placeholder="Username" name="username" class="username-input">
            </div>
            <div class="set-password">
                Password<br>
                <input type="text" placeholder="Set Password" name="password" class="password-input">
            </div>
            <div class="confirm-password">
               Confirm Password <br>
               <input type="text" placeholder="Confirm Password" name="cpassword" class="password-input">
            </div>
            <div class="add-email">
                Email
                <br><input type="email" placeholder="Email" name="email" class="email-input">
            </div>
            <div class="role-select-container">
                 Role<br>
                 <select name="role-select"  class="role-insert">
                    <option value="">-- Select Action --</option>
                <option value="captain" class="role-captain">captain</option>
                <option value="faculty" class="role-faculty">faculty</option>
            </select>
            </div>
            <div class="dep-id-container">
                Department
                <?php $dep=$pdo->query("select * from departments");?>
                <br><select name="dep-select"  class="dep-insert">
                    <option value="">-- Select Action --</option>
                    <?php foreach($dep as $deps):?>
                        <option value="<?=$deps['dept_id']?>"><?=$deps['dept_name']?></option>
                    <?php endforeach; ?>    
                 </select>   
            </div>
            <div class="submit-container">
            <input type="submit" value="Add" class="add-btn" name="submit">
           </div>
        </form>
        </div>
          <?php if (!empty($message)): ?>
            <p class="success-message"><?php echo nl2br(htmlspecialchars($message)); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
