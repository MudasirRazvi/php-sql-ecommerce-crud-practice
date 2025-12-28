<?php
include 'db.php';
include 'middleware/auth_middleware.php';

protect_page(); // Function name check karlein (protect_page ya protectPage)

$limit = 6; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : '';
$cat = isset($_GET['cat']) ? $_GET['cat'] : '';

// --- Price Range Inputs ---
$min_price = (isset($_GET['min_price']) && $_GET['min_price'] !== '') ? (float)$_GET['min_price'] : '';
$max_price = (isset($_GET['max_price']) && $_GET['max_price'] !== '') ? (float)$_GET['max_price'] : '';

$searchTerm = "%$search%";
$cat_result = $conn->query("SELECT DISTINCT category FROM products");

// --- Build Products Query ---
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if ($search != '') {
    $sql .= " AND (name LIKE ? OR description LIKE ? OR category LIKE ?)";
    array_push($params, $searchTerm, $searchTerm, $searchTerm);
    $types .= "sss";
}

if ($cat != '') {
    $sql .= " AND category = ?";
    $params[] = $cat;
    $types .= "s";
}

// Price range conditions
if ($min_price !== '') {
    $sql .= " AND price >= ?";
    $params[] = $min_price;
    $types .= "d"; // 'd' for double/decimal
}
if ($max_price !== '') {
    $sql .= " AND price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

$sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
array_push($params, $limit, $offset);
$types .= "ii";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// --- Count for Pagination (Price Filter ke sath) ---
$count_sql = "SELECT COUNT(*) as total FROM products WHERE 1=1";
$c_params = []; $c_types = "";

if ($search != '') {
    $count_sql .= " AND (name LIKE ? OR description LIKE ? OR category LIKE ?)";
    array_push($c_params, $searchTerm, $searchTerm, $searchTerm);
    $c_types .= "sss";
}
if ($cat != '') {
    $count_sql .= " AND category = ?";
    $c_params[] = $cat; $c_types .= "s";
}
if ($min_price !== '') {
    $count_sql .= " AND price >= ?";
    $c_params[] = $min_price; $c_types .= "d";
}
if ($max_price !== '') {
    $count_sql .= " AND price <= ?";
    $c_params[] = $max_price; $c_types .= "d";
}

$count_stmt = $conn->prepare($count_sql);
if(!empty($c_params)) $count_stmt->bind_param($c_types, ...$c_params);
$count_stmt->execute();
$total_rows = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

$themeClass = (isset($_COOKIE['theme']) && $_COOKIE['theme'] == 'dark') ? 'dark-mode' : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore | E-Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Price Inputs ko chota aur neat karne ke liye extra style */
        .price-inputs { display: flex; align-items: center; gap: 5px; background: #f1f5f9; padding: 0 10px; border-right: 1px solid #ddd; }
        .price-inputs input { width: 60px !important; padding: 5px !important; border: 1px solid #ccc !important; font-size: 12px; }
        .price-inputs span { color: #64748b; font-size: 12px; font-weight: bold; }
        
        /* Dark mode adjustment for price inputs */
        .dark-mode .price-inputs { background: #1e293b; border-color: #334155; }
        .dark-mode .price-inputs input { background: #0f172a; color: white; border-color: #475569; }
    </style>
</head>

<body class="<?php echo $themeClass; ?>">

<nav>
    <a href="index.php" class="logo">E-SHOP</a>

    <form method="GET" class="filter-form">
        <!-- Category Dropdown -->
        <select name="cat" onchange="this.form.submit()">
            <option value="">All Categories</option>
            <?php
            $cat_result->data_seek(0);
            while ($c = $cat_result->fetch_assoc()) {
                $selected = ($cat == $c['category']) ? 'selected' : '';
                echo '<option value="'.$c['category'].'" '.$selected.'>'.$c['category'].'</option>';
            }
            ?>
        </select>

        <!-- Price Range Inputs -->
        <div class="price-inputs">
            <span>$</span>
            <input type="number" name="min_price" placeholder="Min" value="<?php echo $min_price; ?>">
            <span>to</span>
            <input type="number" name="max_price" placeholder="Max" value="<?php echo $max_price; ?>">
        </div>

        <!-- Search Input -->
        <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn-primary">Search</button>
    </form>

    <div class="nav-links">
        <button onclick="toggleDarkMode()" class="btn-primary" style="padding: 5px 12px; font-size: 12px;">🌓 Theme</button>
        <a href="user/profile.php">Profile</a>
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <a href="admin/dashboard.php" style="color: var(--primary); font-weight: bold;">Admin</a>
        <?php endif; ?>
        <a href="auth/logout.php" class="btn-danger" style="padding: 5px 12px; color:white;">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="product-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <span class="category-badge"><?php echo $row['category']; ?></span>
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p class="desc"><?php echo htmlspecialchars(substr($row['description'], 0, 100)); ?>...</p>
                    <p class="price">$<?php echo number_format($row['price'], 2); ?></p>

                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <div class="admin-actions" style="display: flex; gap: 10px; margin-top: 15px; border-top: 1px solid var(--border); padding-top: 15px;">
                            <a href="admin/manage-product.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                            <a href="delete-product.php?id=<?php echo $row['id']; ?>" class="btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                <p style="font-size: 1.2rem; color: var(--text-muted);">No products found matching your criteria.</p>
                <a href="index.php" style="color: var(--primary);">Clear all filters</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Updated Pagination with Price Range -->
    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <?php 
                $url_params = http_build_query([
                    'page' => $i,
                    'search' => $search,
                    'cat' => $cat,
                    'min_price' => $min_price,
                    'max_price' => $max_price
                ]);
                ?>
                <a href="?<?php echo $url_params; ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<script src="assets/js/script.js"></script>
</body>
</html>