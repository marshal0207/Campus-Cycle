<?php
// filepath: c:\xampp\htdocs\CampusCycle\admin_dashboard.php
session_start();

// Optional: Only allow admin users (add your own admin check here)
// if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
//     header("Location: login.php");
//     exit();
// }

$conn = new mysqli("localhost", "root", "", "campuscycle");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id=$del_id");
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all users
$result = $conn->query("SELECT id, fullname, email FROM users ORDER BY id DESC");
$users = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard - Campus Cycle</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', Arial, sans-serif; background: #181818; color: #fff; margin: 0; }
        .admin-container { max-width: 800px; margin: 40px auto; background: #232323; border-radius: 12px; box-shadow: 0 0 15px rgba(255,152,0,0.2); padding: 32px 24px; }
        h2 { color: #ff9800; text-align: center; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; background: #1e1e1e; border-radius: 8px; overflow: hidden; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #333; text-align: left; }
        th { background: #ff9800; color: #181818; font-weight: 600; }
        tr:last-child td { border-bottom: none; }
        .delete-btn { background: #ff3b3b; color: #fff; border: none; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-size: 0.95em; }
        .delete-btn:hover { background: #c62828; }
        @media (max-width: 600px) {
            .admin-container { padding: 12px 4px; }
            th, td { padding: 8px 5px; font-size: 0.95em; }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <h2>Admin Dashboard - Manage Users</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <form method="get" style="display:inline;">
                                    <input type="hidden" name="delete" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="delete-btn" onclick="return confirm('Delete this user?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; color:#ccc;">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>