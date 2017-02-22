<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Dashboard</title>

		<?php
		include('head.php');
		echo '<link href="css/dashboard.css" rel="stylesheet" />';
		echo '<script type="text/javascript" src="js/dashboard.js"></script>';
		?>
	</head>
	<body>
		<div class='.container-fluid'>
			<div class="col-sm-10 col-sm-offset-1">
				<header>
					<?php include('header.php'); ?>
				</header>

				<main>
					<div id="welcome">
						<?php include('welcome.php'); ?>
					</div>

					<div id="profile">
						<?php include('profile.php'); ?>
					</div>

					<div id="properties">
						<?php include('properties.php'); ?>
					</div>

					<div id="documents">
						<?php include('documents.php'); ?>
					</div>

					<div id="directBooking">
						<?php include('direct-booking.php'); ?>
					</div>

					<div id="password">
						<?php include('change-password.php'); ?>
					</div>

					<div id="admin">
						<?php include('admin.php'); ?>
					</div>
				</main>

				<footer>
					<?php include('footer.php'); ?>
				</footer>
			</div>
		</div>
	</body>
</html>
