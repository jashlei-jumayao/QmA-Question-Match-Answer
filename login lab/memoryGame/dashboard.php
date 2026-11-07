<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username'])) {
    header("../Location: login.php");
    exit();
}

// $sql = "DELETE FROM `users` WHERE `username` =  {$_SESSION['username']} ";
// $conn->query($sql);

$result = pg_query($conn, 'SELECT id, question_card, answer_card FROM cards ORDER BY RANDOM() LIMIT 10');
$dbCards = [];
while ($row = pg_fetch_assoc($result)) {
    $dbCards[] = [
        'id' => $row['id'],
        'q' => $row['question_card'],
        'a' => $row['answer_card']
    ];
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balatro Memory Game</title>
    <link rel="stylesheet" href="cards.css">
    <script>
        var dynamicCardList = <?php echo json_encode($dbCards); ?>;
    </script>
    <script src ="cards.js"></script>
</head>
    <body>

        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
            <div style="text-align: center; margin-top: 10px;">
                <a href="admin.php" style="color: yellow; text-decoration: none; font-weight: bold;">⚙️ Admin: Upload Cards</a>
            </div>
        <?php endif; ?>

        <h2 style="color:white" >Errors: <span id="errors">0</span></h2>
        <div id="board"></div>
    </body>
</html>