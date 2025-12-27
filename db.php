<?php
$host = "localhost";
$user = "root";
$pass = "1234";
$dbname = "my_store_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
