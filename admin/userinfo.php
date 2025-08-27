<?php
    require_once "../session_check.php";
    include_once "../nocache.php";
    include "../config.php";

    $userId=isset($_GET['user_id'])?$_GET['user_id'] : 0;

    try{
        $query=$pdo->prepare("
            SELECT * FROM users WHERE user_id=?
        ");
        $query->execute($userId);
        $users=$query->fetch();

    }catch(PDOException $e){

        echo "Failed:".$e->getMessage();
    }

    ob_start();

?>
<span style="float:right; cursor:pointer; font-size:22px; font-weight:bold;"
     onclick="document.getElementById('editUsersModal').style.display='none'">
        &times;</span>
  <h3>Update Users</h3>   
  
  <form action="" method="post">
    <input type="hidden" name="user_id" value="<?=htmlspecialchars($users['user_id'])?>">
    <label>User Name:</label>
    <input type="text" name="user_name" value="<?= htmlspecialchars($users['username']) ?>"><br>
  </form>