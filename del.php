<?php
include "db.php";
$id = $_GET['id'];
mysqli_query($conn, "DELETE FROM products Where id=$id");
header("Location: index.php");