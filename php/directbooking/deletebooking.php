<?php
require('../global.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$bookingID = $_POST['bookingID'];

$sqlBooking = "SELECT guestCheckin, guestCheckout, propertyID FROM DirectBookings WHERE bookingID = $bookingID";
if ($result = mysqli_query($con, $sqlBooking)) {
	$row = mysqli_fetch_assoc($result);
	$currentDate = strtotime($row['guestCheckin']);
	while (strtotime($row['guestCheckout']) >= $currentDate) {
		$date = date('Y-m-d', $currentDate);
		mysqli_query($con,
			"REPLACE INTO Bookings VALUES ((
				SELECT airbnbURL
				FROM Properties
				WHERE propertyID = '" . $row['propertyID'] . "'
			), '$date', 'available')"
		);
		$currentDate = strtotime("+1 day", $currentDate);
	}

	$sql = "DELETE FROM DirectBookings WHERE bookingID = $bookingID";
	if (mysqli_query($con, $sql)) {
		echo 'success';
	} else {
		echo 'fail';
		sendErrorEmail("
		deletebooking.php<br />
		sql: $sql
		");
	}
} else {
	echo 'fail';
	sendErrorEmail("
	deletebooking.php<br />
	sqlBooking: $sqlBooking
	");
}

mysqli_close($con);
?>
