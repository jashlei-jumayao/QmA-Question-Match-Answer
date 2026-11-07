<?php
session_start();
include('../db.php');

// 1. Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 2. Check if user is an admin (You'll need to fetch their is_admin status from the DB)
$username = $_SESSION['username'];
$result = pg_query_params($conn, 'SELECT is_admin FROM users WHERE username = $1', [$username]);
$user = pg_fetch_assoc($result);

if (!$user || $user['is_admin'] !== 't') { // PostgreSQL booleans return as 't' or 'f'
    die("Access Denied: Admins only.");
}

// 3. Handle File Upload and Database Insert
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES['q_image']) && isset($_FILES['a_image'])) {
    // Sanitize both inputs
    $qName = preg_replace('/[^a-zA-Z0-9]/', '', $_POST['q_name']);
    $aName = preg_replace('/[^a-zA-Z0-9]/', '', $_POST['a_name']);
    
    $qFile = $qName . ".png";
    $aFile = $aName . ".png";

    // Upload both files
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
<html>
<body>
    <h3>Admin: Add QnA Pair</h3>
    <form method="POST" enctype="multipart/form-data">
        <label>Question Card Name:</label><br>
        <input type="text" name="q_name" required><br>
        <label>Question PNG Image:</label><br>
        <input type="file" name="q_image" accept=".png" required><br><br>
        
        <label>Answer Card Name:</label><br>
        <input type="text" name="a_name" required><br>
        <label>Answer PNG Image:</label><br>
        <input type="file" name="a_image" accept=".png" required><br><br>
        
        <button type="submit">Upload QnA Pair</button>
    </form>
</body>
</html>