<?php
session_start();
header("Location: memoryGame/dashboard.php");
include('db.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}


// $sql = "DELETE FROM `users` WHERE `username` =  {$_SESSION['username']} ";
// $conn->query($sql);




?>
