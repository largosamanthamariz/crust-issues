<?php /* Crust Issues Home (PHP version) */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Crust Issues Home Page</title>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<?php include 'header.php'; ?>

	<section class="hero">
		<div class="container wrap">
			<div>
				<div class="kicker">ENDLESS</div>
				<h1 class="display">BAKING HAPPINESS FOR EVERY HEART</h1>
				<div class="sub">Taste the joy!</div>
			</div>
			<div class="hero-images">
				<img class="img-main" src="https://images.unsplash.com/photo-1541782814453-cac2146aa2b1?q=80&w=1200&auto=format&fit=crop" alt="Strawberry croissant">
				<img class="img-side" src="https://images.unsplash.com/photo-1517686469429-8bdb88b9f907?q=80&w=1000&auto=format&fit=crop" alt="Baguette">
			</div>
		</div>
	</section>

	<section class="section">
		<div class="container">
			<div class="features">
				<div>
					<div class="quote">“Great selection of baked goods, and everything tastes homemade.”</div>
					<div class="bread-box" style="margin-top:14px">
						<img src="https://images.unsplash.com/photo-1549931319-a545dcf3bc73?q=80&w=1600&auto=format&fit=crop" alt="Bread box">
					</div>
				</div>
				<div class="copy">
					<h3>SWEETNESS DELIVERED IN EVERY BOX, <span class="highlight">GUARANTEED</span> FRESH</h3>
					<p>Crafted daily with pure ingredients and zero shortcuts.</p>
					<button class="btn">Order Now <i class="fa-solid fa-arrow-right" style="margin-left:8px"></i></button>
					<div class="badges">
						<div class="badge"><i class="fa-regular fa-heart"></i><span>No Artificial Additives</span></div>
						<div class="badge"><i class="fa-solid fa-leaf"></i><span>Pure and Simple</span></div>
						<div class="badge"><i class="fa-solid fa-truck"></i><span>Next-Day Delivery</span></div>
						<div class="badge"><i class="fa-solid fa-shield-heart"></i><span>Freshness Guarantee</span></div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="promo">
		<div class="container">
			<h3 style="text-align:center;margin:0 0 18px">TURNING DOUGH INTO DELICIOUS FUN AND YUM!</h3>
			<div class="card">
				<h4>Crust Issues Bakeshoppe</h4>
				<img src="https://images.unsplash.com/photo-1523986371872-9d3ba2e2f642?q=80&w=1600&auto=format&fit=crop" alt="Croissant">
			</div>
		</div>
	</section>

	<footer>
		<div class="container">© <?php echo date('Y'); ?> Crust Issues. All rights reserved.</div>
	</footer>

</body>
</html>
