<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$username = $_POST['username'];
$password = hashPassword($con, $_POST['password']);

$sql = "SELECT password FROM Customer WHERE username = '$username'";

if ($result = mysqli_query($con, $sql)) {
    if (mysqli_fetch_assoc($result)['password'] == $password) {
        echo 'correct';
    } else {
        echo 'incorrect';
    }
} else {
    sendErrorEmail("
    checkcurrentpassword.php<br />
    sql: $sql
    ");
    echo 'fail';
}

mysqli_close($con);
?>
