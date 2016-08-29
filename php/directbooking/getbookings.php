<?php
require('../global.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$customerID = $_POST['customerID'];

$sql = "SELECT * FROM DirectBookings WHERE customerID = '$customerID'";
if ($result = mysqli_query($con, $sql)) {
    $response = [];

    while ($row = mysqli_fetch_assoc($result)) {
        // Get property name from properties table
        $resultProp = mysqli_query($con, "SELECT name FROM Properties WHERE propertyID = '" . $row['propertyID'] . "'");
        $row['propertyName'] = mysqli_fetch_assoc($resultProp)['name'];

        $response[] = $row;
    }

    echo json_encode($response);
} else {
    echo 'fail';
    sendErrorEmail("
    getbookings.php<br />
    sql: $sql
    ");
}

mysqli_close($con);
?>
