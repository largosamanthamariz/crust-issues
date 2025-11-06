<?php
// Admin — Add Product
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';
require_role('admin');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['csrf_token'];

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$msg = $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!hash_equals($csrf, $_POST['csrf'] ?? '')) {
    $err = 'Invalid CSRF token.';
  } else {
    $name     = trim($_POST['name'] ?? '');
    $priceRaw = trim($_POST['price'] ?? '');
    $stockRaw = trim($_POST['stock'] ?? '0');
    $category = trim($_POST['category'] ?? 'classic');

    $price = is_numeric($priceRaw) ? (float)$priceRaw : -1;
    $stock = ctype_digit($stockRaw) ? (int)$stockRaw : -1;

    if ($name === '' || $price <= 0 || $stock < 0) {
      $err = 'Please provide a name, valid price, and non-negative stock.';
    } else {
      // handle image (optional)
      $imagePath = null;
      if (!empty($_FILES['image']['name'])) {
        $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        $tmp  = $_FILES['image']['tmp_name'] ?? '';
        $mime = $tmp ? @mime_content_type($tmp) : '';
        if (!$tmp || !isset($allowed[$mime])) {
          $err = 'Image must be JPG, PNG, or WEBP.';
        } else {
          $ext = $allowed[$mime];
          $dir = __DIR__ . '/../uploads';
          if (!is_dir($dir)) @mkdir($dir, 0777, true);
          $filename = 'prod_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
          if (!move_uploaded_file($tmp, "$dir/$filename")) {
            $err = 'Failed to save image.';
          } else {
            $imagePath = 'uploads/' . $filename; // web path
          }
        }
      }

      if ($err === '') {
        $st = $pdo->prepare("
          INSERT INTO products (name, price, stock, category, image)
          VALUES (?, ?, ?, ?, ?)
        ");
        $st->execute([$name, $price, $stock, $category, $imagePath]);
        $msg = 'Product added successfully!';
        // clear form fields after success
        $_POST = [];
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add Product — Admin | Crust Issues</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700;800&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="admin.css">
  <style>
    .panel form{display:grid;gap:12px;max-width:540px}
    .row{display:grid;gap:6px}
    label{font-weight:700;font-size:14px}
    input[type="text"],input[type="number"],select,input[type="file"]{
      border:1.5px solid #e5e7eb;border-radius:12px;padding:10px 12px;font-size:14px;background:#fff}
    .btn{background:#000;color:#fff;border:none;padding:10px 16px;border-radius:12px;font-weight:700;cursor:pointer;text-decoration:none}
    .btn-secondary{background:#555}
    .msg{color:#0a0;font-weight:700}
    .err{color:#b00020;font-weight:700}
    .actions{display:flex;gap:10px;flex-wrap:wrap}
  </style>
</head>
<body>
  <div class="grid">
    <!-- Sidebar -->
    <aside class="side">
      <div class="logo">
        <img src="../pictures/logo.png" alt="Crust Issues"><b>CRUST <span style="color:#d34f4f">ISSUES</span></b>
      </div>
      <nav class="nav">
        <a href="./dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="./orders.php"><i class="fa-regular fa-clipboard"></i> Orders</a>
        <a href="./users.php"><i class="fa-regular fa-user"></i> Reg Users</a>
        <a href="./products.php"><i class="fa-solid fa-utensils"></i> Menu</a>
      </nav>
      <a class="logout" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOGOUT</a>
    </aside>

    <!-- Main -->
    <main class="main">
      <h1 class="hello">Add <em>Product</em></h1>

      <section class="panel">
        <h2 class="panel-title">New Product</h2>

        <?php if ($msg): ?><div class="msg"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>

        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="csrf" value="<?= e($csrf) ?>">

          <div class="row">
            <label for="name">Name</label>
            <input id="name" name="name" type="text" value="<?= e($_POST['name'] ?? '') ?>" required>
          </div>

          <div class="row">
            <label for="price">Price (PHP)</label>
            <input id="price" name="price" type="number" min="1" step="0.01" value="<?= e($_POST['price'] ?? '') ?>" required>
          </div>

          <div class="row">
            <label for="stock">Stock</label>
            <input id="stock" name="stock" type="number" min="0" step="1" value="<?= e($_POST['stock'] ?? '0') ?>" required>
          </div>

          <div class="row">
            <label for="category">Category</label>
            <select id="category" name="category">
              <?php
                $cats = ['classic'=>'Classic','special'=>'Special','drinks'=>'Drinks'];
                $sel = $_POST['category'] ?? 'classic';
                foreach ($cats as $k=>$v){
                  $s = ($sel === $k) ? 'selected' : '';
                  echo "<option value='".e($k)."' $s>".e($v)."</option>";
                }
              ?>
            </select>
          </div>

          <div class="row">
            <label for="image">Image (optional)</label>
            <input id="image" name="image" type="file" accept="image/*">
            <small>Optional. JPG, PNG, or WEBP.</small>
          </div>

          <div class="actions">
            <button class="btn" type="submit">Save Product</button>
            <a class="btn btn-secondary" href="products.php">Back to Products</a>
            <a class="btn btn-secondary" href="../crustissuesmenu.php">View Menu (User)</a>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>
</html>
