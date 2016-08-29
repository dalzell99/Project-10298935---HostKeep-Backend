<?php
require_once('../global.php');
require_once('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$name = mysqli_real_escape_string($con, $_POST['name']);
$propertyID = $_POST['propertyID'];
$month = $_POST['month'];
$year = $_POST['year'];
$dateUploaded = date('c');
$notes = mysqli_real_escape_string($con, $_POST['notes']);
$filename = $_POST['filename'];

$username = mysqli_fetch_assoc(mysqli_query($con, "SELECT username FROM Properties WHERE propertyID = '$propertyID'"))['username'];

$sql = "INSERT INTO Documents VALUES (DEFAULT, '$username', '$name', '$propertyID', '$month', '$year', '$dateUploaded', '$notes', '$filename')";

// Insert document into database
if (mysqli_query($con, $sql)) {
    // Get documentID of last insert document
    echo json_encode(array('success', $username, mysqli_insert_id($con)));
} else {
    sendErrorEmail("
    adddocument.php<br />
    sql: $sql
    ");
    echo json_encode('fail insert sql');
}

mysqli_close($con);
?>
