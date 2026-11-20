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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Style_Dashboard.css">
</head>
<body class="min-vh-100 d-flex align-items-center py-4 px-4 px-md-5">

    <div class="container-fluid">
        <div class="row align-items-center justify-content-between g-4">

            <div class="col-12 col-md-7 d-flex justify-content-center align-items-center">
                <img src="Style_Asset_2/Logo.png" alt="QMA - Question Match Answer" class="Logo img-fluid">
            </div>

            <div class="col-12 col-md-5 d-flex flex-column align-items-center text-center gap-4">
                <h1 class="fw-bold m-0 text-white" style="text-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);">
                    Welcome, <?php echo htmlspecialchars($username); ?>!
                </h1>

                <div class="dashboard-actions d-flex flex-column align-items-center gap-3 w-50">
                    <button type="button" class="btn w-100" onclick="location.href='AssetsAndGame/game.php'">Play</button>
                    <button type="button" class="btn w-100" onclick="location.href='leaderboard.php'">Leaderboards</button>
                    <button type="button" class="btn w-100" onclick="location.href='profile.php'">Profile</button>
                    <?php if ($isAdmin): ?>
                        <button type="button" class="btn w-100" onclick="location.href='adminUploadCards.php'">Upload Cards</button>
                        <button type="button" class="btn w-100" onclick="location.href='manageAccounts.php'">Manage Accounts</button>
                    <?php endif; ?>
                    <button type="button" class="btn logout-btn w-100" onclick="location.href='../logout.php'">Logout</button>
                </div>
            </div>

        </div>
    </div>

</body>