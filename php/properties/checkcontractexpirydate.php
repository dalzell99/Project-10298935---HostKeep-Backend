<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
	echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sql = "SELECT * FROM Properties WHERE contractExpiryDate <> '' AND contractExpiryEmailSent <> 'y'";
$resultMonths = mysqli_query($con, "SELECT months FROM ContractExpiryEmailTime");
$rowMonths = mysqli_fetch_assoc($resultMonths);
$months = $rowMonths['months'];
if ($result = mysqli_query($con, $sql)) {
	while ($row = mysqli_fetch_assoc($result)) {
		$todayPlusMonths = strtotime("+$months months", strtotime(date('c')));
		if ($todayPlusMonths > strtotime($row['contractExpiryDate'])) {
			$sqlUser = "SELECT firstName, lastName FROM Customer WHERE username = '" . $row['username'] . "'";
			$resultUser = mysqli_query($con, $sqlUser);
			$rowUser = mysqli_fetch_assoc($resultUser);

			$emailSent = sendEmail(
				$hostkeepEmail,
				$noReplyEmail,
				"Contract Renewal Required for " . $row['name'],
				"
				*** THIS IS AN AUTOMATED NOTIFICATION FROM THE OWNERS PORTAL ***

				The contract for " . $row['name'] . " with " . $rowUser['firstName'] . " " . $rowUser['lastName'] . " is due to expire on " . date('D jS M', $row['contractExpiryDate']) . ".

				Please commence contract renewal process with " . $rowUser['firstName'] . ".
				"
			);

			if ($emailSent) {
				$sqlProperty = "UPDATE Properties SET contractExpiryEmailSent = 'y' WHERE propertyID = '" . $row['propertyID'] . "'";
				mysqli_query($con, $sqlProperty);
				echo 'success';
			} else {
				sendErrorEmail("
				checkcontractexpirydate.php<br />
				Email fail
				");
				echo 'failmail';
			}
		}
	}
} else {
	sendErrorEmail("
	checkcontractexpirydate.php<br />
	sql: $sql
	");
	echo 'fail';
}

mysqli_close($con);
?>
