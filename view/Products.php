<?php
session_start();
require_once '../Config/DB.php';

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = $_GET['category'];
    $sql = "SELECT id, name, category, price, description FROM products WHERE category = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category);
    $stmt->execute();
    $result = $stmt->get_result();
}
elseif (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $_GET['search'];
    $search_param = "%" . $search . "%";
    $sql = "SELECT id, name, category, price, description FROM products WHERE name LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
}
else {
    $sql = "SELECT id, name, category, price, description FROM products";
    $result = $conn->query($sql);
}

$cat_sql = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != ''";
$categories_result = $conn->query($cat_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroShop - Products</title>
    <style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    background-color: #fafafa; /* Crisp background */
    color: #111111; /* Pitch black readable text */
    margin: 0;
    padding: 40px 20px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

h1 {
    color: #000000;
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -0.5px;
    text-transform: uppercase;
    margin-bottom: 30px;
    border-bottom: 2px solid #000000; /* Flat structural dividing underline */
    padding-bottom: 10px;
}

/* Minimalist Filter Bar Frame */
.filter-bar {
    background-color: #ffffff;
    padding: 25px;
    border: 1px solid #000000; /* Sharp black framing box */
    border-radius: 0px; /* Zero radius for a premium look */
    margin-bottom: 40px;
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: flex-end;
    box-shadow: 0 4px 15px rgba(0,0,0,0.02);
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.filter-group label {
    font-size: 11px;
    color: #111111;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.filter-group select, 
.filter-group input {
    background-color: #ffffff;
    color: #111111;
    border: 1px solid #e2e8f0;
    padding: 10px 14px;
    border-radius: 0px;
    font-size: 14px;
    outline: none;
    min-width: 200px;
    transition: border-color 0.2s ease;
}

.filter-group select:focus, 
.filter-group input:focus {
    border-color: #000000;
}

/* Action Buttons */
.btn-filter {
    background-color: #000000;
    color: #ffffff;
    border: 1px solid #000000;
    padding: 11px 24px;
    border-radius: 0px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.2s ease-in-out;
}

.btn-filter:hover {
    background-color: #ffffff;
    color: #000000;
}

.btn-clear {
    background-color: #ffffff;
    color: #000000;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    padding: 10px 24px;
    border-radius: 0px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-sizing: border-box;
    display: inline-block;
    height: 40px;
    line-height: 18px;
    transition: border-color 0.2s ease;
}

.btn-clear:hover {
    border-color: #000000;
}

/* Product Grid and Cards */
.product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 30px;
}

.product-card {
    background-color: #ffffff;
    border-radius: 0px;
    padding: 25px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border: 1px solid #e2e8f0; /* Soft initial gray line box */
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

/* Hovering pops the card boundary lines to full black */
.product-card:hover {
    border-color: #000000;
    box-shadow: 0 6px 20px rgba(0,0,0,0.05);
}

.product-title {
    font-size: 18px;
    margin: 0 0 12px 0;
    color: #000000;
    font-weight: 700;
}

.product-category {
    font-size: 10px;
    background-color: #000000; /* Sharp little black indicator tag */
    color: #ffffff;
    padding: 4px 8px;
    border-radius: 0px;
    align-self: flex-start;
    margin-bottom: 12px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.product-desc {
    font-size: 14px;
    color: #666666; /* Elegant muted gray text for layout balance */
    margin-bottom: 20px;
    line-height: 1.5;
}

.product-price {
    font-size: 22px;
    font-weight: 700;
    color: #000000;
    margin-bottom: 20px;
    letter-spacing: -0.5px;
}

/* Purchase Button Actions */
.btn-cart {
    background-color: #000000;
    color: #ffffff;
    border: 1px solid #000000;
    padding: 12px;
    border-radius: 0px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    width: 100%;
    transition: all 0.2s ease-in-out;
}

.btn-cart:hover {
    background-color: #ffffff;
    color: #000000;
}

/* No Results Warning Frame */
.no-results {
    text-align: center;
    color: #666666;
    border: 1px dashed #cbd5e1;
    grid-column: 1 / -1;
    padding: 60px;
    font-size: 16px;
    background: #ffffff;
}
    </style>
</head>
<body>

    <div class="container">
        <h1>Explore Products</h1>

        <form method="GET" action="Products.php" class="filter-bar">
            
            <div class="filter-group">
                <label for="search">Search</label>
                <input type="text" name="search" id="search" placeholder="Type to search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>

            <div class="filter-group">
                <label for="category">Category</label>
                <select name="category" id="category">
                    <option value="">All Categories</option>
                    <?php while($cat_row = $categories_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($cat_row['category']); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] === $cat_row['category']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat_row['category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="btn-filter">Apply Filters</button>
            <a href="Products.php" class="btn-clear">Reset</a>
        </form>

        <div class="product-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <div>
                            <div class="product-category"><?php echo htmlspecialchars($row['category']); ?></div>
                            <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                            <p class="product-desc"><?php echo ($row['description']); ?></p>
                    </div>
        <div>
                        <div class="product-price">$<?php echo htmlspecialchars($row['price']); ?></div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="Cart.php">
                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                <input type="hidden" name="action" value="add">
                <button type="submit" class="btn-cart">Add to Cart</button>
            </form>
        <?php else: ?>
            <div class="login-prompt-zone">
                <a href="view/Auth/Auth.php" class="btn-login-warn" style="text-decoration: none;">
                    <button class="btn-cart" style="background-color: #475569;">Please Login First</button>
                </a>
            </div>
        <?php endif; ?>
                        </div>
                    </div>

                    
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">No products found matching your filter criteria.</div>
            <?php endif; ?>
        </div>
    </div>

    <?php

if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
    ?>
</body>
</html>