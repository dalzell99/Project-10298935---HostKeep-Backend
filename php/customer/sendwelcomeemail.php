<?php
require('../global.php');
require('../sendemail.php');

$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

if (sendWelcomeEmail($con, $_POST['username'])) {
    echo 'success';
} else {
    sendErrorEmail("
    sendwelcomeemail.php<br />
    Mail fail
    ");
    echo 'fail';
}

mysqli_close($con);
?>
