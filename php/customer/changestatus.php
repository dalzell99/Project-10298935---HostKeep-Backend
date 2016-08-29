<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$customerID = $_POST['customerID'];
$status = mysqli_real_escape_string($con, $_POST['status']);

// Get status before changing it
$sqlUser = "SELECT status, username FROM Customer WHERE customerID = '$customerID'";
if ($resultUser = mysqli_query($con, $sqlUser)) {
    $sql = "UPDATE Customer SET status = '$status' WHERE customerID = '$customerID'";
    if ($result = mysqli_query($con, $sql)) {
        echo 'success';
    } else {
        sendErrorEmail("
        changestatus.php<br />
        SQL: $sql
        ");
        echo 'fail' . $sql;
    }
}

mysqli_close($con);
?>
