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

        echo "Registered Successfully";
    }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="register_style.css">
    <title>QmA Registration</title>
</head>

<body>    
    <div class = "container min-vh-100 d-flex align-items-center justify-content-center">
        <div class = "row w-100 align-items-center justify-content-between">
            <div class = "col-md-5 text-center mb-4 mb-md-0">
                <img class="Logo" src = "Style_Assets\Logo.png" alt = "Logo of the game">
        </div>
        <div class = "col-md-5 col-lg-4">
            <div class = "card shadow-sm p-4">
                <form method="POST">
                    <div>
                        <label for="username" class = "form-label fw-bold">USERNAME</label>
                        <input class = "form-control" type="text" name="username" value="<?php echo (isset($_POST['username'])) ? $_POST['username'] : ""; ?>">
                    </div>
                    <br><br>
                    <div>
                        <label class = "form-label fw-bold" for="password">PASSWORD</label>
                        <input class = "form-control" type="password" name="password">
                    </div>
                    <br><br>
                    <div>
                        <label class = "form-label fw-bold" for="confirm password">CONFIRM PASSWORD</label>
                        <input class = "form-control" type="password" name="confirm_password">
                    </div>
                    <br><br>
                    <button type="submit" class="register_submit">REGISTER</button>
                </form>
                <br><br>
                <div class = "text-center">
                    <a class = "text-decoration-none" href="login.php">LOGIN</a>
                </div> 
                <?php
                foreach ($errors as $error) {
                    echo $error . "<br>";
                    }
                    ?>
                    </div>
                </div>
            </div>  
        </div>
    </body>
    </html>