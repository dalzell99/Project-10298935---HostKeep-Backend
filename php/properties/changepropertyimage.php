<?php
require('../global.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$propertyID = $_POST['propertyID'];
$imageURL = mysqli_real_escape_string($con, $_POST['imageURL']);

$sql = "UPDATE Properties SET imageURL = '$imageURL' WHERE propertyID = '$propertyID'";
if ($result = mysqli_query($con, $sql)) {
    echo 'success';
} else {
    echo 'fail';
    sendErrorEmail("
    changepropertyimage.php<br />
    sql: $sql
    ");
}

mysqli_close($con);
?>
