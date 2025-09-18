<?php

/*
define ('LOAN_COUNT', '48');
define ('ROWS_PER_PAGE', '12');
define ('ORGANIZATION_NAME', '12');
*/

$constants = Settings::settings_value();


if(count($constants)>0){
    foreach($constants as $constant){
    
        
                
        define(
            strtoupper(
                preg_replace(
                    "/ /","_",
                    $constant['config_name']
                )
            ),
            $constant['config_value']
        ); 
        
    }
}




?>