
<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Get each player's best completed game
$result = pg_query($conn, "
    SELECT username, errors, completion_seconds, created_at
    FROM (
        SELECT DISTINCT ON (u.id)
            u.id,
            u.username,
            gs.errors,
            gs.completion_seconds,
            gs.created_at
        FROM game_scores gs
        JOIN users u ON gs.user_id = u.id
        ORDER BY
            u.id,
            gs.errors ASC,
            gs.completion_seconds ASC,
            gs.created_at ASC
    ) AS best_scores
    ORDER BY
        errors ASC,
        completion_seconds ASC,
        created_at ASC
    LIMIT 10
");

if (!$result) {
    die("Unable to load leaderboard.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>QmA Leaderboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
    rel="stylesheet">
</head>

<body class="bg-dark text-white">

<div class="container mt-5">

    <h1 class="text-center mb-4">QmA Leaderboard</h1>

    <div class="table-responsive">

        <table class="table table-dark table-striped table-bordered text-center">

            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Username</th>
                    <th>Errors</th>
                    <th>Completion Time</th>
                </tr>
            </thead>

            <tbody>

                <?php
                $rank = 1;

                while ($row = pg_fetch_assoc($result)):

                    $minutes = floor($row['completion_seconds'] / 60);
                    $seconds = $row['completion_seconds'] % 60;
                ?>

                <tr>
                    <td>
                        <?php
                        if ($rank == 1) echo "🥇";
                        elseif ($rank == 2) echo "🥈";
                        elseif ($rank == 3) echo "🥉";
                        else echo $rank;
                        ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row['username']); ?>
                    </td>

                    <td>
                        <?php echo $row['errors']; ?>
                    </td>

                    <td>
                        <?php printf("%02d:%02d", $minutes, $seconds); ?>
                    </td>
                </tr>

                <?php
                    $rank++;
                endwhile;
                ?>

            </tbody>

        </table>

    </div>

    <div class="text-center mt-4">
        <a href="AssetsAndGame/game.php" class="btn btn-warning">
            Back to Game
        </a>
    </div>

</div>

</body>
</html>
