<?php
include '../db.php';
include '../middleware/auth_middleware.php';

admin_only();

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$product = [
    'name' => '',
    'description' => '',
    'price' => '',
    'category' => ''
];

$page_title = "Add New Product";

if ($id > 0) {
    $res = $conn->query("SELECT * FROM products WHERE id=$id");
    if ($res->num_rows > 0) {
        $product = $res->fetch_assoc();
        $page_title = "Edit Product: " . htmlspecialchars($product['name']);
    }
}

if (isset($_POST['save'])) {

    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $cat = $_POST['category'];

    if ($id > 0) {
        $conn->query("UPDATE products SET name='$name', description='$desc', price=$price, category='$cat' WHERE id=$id");
    } else {
        $conn->query("INSERT INTO products (name, description, price, category) VALUES ('$name', '$desc', $price, '$cat')");
    }

    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<nav>
    <a href="../index.php">← Back to Shop</a> | 
    <a href="dashboard.php">Admin Dashboard</a>
</nav>

<div style="max-width:600px; margin:40px auto; padding:30px;" class="card">
    <h2><?php echo $page_title; ?></h2>

    <form method="POST">
        <label>Product Name</label><br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br><br>

        <label>Description</label><br>
        <textarea name="description" rows="4" required><?php echo htmlspecialchars($product['description']); ?></textarea><br><br>

        <label>Price ($)</label><br>
        <input type="number" step="0.01" name="price" value="<?php echo $product['price']; ?>" required><br><br>

        <label>Category</label><br>
        <input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" placeholder="e.g. Electronics, Clothing"><br><br>

        <button type="submit" name="save"><?php echo $id > 0 ? 'Update Product' : 'Add Product'; ?></button>
        <a href="../index.php">Cancel</a>
    </form>
</div>

</body>
</html>
