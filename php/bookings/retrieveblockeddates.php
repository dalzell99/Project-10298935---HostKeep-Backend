<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Get all properties
$r = mysqli_query($con, "SELECT 1 FROM Properties");
$numRows = mysqli_num_rows($r);
$limit = floor($numRows / 6);
$offset = ((intval(date('h')) / 2) - 1) * $limit;

// Don't limit the number of rows to account for rounding down of numbers above
if (intval(date('h')) === 12) {
	$limit = 99999;
}

// $result = mysqli_query($con, "SELECT propertyID, airbnbURL, name FROM Properties ORDER BY propertyID LIMIT $offset, " . ($limit + 1));
$result = mysqli_query($con, "SELECT propertyID, airbnbURL, name FROM Properties");

while ($row = mysqli_fetch_assoc($result)) {
	if ($row['airbnbURL']) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://us.smartbnb.io/api/calendar?user_id=5383441&secret=fJDGMEdLPwCFWDJisYNxSqwPnmaxXCzo&format=full&listing_id=" . $row['airbnbURL'] . "&end_date=" . date('Y-m-d', strtotime("+1 month")),
			// CURLOPT_URL => "https://us.smartbnb.io/api/calendar?user_id=5383441&secret=fJDGMEdLPwCFWDJisYNxSqwPnmaxXCzo&format=full&listing_id=10436414&start_date=2017-02-01&end_date=2017-04-01",
			CURLOPT_RETURNTRANSFER => true
		));

		$response = json_decode(curl_exec($curl));
		$err = curl_error($curl);

		curl_close($curl);

		if (isset($response->calendar_days)) {
			foreach ($response->calendar_days as $day) {
				$available = ($day->type === 'busy' ? $day->notes : false);
				if ($available) {
					mysqli_query($con, "REPLACE INTO Bookings VALUES ({$row['airbnbURL']}, '$day->date', '$available', {$day->price->native_price})");
				}
			}
		}
	}
}

mysqli_close($con);
?>
