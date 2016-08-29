<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$email = mysqli_real_escape_string($con, $_POST['email']);

// Insert new customer
if (sendWelcomeEmail($con, $email)) {
    echo 'success';
} else {
    sendErrorEmail("
    createnewcustomer.php<br />
    Mail fail
    ");
    echo 'fail';
}

mysqli_close($con);
?>
