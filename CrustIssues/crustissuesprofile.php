<?php /* Crust Issues My Account Page */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>My Account - Crust Issues</title>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="stylesheet" href="styles.css">
</head>
<body>
	<?php include 'header.php'; ?>

	<section class="greeting-section">
		<div class="container">
			<h1 class="greeting-text">
				<span class="hi">Hi,</span> <span class="name">Meiji!</span>
			</h1>
		</div>
	</section>

	<section class="account-card">
		<div class="account-section">
			<div class="section-header">
				<h2 class="section-title">My Account</h2>
				<button class="edit-btn">
					<i class="fa-solid fa-pencil"></i>
					Edit
				</button>
			</div>
			<div class="profile-info">
				<img src="pictures/profile.jpg" alt="Profile Picture" class="profile-picture">
				<div class="profile-details">
					<h3>Meiji Ayg</h3>
					<p>Lemente Village, Mati City</p>
				</div>
			</div>
		</div>

		<div class="personal-info">
			<div class="section-header">
				<h2 class="section-title">Personal Information</h2>
				<button class="edit-btn">
					<i class="fa-solid fa-pencil"></i>
					Edit
				</button>
			</div>
			<div class="info-grid">
				<div class="info-item">
					<span class="info-label">First Name</span>
					<span class="info-value">Meiji</span>
				</div>
				<div class="info-item">
					<span class="info-label">Last Name</span>
					<span class="info-value">Ayg</span>
				</div>
				<div class="info-item">
					<span class="info-label">Email Address</span>
					<span class="info-value">largo.samanthamariz@gmail.com</span>
				</div>
				<div class="info-item">
					<span class="info-label">Phone</span>
					<span class="info-value">+63 950 977 6540</span>
				</div>
				<div class="info-item">
					<span class="info-label">Bio</span>
					<span class="info-value">i miss him</span>
				</div>
			</div>
		</div>
	</section>

</body>
</html>

