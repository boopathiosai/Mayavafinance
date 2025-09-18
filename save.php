<?php
session_start();

if(isset($GLOBALS["HTTP_RAW_POST_DATA"])){
    
    $jpg = $GLOBALS["HTTP_RAW_POST_DATA"];
    //$img = $_GET["photo_id"]; 
    
    $picture_name = "tempimage";
    $filename = "customer/images/".$picture_name.".jpg";    
    $_SESSION['picture_name'] = '';
    $_SESSION['picture_name'] = $picture_name;
    
    // remove temp picture if exists
    $old_filename = "customer/images/".$_SESSION['picture_name'].".jpg";
    file_exists($old_filename)? unlink($old_filename):'';
    

    
    file_put_contents($filename, $jpg);
    
    echo $picture_name;
    
} else{
    echo "error";
}

function random_num($n=5)
{
    return rand(0, pow(10, $n));
}
?>