<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$bookingID = $_POST['bookingID'];

$sqlBooking = "SELECT * FROM DirectBookings WHERE bookingID = $bookingID";
if ($result = mysqli_query($con, $sqlBooking)) {
	$row = mysqli_fetch_assoc($result);
	$currentDate = strtotime($row['guestCheckin']);
	while (strtotime($row['guestCheckout']) >= $currentDate) {
		$date = date('Y-m-d', $currentDate);
		mysqli_query($con,
			"INSERT INTO Bookings VALUES ((
				SELECT airbnbURL
				FROM Properties
				WHERE propertyID = '" . $row['propertyID'] . "'
			), '$date', 'available', 0)
			ON DUPLICATE KEY UPDATE availability = 'available'"
		);
		$currentDate = strtotime("+1 day", $currentDate);
	}

	$sql = "DELETE FROM DirectBookings WHERE bookingID = $bookingID";
	if (mysqli_query($con, $sql)) {
		echo 'success';

		$resultProperty = mysqli_query($con, "SELECT name FROM Properties WHERE propertyID = '" . $row['propertyID'] . "'");
		$propertyName = mysqli_fetch_assoc($resultProperty)['name'];
		$guestName = $row['guestName'];
		$guestCheckIn = $row['guestCheckIn'];
		$guestCheckOut = $row['guestCheckOut'];

		sendEmail($hostkeepEmail, $noReplyEmail, "Direct Booking Deleted", "
		Property: $propertyName <br />
		Guest Name: $guestName <br />
		Check-in: " . substr($guestCheckIn, 6, 2) . '/' . substr($guestCheckIn, 4, 2) . '/' . substr($guestCheckIn, 0, 4) . "<br />
		Check-out: " . substr($guestCheckOut, 6, 2) . '/' . substr($guestCheckOut, 4, 2) . '/' . substr($guestCheckOut, 0, 4) . "
		");
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
