<?php

session_start();

define ('ROOT_PATH', '');
$page_title = 'My Account';
$module_name = 'my_account';

include ROOT_PATH . 'includes/config.php';

$menu = Privillege::getModuleDetail($_SESSION[$app->session_usertype1]);

/**
 * Redirect to index page if not logged in
 **/
 $mod = (isset($_GET['mod'])) ? $_GET['mod'] : $_POST['mod'];
if($app->is_logged_in() !== true) {

	if($mod == 'Ajax') {
			echo "session_timed_out";
			exit;
	}
	else{
			header('Location: index.php');
			exit;
	}	
} 

/**
 * Get values from js
 **/

$pval = $_POST['pval'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$uval['password'] = (isset($_GET['password'])) ? $_GET['password'] : $_POST['password'];
$page_no = 1;

$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];
$disp = (isset($_GET['disp'])) ? $_GET['disp'] : $_POST['disp'];
$disp = ($disp!='') ? $disp : 'content_div';
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];

/*
 * Class Instances
 */

$app->access_table = USERS;

switch($type)
{

	case 'formvalidation':
	/*
		 * Validate form. Displaying error, if exist
		 */
		
		/*$missing_vals = Validator::validate_data('Myaccount');  
        if (!empty( $missing_vals )) {
			$missing_data = explode('@@@', $missing_vals);	
			$require_data = Myaccount::${$missing_data[1]};
		    echo $require_data.'~~' . $missing_data[0] . '~~0';			
			exit;
		}*/

		$missing_labels = Validator::validate_json('Myaccount');
        //Common Code for all
		if(trim($pval['password']) =="" && trim($pval['newpassword'])!="" ) 
		{			
			//echo "Required Fields Missing!".'~~' ."#password".'~~0';						
			//exit;
			$missing_labels['password']='Required Fields Missing!';
		}

		if($pval['password'] !="" || ($pval['newpassword']!="" AND $pval["confirmnewpassword"]!="")) 
		{
			  
			/*
			 * Validate the password fields.Displaying error, if exist
			 */	
			 $password = base64_encode(trim($pval['password'])); 
			 $count = $app->find_count(array('conditions' => array('id = ' => $_SESSION['session_userid'] ,'password = ' => $password ))); 
			 if($count == 0 )
		    { 
				//echo "Old Password is Wrong!".'~~' ."#password".'~~0';						
				//exit;
				$missing_labels['password']='Old Password is Wrong!';

			 }else if(trim($pval['newpassword']) != trim($pval["confirmnewpassword"]))
				 {
				//echo "New password and Confirm New password must be same!".'~~' ."#confirmnewpassword".'~~0';	
				//exit;
				$missing_labels['confirmnewpassword']='New password and Confirm New password must be same!';
			 }else if(strlen(trim($pval['newpassword']))<6)
				 {
					 if(trim($pval['newpassword'])==""){
						//echo "Required Fields Missing!".'~~' ."#newpassword".'~~0';	
						
						$missing_labels['newpassword']='Required Fields Missing!';

					 }else{
						//echo "Password length should be greater than 5!".'~~' ."#newpassword".'~~0';		
						
						$missing_labels['newpassword']='Password length should be greater than 5!';

					 }
					//exit;
			      }
		}
		
            if (!empty( $missing_labels )) { 
            
                $fields = implode(",#",array_keys($missing_labels));
                $labels = implode(",",$missing_labels);
                $success = false;
                
            }else{
                $fields ='';
                $labels ='';
                $success = true;
            }
            
            $isMsgDisplay = true;
            
            //p_arr($missing_labels);
            
              echo "
               { 
                \"page\" : \"".$page."\", 
                \"success\" : \"".$success."\", 
                \"disp\" : \"".$disp."\", 
                \"content\" : \"".MYACCOUNT_VALIDATE_ERROR."\", 
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
               }";
			//echo '~~~~1'; 
			//exit;  
            break;
	
    case 'update':
    case 'add':
        /*
		 * update values in database
		 */		
		$pval['modified_datetime'] = date("Y-m-d H:i:s");
		$pval['modified_by'] = $_SESSION['session_userid'];
		unset($pval['password']);
		if($pval['newpassword']!="" )
		{
			$pval['password'] = $pval['newpassword'];
		}
		unset($pval["newpassword"]);
		unset($pval["confirmnewpassword"]);
		
		$status=$app->save($pval, array('id = ' => $uval['id'])); //Update values into database	
        //$success_msg = "Your Account Has Been Successfully Updated!";
		//$_SESSION['success_msg'] 	= $success_msg;

        echo "
        { 
            \"page\" : \"".$page."\", 
            \"status\" : \"".$status."\", 
            \"disp\" : \"".$disp."\", 
            \"content\" : \"".Updated_Sucessfullly."\", 
            \"isAlert\" : \"".true."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
        }";

        exit;
		//header("Location: my_account.php");
		//exit;
		break;

    default:
		/*
		 * Get the current logged-in user details
		 */
		$user_data = Myaccount::Detail($_SESSION['session_userid']);
		$user_data =$user_data[0];
		include ADMIN_LAYOUT_PATH . 'common/header.html';		
		include ADMIN_LAYOUT_PATH . 'forms/my_account.html';		
		include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}
?>