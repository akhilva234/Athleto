
<?php
  session_start();
    include "../config.php";

    $message = "";


?> 
<?php
    
        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            $message = "User added successfully!";
        }

    if($_SERVER["REQUEST_METHOD"] == "POST"){

            $uname=$_POST['username'];
             $plainpassword=$_POST['password'];
            $cpassword=$_POST['cpassword'];
            $email=$_POST['email'];
            $role=$_POST['role-select'];
            $dep=$_POST['dep-select'];
            if (empty($dep)) {
                echo "Please select a valid department.";
                exit;
            }


            if ($plainpassword !== $cpassword) {
                echo "Passwords do not match.";
                exit;
            }
             $password=password_hash($_POST['password'],PASSWORD_DEFAULT);

            $idsql=$pdo->prepare("select dept_id from departments where dept_name=:dep");

            $idsql->execute([
                'dep' =>  $dep
            ]);

            $idExists=$idsql->fetch(PDO::FETCH_ASSOC);
    
            if($idExists){

                $id=$idExists['dept_id'];
            
                try{

                    $usersql=$pdo->prepare("insert into users(username,password,email,role,dept_id) 
                    values(:uname,:password,:email,:role,:dep_id)");

                    $success=$usersql->execute([
                        'uname' => $uname,
                        'password' => $password,
                        'email' => $email,
                        'role' => $role,
                        'dep_id' => $id
                    ]);
                    if($success){
                         header("Location: dashboard.php?page=add_user&status=success");
                            exit;
                    }       

                    else{

                        echo "failed to add user";
                    }

                }catch(PDOException $e){

                    echo "Transaction failed: " . $e->getMessage();
                }
            }else{

                echo "NO department found";
            }
            if (isset($_GET['status']) && $_GET['status'] == 'success') {
                    echo "<p>User added successfully!</p>";
                }    
    }
    
?>    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Athleto</title>
</head>
<body>
    <div class="insert-container">
        <h3 class="add-heading">Add Users</h3>
             <?php if (!empty($message)): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <div class="username">
                USERNAME
                <input type="text" placeholder="Username" name="username" class="username-input">
            </div>
            <div class="set-password">
                PASSWORD
                <input type="text" placeholder="Set Password" name="password" class="password-input">
            </div>
            <div class="confirm-password">
                <input type="text" placeholder="Confirm Password" name="cpassword" class="password-input">
            </div>
            <div class="add-email">
                <input type="email" placeholder="Email" name="email" class="email-input">
            </div>
            <div class="role-select-container">
                 Role<select name="role-select" id="" class="role-select">
                    <option value="">-- Select Action --</option>
                <option value="captain" class="role-captain">captain</option>
                <option value="faculty" class="role-faculty">faculty</option>
            </select>
            </div>
            <div class="dep-id-container">
                Department<select name="dep-select" id="" class="dep-select">
                    <option value="">-- Select Action --</option>
                    <option value="BCA" class="dep-options">BCA</option>
                    <option value="BBA" class="dep-options">BBA</option>
                    <option value="Bcom" class="dep-options">Bcom CA</option>
            </div>
           <div class="submit-container">
            <input type="submit" value="add" class="add-btn" name="submit">
           </div>
        </form>
    </div>
</body>
</html>
