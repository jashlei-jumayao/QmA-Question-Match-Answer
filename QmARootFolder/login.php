<?php
session_start();
include('db.php');

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (empty($username)) {
        $errors[] = "Username is required";
    }

    $result = pg_query_params($conn, 'SELECT * FROM users WHERE username = $1', [$username]);
    $user = pg_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        if (isset($user['is_active']) && ($user['is_active'] === 'f' || $user['is_active'] === false)) {
            $errors[] = "Your account has been disabled.";
        } else {
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = ($user['is_admin'] === 't' || $user['is_admin'] === true);
            header("Location: LoggedIn/dashboard.php");
            exit();
        }
    } else {
        $errors[] = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="login_style.css">
</head>

<body class="min-vh-100 d-flex align-items-center py-4 px-4 px-md-5">

    <div class="container-fluid">
        <div class="row align-items-center justify-content-between g-4">

            <div class="col-12 col-md-7 d-flex justify-content-center align-items-center">
                <img src="Style_Assets/Logo.png" alt="Logo" class="Logo img-fluid">
            </div>

            <div class="col-12 col-md-5 d-flex justify-content-center align-items-center">
                <div class="login-panel login-actions p-4 p-md-5 text-white w-100" style="max-width: 440px;">
                    <h1 class="fw-bold mb-4 text-center" style="text-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);">Login</h1>

                    <form method="POST">
                        <div class="login-section mb-3 text-start">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <input type="text" id="username" name="username" class="form-control" required>
                        </div>

                        <div class="login-section mb-4 text-start">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            <button type="submit" name="login" class="btn w-100">Login</button>
                            <a href="register.php" class="btn w-100 text-decoration-none">Register</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</body>

</html>