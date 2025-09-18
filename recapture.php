<?php
$photo_name = "../customer/images/".$_GET["temp"].".jpg";
$save_type = $_GET["type"];
$save_name = "../customer/images/temp_".$save_type.".jpg";
rename($photo_name,$save_name);
if(file_exists($photo_name)) unlink($photo_name);
echo "{\"type\":\"".$save_type."\",\"file\":\"".preg_replace("/^..\//i","",$save_name)."\"}";
?>