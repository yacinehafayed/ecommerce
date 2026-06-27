<?php
session_start();
require_once '../Config/DB.PHP';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['Email'] ?? '');
    $password = trim($_POST['Password'] ?? '');
    $role = $_POST['role'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $sql = "SELECT * FROM clients WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                if($_SESSION['user_role'] === 'user'){
                    header('Location: /Backends/Ecommerce/view/Dashboard/index.php');
                    exit();
                } elseif($_SESSION['user_role'] === 'admin'){
                    header('Location: /Backends/Ecommerce/Auth/Admin/Admin.php');
                    exit();
                }
            } else {
            header('Location: /Backends/Ecommerce/view/Auth/Auth.php?view=login&error=invalid_credentials');
            exit();
            }
        } else {
            header('Location: /Backends/Ecommerce/view/Auth/Auth.php?view=login&error=invalid_credentials');
            }
    }
}
?>