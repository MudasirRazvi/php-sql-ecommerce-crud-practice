<?php
include '../db.php';
include '../middleware/auth_middleware.php';

admin_only();

$productCount = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$userCount = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

$users = $conn->query("SELECT id, name, email, role, created_at FROM users");

$theme = 'light-mode';
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="<?php echo $theme; ?>">

<nav>
    <a href="../index.php">← Back to Shop</a> | 
    <strong>Admin Panel</strong> | 
    <button onclick="toggleDarkMode()">Toggle Dark / Light</button>
</nav>

<div style="padding: 20px;">

    <h2>System Overview</h2>
    <div style="display: flex; gap: 20px; margin-bottom: 30px;">
        <div class="card"><h3>Products: <?php echo $productCount; ?></h3></div>
        <div class="card"><h3>Users: <?php echo $userCount; ?></h3></div>
    </div>

    <h2>Registered Users</h2>
    <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($u = $users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo $u['role']; ?></td>
                    <td><?php echo $u['created_at']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</div>

<script src="../assets/js/script.js"></script>
</body>
</html>