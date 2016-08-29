<?php
require '../global.php';
$beyondPricingAPIKey = '58cea83371b689feddf4cfc70589ab1a';

$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo 'Failed to connect to MySQL: '.mysqli_connect_error();
}

$sql = 'SELECT beyondPricingID FROM Properties';
if ($result = mysqli_query($con, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$beyondPricing/listings/" . $row['beyondPricingID'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'accept: application/json',
                "token: $beyondPricingAPIKey",
            ),
        ));

        $response = (array) json_decode(curl_exec($curl));
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err && $response['min_price'] != null) {
            // Update base and min prices
            $sqlUpdate = "UPDATE Properties SET basePrice = " . $response['base_price'] . ", minimumNightlyPrice = " . $response['min_price'] . " WHERE beyondPricingID = " . $row['beyondPricingID'];
            if (!mysqli_query($con, $sqlUpdate)) { echo $sqlUpdate . '<br />'; }

            // Update bookings for this property
            foreach ($response['calendar'] as $day) {
                $day = (array) $day;
                $sqlBooking = "INSERT INTO Bookings VALUES (" . $row['beyondPricingID'] . ", '" . $day['date'] . "', '" . $day['availability'] . "', " . $day['price_scraped'] . ") ON DUPLICATE KEY UPDATE availability = '" . $day['availability'] . "', price = " . $day['price_scraped'];
                if(!mysqli_query($con, $sqlBooking)) { echo $sqlBooking . '<br />'; }
            }
        }
    }
} else {
    echo 'fail';
}

mysqli_close($con);
