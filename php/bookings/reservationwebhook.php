<?php
require('../global.php');
require('../sendemail.php');

$reservation = file_get_contents('php://input');

sendEmail('dalzell99@hotmail.com', $noReplyEmail, 'Reservation Webhook', $reservation);
?>
