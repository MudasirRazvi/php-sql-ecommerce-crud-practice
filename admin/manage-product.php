<?php
include "../db.php";
include "../middleware/auth_middleware.php";

admin_only();

$id = isset($_GET['id']) ? $_GET['id'] : "";

$name = "";
$desc = "";
$price = "";
$cat = "";

if ($id != "") {
    $id = mysqli_real_escape_string($conn, $id); // Sanitize ID
    $res = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
    if ($row = mysqli_fetch_assoc($res)) {
        $name = $row['name'];
        $desc = $row['description'];
        $price = $row['price'];
        $cat = $row['category'];
    }
}

if (isset($_POST['save'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $desc = mysqli_real_escape_string($conn, $_POST['desc']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $cat = mysqli_real_escape_string($conn, $_POST['cat']);

    if ($id != "") {
        mysqli_query($conn, "UPDATE products SET name='$name', description='$desc', price='$price', category='$cat' WHERE id=$id");
    } else {
        mysqli_query($conn, "INSERT INTO products (name, description, price, category) VALUES ('$name','$desc','$price','$cat')");
    }

    header("Location: ../index.php"); // Redirect after save
    exit;
}

$themeClass = (isset($_COOKIE['theme']) && $_COOKIE['theme'] == 'dark') ? 'dark-mode' : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($id != "") ? "Edit Product" : "Add Product"; ?> | E-Shop Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="<?php echo $themeClass; ?>">

    <div class="auth-wrapper">
        <div class="auth-card" style="max-width: 600px;"> <!-- Product form ke liye thoda wide card -->
            <div style="margin-bottom: 25px;">
                <a href="../index.php" style="text-decoration: none; color: var(--text-muted); font-size: 0.9rem;">← Cancel & Go Back</a>
                <h2 style="margin-top: 15px; text-align: left;">
                    <?php echo ($id != "") ? "Update Product" : "Create New Product"; ?>
                </h2>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Fill in the information below to <?php echo ($id != "") ? "edit" : "add"; ?> a product.</p>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" placeholder="e.g. Wireless Headphones" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="cat" placeholder="e.g. Electronics" value="<?php echo htmlspecialchars($cat); ?>" required>
                </div>

                <div class="form-group">
                    <label>Price ($)</label>
                    <input type="number" step="0.01" name="price" placeholder="0.00" value="<?php echo htmlspecialchars($price); ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="desc" rows="5" style="width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 8px; background: var(--bg); color: var(--text-main); font-family: inherit; resize: vertical;" placeholder="Write a short description about the product..."><?php echo htmlspecialchars($desc); ?></textarea>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" name="save" class="btn-primary" style="width: 100%; padding: 14px;">
                        <?php echo ($id != "") ? "Save Changes" : "Publish Product"; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>