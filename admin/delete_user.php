<?php 

    require_once "../session_check.php";
    include_once "../nocache.php";
    include "../config.php";
    header('Content-Type: application/json');

    $user=isset($_GET['user_id'])?$_GET['user_id'] : 0;
try{

    $pdo->beginTransaction();

    $stmt=$pdo->prepare("DELETE FROM users WHERE user_id=?");
    $success=$stmt->execute([$user]);
    $pdo->commit();
    echo json_encode(["success" => true, "message" => "User deleted sucessfully"]);

}catch(PDOException $e){
    echo json_encode(["success" => false, "message" => $e->getMessage()."User deletion failed"]);

}
?>