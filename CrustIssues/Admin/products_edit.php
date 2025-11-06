<?php
require __DIR__ . '/../../includes/db.php'; 
require __DIR__ . '/../../includes/auth.php';
require_role('admin');

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
$csrf = $_SESSION['csrf_token'];

function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

$id = (int)($_GET['id'] ?? 0);
$prod = null;

if ($id > 0) {
  $st = $pdo->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
  $st->execute([$id]);
  $prod = $st->fetch();
}
if (!$prod) { header('Location: products.php'); exit; }

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
      $imagePath = $prod['image'];

      if (!empty($_FILES['image']['name'])) {
        $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
        $tmp = $_FILES['image']['tmp_name'];
        $mime = @mime_content_type($tmp);
        if (!isset($allowed[$mime])) {
          $err = 'Image must be JPG, PNG, or WEBP.';
        } else {
          $ext = $allowed[$mime];
          $dir = __DIR__ . '/../uploads';
          if (!is_dir($dir)) @mkdir($dir, 0777, true);
          $filename = 'prod_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
          if (!move_uploaded_file($tmp, "$dir/$filename")) {
            $err = 'Failed to save image.';
          } else {
            $imagePath = 'uploads/' . $filename;
          }
        }
      }

      if ($err === '') {
        $u = $pdo->prepare("
          UPDATE products
          SET name = ?, price = ?, stock = ?, category = ?, image = ?
          WHERE id = ? LIMIT 1
        ");
        $u->execute([$name, $price, $stock, $category, $imagePath, $id]);
        $msg = 'Product updated.';
        // refresh product
        $st = $pdo->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
        $st->execute([$id]);
        $prod = $st->fetch();
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Product — Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700;800&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="admin.css">
  <style>
    .panel form{display:grid;gap:12px;max-width:520px}
    .row{display:grid;gap:6px}
    label{font-weight:700;font-size:14px}
    input[type="text"],input[type="number"],select,input[type="file"]{
      border:1.5px solid #e5e7eb;border-radius:12px;padding:10px 12px;font-size:14px;background:#fff}
    .btn{background:#000;color:#fff;border:none;padding:10px 16px;border-radius:12px;font-weight:700;cursor:pointer;text-decoration:none}
    .msg{color:#0a0;font-weight:700}
    .err{color:#b00020;font-weight:700}
    .thumb{width:220px;height:140px;object-fit:cover;border-radius:10px;background:#f6c7cf;margin-bottom:8px}
  </style>
</head>
<body>
  <div class="grid">
    <aside class="side">
      <div class="logo">
        <img src="../pictures/logo.png" alt="Crust Issues"><b>CRUST <span>ISSUES</span></b>
      </div>
      <nav class="nav">
        <a href="./dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="./products.php" class="active"><i class="fa-solid fa-utensils"></i> Menu</a>
      </nav>
      <a class="logout" href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> LOGOUT</a>
    </aside>

    <main class="main">
      <h1 class="hello">Edit <em>Product</em></h1>
      <section class="panel">
        <h2 class="panel-title">#<?= (int)$prod['id'] ?> — <?= e($prod['name']) ?></h2>

        <?php if ($msg): ?><div class="msg"><?= e($msg) ?></div><?php endif; ?>
        <?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>

        <img class="thumb" src="<?= e($prod['image'] ?: '../pictures/placeholder.jpg') ?>" alt="">

        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="csrf" value="<?= e($csrf) ?>">

          <div class="row">
            <label for="name">Name</label>
            <input id="name" name="name" type="text" value="<?= e($prod['name']) ?>" required>
          </div>

          <div class="row">
            <label for="price">Price (PHP)</label>
            <input id="price" name="price" type="number" min="1" step="0.01" value="<?= e($prod['price']) ?>" required>
          </div>

          <div class="row">
            <label for="stock">Stock</label>
            <input id="stock" name="stock" type="number" min="0" step="1" value="<?= (int)$prod['stock'] ?>" required>
          </div>

          <div class="row">
            <label for="category">Category</label>
            <select id="category" name="category">
              <?php
                $cats = ['classic'=>'Classic','special'=>'Special','drinks'=>'Drinks'];
                foreach ($cats as $k=>$v){
                  $sel = ($prod['category']===$k)?'selected':'';
                  echo "<option value='".e($k)."' $sel>".e($v)."</option>";
                }
              ?>
            </select>
          </div>

          <div class="row">
            <label for="image">Image (optional)</label>
            <input id="image" name="image" type="file" accept="image/*">
          </div>

          <div style="display:flex;gap:10px">
            <button class="btn" type="submit">Save Changes</button>
            <a class="btn" style="background:#555" href="products.php">Back</a>
          </div>
        </form>
      </section>
    </main>
  </div>
</body>
</html>
