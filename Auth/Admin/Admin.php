<?php
session_start();
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /Backends/Ecommerce/view/Auth/Auth.php?view=login&error=unauthorized');
    exit();
}
require_once '../../Config/DB.PHP';

$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product_name = '';
$product_category = '';
$product_description = '';
$product_price = '';

if ($action === 'edit' && $id > 0) {
    $stmt = $conn->prepare("SELECT name, category, description, price FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($product_data = $res->fetch_assoc()) {
        $product_name        = $product_data['name'];
        $product_category    = $product_data['category'];
        $product_description = $product_data['description'];
        $product_price       = $product_data['price'];
    }
}
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f4f6f9; }
        .form-card { background: white; padding: 30px; border-radius: 8px; max-width: 500px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; }
        .btn-submit { background: #000000; color: white; padding: 10px 15px; border: none; cursor: pointer; border-radius: 4px; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { margin-right: 15px; color: #000000; text-decoration: none; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #343a40; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .btn-add { background-color: #000000; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin-bottom: 15px; }
        
        /* Modal Backdrop styling */
        .modal-overlay {
            display: none; 
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.5); 
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .form-card { 
            background: white; 
            padding: 30px; 
            border-radius: 8px; 
            width: 100%;
            max-width: 500px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); 
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 15px; right: 20px;
            font-size: 24px;
            cursor: pointer;
            color: #aaa;
        }
        .close-modal:hover { color: #000; }
    </style>
</head>
<body>

    <div class="nav-links">
        <a href="/Backends/Ecommerce/Auth/Logout.php">LOG OUT</a>
    </div>
    
    <h1>ADMIN DASHBOARD</h1>
    
    <a href="Admin.php?action=insert" class="btn-add">ADD PRODUCT</a>
    
    <div class="modal-overlay" id="productModal">
        <div class="form-card">
            <span class="close-modal" id="closeBtn">&times;</span>
            
            <h2><?php echo strtoupper($action); ?> PRODUCT</h2>
            
            <form action="GestionProduct.php" method="POST">
                <input type="hidden" name="action" value="<?php echo htmlspecialchars($action); ?>">
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($product_name); ?>" required>
                </div>

                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" value="<?php echo htmlspecialchars($product_category); ?>" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" value="<?php echo htmlspecialchars($product_description); ?>" required>
                </div>

                <div class="form-group">
                    <label>Price ($)</label>
                    <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($product_price); ?>" required>
                </div>

                <button type="submit" class="btn-submit">Save Product Data</button>
            </form>
        </div>
    </div>

    <h2>STORE INVENTORY</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($product = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td>
                            <a href="Admin.php?action=edit&id=<?php echo $product['id']; ?>" style="color: #000000; margin-right: 10px; text-decoration: none; font-weight: bold;">Edit</a>
                            <a href="GestionProduct.php?action=delete&id=<?php echo $product['id']; ?>" style="color: #000000; text-decoration: none; font-weight: bold;" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No products found in the database inventory.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>

<script>
    const modal = document.getElementById('productModal');
    const closeBtn = document.getElementById('closeBtn');
    const addProductBtn = document.querySelector('.btn-add');

    const urlParams = new URLSearchParams(window.location.search);
    const currentAction = urlParams.get('action');

    // Display modal window instantly if the URL parameters require it
    if (currentAction === 'edit' || currentAction === 'insert') {
        modal.style.display = 'flex';
    }

    addProductBtn.addEventListener('click', function(e) {
        if (currentAction === 'insert') {
            e.preventDefault();
            modal.style.display = 'flex';
        }
    });

    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        window.location.href = 'Admin.php';
    });

    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            window.location.href = 'Admin.php';
        }
    });
</script>
</html>