<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mayava Finance</title>
<link href="css/reset.css" rel="stylesheet" type="text/css" />
<link href="css/pop-up.css" rel="stylesheet" type="text/css" />
<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/p7exp.css" rel="stylesheet" type="text/css" />
<link href="css/jquery.autocomplete.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/anylinkmenu.css" />
<link rel="stylesheet" type="text/css" href="css/jquery-ui.custom.css" />
<!-- calender -->
<link type="text/css" rel="stylesheet" href="css/jscal2.css" />
<link type="text/css" rel="stylesheet" href="css/border-radius.css" />
<link id="skin-win2k" title="Win 2K" type="text/css" rel="alternate stylesheet" href="css/win2k.css" />
<style type="text/css">
 body
 .main-container-ip{
  margin-left: 260px;
 }
 .ip-address{
  font-size: 30px !important;
  margin-top: 260px;
 }
</style>
</head>
<body>
<div id="common-floating-message" align="center" class="header-floating" style="display:none"></div>

<div id="main-container"><!--Header cintainer starts here-->
    <div id="header">
            <div class="lftcont"><img src="images/logo.jpg" /></div>
    </div><br><br><br>
      <div class="main-container-ip">
        <span class="ip-address">System Ip-address: </span>
        <span class="ip-address">
          <?php
          function get_client_ip() {
          $ipaddress = '';
          if (isset($_SERVER['HTTP_CLIENT_IP']))
              $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
          else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
              $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
          else if(isset($_SERVER['HTTP_X_FORWARDED']))
              $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
          else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
              $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
          else if(isset($_SERVER['HTTP_FORWARDED']))
              $ipaddress = $_SERVER['HTTP_FORWARDED'];
          else if(isset($_SERVER['REMOTE_ADDR']))
              $ipaddress = $_SERVER['REMOTE_ADDR'];
          else
              $ipaddress = 'UNKNOWN';

          return $ipaddress;
          }
      echo $ipaddress = $_SERVER['REMOTE_ADDR'];
          //$client_ip_addr = get_client_ip();
          //echo "<pre>";print_r($_SERVER);
         // echo get_client_ip();
          exit; ?></span></div>
      </div>



