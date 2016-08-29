<?php
require_once("phpmailer/class.phpmailer.php");
require_once("global.php");

function sendWelcomeEmail($con, $email) {
    $password = substr(md5(rand()), 0, 7); // A random 7 character password

    $sql = "INSERT INTO Customer(username, password, status) VALUES ('$email', '" . hashPassword($con, $password) . "', 'proposal') ON DUPLICATE KEY UPDATE password = '" . hashPassword($con, $password) . "'";

    require("../welcomeemail.php");

    // Send welcome email with login details
    if (mysqli_query($con, $sql) && sendEmail($email, $noReplyEmail, 'Welcome to HostKeep', $message)) {
        return true;
    } else {
        return false;
    }
}

function sendErrorEmail($message) {
    sendEmail('dalzell99@hotmail.com', $noReplyEmail, 'HostKeep Error', $message);
}

function sendEmail($to, $from, $subject, $message) {
    $mail = new PHPMailer;

    $mail->IsSMTP();                                      // Set mailer to use SMTP
    //$mail->SMTPDebug  = 1;
    $mail->Host = "box306.bluehost.com";                 // Specify main and backup server
    $mail->Port = 465;                                    // Set the SMTP port
    $mail->SMTPAuth = false;                               // Enable SMTP authentication
    //$mail->Username = 'noreply@hostkeep.com.au';                // SMTP username
    //$mail->Password = "S\kZDEiw8*";                  // SMTP password
    $mail->SMTPSecure = "ssl";                            // Enable encryption, 'ssl' also accepted


    $mail->From = 'noreply@hostkeep.com.au';
    $mail->FromName = "HostKeep";

    $mail->addAddress($to);

    $mail->isHTML(true);

    $mail->Subject = $subject;
    $mail->Body = $message;

    if(!$mail->send()) {
        return false;
    } else {
        return true;
    }
}

?>
