<?php
require('../global.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$bookingID = $_POST['bookingID'];

$sql = "DELETE FROM DirectBookings WHERE bookingID = $bookingID";
if (mysqli_query($con, $sql)) {
    echo 'success';
} else {
    echo 'fail';
    sendErrorEmail("
    deletebooking.php<br />
    sql: $sql
    ");
}

mysqli_close($con);
?>
