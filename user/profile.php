<?php
include '../db.php';
include '../middleware/auth_middleware.php';

protect_page();

$user_id = $_SESSION['user_id'];
$message = "";

if (isset($_POST['update'])) {

    $newName = $_POST['name'];
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
?>


<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav>
    <a href="../index.php">Home</a>
</nav>

<div style="max-width:400px; margin:50px auto;" class="card">
    <h2>Update Profile</h2>

    <?php if ($message != "") {
        echo "<p style='color:green'>$message</p>";
    } ?>

    <form method="POST">
        <label>Email (Cannot change):</label><br>
        <input type="text" value="<?php echo $user['email']; ?>" disabled><br><br>

        <label>Full Name:</label><br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required><br><br>

        <label>New Password:</label><br>
        <input type="password" name="password" placeholder="********"><br><br>

        <button name="update">Update Profile</button>
    </form>
</div>

</body>
</html>
