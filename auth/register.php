<?php
session_start();
include '../db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass  = $_POST['password'];
    $password = password_hash($pass, PASSWORD_DEFAULT);
    $check = $conn->query("SELECT id FROM users WHERE email='$email'");

    if ($check->num_row > 0) {
        $error = "Email already exists!";
    } else {
        $conn->query("INSERT INTO users (name, email, password)
                      VALUES ('$name', '$email', '$password')");

        header("Location: login.php");
        exit;
    }
}
?>

<form method="POST">
    <h2>Register</h2>
    <input type="text" name="name" placeholder="Full Name" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit" name="register">Register</button>
    <p style="color:red;"><?php echo $error; ?></p>
    <a href="login.php">Already have an account?</a>
</form>