<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];
$isAdmin = isset($_SESSION['is_admin']) && ($_SESSION['is_admin'] === true || $_SESSION['is_admin'] === 't');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>

    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

    <button type="button" onclick="location.href='AssetsAndGame/game.php'">Play</button>
    <button type="button" onclick="location.href='leaderboard.php'">Check Leaderboard</button>
    <button type="button" onclick="location.href='profile.php'">Profile</button>
    <?php if ($isAdmin): ?>
        <button type="button" onclick="location.href='adminUploadCards.php'">Upload Cards</button>
    <?php endif; ?>
    <button type="button" onclick="location.href='../logout.php'">Logout</button>

</body>
</html>