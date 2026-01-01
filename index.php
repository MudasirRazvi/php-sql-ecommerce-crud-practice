<?php
include 'db.php';
include 'middleware/auth_middleware.php';

protect_page();

/* Basic pagination and filter configuration */
$limit = 9;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$skip = ($page - 1) * $limit;

/* User filters */
$search = $_GET['search'] ?? '';
$cat = $_GET['cat'] ?? '';
$min_price = (isset($_GET['min_price']) && $_GET['min_price'] !== '') ? (float)$_GET['min_price'] : '';
$max_price = (isset($_GET['max_price']) && $_GET['max_price'] !== '') ? (float)$_GET['max_price'] : '';

/* API caching function to improve performance */
function fetch_api_data($url)
{
    $cache_dir = 'cache/';
    if (!is_dir($cache_dir)) mkdir($cache_dir, 0777, true);

    $cache_file = $cache_dir . md5($url) . '.json';
    $cache_time = 3600;

    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
        return json_decode(file_get_contents($cache_file), true);
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10
    ]);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        file_put_contents($cache_file, $response);
        return json_decode($response, true);
    }

    return null;
}

/* Build API endpoint based on filters */
if ($search !== '') {
    $api_url = "https://dummyjson.com/products/search?q=" . urlencode($search) . "&limit=0";
} elseif ($cat !== '') {
    $api_url = "https://dummyjson.com/products/category/" . urlencode($cat) . "?limit=0";
} else {
    $api_url = "https://dummyjson.com/products?limit=0";
}

/* Fetch products */
$data = fetch_api_data($api_url);
$all_products = $data['products'] ?? [];

/* Apply price range filtering */
$filtered_products = [];
foreach ($all_products as $p) {
    if (
        ($min_price === '' || $p['price'] >= $min_price) &&
        ($max_price === '' || $p['price'] <= $max_price)
    ) {
        $filtered_products[] = $p;
    }
}

/* Pagination after filtering */
$total_rows = count($filtered_products);
$total_pages = ceil($total_rows / $limit);
$products = array_slice($filtered_products, $skip, $limit);

/* Fetch categories for dropdown */
$categories_data = fetch_api_data("https://dummyjson.com/products/categories");

/* Theme handling */
$themeClass = (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') ? 'dark-mode' : '';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explore | E-Shop API</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pagination.css">
    <style>
        .price-inputs {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .price-inputs input {
            width: 80px;
        }

    </style>
</head>

<body class="<?php echo $themeClass; ?>">

    <!-- NAVBAR START -->
    <nav>
        <a href="index.php" class="logo" style="font-size: 24px; font-weight: 800; color: var(--primary); text-decoration: none;">E-SHOP</a>

        <form method="GET" style="display: flex; gap: 10px; align-items: center; flex-grow: 1; max-width: 700px; margin: 0 20px;">
            <!-- Category -->
            <select name="cat" onchange="this.form.submit()" style="padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                <option value="">All Categories</option>
                <?php foreach ($categories_data as $c): ?>
                    <?php $c_slug = is_array($c) ? $c['slug'] : $c; ?>
                    <option value="<?php echo $c_slug; ?>" <?php echo ($cat == $c_slug) ? 'selected' : ''; ?>>
                        <?php echo ucfirst(is_array($c) ? $c['name'] : $c); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Price Range -->
            <div class="price-inputs">
                <span>$</span>
                <input type="number" name="min_price" placeholder="Min" value="<?php echo $min_price; ?>">
                <span>-</span>
                <input type="number" name="max_price" placeholder="Max" value="<?php echo $max_price; ?>">
            </div>

            <!-- Search -->
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>" style="flex-grow: 1; padding: 8px; border-radius: 5px; border: 1px solid #ddd;">

            <button type="submit" class="btn-primary" style="padding: 8px 20px;">Search</button>
        </form>

        <div class="nav-links" style="display: flex; gap: 15px; align-items: center;">
            <button onclick="toggleDarkMode()" class="btn-primary" style="padding: 6px 12px; font-size: 12px; cursor: pointer;">🌓 Theme</button>
            <a href="user/profile.php" style="text-decoration: none; color: var(--text);">Profile</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                <a href="admin/dashboard.php" style="color: var(--primary); font-weight: bold; text-decoration: none;">Admin</a>
            <?php endif; ?>
            <a href="auth/logout.php" class="btn-danger" style="padding: 6px 15px; border-radius: 5px; text-decoration: none; color: white;">Logout</a>
        </div>
    </nav>
    <!-- NAVBAR END -->

    <div class="container" style="padding: 30px 5%;">
        <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 25px;">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $row): ?>
                    <div class="card" style="background: var(--bg-card); padding: 15px; border-radius: 15px; box-shadow: var(--shadow); transition: transform 0.3s;">
                        <img src="<?php echo $row['thumbnail']; ?>" loading="lazy" class="product-img" alt="product">
                        <span class="category-badge" style="font-size: 10px; background: var(--primary-light); color: var(--primary); padding: 3px 8px; border-radius: 5px;"><?php echo $row['category']; ?></span>
                        <h3 style="font-size: 18px; margin: 10px 0; height: 45px; overflow: hidden;"><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class="price" style="font-size: 20px; font-weight: 700; color: var(--primary);">$<?php echo number_format($row['price'], 2); ?></p>
                        <div style="margin-top: 15px; display: flex; gap: 10px;">
                            <button class="btn-primary" style="flex: 1; padding: 10px; border-radius: 8px;">View</button>
                            <button class="btn-edit" style="padding: 10px; border-radius: 8px;">🛒</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 100px;">
                    <h2>No products found!</h2>
                    <a href="index.php" style="color: var(--primary);">Refresh Page</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- SMART PAGINATION -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-container">
                <div class="pagination">
                    <?php
                    $range = 2;
                    $show_dots = false;

                    for ($i = 1; $i <= $total_pages; $i++):
                        if ($i == 1 || $i == $total_pages || ($i >= $page - $range && $i <= $page + $range)):
                            $query_data = $_GET;
                            $query_data['page'] = $i;
                            $url = "?" . http_build_query($query_data);
                            echo '<a href="' . $url . '" class="' . ($page == $i ? 'active' : '') . '">' . $i . '</a>';
                            $show_dots = true;
                        elseif ($show_dots):
                            echo '<span class="dots">...</span>';
                            $show_dots = false;
                        endif;
                    endfor;
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="assets/js/script.js"></script>
</body>

</html>