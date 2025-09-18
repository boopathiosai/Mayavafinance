<?php
//**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE
 // Module           :  Defaults
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Vinothkuamr V
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  05-Feb-2010
 //**************************************************************************
 // Description      :  This is the Defaults file  for Mayavafinance.
 //**************************************************************************


session_start();

define ('RANGE', 4);

$date_formats = array();
$date_formats['%m-%d-%Y'] = 'MM-DD-YYYY';
$date_formats['%d-%m-%Y'] = 'DD-MM-YYYY';


$pagenation_array = array(10 => '10', 15 => '15',20 => '20', 25 => '25');

$status_array = array(1 => 'Enable', 0 => 'Disable');

$status_arraytext = array(1 => 'Read', 0 => 'Unread');