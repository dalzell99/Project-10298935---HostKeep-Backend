<?php
require('../global.php');
require('../sendemail.php');
$con = mysqli_connect('localhost', $dbusername, $dbpassword, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$sql = "SELECT * FROM Documents WHERE username = '" . $_POST['username'] . "'";
$sqlUsedFilenames = "SELECT DISTINCT documentFilename FROM Documents";

if (($result = mysqli_query($con, $sql)) && ($resultFilenames = mysqli_query($con, $sqlUsedFilenames))) {
    $response = [];
    $filenames = [];
    $usedFilenames = [];
    $distinctFilenames = [];

    // Add results to an array
    while ($row = mysqli_fetch_assoc($result)) {
        $resultProp = mysqli_query($con, "SELECT name FROM Properties WHERE propertyID = '" . $row["propertyID"] . "'");
        $row['propertyName'] = mysqli_fetch_assoc($resultProp)['name'];

        $response[] = $row;
    }

    // Add already used filenames to usedFilenames array
    while ($rowUsedFilenames = mysqli_fetch_assoc($resultFilenames)) {
        $usedFilenames[] = $rowUsedFilenames['documentFilename'];
    }

    // Get all the filenames from the documents folder
    if ($handle = opendir('../../documents')) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $filenames[] = $entry;
            }
        }
        closedir($handle);
    } else {
        sendErrorEmail("
        getdocuments.php<br />
        Filename errors
        ");
        echo json_encode("fail");
    }

    // Remove filenames that have already been used
    $filenames = array_diff($filenames, $usedFilenames);

    // Echo array as json
    echo json_encode(array($response, $filenames));
} else {
    sendErrorEmail("
    getdocuments.php<br />
    sql: $sql
    ");
    echo json_encode('fail');
}

mysqli_close($con);
?>
