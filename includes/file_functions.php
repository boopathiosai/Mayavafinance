<?php

function unlinkPhoto($filesuffix, $filesize=""){

    /*$filesuffix = preg_replace("/^".$filesize."/i","",$filename);*/

    $photothumbname = THUMB.$filesuffix;
    $photomediumname = MEDIUM.$filesuffix;
    $photosmallname = SMALL.$filesuffix;

    $photothumbupload = PRODUCT_IMAGES_PATH.$photothumbname;
    $photomediumupload = PRODUCT_IMAGES_PATH.$photomediumname;
    $photosmallupload = PRODUCT_IMAGES_PATH.$photosmallname;
    if(file_exists($photothumbupload))  unlink($photothumbupload);
    if(file_exists($photomediumupload)) unlink($photomediumupload);
    if(file_exists($photosmallupload))  unlink($photosmallupload);
    
    return true;
}


function deleteFiles($str){
    $files = explode(",",$str);
    foreach($files as $file){
        unlinkPhoto($file);
    }
}

function copyFolder($topaste, $tolocation){
    
    if (is_dir($topaste)) {
        if ($dirhand = opendir($topaste)) {
            if(!readdir($dirhand)) return true;
            while (($file = readdir($dirhand)) !== false) {
                if($file !="." && $file !=".." ){
                //echo "<br>Chk path ".$topaste."/".$file."<br> FOLDER {$topaste} <br> {$tolocation} <br><br>";
                if(is_dir($topaste."/".$file)){
                    
                    if(!is_dir($tolocation."/".$file))
                    {
                     //echo "<br>{$file} folder created<br>";
                     mkdir($tolocation."/".$file,777);
                     }
                    copyFolder($topaste."/".$file, $tolocation."/".$file);
                }else{
                    //echo "<br> FILE ".$topaste."/".$file." .::. ".$tolocation."/".$file." <br>";
                    copy($topaste."/".$file, $tolocation."/".$file);
                }
                }
            }
            closedir($dirhand);
        }
        return true;
    }
    return true;
}

function deleteFolder($todelete){
    if (is_dir($todelete)) {
        if ($dirhand = opendir($todelete)) {
            if(!readdir($dirhand)) return true;
            while (($file = readdir($dirhand)) !== false) {
                if($file !="." && $file !=".." ){
                //echo "<br>Chk path ".$todelete."/".$file."<br> FOLDER {$topaste} <br> {$tolocation} <br><br>";
                if(is_dir($todelete."/".$file)){
                    deleteFolder($todelete."/".$file);
                }else{
                    //echo "<br> FILE ".$todelete."/".$file." <br>";
                    unlink($todelete."/".$file);
                }
                }
            }
            closedir($dirhand);
        }
        rmdir($todelete);
        return true;
    }
    sleep(1);
    return true;
}

function deleteFilesByModifyDate($todelete){
    if (is_dir($todelete)) {
        if($dirhand = opendir($todelete)) {
            if(!readdir($dirhand)) return true;
            while (($file = readdir($dirhand)) !== false) {
                if($file !="." && $file !=".." && file!= "tmp.html"){
                    //echo "<br>Chk path ".$todelete."/".$file."<br> FOLDER {$topaste} <br> {$tolocation} <br><br>";
                    $mod_date = date("Y-m-d",fileatime($todelete."/".$file));
                    $cur_date = date("Y-m-d");
                    
                    if(!is_dir($todelete."/".$file) && $cur_date > $mod_date){
                        unlink($todelete."/".$file);
                    }

                }
            }
            closedir($dirhand);
        }
        return true;
    }
    return true;
}