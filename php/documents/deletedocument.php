<?php
require('../global.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$documentID = $_POST['documentID'];

$sql = "DELETE FROM Documents WHERE documentID = $documentID";
if (mysqli_query($con, $sql)) {
    echo 'success';
} else {
    echo 'fail';
    sendErrorEmail("
    deletedocument.php<br />
    sql: $sql
    ");
}

mysqli_close($con);
?>
