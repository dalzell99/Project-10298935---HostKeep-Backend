<?php
require('../global.php');

$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://us.smartbnb.io/api/calendar?user_id=5383441&secret=fJDGMEdLPwCFWDJisYNxSqwPnmaxXCzo&start_date=2016-11-25&end_date=2016-12-15&format=full&listing_id=8279484",
	// CURLOPT_URL => "https://us.smartbnb.io/api/calendar?user_id=5383441&secret=fJDGMEdLPwCFWDJisYNxSqwPnmaxXCzo&format=full&listing_id=11419346&start_date=2016-10-13&end_date=2016-10-15",
	CURLOPT_RETURNTRANSFER => true
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

echo $response;

mysqli_close($con);
?>
