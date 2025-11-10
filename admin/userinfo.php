<?php
require_once "../session_check.php";
include_once "../nocache.php";
include "../config.php";

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

try {
    $query = $pdo->prepare("
        SELECT u.username, u.role, u.user_id, u.email, hd.hd_name
        FROM users u
        JOIN headdepartment hd ON u.hd_id = hd.hd_id
        WHERE u.user_id = ?
    ");
    $query->execute([$userId]);
    $users = $query->fetch(PDO::FETCH_ASSOC);

    if (!$users) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'User not found']);
        exit;
    }

    ob_start();
    ?>
    <div class="modal-content">
        <span class="close-btn" onclick="document.querySelector('.modal').style.display='none'">&times;</span>
        <h3>Update User</h3>

        <form action="" method="post">
            <input type="hidden" name="user_id" value="<?=htmlspecialchars($users['user_id'])?>">

            <div class="form-group">
                <label for="user_name">User Name</label>
                <input type="text" id="user_name" name="user_name" value="<?=htmlspecialchars($users['username'])?>" required>
            </div>

            <div class="form-group">
                <label for="user_mail">Email</label>
                <input type="email" id="user_mail" name="user_mail" value="<?=htmlspecialchars($users['email'])?>" required>
            </div>

            <div class="form-group">
                <label for="user_role">Role</label>
                <input type="text" id="user_role" name="user_role" value="<?=htmlspecialchars($users['role'])?>" required>
            </div>

            <div class="form-group">
                <label for="user_new_pass">New Password</label>
                <input type="password" id="user_new_pass" name="user_new_pass" placeholder="Leave blank to keep current">
            </div>

            <div class="form-group">
                <label for="user_conf">Confirm Password</label>
                <input type="password" id="user_conf" name="user_conf" placeholder="Confirm new password">
            </div>

            <button type="submit" name="update">Update</button>
        </form>
    </div>
    <?php
    $htmlForm = ob_get_clean();
    header('Content-Type: application/json');
    echo json_encode(['html' => $htmlForm]);
    exit;

} catch(PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}
