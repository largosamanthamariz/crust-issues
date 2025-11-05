<?php /* Crust Issues Classic Pastries Page */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Classic Pastries - Crust Issues</title>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<?php include 'header.php'; ?>

	<section class="page-header">
		<div class="container">
			<div class="page-header-content">
				<button class="back-btn" onclick="history.back()" aria-label="Go back">
					<i class="fa-solid fa-arrow-left"></i>
				</button>
				<div>
					<h1 class="page-title">Classic Pastries</h1>
					<p class="page-subtitle">Timeless, buttery favorites that bring comfort in every bite.</p>
				</div>
			</div>
		</div>
	</section>

	<section class="products-section">
		<div class="container">
			<div class="products-grid">
				<!-- Product Card 1 -->
				<div class="product-card">
					<img src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?q=80&w=800&auto=format&fit=crop" alt="Butter Croissant" class="product-image">
					<div class="product-info">
						<h3 class="product-name">Butter Croissant</h3>
						<p class="product-price">P95</p>
						<button class="add-cart-btn">
							<i class="fa-solid fa-cart-shopping"></i>
							Add to Cart
						</button>
					</div>
				</div>

				<!-- Product Card 2 -->
				<div class="product-card">
					<img src="https://images.unsplash.com/photo-1589010588553-46e8e80c0bce?q=80&w=800&auto=format&fit=crop" alt="Choc Croissant" class="product-image">
					<div class="product-info">
						<h3 class="product-name">Choc Croissant</h3>
						<p class="product-price">P105</p>
						<button class="add-cart-btn">
							<i class="fa-solid fa-cart-shopping"></i>
							Add to Cart
						</button>
					</div>
				</div>

				<!-- Product Card 3 -->
				<div class="product-card">
					<img src="https://images.unsplash.com/photo-1517686469429-8bdb88b9f907?q=80&w=800&auto=format&fit=crop" alt="Baguette" class="product-image">
					<div class="product-info">
						<h3 class="product-name">Baguette</h3>
						<p class="product-price">P70</p>
						<button class="add-cart-btn">
							<i class="fa-solid fa-cart-shopping"></i>
							Add to Cart
						</button>
					</div>
				</div>

				<!-- Product Card 4 -->
				<div class="product-card">
					<img src="https://images.unsplash.com/photo-1549931319-a545dcf3bc73?q=80&w=800&auto=format&fit=crop" alt="Sourdough Bread" class="product-image">
					<div class="product-info">
						<h3 class="product-name">Sourdough Bread</h3>
						<p class="product-price">P95</p>
						<button class="add-cart-btn">
							<i class="fa-solid fa-cart-shopping"></i>
							Add to Cart
						</button>
					</div>
				</div>

				<!-- Product Card 5 (Repeat) -->
				<div class="product-card">
					<img src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?q=80&w=800&auto=format&fit=crop" alt="Butter Croissant" class="product-image">
					<div class="product-info">
						<h3 class="product-name">Butter Croissant</h3>
						<p class="product-price">P95</p>
						<button class="add-cart-btn">
							<i class="fa-solid fa-cart-shopping"></i>
							Add to Cart
						</button>
					</div>
				</div>

				<!-- Product Card 6 (Repeat) -->
				<div class="product-card">
					<img src="https://images.unsplash.com/photo-1589010588553-46e8e80c0bce?q=80&w=800&auto=format&fit=crop" alt="Choc Croissant" class="product-image">
					<div class="product-info">
						<h3 class="product-name">Choc Croissant</h3>
						<p class="product-price">P105</p>
						<button class="add-cart-btn">
							<i class="fa-solid fa-cart-shopping"></i>
							Add to Cart
						</button>
					</div>
				</div>

				<!-- Product Card 7 (Repeat) -->
				<div class="product-card">
					<img src="https://images.unsplash.com/photo-1517686469429-8bdb88b9f907?q=80&w=800&auto=format&fit=crop" alt="Baguette" class="product-image">
					<div class="product-info">
						<h3 class="product-name">Baguette</h3>
						<p class="product-price">P70</p>
						<button class="add-cart-btn">
							<i class="fa-solid fa-cart-shopping"></i>
							Add to Cart
						</button>
					</div>
				</div>

				<!-- Product Card 8 (Repeat) -->
				<div class="product-card">
					<img src="https://images.unsplash.com/photo-1549931319-a545dcf3bc73?q=80&w=800&auto=format&fit=crop" alt="Sourdough Bread" class="product-image">
					<div class="product-info">
						<h3 class="product-name">Sourdough Bread</h3>
						<p class="product-price">P95</p>
						<button class="add-cart-btn">
							<i class="fa-solid fa-cart-shopping"></i>
							Add to Cart
						</button>
					</div>
				</div>
			</div>
		</div>
	</section>

</body>
</html>
