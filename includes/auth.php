<?php
// includes/auth.php
// Session & auth helpers

// EDIT THIS to match your local URL base *once*.
// Example below matches: http://localhost/CrustIssues/crust-issues/CrustIssues/...
if (!defined('APP_URL_BASE')) {
  define('APP_URL_BASE', '/CrustIssues/crust-issues/CrustIssues');
}

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

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

/**
 * Redirect to login with a consistent URL.
 */
function redirect_to_login(): void {
  header('Location: ' . APP_URL_BASE . '/crustissueslogin.php');
  exit;
}

/**
 * Require login only.
 */
function require_login(): void {
  if (!current_user()) {
    redirect_to_login();
  }
}

/**
 * Require a specific role or set of roles.
 * @param string|array $role 'user' | 'admin' | ['admin','manager'] etc.
 */
function require_role(string|array $role = 'user'): void {
  $u = current_user();
  if (!$u) {
    redirect_to_login();
  }

  $userRole = $u['role'] ?? 'user';
  $allowed = is_array($role) ? $role : [$role];

  // If 'user' is allowed, any logged-in user passes
  if (in_array('user', $allowed, true)) {
    return;
  }

  if (!in_array($userRole, $allowed, true)) {
    http_response_code(403);
    echo "<h2 style='font-family:sans-serif'>Forbidden</h2><p>You don't have permission to view this page.</p>";
    exit;
  }
}
