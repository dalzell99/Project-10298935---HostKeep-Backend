<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$email = mysqli_real_escape_string($con, $_POST['username']);
$firstName = mysqli_real_escape_string($con, $_POST['firstName']);
$lastName = mysqli_real_escape_string($con, $_POST['lastName']);

$sql = "INSERT INTO Customer(username, firstName, lastName, status) VALUES ('$email', '$firstName', '$lastName', 'proposal')";

// Insert new customer
if ($result = mysqli_query($con, $sql)) {
    echo 'success' . mysqli_insert_id($con);
} else {
    // Check if it failed because the username/email is already in the database
    $sql2 = "SELECT 1 FROM Customer WHERE username = '$email'";
    if ($resultUser = mysqli_query($con, $sql2)) {
        if (mysqli_num_rows($resultUser) > 0) {
            echo "alreadyexists";
        } else {
            sendErrorEmail("
            createnewcustomeradmin.php<br />
            sql: $sql
            ");
            echo "fail";
        }
    } else {
        sendErrorEmail("
        createnewcustomeradmin.php<br />
        sql: $sql2
        ");
        echo "fail";
    }
}

mysqli_close($con);
?>
