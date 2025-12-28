<?php
include '../db.php';
include '../middleware/auth_middleware.php';

admin_only();

$productCount = $conn->query("SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];
$userCount = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

$users = $conn->query("SELECT id, name, email, role, created_at FROM users");

$theme = (isset($_COOKIE['theme']) && $_COOKIE['theme'] == 'dark') ? 'dark-mode' : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | E-Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="<?php echo $theme; ?>">

<nav>
    <div style="display: flex; align-items: center; gap: 20px;">
        <a href="../index.php" style="text-decoration: none; color: var(--text-main); font-weight: 500;">← Back to Shop</a>
        <span style="color: var(--border);">|</span>
        <span class="logo" style="font-size: 1.1rem;">Admin Dashboard</span>
    </div>
    <button onclick="toggleDarkMode()" class="btn-primary" style="padding: 5px 15px;">🌓 Toggle Theme</button>
</nav>

<div class="container">
    <h2 style="margin-top: 30px;">System Overview</h2>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Products</h3>
            <span class="number"><?php echo $productCount; ?></span>
        </div>
        <div class="stat-card">
            <h3>Registered Users</h3>
            <span class="number"><?php echo $userCount; ?></span>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">Registered Users</h2>
        <button class="btn-primary">+ Add New User</button>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Joined Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($u = $users->fetch_assoc()): ?>
                    <tr>
                        <td style="color: var(--text-muted);">#<?php echo $u['id']; ?></td>
                        <td style="font-weight: 600;"><?php echo htmlspecialchars($u['name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><span class="role-badge"><?php echo strtoupper($u['role']); ?></span></td>
                        <td style="color: var(--text-muted); font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($u['created_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>