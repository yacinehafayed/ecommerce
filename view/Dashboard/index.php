<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ElectroShop</title>

<style>
    *{
        margin:0;
        padding:0;
        box-sizing:border-box;
        font-family: Arial, sans-serif;
    }

    body{
        background:#f5f5f5;
    }

    /* Navbar */
    nav{
        background:#222;
        color:white;
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:15px 50px;
    }

    .logo{
        font-size:24px;
        font-weight:bold;
    }

    .nav-links{
        display:flex;
        gap:20px;
    }

    .nav-links a{
        color:white;
        text-decoration:none;
    }

    .search-box input{
        padding:8px;
        border:none;
        border-radius:5px;
    }

    /* Hero Section */
    .hero{
        height:400px;
        display:flex;
        flex-direction:column;
        justify-content:center;
        align-items:center;
        text-align:center;
        background:white;
    }

    .hero h1{
        font-size:48px;
        margin-bottom:15px;
    }

    .hero p{
        font-size:18px;
        margin-bottom:20px;
    }

    .btn{
        padding:12px 25px;
        border:none;
        cursor:pointer;
        font-size:16px;
        border-radius:5px;
    }

    .btn-primary{
        background:#222;
        color:white;
    }

    /* Categories */
    .section{
        padding:50px;
    }

    .section h2{
        text-align:center;
        margin-bottom:30px;
    }

    .categories{
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
        gap:20px;
    }

    .category-card{
        background:white;
        padding:30px;
        text-align:center;
        border-radius:10px;
        box-shadow:0 2px 10px rgba(0,0,0,0.1);
        cursor:pointer;
    }

    .category-card:hover{
        transform:translateY(-5px);
        transition:0.3s;
    }

    /* Products */
    .products{
        display:grid;
        grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
        gap:20px;
    }

    .product-card{
        background:white;
        padding:20px;
        border-radius:10px;
        box-shadow:0 2px 10px rgba(0,0,0,0.1);
    }

    .product-card img{
        width:100%;
        height:180px;
        object-fit:cover;
        border-radius:5px;
    }

    .product-card h3{
        margin-top:10px;
    }

    .price{
        font-weight:bold;
        margin:10px 0;
    }

    footer{
        background:#222;
        color:white;
        text-align:center;
        padding:20px;
        margin-top:40px;
    }
</style>
</head>
<body>

<!-- Navbar -->
<nav>
    <div class="logo">ElectroShop</div>

    <div class="nav-links">
        <a href="#">Home</a>
        <a href="../Products.php">Products</a>
        <a href="../Cart.php">Cart</a>
        
    </div>
    <div class="register-login">
        <?php
        if (isset($_SESSION['user_email'])) {
            echo '<a href="/Backends/Ecommerce/Auth/Logout.php">Logout</a>';
        } else {
            echo '<a href="/Backends/Ecommerce/view/Auth/Auth.php">Register</a>';
        }
        ?>
    </div>
</nav>

<!-- Hero -->
<section class="hero">
    <h1>Welcome to ElectroShop</h1>
    <p>Find the latest electronics at the best prices.</p>
    <button class="btn btn-primary" onclick="shopNow()">
        Shop Now
    </button>
</section>

<!-- Categories -->
<!-- <section class="section">
    <h2>Categories</h2>

    <div class="categories">
        <div class="category-card">Laptops</div>
        <div class="category-card">Phones</div>
        <div class="category-card">Accessories</div>
        <div class="category-card">Gaming</div>
    </div>
</section> -->

<!-- Featured Products -->
<section class="section">
    <h2>Featured Products</h2>

    <div class="products">

        <div class="product-card">
            <img src="https://via.placeholder.com/300x180" alt="">
            <h3>Gaming Laptop</h3>
            <p class="price">$999</p>
            <button class="btn btn-primary">
                View Details
            </button>
        </div>

        <div class="product-card">
            <img src="https://via.placeholder.com/300x180" alt="">
            <h3>Smartphone</h3>
            <p class="price">$699</p>
            <button class="btn btn-primary">
                View Details
            </button>
        </div>

        <div class="product-card">
            <img src="https://via.placeholder.com/300x180" alt="">
            <h3>Wireless Headphones</h3>
            <p class="price">$149</p>
            <button class="btn btn-primary">
                View Details
            </button>
        </div>

    </div>
</section>

<footer>
    <p>© 2026 ElectroShop. All rights reserved.</p>
</footer>

<script>
    function shopNow(){
        alert("Redirecting to products page...");
        // window.location.href = "products.html";
    }

    document
        .getElementById("searchInput")
        .addEventListener("keyup", function(){

            let value = this.value.toLowerCase();

            console.log("Searching:", value);

            // Later:
            // Send request to backend
            // Fetch matching products
        });
</script>

</body>
</html>