<?php
 //**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE                   
 // Module           :  Autoload     
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Vinothkuamr V
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  03-Feb-2010
 //**************************************************************************
 // Description      :  This is the Autoload  file  for Mayavafinance.
//**************************************************************************

class AutoloadClass {

    public static function autoload($class_name) {
   
        include_once ROOT_PATH . 'classes/'.strtolower($class_name) . '.class.php';
    }

}

spl_autoload_register(array('AutoloadClass', 'autoload'));
 