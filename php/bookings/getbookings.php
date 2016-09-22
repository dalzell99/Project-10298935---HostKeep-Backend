<?php
require('../global.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$airbnbID = $_GET['airbnbID'];

$sql = "SELECT * FROM Bookings WHERE airbnbID = $airbnbID";
if ($result = mysqli_query($con, $sql)) {
	$response = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$response[] = $row;
	}

	echo json_encode($response);
} else {
	echo 'fail';
}

mysqli_close($con);
?>
