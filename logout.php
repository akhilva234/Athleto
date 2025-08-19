<?php
session_start();
session_unset();      // remove all session variables
session_destroy();    // destroy the session file
header("Location: ../login.php");
exit();
?>
