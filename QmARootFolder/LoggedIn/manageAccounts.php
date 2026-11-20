<?php
session_start();
include('../db.php');

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    die("Access Denied: Admins only.");
}

$me = $_SESSION['username'];
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $user = $_POST['username'];
    $action = $_POST['action'];

    if ($user != $me) {
        if ($action == "enable") {
            pg_query_params($conn, "UPDATE users SET is_active = TRUE WHERE username = $1", [$user]);
            $msg = "$user enabled.";
        }
        if ($action == "disable") {
            pg_query_params($conn, "UPDATE users SET is_active = FALSE WHERE username = $1", [$user]);
            $msg = "$user disabled.";
        }
        if ($action == "delete") {
            pg_query_params($conn, "DELETE FROM users WHERE username = $1 AND is_admin = FALSE", [$user]);
            $msg = "$user deleted.";
        }
    }
}

$result = pg_query($conn, "SELECT username, is_admin, is_active FROM users ORDER BY is_admin DESC, username ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Accounts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin_style.css">
</head>
<body>

<div class="position-absolute top-0 start-0 p-3">
    <a href="dashboard.php" class="btn btn-warning fw-bold">⬅️ Back to Dashboard</a>
</div>

<div class="container py-5 mt-4">
    <div class="card glass-card p-4 text-white mx-auto" style="max-width: 750px;">
        <h3 class="fw-bold text-center mb-3">🛡️ Manage Accounts</h3>

        <?php if ($msg != ""): ?>
            <div class="alert alert-success text-center py-2"><?php echo $msg; ?></div>
        <?php endif; ?>

        <table class="table table-dark table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = pg_fetch_assoc($result)): 
                    $isAdmin = ($row['is_admin'] == 't' || $row['is_admin'] === true);
                ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td>
                            <?php if ($isAdmin): ?>
                                <span class="badge bg-warning text-dark">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Player</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['is_active'] == 't'): ?>
                                <span class="badge bg-success">Enabled</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Disabled</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <?php if ($row['username'] == $me): ?>
                                <span class="text-muted small">You</span>
                            <?php elseif ($isAdmin): ?>
                                <span class="text-warning small">Protected Admin</span>
                            <?php else: ?>
                                <form method="POST">
                                    <input type="hidden" name="username" value="<?php echo htmlspecialchars($row['username']); ?>">
                                    <button name="action" value="enable" class="btn btn-sm btn-success">Enable</button>
                                    <button name="action" value="disable" class="btn btn-sm btn-warning">Disable</button>
                                    <button name="action" value="delete" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>