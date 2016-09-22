<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$customerID = $_POST['customerID'];
$propertyID = $_POST['propertyID'];
$guestName = mysqli_real_escape_string($con, $_POST['guestName']);
$guestMobile = mysqli_real_escape_string($con, $_POST['guestMobile']);
$guestEmail = mysqli_real_escape_string($con, $_POST['guestEmail']);
$guestCheckIn = $_POST['guestCheckIn'];
$guestCheckOut = $_POST['guestCheckOut'];
$invoiced = $_POST['invoiced'];
$cleanUp = $_POST['cleanUp'];
$notes = mysqli_real_escape_string($con, $_POST['notes']);
$admin = $_POST['admin'];
$username = $_POST['username'];
$propertyName = mysqli_real_escape_string($con, $_POST['propertyName']);
$nightlyRate = $_POST['nightlyRate'];
$creationDate = date('c');

$sql = "INSERT INTO DirectBookings VALUES (DEFAULT, '', '$customerID', '$propertyID', '$guestName', '$guestMobile', '$guestEmail', '$guestCheckIn', '$guestCheckOut', '$invoiced', '$cleanUp', '$notes', '$nightlyRate', '$creationDate')";
if (mysqli_query($con, $sql)) {
	$id = mysqli_insert_id($con);

	// Create custom direct booking id with format DXXXXX where X is a number
	$directBookingID = $id;
	while (strlen($directBookingID) < 5) {
		$directBookingID = '0' . $directBookingID;
	}
	$directBookingID = 'D' . $directBookingID;

	mysqli_query($con, "UPDATE DirectBookings SET directBookingID = '$directBookingID' WHERE bookingID = '$id'");

	$currentDate = strtotime($guestCheckIn);
	while (strtotime($guestCheckOut) >= $currentDate) {
		$date = date('Y-m-d', $currentDate);
		mysqli_query($con,
			"REPLACE INTO Bookings VALUES ((
				SELECT airbnbURL
				FROM Properties
				WHERE propertyID = '$propertyID'
			), '$date', 'booked')"
		);
		$currentDate = strtotime("+1 day", $currentDate);
	}

	echo 'success' . $id;

} else {
	echo 'fail';
	sendErrorEmail("
	addbooking.php<br />
	sql: $sql
	");
}

mysqli_close($con);
?>
