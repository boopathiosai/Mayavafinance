<?php
    $query = "SELECT config_value FROM ".SETTINGS." WHERE config_name='organization_name'";
    $organization_data = $app->getrow($query);
    $config_value = $organization_data['config_value'];
	
    $organization_data = array($config_value => $config_value);
?>