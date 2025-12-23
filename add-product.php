<?php include "db.php"; 

$name = $price = $category = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];
}

mysqli_query($conn, "INSERT INTO products (name, price, category) VALUES ('$name', '$price', '$category')");
?>

<form method="post">
    <input type="text" name="name" placeholder="Product Name">
    <input type="number" name="price" placeholder="Price">
    <select name="category">
        <option value="mobile">Mobile</option>
        <option value="mobile">Laptop</option>
        <option value="mobile">Accessory</option>
    </select>
    <button name="add">Add Product</button>
</form>
