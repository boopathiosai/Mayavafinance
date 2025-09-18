<?php
session_start();

//Include the necessary files
//===========================
define ('ROOT_PATH', '');
$page_title = '';
$module_name = '';
include ROOT_PATH . 'includes/config.php';


//Check Module Access Privillege & Session logged in
//==================================================
/*if($app->is_logged_in() !== true || ($_SESSION[$app->logged_session_id] > 1 && Settings::LoadConfigurations('banner_mgmt') <> 1)) {
	header('Location: index.php');
	exit;
}*/

//Get the Mode From Related Page 
//===============================

$mode = (isset($_GET['mode'])) ? $_GET['mode'] : $_POST['mode'];

//Get User Login ID
//=================
$user_id = $_SESSION[$app->logged_session_id];

//GET Community Admin Id
//======================
$sess_comm_id = $_SESSION[$app->logged_session_community_id];

//Get the ID
//==========
$id = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];

//Get the Type
//============

$type = $_GET['type'];

$output='<select name="user" id="user" style="width:80%;" multiple="multiple">';
            
//Diplay Users based on the User Type
//===================================

if($type=="getemail")
{
	// $cust_support_admin = $_GET['user_cust'];
	 $branchemail = $_GET['branch_id'];
     if($branchemail!='')
			{
				$select_data = "select id,name,email  from ".USERS." where branch_id='$branchemail'";
				$data=$app->getrows($select_data);
				print_r($data);exit;
			}
    

    /*if($cust_support_admin!='')
	{
		$select_usertype_cust = "select id,username,user_type,email_id  from ".USER_MANAGEMENT." where user_type='$cust_support_admin'";
	    $data_cust=$app->getrows($select_usertype_cust);
	}*/
    
	if(count($data_cust)>0)
	{
		foreach ($data_cust as $key => $row ) {

		$selected = ($row[user_type]==$user_type) ? 'selected=" selected "' : '';
       
      	$output.='<option value="'.$row[id].'" '.$selected.'>'.$row[email_id].'</option>';
		}
	}

	

	

}

echo $output;

?>

