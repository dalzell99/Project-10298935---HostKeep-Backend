<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sql = "UPDATE Customer SET
        salutation = '" . $_POST['salutation'] . "',
        firstName = '" . mysqli_real_escape_string($con, $_POST['firstName']) . "',
        lastName = '" . mysqli_real_escape_string($con, $_POST['lastName']) . "',
        company = '" . mysqli_real_escape_string($con, $_POST['company']) . "',
        phoneNumber = '" . mysqli_real_escape_string($con, $_POST['telephone']) . "',
        mobileNumber = '" . mysqli_real_escape_string($con, $_POST['mobile']) . "',
        bankName = '" . mysqli_real_escape_string($con, $_POST['bankName']) . "',
        bsb = '" . mysqli_real_escape_string($con, $_POST['bsb']) . "',
        accountNumber = '" . mysqli_real_escape_string($con, $_POST['accountNumber']) . "',
        postalAddress = '" . mysqli_real_escape_string($con, $_POST['address']) . "',
        suburb = '" . mysqli_real_escape_string($con, $_POST['suburb']) . "',
        state = '" . mysqli_real_escape_string($con, $_POST['state']) . "',
        postcode = '" . mysqli_real_escape_string($con, $_POST['postcode']) . "',
        country = '" . mysqli_real_escape_string($con, $_POST['country']) . "',
        lastModified = '" . date('c') . "'
        WHERE username = '" . $_POST['username'] . "'";

if (mysqli_query($con, $sql)) {
    echo 'success';

    if ($_POST['admin'] != 'true') {
        $changesArray = json_decode($_POST['changes']);
        $changesString = '';

        foreach ($changesArray as $change) {
            switch ($change) {
                case 'company':
                    $changesString .= 'Company: ' . $_POST['company'] . "<br />";
                    break;
                case 'telephone':
                    $changesString .= 'Phone Number: ' . $_POST['telephone'] . "<br />";
                    break;
                case 'mobile':
                    $changesString .= 'Mobile Number: ' . $_POST['mobile'] . "<br />";
                    break;
                case 'bankName':
                    $changesString .= 'Bank Name: ' . $_POST['bankName'] . "<br />";
                    break;
                case 'BSB':
                    $changesString .= 'BSB: ' . $_POST['bsb'] . "<br />";
                    break;
                case 'accountNumber':
                    $changesString .= 'Bank Account Number: ' . $_POST['accountNumber'] . "<br />";
                    break;
                case 'address':
                    $changesString .= 'Postal Address: ' . $_POST['address'] . "<br />";
                    break;
                case 'suburb':
                    $changesString .= 'Suburb: ' . $_POST['suburb'] . "<br />";
                    break;
                case 'state':
                    $changesString .= 'State: ' . $_POST['state'] . "<br />";
                    break;
                case 'postcode':
                    $changesString .= 'Postcode: ' . $_POST['postcode'] . "<br />";
                    break;
                case 'country':
                    $changesString .= 'Country: ' . $_POST['country'] . "<br />";
                    break;
            }
        }

        $message = "Username: " . $_POST['username'] . "<br />" . $changesString;

        if (!sendEmail($hostkeepEmail, $noReplyEmail, 'Profile Changes', $message)) {
            sendErrorEmail("
            saveprofilechanges.php<br />
            Email Fail
            " . $message);
        }
    }
} else {
    sendErrorEmail("
    saveprofilechanges.php<br />
    sql: $sql
    ");
    echo 'fail' . $sql;
}

mysqli_close($con);
?>
