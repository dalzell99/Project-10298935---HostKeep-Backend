<?php
require('../global.php');
require('../sendemail.php');

$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$input = file_get_contents('php://input');

// sendEmail('dalzell99@hotmail.com', $noReplyEmail, 'Reservation Webhook', $input . "<br /><br />REPLACE INTO Reservations (groupID, propertyID, startDate, endDate, guestFullName, guestFirstName, guestImage, guestThumbnail, numNights, numGuests, totalCost, status) VALUES ('$groupID', '$propertyID', '$startDate', '$endDate', '$guestFullName', '$guestFirstName', '$guestImage', '$guestThumbnail', $numNights, $numGuests, $totalCost, '$status')");

try {
	$reservation = json_decode($input);
	$listing = $reservation->listing;
	$guest = $reservation->guest;

	$res = mysqli_query($con, "SELECT propertyID FROM Properties WHERE airbnbURL = '{$listing->id}'");
	$propertyID = mysqli_fetch_assoc($res)['propertyID'];

	$groupID = $reservation->code;
	$startDate = $reservation->start_date;
	$numNights = $reservation->nights;
	$endDate = $reservation->end_date;
	$numGuests = $reservation->guests;
	$totalCost = $reservation->total_price;
	$status = $reservation->status;

	$guestFirstName = mysqli_real_escape_string($con, $guest->first_name);
	$guestFullName = $guestFirstName . ' ' . mysqli_real_escape_string($con, $guest->last_name);
	$guestImage = mysqli_real_escape_string($con, $guest->picture_url);
	$guestThumbnail = mysqli_real_escape_string($con, $guest->picture_url);

	mysqli_query($con, "REPLACE INTO Reservations (groupID, propertyID, startDate, endDate, guestFullName, guestFirstName, guestImage, guestThumbnail, numNights, numGuests, totalCost, status) VALUES ('$groupID', '$propertyID', '$startDate', '$endDate', '$guestFullName', '$guestFirstName', '$guestImage', '$guestThumbnail', $numNights, $numGuests, $totalCost, '$status')");

	$error = false;
} catch(Exception $e) {
	$error = $e->getMessage();
}

sendEmail('dalzell99@hotmail.com', $noReplyEmail, 'Reservation Webhook', $input . "<br /><br />REPLACE INTO Reservations (groupID, propertyID, startDate, endDate, guestFullName, guestFirstName, guestImage, guestThumbnail, numNights, numGuests, totalCost, status) VALUES ('$groupID', '$propertyID', '$startDate', '$endDate', '$guestFullName', '$guestFirstName', '$guestImage', '$guestThumbnail', $numNights, $numGuests, $totalCost, '$status')" . ($error ? "<br /><br />Error: $error" : ""));

mysqli_close($con);
?>
