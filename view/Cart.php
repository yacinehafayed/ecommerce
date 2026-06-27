<?php
session_start();
require_once '../Config/DB.php';

// check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

// insert item into cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add'){
    $product_id = intval($_POST['product_id']);
    
    $sql = "INSERT INTO cart (user_id, product_id, quantity)
    VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE quantity = quantity + 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $current_user_id, $product_id);
    $stmt->execute();
    $stmt->close();
    header("Location: Cart.php");
    exit();
}

// handle quantity update from cart page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update'){
    
    $product_id = intval($_POST['product_id']);
    $new_quantity = intval($_POST['quantity']);
    
    if ($new_quantity > 0) {
        // Update to the quantity that user typed
        $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $new_quantity, $current_user_id, $product_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: Cart.php");
    exit();
}

//remove item from cart
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    $product_id = intval($_GET['product_id']);
    
    $sql = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $current_user_id, $product_id);
    $stmt->execute();
    $stmt->close();

    header("Location: Cart.php");
    exit();
}
// fetch results for the current user's cart
$total_items = 0;
$subtotal = 0;
$cart_products = array();

$sql = "SELECT p.id, p.name, p.category, p.price, c.quantity
        FROM cart c
        INNER JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $total_items += $row['quantity'];
    $subtotal += $row['price'] * $row['quantity'];
    $cart_products[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroShop - Shopping Cart</title>
    <style>
       body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    background-color: #fafafa; /* Clean, crisp light background */
    color: #111111; /* Rich black readable text */
    margin: 0;
    padding: 40px 20px;
}

.container {
    max-width: 1000px;
    margin: 0 auto;
}

h1 {
    color: #000000;
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -0.5px;
    text-transform: uppercase;
    margin-bottom: 40px;
    border-bottom: 2px solid #000000; /* Flat structural dividing underline */
    padding-bottom: 12px;
}

.cart-layout {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 40px;
}

@media (max-width: 768px) {
    .cart-layout {
        grid-template-columns: 1fr;
        gap: 30px;
    }
}

