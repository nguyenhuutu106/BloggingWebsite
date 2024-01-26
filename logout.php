<?php
    session_start();
    setcookie('userAccount','',time() + (365 * 24 * 60 * 60));
    unset($_SESSION['userId']);
    unset($_SESSION['userAccount']);
    unset($_SESSION['userName']);
    unset($_SESSION['userRole']);
    session_destroy();
    header("Location: index.php");
?>