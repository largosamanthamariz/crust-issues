<?php /* Crust Issues About Us Page */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>About Us - Crust Issues</title>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<?php include 'header.php'; ?>

	<!-- Section 1: Our Crust Story -->
	<section class="crust-story">
		<div class="container">
			<h1 class="crust-story-title">OUR CRUST STORY</h1>
			<p class="crust-story-subtitle">Where every bite is soft, flaky, and guaranteed to fix your crust issues.</p>
			<img src="https://images.unsplash.com/photo-1549931319-a545dcf3bc73?q=80&w=1600&auto=format&fit=crop" alt="Rustic bread loaves" class="bread-image">
		</div>
	</section>

	<!-- Section 2: Crust Issues Bakeshoppe -->
	<section class="bakeshoppe-section">
		<div class="container">
			<div class="bakeshoppe-card">
				<img src="https://images.unsplash.com/photo-1523986371872-9d3ba2e2f642?q=80&w=800&auto=format&fit=crop" alt="Kneading dough" class="bakeshoppe-image">
				<div class="bakeshoppe-content">
					<h2>Crust Issues Bakeshoppe</h2>
					<p>At Crust Issues Bakeshop, we rise to the occasion—literally. We're not just about baking, we're about making every slice worth the bite. Whether you're craving something sweet, savory, or just downright comforting, we've got the dough to fix your crust issues.</p>
					
					<div class="opening-hours">
						<div class="hours-row">
							<span class="hours-day">Monday - Friday</span>
							<span class="hours-time">7:00 am - 6:00 pm</span>
						</div>
						<div class="hours-row">
							<span class="hours-day">Saturday - Sunday</span>
							<span class="hours-time">7:00 am - 3:00 pm</span>
						</div>
					</div>

					<div class="social-icons">
						<a href="#" class="social-icon" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
						<a href="#" class="social-icon" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
						<a href="#" class="social-icon" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Section 3: Benefits Of Bread -->
	<section class="benefits-section">
		<div class="container">
			<h2 class="benefits-title">Benefits Of Bread</h2>
			<p class="benefits-subtitle">Bread is more than just a staple—it's a versatile, affordable, and nutrient-packed food that fuels the body and fits any meal.</p>
			
			<div class="benefits-content">
				<img src="https://images.unsplash.com/photo-1549931319-a545dcf3bc73?q=80&w=800&auto=format&fit=crop" alt="Baguettes in paper bag" class="baguette-image">
				
				<div class="benefit-labels">
					<!-- Left Column Benefits -->
					<div class="benefit-label benefit-left-1">Fiber for Your Tummy</div>
					<div class="benefit-arrow arrow-left-1"></div>
					
					<div class="benefit-label benefit-left-2">Packed with B-Vits</div>
					<div class="benefit-arrow arrow-left-2"></div>
					
					<div class="benefit-label benefit-left-3">Heart's Bestie (Whole Grain Style)</div>
					<div class="benefit-arrow arrow-left-3"></div>
					
					<!-- Right Column Benefits -->
					<div class="benefit-label benefit-right-1">Energy on the Go</div>
					<div class="benefit-arrow arrow-right-1"></div>
					
					<div class="benefit-label benefit-right-2">Budget-Friendly Bite</div>
					<div class="benefit-arrow arrow-right-2"></div>
					
					<div class="benefit-label benefit-right-3">Endless Meal MVP</div>
					<div class="benefit-arrow arrow-right-3"></div>
				</div>
			</div>
		</div>
	</section>

</body>
</html>