/* Cart Items List Container */
.cart-items {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.cart-item {
    background-color: #ffffff;
    border: 1px solid #e2e8f0; /* Subtle initial frame */
    border-radius: 0px; /* Sharp premium corners */
    padding: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

/* Elegant border pop instead of sliding out on hover */
.cart-item:hover {
    border-color: #000000;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}

.item-details h3 {
    margin: 0 0 6px 0;
    color: #000000;
    font-size: 18px;
    font-weight: 700;
}

.item-details p {
    margin: 0;
    color: #666666; /* Muted gray for minor context details */
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

.item-actions {
    display: flex;
    align-items: center;
    gap: 25px;
}

/* Minimalist Form Inputs */
.item-actions label {
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.item-qty {
    background-color: #ffffff;
    color: #111111;
    border: 1px solid #e2e8f0;
    padding: 8px 10px;
    border-radius: 0px;
    font-size: 14px;
    outline: none;
    transition: border-color 0.2s ease;
}

.item-qty:focus {
    border-color: #000000;
}

.item-price {
    font-size: 18px;
    font-weight: 700;
    color: #000000;
    min-width: 90px;
    text-align: right;
    letter-spacing: -0.5px;
}

/* Sharp Outlined Removal Link Button */
.btn-remove {
    background-color: transparent;
    color: #666666;
    border: 1px solid #e2e8f0;
    padding: 8px 14px;
    border-radius: 0px;
    cursor: pointer;
    font-size: 11px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.2s ease-in-out;
}

.btn-remove:hover {
    border-color: #000000;
    color: #000000;
    background-color: #fafafa;
}

/* Cart Summary Sidebar Column */
.cart-summary {
    background-color: #ffffff;
    border: 1px solid #000000; /* Bold black structural box frame */
    border-radius: 0px;
    padding: 30px;
    align-self: flex-start;
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
}

.summary-title {
    font-size: 18px;
    color: #000000;
    text-transform: uppercase;
    font-weight: 800;
    letter-spacing: 0.5px;
    margin: 0 0 25px 0;
    border-bottom: 1px solid #111111;
    padding-bottom: 12px;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 18px;
    font-size: 14px;
    color: #666666;
}

.summary-row span:first-child {
    text-transform: uppercase;
    font-weight: 500;
    font-size: 13px;
    letter-spacing: 0.5px;
}

.summary-row.total {
    border-top: 1px dashed #e2e8f0;
    padding-top: 20px;
    margin-top: 10px;
    font-size: 18px;
    font-weight: 700;
    color: #000000;
}

.summary-row.total .total-price {
    color: #000000;
    font-size: 22px;
    letter-spacing: -0.5px;
}

/* Primary Dark Actions */
.btn-checkout {
    background-color: #000000;
    color: #ffffff;
    border: 1px solid #000000;
    padding: 14px;
    border-radius: 0px;
    cursor: pointer;
    font-weight: 600;
    width: 100%;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-top: 15px;
    transition: all 0.2s ease-in-out;
}

.btn-checkout:hover {
    background-color: #ffffff;
    color: #000000;
}

.btn-continue {
    display: inline-block;
    text-align: center;
    color: #666666;
    text-decoration: none;
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
    width: 100%;
    margin-top: 20px;
    transition: color 0.2s ease;
}

.btn-continue:hover {
    color: #000000;
}

/* Empty Alert Messaging Layout */
.empty-cart-msg {
    text-align: center;
    color: #111111;
    border: 1px dashed #cbd5e1;
    padding: 60px 40px;
    background: #ffffff;
}

.empty-cart-msg h2 {
    margin-top: 0;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 20px;
}

.empty-cart-msg p {
    color: #666666;
    font-size: 14px;
    margin-bottom: 25px;
}
    </style>
</head>
<body>

    <div class="container">
        <h1>Your Shopping Cart</h1>

        <?php if (!empty($cart_products)): ?>
            <div class="cart-layout">
                
                <div class="cart-items">
                    <?php foreach ($cart_products as $item): ?>
    <div class="cart-item">
        <div class="item-details">
            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
            <p>Category: <?php echo htmlspecialchars($item['category']); ?></p>
        </div>
        
        <div class="item-actions">
            
            <form method="POST" action="Cart.php" style="display: flex; align-items: center; gap: 10px;">
                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                <input type="hidden" name="action" value="update">
                
                <label for="qty_<?php echo $item['id']; ?>">Quantity:</label>
                <input type="number"
                    id="qty_<?php echo $item['id']; ?>"
                    name="quantity"
                    value="<?php echo $item['quantity']; ?>"
                    min="1"
                    class="item-qty"
                    onchange="this.form.submit();"> <button type="submit" style="display: none;">Update</button>
            </form>
            
            <div class="item-price">
                $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
            </div>
            
            <a href="Cart.php?action=delete&product_id=<?php echo $item['id']; ?>">
                <button type="button" class="btn-remove">Remove</button>
            </a>
        </div>
    </div>
<?php endforeach; ?>
                </div>

                <div class="cart-summary">
                    <h2 class="summary-title">Order Summary</h2>
                    
                    <div class="summary-row">
                        <span>Items Count</span>
                        <span><?php echo $total_items; ?> items</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                                        
                    <div class="summary-row total">
                        <span>Total</span>
                        <span class="total-price">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>

                    <button class="btn-checkout">Proceed to Checkout</button>
                    <a href="Products.php" class="btn-continue">← Continue Shopping</a>
                </div>

            </div>
        
        <?php else: ?>
            <div class="empty-cart-msg">
                <h2>Your cart is currently empty.</h2>
                <p>Head back to the store to add some premium gadgets!</p>
                <a href="Products.php" class="btn-checkout" style="display:inline-block; width:auto; padding: 12px 25px; text-decoration:none;">Browse Products</a>
            </div>
        <?php endif; ?>

    </div>

</body>
</html>