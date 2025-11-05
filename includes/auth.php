<?php
// Simple session-based auth helpers used by the CRUD pages.
if (session_status() === PHP_SESSION_NONE) session_start();

function login_user(array $user): void {
  $_SESSION['user'] = [
    'id'       => $user['id'],
    'username' => $user['username'],
    'email'    => $user['email'],
    'role'     => $user['role'] ?? 'user',
  ];
}

function current_user(): ?array {
  return $_SESSION['user'] ?? null;
}

function logout_user(): void {
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
  }
  session_destroy();
}

function require_role(string $role = 'user'): void {
  $u = current_user();
  if (!$u) {
    header('Location: /login.php');
    exit;
  }
  if ($role !== 'user' && ($u['role'] ?? 'user') !== $role) {
    http_response_code(403);
    echo "Forbidden";
    exit;
  }
}
