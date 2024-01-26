<?php
    session_start();
    $conn=mysqli_connect('localhost','root','','blog') or die("no connect");
    mysqli_query($conn,'set names "utf8"');

    $_SESSION['userId'] = session_id();
    if(isset($_COOKIE['userAccount']))
    {
        $_SESSION['userAccount'] = $_COOKIE['userAccount'];
    }
    if(isset($_SESSION['userAccount']))
    {
        $account = $_SESSION['userAccount'];
        $account_select_query = "select * from users where UserAccount = '$account'";
        // Save all $_SESSION to variable $account to use (EX: $account[UserId])
        $account = mysqli_fetch_assoc(mysqli_query($conn,$account_select_query));
    }
?>