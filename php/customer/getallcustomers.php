<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sql = "SELECT username, firstName, lastName, status, customerID, lastLogin FROM Customer";

if ($result = mysqli_query($con, $sql)) {
    $response = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Get all the property names for each user and add to user info
        $sqlProp = "SELECT name, propertyID FROM Properties WHERE username = '" . $row['username'] . "'";
        $resultProp = mysqli_query($con, $sqlProp);
        $propList = [];
        while ($rowProp = mysqli_fetch_assoc($resultProp)) {
            $propList[] = $rowProp;
        }

        $row['properties'] = $propList;
        $response[] = $row;
    }
    echo json_encode($response);
} else {
    sendErrorEmail("
    getallcustomers.php<br />
    sql: $sql
    ");
    echo 'fail';
}

mysqli_close($con);
?>
