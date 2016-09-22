<?php
require('../global.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://us.smartbnb.io/api/calendar?user_id=5383441&secret=fJDGMEdLPwCFWDJisYNxSqwPnmaxXCzo",
	CURLOPT_RETURNTRANSFER => true
));

$response = json_decode(curl_exec($curl));
$err = curl_error($curl);

curl_close($curl);

foreach ($response->operations as $property) {
	$airbnbID = $property->query->listing_id;
	foreach ($property->response->calendar_days as $day) {
		$available = ($day->available ? 'available' : 'booked');
		$sql = "REPLACE INTO Bookings VALUES ($airbnbID, '$day->date', '$available')";
		mysqli_query($con, $sql);
	}
}

mysqli_close($con);
?>
