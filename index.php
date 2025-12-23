<?php
include "db.php";

$cat = "";
$a = "";

if ($_SERVER["REQUEST_METHOD"] == "GET" && !empty($_GET['category'])) {
    $cat = $_GET['category'];
    $a = "WHERE category='$cat'";
}

$result = mysqli_query($conn, "SELECT * FROM products $a");

while ($row = mysqli_fetch_assoc($result)) {
    echo "
    <div>
        <b>{$row['name']}</b><br>
        Price: {$row['price']}<br>
        Category: {$row['category']}<br>
        <a href='del.php?id={$row['id']}'>Delete</a>
    </div><hr>";
}
?>

<form method="get">
    <select name="category">
        <option value="">ALL</option>
        <option value="mobile">Mobile</option>
        <option value="laptop">Laptop</option>
        <option value="accessory">Accessory</option>
    </select>
    <input type="submit" value="Submit">
</form>
