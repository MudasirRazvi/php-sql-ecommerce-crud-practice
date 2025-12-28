<?php
include '../db.php';
session_start();

$error = "";
$success = "";

if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password != $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $query = "SELECT id FROM users WHERE email = '$email'";
        $res = $conn->query($query);
        if ($res->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed', 'user')";
            if ($conn->query($insert)) {
                $success = "Registration successful! Redirecting...";
                header("refresh:2;url=login.php"); // Redirect after success
            } else {
                $error = "Something went wrong!";
            }
        }
    }
}

$themeClass = (isset($_COOKIE['theme']) && $_COOKIE['theme'] == 'dark') ? 'dark-mode' : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | E-Shop</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .alert-success {
            background: #dcfce7;
            color: #166534;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>

<body class="<?php echo $themeClass; ?>">

    <div class="auth-wrapper">
        <div class="auth-card">
            <h2>Create Account</h2>

            <?php if ($error != "") {
                echo '<div class="alert-error">' . $error . '</div>';
            } ?>
            <?php if ($success != "") {
                echo '<div class="alert-success">' . $success . '</div>';
            } ?>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="name@example.com" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-primary">Register Now</button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="login.php">Sign In</a>
            </div>
        </div>
    </div>

</body>

</html>