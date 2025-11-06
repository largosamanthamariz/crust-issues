<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';
require_role('admin');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf'] ?? '')) {
  http_response_code(400);
  exit('Bad CSRF');
}

$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
  $st = $pdo->prepare("DELETE FROM products WHERE id = ? LIMIT 1");
  $st->execute([$id]);
}

header('Location: products.php');
exit;
