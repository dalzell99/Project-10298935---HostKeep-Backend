<?php
$target_file = "../../documents/" . $_FILES["file"]["name"];

move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
?>
