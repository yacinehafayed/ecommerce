<?php
session_start();
$_SESSION = array();
session_destroy();
header('Location: /Backends/Ecommerce/view/Auth/Auth.php');
exit();
?>