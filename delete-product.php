<?php
include 'db.php';
include 'middleware/auth_middleware.php';

admin_only();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $id = intval($id);
    $conn->query("DELETE FROM products WHERE id=$id");
}

header("Location: index.php");
exit;
