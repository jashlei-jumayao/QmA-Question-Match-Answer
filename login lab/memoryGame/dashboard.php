<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username'])) {
    header("../Location: login.php");
    exit();
}


// $sql = "DELETE FROM `users` WHERE `username` =  {$_SESSION['username']} ";
// $conn->query($sql);




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balatro Memory Game</title>
    <link rel="stylesheet" href="cards.css">
    <script src ="cards.js"></script>
</head>
    <body>
        <h2 style="color:white" >Errors: <span id="errors">0</span></h2>
        <div id="board"></div>
    </body>
</html>