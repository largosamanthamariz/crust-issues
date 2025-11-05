<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$id  = isset($_POST['id'])  ? (int)$_POST['id']  : 0;
$qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 0;

if ($id > 0 && isset($_SESSION['cart'][$id])) {
  if ($qty <= 0) {
    unset($_SESSION['cart'][$id]);
  } else {
    $_SESSION['cart'][$id] = $qty;
  }
}
$back = $_SERVER['HTTP_REFERER'] ?? 'crustissuescart.php';
header("Location: $back");
exit;
