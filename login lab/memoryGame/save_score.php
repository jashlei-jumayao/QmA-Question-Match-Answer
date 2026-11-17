
<?php
session_start();
include('../db.php');

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (
    !isset($data['errors']) ||
    !isset($data['completion_seconds']) ||
    !is_numeric($data['errors']) ||
    !is_numeric($data['completion_seconds'])
) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid score']);
    exit();
}

$errors = (int)$data['errors'];
$completionSeconds = (int)$data['completion_seconds'];

if ($errors < 0 || $completionSeconds < 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid score']);
    exit();
}

$username = $_SESSION['username'];

$userResult = pg_query_params(
    $conn,
    'SELECT id FROM users WHERE username = $1 LIMIT 1',
    [$username]
);

if (!$userResult || pg_num_rows($userResult) == 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

$user = pg_fetch_assoc($userResult);

$result = pg_query_params(
    $conn,
    'INSERT INTO game_scores (user_id, errors, completion_seconds)
     VALUES ($1, $2, $3)
     RETURNING id',
    [$user['id'], $errors, $completionSeconds]
);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Score saved']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save score']);
}
?>
