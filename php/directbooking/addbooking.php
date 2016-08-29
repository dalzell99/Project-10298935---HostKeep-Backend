<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$customerID = $_POST['customerID'];
$propertyID = $_POST['propertyID'];
$guestName = mysqli_real_escape_string($con, $_POST['guestName']);
$guestMobile = mysqli_real_escape_string($con, $_POST['guestMobile']);
$guestEmail = mysqli_real_escape_string($con, $_POST['guestEmail']);
$guestCheckIn = $_POST['guestCheckIn'];
$guestCheckOut = $_POST['guestCheckOut'];
$invoiced = $_POST['invoiced'];
$cleanUp = $_POST['cleanUp'];
$notes = mysqli_real_escape_string($con, $_POST['notes']);
$admin = $_POST['admin'];
$username = $_POST['username'];
$propertyName = mysqli_real_escape_string($con, $_POST['propertyName']);
$nightlyRate = $_POST['nightlyRate'];
$creationDate = date('c');

$sql = "INSERT INTO DirectBookings VALUES (DEFAULT, '', '$customerID', '$propertyID', '$guestName', '$guestMobile', '$guestEmail', '$guestCheckIn', '$guestCheckOut', '$invoiced', '$cleanUp', '$notes', '$nightlyRate', '$creationDate')";
if (mysqli_query($con, $sql)) {
    $id = mysqli_insert_id($con);

    // Create custom direct booking id with format DXXXXX where X is a number
    $directBookingID = $id;
    while (strlen($directBookingID) < 5) {
        $directBookingID = '0' . $directBookingID;
    }
    $directBookingID = 'D' . $directBookingID;

    mysqli_query($con, "UPDATE DirectBookings SET directBookingID = '$directBookingID' WHERE bookingID = '$id'");

    echo 'success' . $id;

    sendEmail($hostkeepEmail, $noReplyEmail, "New Direct Booking", "
    Username: $username <br />
    Property: $propertyName <br />
    Guest Name: $guestName <br />
    Guest Mobile: $guestMobile <br />
    Guest Email: $guestEmail <br />
    Check-in: " . substr($guestCheckIn, 6, 2) . '/' . substr($guestCheckIn, 4, 2) . '/' . substr($guestCheckIn, 0, 4) . "<br />
    Check-out: " . substr($guestCheckOut, 6, 2) . '/' . substr($guestCheckOut, 4, 2) . '/' . substr($guestCheckOut, 0, 4) . "<br />
    Invoiced: $invoiced <br />
    " . ($invoiced == 'true' ? "Nightly Rate: $nightlyRate <br />" : "") . "
    Cleanup: $cleanUp <br />
    Notes: $notes
    ");
} else {
    echo 'fail';
    sendErrorEmail("
    addbooking.php<br />
    sql: $sql
    ");
}

mysqli_close($con);
?>
