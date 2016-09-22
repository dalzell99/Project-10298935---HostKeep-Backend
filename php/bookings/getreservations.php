<?php
require('../global.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$airbnbID = $_GET['airbnbID'];
$resultProperty = mysqli_query($con, "SELECT propertyID FROM Properties WHERE airbnbURL = '$airbnbID'");
$propertyID = mysqli_fetch_assoc($resultProperty)['propertyID'];

$sqlBooking = "SELECT * FROM Bookings WHERE airbnbID = $airbnbID";
$sqlReservation = "SELECT * FROM Reservations WHERE propertyID = '$propertyID'";
if (($resultBooking = mysqli_query($con, $sqlBooking)) && ($resultReservation = mysqli_query($con, $sqlReservation))) {
	$response['bookings'] = [];
	while ($rowBooking = mysqli_fetch_assoc($resultBooking)) {
		$response['bookings'][] = $rowBooking;
	}

	$response['reservations'] = [];
	while ($rowReservation = mysqli_fetch_assoc($resultReservation)) {
		$response['reservations'][] = $rowReservation;
	}

	echo json_encode($response);
} else {
	echo 'fail';
}

mysqli_close($con);
?>