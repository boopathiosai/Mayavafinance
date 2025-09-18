<?php
$val = 12.054;
print_r(splitter($val));

function splitter($val)
{
  $str = (string) $val ;
  $splitted = explode(".",$str);
  $whole = (integer)$splitted[0] ;
  $num = (integer) $splitted[1];
  $den = (integer)  pow(10,strlen($splitted[1]));
  return array('whole' => $whole, 'num' => $num,'den' => $den);
}

?>