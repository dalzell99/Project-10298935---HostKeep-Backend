<?php
$target_file = "../../images/properties/" . $_FILES["file"]["name"];

move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
?>
