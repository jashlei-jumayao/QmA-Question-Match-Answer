<?php
session_start();
include('../../db.php');

if (!isset($_SESSION['username'])) {
    header("Location: ../../login.php");
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
    <title>QmA-Question-Match-Answer</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../cards.css">
    <script>
        var dynamicCardList = <?php echo json_encode($dbCards); ?>;
    </script>
    <script src="cards.js"></script>
</head>
<body>

<div class="position-absolute top-0 start-0 p-3">
    <a href="../dashboard.php" class="btn game-nav-btn">Back to Dashboard</a>
</div>

<div class="position-absolute top-0 end-0 p-3 d-flex align-items-center gap-2">
    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
        <a href="../adminUploadCards.php" class="btn game-nav-btn">Upload Cards</a>
    <?php endif; ?>
    <a href="../leaderboard.php" class="btn game-nav-btn">View Leaderboard</a>
</div>

    <h2 class="text-white mt-4">Errors: <span id="errors">0</span></h2>
    <div id="gameStatus" class="text-white mt-3"></div>
    <div id="board"></div>

</body>
</html>