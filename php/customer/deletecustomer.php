<?php
require('../global.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sql = "DELETE FROM Customer WHERE username = '" . $_POST['username'] . "'";
if (mysqli_query($con, $sql)) {
    echo 'success';
} else {
    sendErrorEmail("
    deletecustomer.php<br />
    SQL: $sql
    ");
    echo 'fail';
}

mysqli_close($con);
?>
