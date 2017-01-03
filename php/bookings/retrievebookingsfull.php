<?php
require('../global.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$result = mysqli_query($con, "SELECT airbnbURL FROM Properties WHERE propertyID = '" . $_GET['propertyID'] . "'");

$airbnbURL = mysqli_fetch_assoc($result)['airbnbURL'];
$start = $_GET['start'];
$end = $_GET['end'];

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://us.smartbnb.io/api/calendar?user_id=5383441&secret=fJDGMEdLPwCFWDJisYNxSqwPnmaxXCzo&format=full" . "&listing_id=$airbnbURL&start_date=$start&end_date=$end",
	// CURLOPT_URL => "https://us.smartbnb.io/api/calendar?user_id=5383441&secret=fJDGMEdLPwCFWDJisYNxSqwPnmaxXCzo&format=full&listing_id=11419346&start_date=2016-10-13&end_date=2016-10-15",
	CURLOPT_RETURNTRANSFER => true
));

$response = json_decode(curl_exec($curl));
$err = curl_error($curl);

curl_close($curl);

if (isset($response->calendar_days)) {
	foreach ($response->calendar_days as $day) {
		try {
			if (isset($day->reservation)) {
				$groupID = $day->group_id;
				$propertyID = $_GET['propertyID'];

				$reservation = $day->reservation;
				$startDate = $reservation->start_date;
				$numNights = $reservation->nights;
				$endDate = date('Y-m-d', strtotime("+$numNights day", strtotime($startDate)));
				$numGuests = $reservation->number_of_guests;
				$priceExplode = explode(" ", $reservation->formatted_host_base_price);
				$grossCost = substr($priceExplode[0], 1);
				$netCost = $reservation->formatted_host_base_price;

				if (isset($reservation->guest)) {
					$guest = $reservation->guest;
					$guestFullName = mysqli_real_escape_string($con, $guest->full_name);
					$guestFirstName = mysqli_real_escape_string($con, $guest->first_name);
					$guestImage = mysqli_real_escape_string($con, $guest->picture_url);
					$guestThumbnail = mysqli_real_escape_string($con, $guest->thumbnail_url);
				}

				$sql = "REPLACE INTO Reservations (groupID, propertyID, startDate, endDate, guestFullName, guestFirstName, guestImage, guestThumbnail, numNights, numGuests, grossCost, netCost, status) VALUES ('$groupID', '$propertyID', '$startDate', '$endDate', '$guestFullName', '$guestFirstName', '$guestImage', '$guestThumbnail', $numNights, $numGuests, $grossCost, $netCost, 'accepted')";

				if (!mysqli_query($con, $sql)) {
					echo "------- ERROR: -------" . $sql . "<br /><br />";
				} else {
					echo $sql . "<br /><br />";
				}
			}
		} catch (Exception $e) {
			echo $e;
		}
	}
}

mysqli_close($con);
?>
