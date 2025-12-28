<?php
include '../db.php';
session_start();

$error = "";
$success = "";

$themeClass = (isset($_COOKIE['theme']) && $_COOKIE['theme'] == 'dark') ? 'dark-mode' : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $query = "SELECT id FROM users WHERE email = '$email'";
    $res = $conn->query($query);

    if ($res->num_rows > 0) {
        $success = "Password reset instructions have been sent to your email.";
    } else {
        $error = "No account found with that email address.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | E-Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="<?php echo $themeClass; ?>">

    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header" style="text-align: center; margin-bottom: 1.5rem;">
                <div style="font-size: 3rem; margin-bottom: 10px;">🔒</div>
                <h2>Forgot Password?</h2>
                <p style="color: #64748b; font-size: 0.9rem;">No worries, we'll send you reset instructions.</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your registered email" required>
                </div>

                <button type="submit" class="btn-primary">Send Reset Link</button>
            </form>

            <div class="auth-footer">
                <a href="login.php" style="text-decoration: none; color: var(--primary); font-weight: 500;">
                    ← Back to Login
                </a>
            </div>
        </div>
    </div>

</body>
</html>