<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$username = mysqli_real_escape_string($con, $_POST['username']);
$password = hashPassword($con, $_POST['password']);

$sql = "SELECT * FROM Customer WHERE username = '$username'";

// Get customer info
if ($result = mysqli_query($con, $sql)) {
    if (mysqli_num_rows($result) == 0) {
        // If username doesn't exist in database, then respond with incorrectusername
        echo 'incorrectusername';
    } else {
        $assoc = mysqli_fetch_assoc($result);
        if ($assoc['status'] == 'retired') {
            // Echo retired if the account has been marked as retired
            echo 'retired';
        } else if ($assoc['lastLogin'] == '') {
            // If the customer hasn't logged in yet then update last login database info and send firsttime and json array of customer info
            mysqli_query($con, "UPDATE Customer SET lastLoginIP = '" . $_SERVER['REMOTE_ADDR'] . "', lastLogin = '" . date('c') . "' WHERE username = '$username'");
            echo 'firsttime' . json_encode($assoc);
        } else if ($assoc['password'] == $password) {
            // If the customer has logged in before then update last login database info and send correct and json array of customer info
            mysqli_query($con, "UPDATE Customer SET lastLoginIP = '" . $_SERVER['REMOTE_ADDR'] . "', lastLogin = '" . date('c') . "' WHERE username = '$username'");
            echo 'correct' . json_encode($assoc);
        } else {
            // If password is incorrect then respond with incorrectpassword
            echo 'incorrectpassword';
        }
    }
} else {
    sendErrorEmail("
    login.php<br />
    sql: $sql
    ");
    echo 'fail';
}

mysqli_close($con);
?>
