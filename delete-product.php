<?php
include 'db.php';
include 'middleware/auth_middleware.php';

admin_only();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Product ID
    $conn->query("DELETE FROM products WHERE id = $id");
}

header("Location: index.php");
exit;
