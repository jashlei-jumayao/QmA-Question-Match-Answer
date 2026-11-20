<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$errors = [];
$success = "";

$username = $_SESSION['username'];
$result = pg_query_params($conn, "SELECT * FROM users WHERE username = $1", [$username]);
$user = pg_fetch_assoc($result);

if (!$user) {
    session_destroy();
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (isset($_POST['update_username'])) {
        $newUsername = trim($_POST['username']);

        if (empty($newUsername)) {
            $errors[] = "Username is required";
        } elseif ($newUsername !== $username) {
            $checkResult = pg_query_params(
                $conn,
                "SELECT id FROM users WHERE username = $1 AND id != $2",
                [$newUsername, $user['id']]
            );

            if (pg_num_rows($checkResult) > 0) {
                $errors[] = "Username is already taken";
            }
        }

        if (empty($errors)) {
            $updateResult = pg_query_params(
                $conn,
                "UPDATE users SET username = $1 WHERE id = $2",
                [$newUsername, $user['id']]
            );

            if ($updateResult) {
                $_SESSION['username'] = $newUsername;
                $username = $newUsername;
                $user['username'] = $newUsername;
                $success = "Username updated successfully";
            } else {
                $errors[] = "Failed to update username";
            }
        }
    }

    if (isset($_POST['delete_account'])) {
        // Delete associated game scores first in case there is no ON DELETE CASCADE constraint
        pg_query_params($conn, "DELETE FROM game_scores WHERE user_id = $1", [$user['id']]);
        $deleteResult = pg_query_params($conn, "DELETE FROM users WHERE id = $1", [$user['id']]);

        if ($deleteResult) {
            session_destroy();
            header("Location: ../login.php");
            exit();
        } else {
            $errors[] = "Failed to delete account";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>

<body>

    <h1>Profile</h1>

    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        <button type="submit" name="update_username">Update</button>
    </form>

    <br>

    <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
        <button type="submit" name="delete_account">Delete Account</button>
    </form>

    <br>

    <button type="button" onclick="location.href='dashboard.php'">Back to Dashboard</button>

    <br><br>

    <?php
    if ($success) {
        echo htmlspecialchars($success);
    }

    foreach ($errors as $error) {
        echo htmlspecialchars($error) . "<br>";
    }
    ?>

</body>

</html>