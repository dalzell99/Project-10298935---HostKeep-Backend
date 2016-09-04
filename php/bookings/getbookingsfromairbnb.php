<?php
require('../global.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$client_id = 'd306zoyjsyarp7ifhu67rjxn52tv0t20';
$username = 'hostkeepbookings@gmail.com';
$password = 'airbnb198';

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://api.airbnb.com/v1/authorize?client_id=$client_id",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => [
		'grant_type' => 'password',
		'password' => $password,
		'username' => $username
	]
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

$token = substr($response, strpos($response, ':') + 2, 25);

// Iterate all properties and get the calendar for each then iterate through the calendar and add the booking status for each day to the booking database.
$propertyID = "8279484";
$query = '{"operations":{"method":"GET","path":"/calendar_days","query":{"start_date":"' . date('Y-m-d') . '","listing_id":"' . $propertyID . '","_format":"host_calendar","end_date":"' . date('Y-m-d', strtotime('+1 year')) . '"}},"_transaction":false}';

$curl = curl_init();

curl_setopt_array($curl, array(
	CURLOPT_URL => "https://api.airbnb.com/v2/batch/?client_id=$client_id",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_HTTPHEADER => [
		"Content-Type" => "application/json; charset=UTF-8",
		"X-Airbnb-OAuth-Token" => $token
	],
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => $query
));

$calendar = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

echo $calendar;

mysqli_close($con);
?>
