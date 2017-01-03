<?php
require('../global.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$result = mysqli_query($con, "SELECT propertyID, airbnbURL FROM Properties");
$start = $_GET['start'];
$end = $_GET['end'];

while ($row = mysqli_fetch_assoc($result)) {
	if ($row['airbnbURL']) {
		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://us.smartbnb.io/api/calendar?user_id=5383441&secret=fJDGMEdLPwCFWDJisYNxSqwPnmaxXCzo&format=full&start_date=$start&end_date=$end&listing_id=" . $row['airbnbURL'],
			// CURLOPT_URL => "https://us.smartbnb.io/api/calendar?user_id=5383441&secret=fJDGMEdLPwCFWDJisYNxSqwPnmaxXCzo&format=full&start_date=$start&end_date=$end&listing_id=8279484",
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
					$netCost = $reservation->payout_price_in_host_currency;

					$sql = "UPDATE Reservations SET netCost = $netCost WHERE propertyID = '$propertyID' AND startDate = '$startDate' AND endDate = '$endDate'";

					if (!mysqli_query($con, $sql)) {
						echo "------- ERROR: -------" . $sql . "<br />";
					} else {
						echo $sql . "<br />";
					}
				}
			}
		}
	}
}

mysqli_close($con);
?>
