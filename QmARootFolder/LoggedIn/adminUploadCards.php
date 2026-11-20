<?php
session_start();
include('../db.php');

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}


$username = $_SESSION['username'];
$result = pg_query_params($conn, 'SELECT is_admin FROM users WHERE username = $1', [$username]);
$user = pg_fetch_assoc($result);

if (!$user || $user['is_admin'] !== 't') {
    die("Access Denied: Admins only.");
}


if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES['q_image']) && isset($_FILES['a_image'])) {

    $qName = preg_replace('/[^a-zA-Z0-9]/', '', $_POST['q_name']);
    $aName = preg_replace('/[^a-zA-Z0-9]/', '', $_POST['a_name']);
    
    $qFile = $qName . ".png";
    $aFile = $aName . ".png";

    if (move_uploaded_file($_FILES['q_image']['tmp_name'], $qFile) && 
        move_uploaded_file($_FILES['a_image']['tmp_name'], $aFile)) {
        
        pg_query_params(
            $conn, 
            'INSERT INTO cards (question_card, answer_card) VALUES ($1, $2)', 
            [$qName, $aName]
        );
        echo "<p style='color:green;'>QnA Pair uploaded successfully!</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin_Upload Cards</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

    <div class="position-absolute top-0 end-0 p-3">
        <a href="AssetsAndGame/game.php" class="btn btn-outline-light fw-bold shadow-sm">Back to Game</a>
    </div>

     <div class="position-absolute top-0 start-0 p-3">
            <a href="dashboard.php" class="btn btn-warning fw-bold shadow-sm d-flex align-items-center gap-1" style="border-radius: 8px;">
                <span>⬅️</span> Back to Dashboard</a>
        </div>

    <div class="container d-flex justify-content-center">
        <div class="card glass-card p-4 text-white w-150">
            
            <h3 class="fw-bold text-center mb-4">⚙️ Upload Card Pair</h3>

            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <!-- Question Card File Input (Left Side) -->
                    <div class="col-md-6 mb-3">
                        <div class="p-3 border border-light rounded h-100" style="background: rgba(255, 255, 255, 0.05);">
                            <label for="question_image" class="form-label fw-bold">Question Card Image (.png)</label>
                            <input type="file" class="form-control" id="question_image" name="question_image" accept="image/png" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="p-3 border border-light rounded h-100" style="background: rgba(255, 255, 255, 0.05);">
                            <label for="answer_image" class="form-label fw-bold">Answer Card Image (.png)</label>
                            <input type="file" class="form-control" id="answer_image" name="answer_image" accept="image/png" required>
                        </div>
                    </div>
                </div>

                <div class="d-grid col-6 mx-auto mt-2">
                    <button type="submit" class="btn btn-custom fw-bold py-2 shadow-sm">Upload Pair</button>
                </div>
            </form>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success mt-3 text-center py-2" role="alert">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mt-3 py-2" role="alert">
                    <?php foreach ($errors as $error) { echo $error . "<br>"; } ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

</body>
</html>