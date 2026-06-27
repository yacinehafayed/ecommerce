<?php
require_once '../Config/DB.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$Name = $_POST['Name'];
$Email = $_POST['Email'];
$Password = $_POST['Password'];
$HashPassword = password_hash($Password, PASSWORD_DEFAULT);

$sql = "insert into clients(name,email,password) values(?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss",$Name,$Email,$HashPassword);

if($stmt->execute()){
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['user_email'] = $Email;
    $stmt->close();
    $conn->close();
    header('Location: /Backends/Ecommerce/view/Auth/Auth.php');
    exit();
}else{
    header('Location: /Backends/Ecommerce/view/Auth/Auth.php?view=register&error=email_taken');
    exit();
}
}
?>