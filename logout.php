<?php
 //**************************************************************************
   //**************************************************************************
   // Project          :  MAYAVA FINANCE
   // Module           :  logout
   // Domain           :  http://wwa.mayavafinance.com
   // Programmer       :  Vinothkuamr V
   // Inprogress       :  \\offlinev5\Mayavafinance
   // Backup           :  \\dataserver\EIT\Projects\In Progress\
   // Last Modified    :  05-Feb-2010
   //**************************************************************************
   // Description      :  This is the logout php file  for Mayavafinance.
//**************************************************************************

session_start();

if(count($_SESSION) > 0) {
	foreach($_SESSION as $key => $value) {
		unset($_SESSION[$key]);
	}

}
header('Location: index.php');
die();

?>