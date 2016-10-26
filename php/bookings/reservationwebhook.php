<?php
require('../global.php');
require('../sendemail.php');

$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$input = file_get_contents('php://input');
// $reservation = json_decode('{"user_id":5383441,"code":"RSE8HP","start_date":"2016-10-26","end_date":"2016-10-28","nights":2,"guests":3,"listing":{"id":12740579,"name":"BORDEAUX: The Richmond high life","address":"18 Hull Street Apartment 205, Richmond, VIC 3121, Australia","picture_url":"https:\/\/a2.muscache.com\/im\/pictures\/4f805dba-48d7-43e2-9215-1a6f148a87d7.jpg?aki_policy=large"},"guest":{"id":58506388,"first_name":"Misbah","last_name":"Nawab","picture_url":"https:\/\/a2.muscache.com\/im\/pictures\/33b6322d-21d6-4130-8f5e-3c8d3328dd4b.jpg?aki_policy=profile_x_medium","location":null},"currency":"AUD","security_price":399,"security_price_formatted":"$399","base_price":202,"base_price_formatted":"$202","guest_fee":42,"guest_fee_formatted":"$42","tax_amount":0,"tax_amount_formatted":"$0","extras_price":141,"extras_price_formatted":"$141","subtotal":343,"subtotal_formatted":"$343","total_price":385,"total_price_formatted":"$385","per_night_price":101,"per_night_price_formatted":"$101","payout_price":332}');

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

$guestFirstName = $guest->first_name;
$guestFullName = $guestFirstName . ' ' . $guest->last_name;
$guestImage = $guest->picture_url;
$guestThumbnail = $guest->picture_url;

mysqli_query($con, "REPLACE INTO Reservations (groupID, propertyID, startDate, endDate, guestFullName, guestFirstName, guestImage, guestThumbnail, numNights, numGuests, totalCost) VALUES ('$groupID', '$propertyID', '$startDate', '$endDate', '$guestFullName', '$guestFirstName', '$guestImage', '$guestThumbnail', $numNights, $numGuests, $totalCost)");
// echo "REPLACE INTO Reservations (groupID, propertyID, startDate, endDate, guestFullName, guestFirstName, guestImage, guestThumbnail, numNights, numGuests, totalCost) VALUES ('$groupID', '$propertyID', '$startDate', '$endDate', '$guestFullName', '$guestFirstName', '$guestImage', '$guestThumbnail', $numNights, $numGuests, $totalCost)";

mysqli_close($con);

sendEmail('dalzell99@hotmail.com', $noReplyEmail, 'Reservation Webhook', $input);
?>
