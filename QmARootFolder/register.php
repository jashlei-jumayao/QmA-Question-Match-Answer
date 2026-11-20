<?php

include('db.php');

$errors = [];



if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($username)) {
        $errors[] = "Username is required";
    } else if (strlen($username) < 8) {
        $errors[] = "Username must be 8 characters or more";
    }

    $usernamePattern = '/^(?=.*[\S])(?=.*[\s]).+$/';

    if (preg_match($usernamePattern, $username)) {
        $errors[] = "Username must not have white space";
    }

    // " ynzon salas "
    $result = pg_query_params($conn, 'SELECT username FROM users WHERE username = $1', [$username]);

    if (pg_fetch_assoc($result)) {
        $errors[] = "Username already exist";
    }

    if ($password != $confirmPassword) {
        $errors[] = "Password did not match";
    }
    if (empty($password) || empty($confirmPassword)) {
        $errors[] = "Please provide a passsword";
    }
    $pattern = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*[\W_]).+$/';

    if (!preg_match($pattern, $password)) {
        $errors[] = "password must contain lower, upper case and special characters";
    }

    if (strlen($password) < 8) {
        $errors[] = "password must be 8 or more characters long";
    }

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);
    if (empty($errors)) {
        pg_query_params(
            $conn,
            'INSERT INTO users (username, password) VALUES ($1, $2)',
            [$username, $passwordHash]
        );

        header("Location: login.php");
    }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="register_style.css?v=2">
</head>

<body class="min-vh-100 d-flex align-items-center py-4 px-4 px-md-5">

    <div class="container-fluid">
        <div class="row align-items-center justify-content-between g-4">

            <div class="col-12 col-md-7 d-flex justify-content-center align-items-center">
                <img src="Style_Assets/Logo.png" alt="QMA - Question Match Answer" class="Logo img-fluid">
            </div>

            <div class="col-12 col-md-5 d-flex justify-content-center align-items-center">
                <div class="register-panel register-actions p-4 p-md-5 text-white w-100" style="max-width: 440px;">
                    <h1 class="fw-bold mb-4 text-center" style="text-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);">Register</h1>

                    <form method="POST">
                        <div class="register-section mb-3 text-start">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <input type="text" id="username" name="username" class="form-control" required>
                        </div>

                        <div class="register-section mb-3 text-start">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        <div class="register-section mb-4 text-start">
                            <label for="confirm_password" class="form-label fw-semibold">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            <button type="submit" name="register" class="btn w-100">Register</button>
                            <a href="login.php" class="btn w-100 text-decoration-none">Login</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</body>

</html>