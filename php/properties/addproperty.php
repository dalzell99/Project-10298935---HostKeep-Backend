<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$username = $_POST['username'];
$propertyID = mysqli_real_escape_string($con, $_POST['propertyID']);
$name = mysqli_real_escape_string($con, $_POST['name']);
$description = mysqli_real_escape_string($con, $_POST['description']);
$address = mysqli_real_escape_string($con, $_POST['address']);
$minimumNightlyPrice = $_POST['minimumNightlyPrice'];
$propertyFee = $_POST['propertyFee'];
$imageURL = mysqli_real_escape_string($con, $_POST['imageURL']);

$sql = "INSERT INTO Properties (propertyID, username, name, description, address, minimumNightlyPrice, imageURL, propertyFee, status) VALUES ('$propertyID', '$username', '$name', '$description', '$address', '$minimumNightlyPrice', '$imageURL', '$propertyFee', 'proposal')";

// Insert property into database
if (mysqli_query($con, $sql)) {
    echo 'success';
} else {
    sendErrorEmail("
    addproperty.php<br />
    sql: $sql
    ");
    echo 'fail insert sql';
}

mysqli_close($con);
?>
