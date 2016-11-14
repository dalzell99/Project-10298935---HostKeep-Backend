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
$limit = floor($numRows / 6);
$offset = ((intval(date('h')) / 2) - 1) * $limit;

// Don't limit the number of rows to account for rounding down of numbers above
if (intval(date('h')) === 12) {
	$limit = 99999;
}

$result = mysqli_query($con, "SELECT airbnbURL FROM Properties ORDER BY propertyID LIMIT $offset, $limit");

$string = date('h') . ": SELECT airbnbURL FROM Properties ORDER BY propertyID LIMIT $offset, $limit<br />";

while ($row = mysqli_fetch_assoc($result)) {
	if ($row['airbnbURL']) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://us.smartbnb.io/api/calendar?user_id=5383441&secret=fJDGMEdLPwCFWDJisYNxSqwPnmaxXCzo&format=medium&listing_id=" . $row['airbnbURL'],
			// CURLOPT_URL => "https://us.smartbnb.io/api/calendar?user_id=5383441&secret=fJDGMEdLPwCFWDJisYNxSqwPnmaxXCzo&format=medium&listing_id=11419346&start_date=2016-11-13&end_date=2016-11-15",
			CURLOPT_RETURNTRANSFER => true
		));

		$response = json_decode(curl_exec($curl));
		$err = curl_error($curl);

		curl_close($curl);

		if (isset($response->calendar_days)) {
			foreach ($response->calendar_days as $day) {
				$available = ($day->available ? 'available' : 'booked');
				mysqli_query($con, "REPLACE INTO Bookings VALUES ({$row['airbnbURL']}, '$day->date', '$available', {$day->price->native_price})");
				$string .= "REPLACE INTO Bookings VALUES ({$row['airbnbURL']}, '$day->date', '$available', {$day->price->native_price})<br />";
			}
		}
	}
}

mysqli_close($con);
?>
