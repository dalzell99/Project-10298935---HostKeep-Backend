<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Get all properies
$r = mysqli_query($con, "SELECT 1 FROM Properties");
$numRows = mysqli_num_rows($r);
$limit = floor($numRows / 5);
$offset = (floor(date('i') / 6) % 5) * $limit;

// Don't limit the number of rows to account for rounding down of numbers above
// when
if (floor(date('i') / 6) % 5 === 4) {
	$limit = 99999;
}

$result = mysqli_query($con, "SELECT propertyID, airbnbURL FROM Properties ORDER BY propertyID LIMIT $offset, $limit");

if (date('H:i') === '02:00') {
	$start = '&start_date=' . date('Y-m-d', strtotime("+91 days", time()));
	$end = ''; // Use default (+6 months)
} else {
	$start = ''; // Use default (today)
	$end = '&end_date=' . date('Y-m-d', strtotime("+90 days", time()));
}

echo "SELECT propertyID, airbnbURL FROM Properties LIMIT $offset, $limit<br /><br />";
while ($row = mysqli_fetch_assoc($result)) {
	if ($row['airbnbURL']) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://us.smartbnb.io/api/calendar?user_id=5383441&secret=fJDGMEdLPwCFWDJisYNxSqwPnmaxXCzo$start$end&format=full&listing_id=" . $row['airbnbURL'],
			// CURLOPT_URL => "https://us.smartbnb.io/api/calendar?user_id=5383441&secret=fJDGMEdLPwCFWDJisYNxSqwPnmaxXCzo&format=full&listing_id=11419346&start_date=2016-10-13&end_date=2016-10-15",
			CURLOPT_RETURNTRANSFER => true
		));

		$response = json_decode(curl_exec($curl));
		$err = curl_error($curl);

		curl_close($curl);

		if (isset($response->calendar_days)) {
			foreach ($response->calendar_days as $day) {
				if (isset($day->reservation)) {
					$groupID = $day->group_id;
					$propertyID = $row['propertyID'];

					$reservation = $day->reservation;
					$startDate = $reservation->start_date;
					$numNights = $reservation->nights;
					$endDate = date('Y-m-d', strtotime("+$numNights day", strtotime($startDate)));
					$numGuests = $reservation->number_of_guests;
					$priceExplode = explode(" ", $reservation->formatted_host_base_price);
					$totalCost = substr($priceExplode[0], 1);

					if (isset($reservation->guest)) {
						$guest = $reservation->guest;
						$guestFullName = $guest->full_name;
						$guestFirstName = $guest->first_name;
						$guestImage = $guest->picture_url;
						$guestThumbnail = $guest->thumbnail_url;
					}

					mysqli_query($con, "REPLACE INTO Reservations (groupID, propertyID, startDate, endDate, guestFullName, guestFirstName, guestImage, guestThumbnail, numNights, numGuests, totalCost) VALUES ('$groupID', '$propertyID', '$startDate', '$endDate', '$guestFullName', '$guestFirstName', '$guestImage', '$guestThumbnail', $numNights, $numGuests, $totalCost)");
				}

				$type = $day->type;
				$subtype = $day->subtype;
				$reason = $day->reason;
				$available = ($day->available ? 'available' : 'booked');
				mysqli_query($con, "REPLACE INTO Bookings VALUES ({$row['airbnbURL']}, '$day->date', '$available', {$day->price->native_price}, '$type', '$subtype', '$reason')");
			}
		}
	}
}

mysqli_close($con);
?>
