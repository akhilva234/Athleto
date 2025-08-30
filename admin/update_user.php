<?php
require_once "../session_check.php";
include_once "../nocache.php";
include "../config.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = (int)$_POST['user_id'];
    $username = htmlspecialchars(trim($_POST['user_name']));
    $email = htmlspecialchars(trim($_POST['user_mail']));
    $role = htmlspecialchars(trim($_POST['user_role']));
    $dept = htmlspecialchars(trim($_POST['user_dept']));
    $newPassword = $_POST['user_new_pass'];
    $confirmPassword = $_POST['user_conf'];

    // Validation
    if (empty($userId) || empty($username) || empty($email) || empty($role)) {
        $_SESSION['message'] = "Failed: Required fields cannot be empty.";
        header("Location: adm_dashboard.php?page=adm_home&status=failure");
        exit();
    }
     $allowedRoles = ['captain', 'admin', 'faculty'];
    if (!in_array($role, $allowedRoles, true)) {
        $_SESSION['message'] = "Invalid role selected.";
        header("Location: adm_dashboard.php?page=adm_home&status=failure");
        exit();
    }

    try {
        // Base query (without password first)
        $sql = "UPDATE users 
                   SET username = :username, 
                       email = :email, 
                       role = :role";

        // Parameters for binding
        $params = [
            ':username' => $username,
            ':email'    => $email,
            ':role'     => $role,
            ':user_id'  => $userId
        ];

        // Check if password needs update
        if (!empty($newPassword)) {
            if ($newPassword !== $confirmPassword) {
                $_SESSION['message'] = "Failed: Passwords do not match.";
                header("Location: adm_dashboard.php?page=adm_home&status=failure");
                exit();
            }
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql .= ", password = :password";
            $params[':password'] = $hashedPassword;
        }

        // Add WHERE condition
        $sql .= " WHERE user_id = :user_id";

        // Execute update
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $_SESSION['message'] = "User updated successfully.";
        header("Location: adm_dashboard.php?page=adm_home&status=success");
        exit();

    } catch (PDOException $e) {
        $_SESSION['message'] = "Failed: " . $e->getMessage();
        header("Location: adm_dashboard.php?page=adm_home&status=failure");
        exit();
    }
}
?>
