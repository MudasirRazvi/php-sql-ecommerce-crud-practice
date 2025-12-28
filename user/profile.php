<?php
include '../db.php';
include '../middleware/auth_middleware.php';

protect_page();

$user_id = $_SESSION['user_id'];
$message = "";

if (isset($_POST['update'])) {
    // Basic Sanitization
    $newName = mysqli_real_escape_string($conn, $_POST['name']);
    $newPass = $_POST['password'];

    if ($newPass != "") {
        $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET name='$newName', password='$hashedPass' WHERE id=$user_id");
    } else {
        $conn->query("UPDATE users SET name='$newName' WHERE id=$user_id");
    }

    $_SESSION['user_name'] = $newName;
    $message = "Profile updated successfully!";
}

$res = $conn->query("SELECT name, email FROM users WHERE id=$user_id");
$user = $res->fetch_assoc();

// Dark Mode logic
$themeClass = (isset($_COOKIE['theme']) && $_COOKIE['theme'] == 'dark') ? 'dark-mode' : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | E-Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="<?php echo $themeClass; ?>">

    <nav>
        <div style="display: flex; align-items: center; gap: 20px;">
            <a href="../index.php" style="text-decoration: none; color: var(--text-main); font-weight: 500;">← Back to Home</a>
        </div>
        <div class="logo" style="font-size: 1.1rem;">User Profile</div>
    </nav>

    <div class="auth-wrapper">
        <div class="auth-card">
            <div style="text-align: center; margin-bottom: 25px;">
                <!-- User Avatar Circle -->
                <div style="width: 80px; height: 80px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: bold; margin: 0 auto 15px;">
                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                </div>
                <h2 style="margin: 0;">Profile Settings</h2>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Manage your account details</p>
            </div>

            <?php if ($message != ""): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="text" value="<?php echo $user['email']; ?>" disabled 
                           style="background: var(--bg); cursor: not-allowed; opacity: 0.7;">
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Email cannot be changed.</small>
                </div>

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label>New Password</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current">
                    <small style="color: var(--text-muted); font-size: 0.75rem;">Only fill this if you want to change password.</small>
                </div>

                <button type="submit" name="update" class="btn-primary" style="width: 100%;">
                    Update Profile
                </button>
            </form>
        </div>
    </div>

</body>
</html>