<?php
require('global.php');

$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$find = "http://owners.hostkeep.com.au";
$replace = "https://stephencolman.com.au/owners";

$result = mysqli_query($con, "SELECT propertyID, imageURL FROM Properties");
while ($row = mysqli_fetch_assoc($result)) {
	// echo $row['imageURL'] . " - " . str_replace($find,$replace,$row['imageURL']) . "<br />";
	mysqli_query($con, "UPDATE Properties SET imageURL = '" . str_replace($find,$replace,$row['imageURL']) . "' WHERE propertyID = '" . $row['propertyID'] . "'");
}

mysqli_close($con);
?>
