<?php
// remove_from_cart.php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id > 0 && isset($_SESSION['cart'][$id])) {
  unset($_SESSION['cart'][$id]);
}

// Send back to the previous page or cart as fallback
$back = $_SERVER['HTTP_REFERER'] ?? 'crustissuescart.php';
header("Location: $back");
exit;
