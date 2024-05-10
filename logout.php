<?php
    session_start();
    unset($_SESSION['user']);
    unset($_SESSION['userID']);
    unset($_SESSION['userName']);
    header("Location: login.php");
?>