<?php
session_start();
include '../db.php';
$message = "";

// Form submit
if (isset($_POST['reset'])) {

    $email = $_POST['email'];

    $result = $conn->query("SELECT id FROM users WHERE email='$email'");

    if ($result->num_rows > 0) {

        $tempPass = substr(bin2hex(random_bytes(4)), 0, 8);
        $hashedTemp = password_hash($tempPass, PASSWORD_DEFAULT);

        $conn->query("UPDATE users SET password='$hashedTemp' WHERE email='$email'");

        $message = "Your temporary password is: <b style='color:red'>$tempPass</b><br>Please login and change it immediately.";
    } else {
        $message = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div style="max-width:400px; margin:50px auto; text-align:center;" class="card">
    <h2>Reset Password</h2>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter your email" required><br><br>
        <button name="reset">Generate Temp Password</button>
    </form>

    <p><?php echo $message; ?></p>

    <a href="login.php">Back to Login</a>
</div>

</body>
</html>
