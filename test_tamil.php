<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<?php
$data = 'தமிழ்';
$data = 'பாலகிருஷ்ணமூர்த்�';

//Convert Tamil text to hexa decimal codes
$mb_hex = '';
for($i = 0 ; $i<mb_strlen($data,'UTF-8') ; $i++){
    $c = mb_substr($data,$i,1,'UTF-8');
    $o = unpack('N',mb_convert_encoding($c,'UCS-4BE','UTF-8'));
    $mb_hex .= sprintf('%04X',$o[1]);
}
echo "Hex Chars = ".$mb_hex;
echo '<hr>';
echo $data;

?>
</body>
</html>