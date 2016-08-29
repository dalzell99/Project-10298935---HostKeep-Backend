<?php
/**
 * Parses the ics of a google calendar to return a json array of dates already booked
 *
 * PHP Version 5
 *
 * @category Example
 * @package  ics-parser
 * @author   Martin Thoma <info@martin-thoma.de>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     https://github.com/MartinThoma/ics-parser/
 */
require('class.iCalReader.php');
require_once('../global.php');
require_once('../sendemail.php');

$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sql = "SELECT icalURL FROM Properties WHERE propertyID = '" . $_GET['propertyID'] . "'";

if ($result = mysqli_query($con, $sql)) {
    $ical = new ICal('https://calendar.google.com/calendar/ical/' . mysqli_fetch_assoc($result)['icalURL'] . '/public/basic.ics');
    $events = $ical->events();
    $response = [];

    foreach ($events as $event) {
        array_push($response, [$event['DTSTART'], $event['DTEND']]);
    }

    echo json_encode($response);
} else {
    sendErrorEmail("
    getbookeddates.php<br />
    sql: $sql
    ");
    echo 'fail';
}

?>
