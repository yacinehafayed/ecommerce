<?php
session_start();
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /Backends/Ecommerce/view/Auth/Auth.php?view=login&error=unauthorized');
    exit();
}
require_once '../../Config/DB.PHP';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = $_POST['name'] ?? '';
    $category= $_POST['category'] ?? '';
    $description = $_POST['description'] ?? '';
    $price= isset($_POST['price']) ? floatval($_POST['price']) : 0.0;

    if ($action === 'insert') {
        $sql = "INSERT INTO products (name, category, description, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssd", $name, $category, $description, $price); 
        if ($stmt->execute()) {
            header('Location: Admin.php?msg=created');
            exit();
        } else {
            die("Database insertion failed: " . $conn->error);
        }
    }
    else if ($action === 'update' && $id > 0) {
        $sql = "UPDATE products SET name = ?, category = ?, description = ?, price = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssdi", $name, $category, $description, $price, $id);
        if ($stmt->execute()) {
            header('Location: Admin.php?msg=updated');
            exit();
        } else {
            die("Database update failed: " . $conn->error);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']); 
    
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header('Location: Admin.php?msg=deleted');
        exit();
    } else {
        die("Database deletion failed: " . $conn->error);
    }
}
header('Location: Admin.php');
exit();
?>