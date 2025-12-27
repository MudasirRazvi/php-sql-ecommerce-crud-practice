<?php
include "db.php";
include "middleware/auth_middleware.php";

protect_page();


$theme = 'light-mode';
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}

$search = "";
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $products = $conn->query(
        "SELECT * FROM products 
         WHERE name LIKE '%$search%' 
         OR category LIKE '%$search%'"
    );
} else {
    $products = $conn->query("SELECT * FROM products");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>E-Shop</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?php echo $theme; ?>">

<nav>
    <div>
        Welcome, <?php echo $_SESSION['user_name']; ?><br><br>

        Search Products: 
        <input type="text" placeholder="Search..." value="<?php echo $search; ?>" 
               onchange="window.location='?search='+this.value"><br><br>

        <button onclick="toggleDarkMode()">Toggle Dark / Light</button><br><br>

        <a href="user/profile.php">Profile</a><br>

        <?php 
        if ($_SESSION['role'] == 'admin') {
            echo '<a href="admin/dashboard.php">Admin Panel</a><br>';
            echo '<a href="admin/manage-product.php">Add Product</a><br>';
        }
        ?>

        <a href="auth/logout.php">Logout</a>
    </div>
</nav>

<hr>

<div class="product-grid">
<?php
if ($products->num_rows > 0) {
    while ($row = $products->fetch_assoc()) {
        echo "<div class='card'>";
        echo "<h3>".htmlspecialchars($row['name'])."</h3>";
        echo "<p>".htmlspecialchars($row['description'])."</p>";
        echo "<p><strong>$".$row['price']."</strong> | ".htmlspecialchars($row['category'])."</p>";

        if ($_SESSION['role'] == 'admin') {
            echo "<a class='btn-edit' href='admin/manage-product.php?id=".$row['id']."'>Edit</a>";
            echo "<a class='btn-danger' href='delete-product.php?id=".$row['id']."' onclick=\"return confirm('Delete product?')\">Delete</a>";
        }

        echo "</div>";
    }
} else {
    echo "<p>No products found.</p>";
}
?>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>
