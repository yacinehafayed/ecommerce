<?php
session_start();

if (isset($_SESSION['user_email'])) {
    header('Location: /Backends/Ecommerce/view/index.php');
    exit();
}

$active_view = isset($_GET['view']) ? $_GET['view'] : 'login';
$error_message = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroShop - Authentication</title>
    <style>
        :root {
    --primary: #000000;       /* Bold black for primary buttons */
    --bg-dark: #fafafa;       /* Crisp, warm light grey for background */
    --border-color: #e2e8f0;  /* Subtle grey for input borders */
    --text-main: #111111;     /* Rich deep charcoal black for text */
    --text-muted: #666666;    /* Medium grey for toggle and helper labels */
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    background-color: rgba(31, 28, 28, 0.87);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.auth-container {
    background: #ffffff;
    width: 380px;
    padding: 40px;
    border-radius: 0px; /* Zero radius gives it a premium, sharp geometric look */
    border: 1px solid #111111; /* Thin black framing line */
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    overflow: hidden;
    position: relative;
}

.form-box {
    transition: all 0.3s ease;
}

.hidden {
    display: none;
}

h2 {
    margin-top: 0;
    margin-bottom: 30px;
    color: var(--text-main);
    text-align: center;
    font-weight: 700;
    letter-spacing: -0.5px;
    text-transform: uppercase; /* Elegant typography */
    font-size: 22px;
}

.input-group {
    margin-bottom: 20px;
}

.input-group label {
    display: block;
    margin-bottom: 6px;
    color: var(--text-main);
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.input-group input, 
.input-group select {
    width: 100%;
    padding: 12px;
    box-sizing: border-box;
    border: 1px solid var(--border-color);
    border-radius: 0px; /* Kept sharp to match container */
    font-size: 15px;
    background-color: #ffffff;
    color: var(--text-main);
    transition: border-color 0.2s ease;
}

/* When clicking into an input, give it a bold black border highlight */
.input-group input:focus,
.input-group select:focus {
    outline: none;
    border-color: #000000;
}

button.btn-submit {
    width: 100%;
    padding: 14px;
    background-color: var(--primary);
    color: #ffffff;
    border: 1px solid var(--primary);
    border-radius: 0px;
    font-size: 14px;
    cursor: pointer;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.2s ease-in-out;
}

/* Hovering turns the button white with black text */
button.btn-submit:hover {
    background-color: #ffffff;
    color: #000000;
}

.toggle-text {
    text-align: center;
    margin-top: 20px;
    font-size: 13px;
    color: var(--text-muted);
}

.toggle-text span {
    color: var(--text-main);
    cursor: pointer;
    font-weight: 600;
    text-decoration: underline;
}

/* Minimalist Error Banner */
.error-banner {
    background-color: #000000;
    color: #ffffff;
    padding: 12px;
    border-radius: 0px;
    margin-bottom: 20px;
    font-size: 13px;
    text-align: center;
    font-weight: 500;
    letter-spacing: 0.5px;
}
    </style>
</head>
<body>

    <div class="auth-container">
        
        <?php if (!empty($error_message)): ?>
            <div class="error-banner">
                <?php 
                    if ($error_message === 'invalid_credentials') echo "Invalid email or password.";
                    elseif ($error_message === 'email_taken') echo "That email is already registered.";
                    elseif ($error_message === 'empty_fields') echo "Please fill in all fields.";
                    else echo "An error occurred. Please try again.";
                ?>
            </div>
        <?php endif; ?>

        <div id="login-box" class="form-box <?php echo ($active_view === 'register') ? 'hidden' : ''; ?>">
            <h2>Login to ElectroShop</h2>
            <form action="/Backends/Ecommerce/Auth/Login.php" method="POST">
                <div class="input-group">
                    <label>role</label>
                    <select name="role" required>
                        <option value="">Select Role</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                </div>
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="Email" required>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="Password" required>
                </div>
                <button type="submit" class="btn-submit">Sign In</button>
                <div class="toggle-text">
                    Don't have an account? <span id="to-register">Sign Up</span>
                </div>
            </form>
        </div>

        <div id="register-box" class="form-box <?php echo ($active_view === 'login') ? 'hidden' : ''; ?>">
            <h2>Create an Account</h2>
            <form action="/Backends/Ecommerce/Auth/Register.php" method="POST">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="Name" required>
                </div>
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="Email" required>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="Password" required>
                </div>
                <button type="submit" class="btn-submit">Register</button>
                <div class="toggle-text">
                    Already have an account? <span id="to-login">Log In</span>
                </div>
            </form>
        </div>

    </div>

    <script>
        const loginBox = document.getElementById('login-box');
        const registerBox = document.getElementById('register-box');
        const toRegister = document.getElementById('to-register');
        const toLogin = document.getElementById('to-login');

        // Switch to Register View
        toRegister.addEventListener('click', () => {
            loginBox.classList.add('hidden');
            registerBox.classList.remove('hidden');
        });

        // Switch to Login View
        toLogin.addEventListener('click', () => {
            registerBox.classList.add('hidden');
            loginBox.classList.remove('hidden');
        });
    </script>
</body>
</html>