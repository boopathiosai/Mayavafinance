<?php
 //**************************************************************************
  //**************************************************************************
  // Project          :  MAYAVA FINANCE
  // Module           :  index
  // Domain           :  http://wwa.mayavafinance.com
  // Programmer       :  Vinothkuamr V
  // Inprogress       :  \\offlinev5\Mayavafinance
  // Backup           :  \\dataserver\EIT\Projects\In Progress\
  // Last Modified    :  05-Feb-2010
  //**************************************************************************
  // Description      :  This is the index php file  for Mayavafinance.
//**************************************************************************
session_start();
error_reporting(1);

define ('ROOT_PATH', '');
$page_title = 'Mayava Finance';
$module_name = 'index';

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = DASHBOARD_CAT_ID;
/*
* If logged in, redirect to myaccount page
*/
if($app->is_logged_in() == true) {
    header('Location:dashboard.php');
    exit;
}


$app->access_table = USERS;

$mod = (isset($_GET['mod'])) ? $_GET['mod'] : $_POST['mod'];

$pval = $_POST['pval'];

$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
class Login {
    public $check_required = array('username','password');
    //public $check_min_length = array(array('password'), 6);
    public static $check_required_text = 'Required Fields Missing!';
    public static $check_min_length_text = 'Password length should be greater than 5!';
}

/*
 *  AJAX validations for correct username / password
 */
if($type == 'login') {
    /*
     * Checking for required fields
     */
    $missing_vals = Validator::validate_data('login');


   if (!empty( $missing_vals )) {
        $missing_data = explode('@@@', $missing_vals);
        $require_data = Login::${$missing_data[1]};
        echo '<div style="color:red;"><b>'.$require_data.'</b></div>';
        exit;
    }

    if($app->login(trim($pval['username']), $pval['password']) != true) {

        echo '<div style="color:red;"><b>' . $_SESSION['notice'] . '</b></div>';
        exit;
    }

//echo 'test';

	$_SESSION['msg_inbox'] = 'You have unread message(s)';
    //else{
            $app->access_table = USERS;
			$timezone = "Asia/Calcutta";
			if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
            $val['last_login'] = date("Y-m-d H:i:s");
            //echo $_SESSION['session_userid'];
            $app->save($val, array('id = ' => $_SESSION['session_userid']));
        //  exit;
    //}
}

if($app->is_logged_in() !== true) {
    include ADMIN_LAYOUT_PATH . 'index.html';

    /*
       FOR UNDER CONSTRUCTION TEMPLATE
    include ADMIN_LAYOUT_PATH . 'under_construction.html';

    */
    die();
}
