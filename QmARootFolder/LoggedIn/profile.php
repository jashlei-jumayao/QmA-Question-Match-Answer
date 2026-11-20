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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Style_Profile.css">
</head>

<body class="min-vh-100 d-flex align-items-center py-4 px-4 px-md-5">

    <div class="container-fluid">
        <div class="row align-items-center justify-content-between g-4">

            <div class="col-12 col-md-6 d-flex justify-content-center align-items-center">
                <img src="Style_Asset_2/Logo.png" alt="Logo" class="Logo img-fluid">
            </div>

            <div class="col-12 col-md-6 d-flex justify-content-center align-items-center">
                <div class="profile-panel profile-actions p-4 p-md-5 text-white text-center w-100" style="max-width: 440px;">
                    <h1 class="fw-bold mb-4" style="text-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);">Profile</h1>

                    <form method="POST" class="mb-3">
                        <div class="mb-3 text-start">
                            <label for="username" class="form-label fw-semibold">Username:</label>
                            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <button type="submit" name="update_username" class="btn w-100">Update</button>
                    </form>

                    <form method="POST" class="mb-3" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
                        <button type="submit" name="delete_account" class="btn delete-btn w-100">Delete Account</button>
                    </form>

                    <button type="button" class="btn w-100" onclick="location.href='dashboard.php'">Back to Dashboard</button>

                    <?php if ($success || !empty($errors)): ?>
                        <div class="mt-3 small fw-semibold">
                            <?php
                            if ($success) {
                                echo '<div class="text-white">' . htmlspecialchars($success) . '</div>';
                            }

                            foreach ($errors as $error) {
                                echo '<div class="text-warning">' . htmlspecialchars($error) . '</div>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

</body>

</html>