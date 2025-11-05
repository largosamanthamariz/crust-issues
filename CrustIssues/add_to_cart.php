<?php
require __DIR__ . '/../includes/db.php';
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// CSRF check (same style as your edit handler)
$csrfSess = $_SESSION['csrf_token'] ?? '';
$csrfPost = $_POST['csrf'] ?? '';
if (!hash_equals($csrfSess, $csrfPost)) {
  http_response_code(400);
  $resp = ['ok'=>false,'error'=>'Invalid CSRF'];
  if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
    header('Content-Type: application/json'); echo json_encode($resp); exit;
  }
  // non-AJAX fallback
  header('Location: crustissuesmenu.php?err=csrf'); exit;
}

// Inputs
$id  = isset($_POST['id'])  ? (int)$_POST['id']  : 0;
$qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;

if ($id <= 0) {
  http_response_code(422);
  $resp = ['ok'=>false,'error'=>'Invalid product'];
  if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
    header('Content-Type: application/json'); echo json_encode($resp); exit;
  }
  header('Location: crustissuesmenu.php?err=product'); exit;
}

// Optional: verify product exists (and could check stock if you store it)
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
if (!$stmt->fetch()) {
  http_response_code(404);
  $resp = ['ok'=>false,'error'=>'Product not found'];
  if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
    header('Content-Type: application/json'); echo json_encode($resp); exit;
  }
  header('Location: crustissuesmenu.php?err=missing'); exit;
}

// Add to session cart
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
$_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty;

// Build count for badge
$cartCount = 0;
foreach ($_SESSION['cart'] as $q) $cartCount += (int)$q;

// AJAX?
if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') {
  header('Content-Type: application/json');
  echo json_encode(['ok'=>true,'count'=>$cartCount]);
  exit;
}

// Non-AJAX: go back to previous page or menu
$back = $_SERVER['HTTP_REFERER'] ?? 'crustissuesmenu.php';
header("Location: $back");
exit;
