<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE                   
// Module           :  Brand          
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Vinothkuamr V
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  05-Feb-2010
//**************************************************************************
// Description      :  This is the Brand php file  for Brand module,used to process the view,add,edit and delete in Brand module.
//**************************************************************************
 
session_start();

define ('ROOT_PATH', '');
$page_title = 'Dash Board';
$module_name = 'dashboard';
//print_r($_SESSION);

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = DASHBOARD_CAT_ID;

$menu = Privillege::getModuleDetail($_SESSION[$app->session_usertype1]); 

/*
 * Redirect to index page if not logged in
 */
 if($app->is_logged_in() !== true) {

    if($mod == 'Ajax') {
        echo "session_timed_out";
        exit;
    } else {
        header('Location: index.php');
        exit;
    }   
}  

/*
 * Get values from js
 */
$pval = $_POST['pval']; 
$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];

 
if($page_no <= 0) {
    $page_no = 1;
}
if($rg <= 0) {
    $rg = 1;
}
$page_no = (int)$page_no;  

/*
 * Class Instances
 */

//$app->access_table = DASHBOARD;

/*
 * Assign limit to display records
 */
//Paging::set_per_page(ROWS_PER_PAGE);
//Paging::set_current_page($page_no);

//$textbox1 = $_POST['textbox1'];
/*$textbox1 = (isset($_GET['textbox1'])) ? $_GET['textbox1'] : $_POST['textbox1'];
$search_condition .= ($textbox1 != "")? "AND (CFT.transaction_type = '".$textbox1."')  ":"";
$app = $GLOBALS['app'];
$app->access_table = customers;
$query = "SELECT DISTINCT CFT.name FROM
".customers." AS CFT
WHERE  ".$search_condition."";
$cash_type_result = $app->getrows($query);*/

if($_POST['textbox1'] !=''){
    $valueToSearch = $_POST['textbox1'];
    $app = $GLOBALS['app'];
    $query = "SELECT id,name,contact_no,email FROM ".CUSTOMERS." WHERE customer_id LIKE '".$valueToSearch."'";
    $cash_type_result = $app->getrows($query);
    echo json_encode($cash_type_result);
    exit;
}


if ($_POST['customer_code'] && $_POST['customer_mobile'] ) {
    $contact_no = $_POST['customer_mobile'];
    $customer_code = $_POST['customer_code'];
    $app = $GLOBALS['app'];
    $app->access_table = CUSTOMERS;
    $query = "UPDATE ".CUSTOMERS." SET contact_no = '".$contact_no."' WHERE customer_id='".$customer_code."'";
    $app->execute($query);
   // exit();
}













switch($type) {   
        
    default:
        /*
         * Make the pages with header and footer
         */             
        
        $scheme_data = Scheme::Listview(); 
        $rate_data = Settings::CategoryDetail(); 
        $msg_data = Message::AnnouncementDetail();
		$msg_count = Message::ListviewCount();
        
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        //$data = DashBoard::Listview();
        //$count = DashBoard::ListviewCount(); 
        include ADMIN_LAYOUT_PATH . 'customers_details.html';
        //include ADMIN_LAYOUT_PATH . 'common/footer.html';       
        break;
}

?>