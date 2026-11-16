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

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = ($user['is_admin'] === 't' || $user['is_admin'] === true);
            header("Location: dashboard.php");
            exit();
        }
    }

    if (empty($errors)) {
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="login_style.css" rel="stylesheet">
    <title>QmA-Question Match Answer</title>
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
                        <label for = "username" class ="form-label fw-bold">USERNAME</label>
                        <input type="text" name="username" class = "form-control" value="<?php echo (isset($_POST['username'])) ? $_POST['username'] : ""; ?>">
                    </div>
                    <br><br>
                    <div>
                        <label for="password" class="form-label fw-bold">PASSWORD</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <br><br>
                    <button type="submit" class="login_submit">LOGIN</button>
                </form>
                <br><br>
                <div class="text-center">
                    <a href="register.php" class = "text-decoration-none">REGISTER</a>
                </div>
                <br><br>
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