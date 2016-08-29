<?php
require('../global.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sql = "SELECT months FROM ContractExpiryEmailTime";
if ($result = mysqli_query($con, $sql)) {
    echo mysqli_fetch_assoc($result)['months'];
} else {
    echo 'fail';
}
mysqli_close($con);
?>
