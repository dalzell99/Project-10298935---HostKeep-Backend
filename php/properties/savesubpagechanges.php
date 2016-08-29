<?php
require('../global.php');
require('../sendemail.php');
$beyondPricingAPIKey = '58cea83371b689feddf4cfc70589ab1a';

$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

// Get current min and base prices to check if they have been changed
$sqlGetPrices = "SELECT basePrice, minimumNightlyPrice, beyondPricingID FROM Properties WHERE propertyID = '" . $_POST['propertyID'] . "'";
$resultGetPrices = mysqli_query($con, $sqlGetPrices);
$rowGetPrices = mysqli_fetch_assoc($resultGetPrices);
$minPrice = $rowGetPrices['minimumNightlyPrice'];
$basePrice = $rowGetPrices['basePrice'];
$beyondPricingID = $rowGetPrices['beyondPricingID'];

// If either price is different, update the prices in beyond pricing
if ($minPrice != $_POST['minimumNightlyPrice'] || $basePrice != $_POST['basePrice']) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "$beyondPricing/listings/$beyondPricingID",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{
            \"min_price\":" . $_POST['minimumNightlyPrice'] . ",
            \"base_price\":" . $_POST['basePrice'] . ",
            \"id\":" . $beyondPricingID . "
        }",
        CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "content-type: application/json",
            "token: $beyondPricingAPIKey"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    }
}

// If the contract expiry date was changed, reset the contractExpiryEmailSent value
// in the database to 'n'
if ($_POST['contractExpiryDateChanged'] == 'true') {
    $contractExpiryDateChanged = ", contractExpiryEmailSent = 'n'";
} else {
    $contractExpiryDateChanged = "";
}

$sql = "UPDATE Properties SET
name = '" . mysqli_real_escape_string($con, $_POST['name']) . "',
description = '" . mysqli_real_escape_string($con, $_POST['description']) . "',
address = '" . mysqli_real_escape_string($con, $_POST['address']) . "',
basePrice = '" . mysqli_real_escape_string($con, $_POST['basePrice']) . "',
minimumNightlyPrice = '" . mysqli_real_escape_string($con, $_POST['minimumNightlyPrice']) . "',
commencementDate = '" . mysqli_real_escape_string($con, $_POST['commencementDate']) . "',
propertyFee = '" . mysqli_real_escape_string($con, $_POST['propertyFee']) . "',
cleaningFee = '" . mysqli_real_escape_string($con, $_POST['cleaningFee']) . "',
contractExpiryDate = '" . mysqli_real_escape_string($con, $_POST['contractExpiryDate']) . "',
airbnbURL = '" . mysqli_real_escape_string($con, $_POST['airbnbURL']) . "',
guestGreetURL = '" . mysqli_real_escape_string($con, $_POST['guestGreetURL']) . "',
selfCheckinURL = '" . mysqli_real_escape_string($con, $_POST['selfCheckinURL']) . "',
icalURL = '" . mysqli_real_escape_string($con, $_POST['icalURL']) . "',
status = '" . $_POST['status'] . "',
commencementFee = '" . mysqli_real_escape_string($con, $_POST['commencementFee']) . "',
commencementFeeReceived = '" . mysqli_real_escape_string($con, $_POST['commencementFeeReceived']) . "'
$contractExpiryDateChanged
WHERE propertyID = '" . $_POST['propertyID'] . "'";

if ($result = mysqli_query($con, $sql)) {
    echo 'success';
} else {
    sendErrorEmail("
    savesubpagechanges.php<br />
    sql: $sql
    ");
    echo 'fail';
}

mysqli_close($con);
?>
