<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Customer
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Vinothkuamr V
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  05-Feb-2010
//**************************************************************************
// Description      :  This is the Customer php file  for Customer module,used to process the view,add,edit and delete in Customer module.
//**************************************************************************

session_start();

define ('ROOT_PATH', '');
$page_title = 'Customer Slip';
$module_name = 'customerslip';

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = TRANSACTIONS_CAT_ID;

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
 * Redirect to index page if user is not allowed to access the page
 */
if(!is_accessible($menu))
{
	    //echo"notaccessible";
		header('Location: index.php');
		exit;
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


/*
 * Get search options
 */
$search_key = (isset($_GET['search_key'])) ? $_GET['search_key'] : $_POST['search_key'];
$search_cus_id = (isset($_GET['search_cus_id'])) ? $_GET['search_cus_id'] : $_POST['search_cus_id'];

$search_args = "";
if($search_key != ""){
	$search_args.="search_key=".$search_key."&";
}
if($search_cus_id != ""){
	$search_args.="search_cus_id=".$search_cus_id."&";
}


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

$app->access_table = PLEDGE;

/*
 * Assign limit to display records
 */
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);


switch($type) {

   #case 'formvalidation':

            /*
			 * Form validation here . notify error, if exists.
   		     */
			/*$missing_vals = Validator::validate_data('Customer');
			if (!empty( $missing_vals )) {
			$missing_data = explode('@@@', $missing_vals);
			$require_data = Customer::${$missing_data[1]};
			echo $require_data.'~~' . $missing_data[0] . '~~0';
			exit;
	    	}
			echo '~~~~1';
			exit;
            break;*/

   case 'add':
   case 'update':

		/*if($type == 'add') {
			$pval['created_datetime'] = date('Y-m-d H:i:s');
			$pval['created_by'] = $_SESSION['session_userid'];
			$app->add($pval);
			$uval['id'] = $app->last_inserted_id();

			// rename the picture using user-id
			rename(CUSTOMER_IMAGE_PATH.'temp.jpg', CUSTOMER_IMAGE_PATH.$uval['id'].'.jpg');

			//update the database with the new details
			$pval['photo'] = CUSTOMER_IMAGE_PATH.$uval['id'].'.jpg';
			$pval['modified_datetime'] = date("Y-m-d H:i:s");
			$pval['modified_by'] = $_SESSION['session_userid'];
			$app->save($pval, array('id = ' => $uval['id']));

		} else {
			$pval['modified_datetime'] = date("Y-m-d H:i:s");
			$pval['modified_by'] = $_SESSION['session_userid'];
			$app->save($pval, array('id = ' => $uval['id']));

		}*/

		if($type == 'add') {
		echo "<meta http-equiv='refresh' content='0'/>";
		#header("Location: ".ADMIN_LAYOUT_PATH . 'forms/customerslip.html');
		#header("Location: customers.php");
		}else{
		echo "<meta http-equiv='refresh' content='0'/>";
		#header("Location: ".ADMIN_LAYOUT_PATH . 'forms/customerslip.html');
		#header("Location: customers.php?".$search_args."pageno=".$page_no."");
		}
		$_SESSION['flash_msg'] = CUSTOMER_UPDATE;
		exit;
		break;

   case 'create':
   case 'edit':
   		/*
   		 * Get the Customer details, if type is edit
   		 */
   		$customer_data = ProductPledge::Detail($uval['id']);
        
   		$customer_data = $customer_data[0];
		include ADMIN_LAYOUT_PATH . 'forms/customerslip.html';
   		#include ADMIN_LAYOUT_PATH . 'forms/customer.html';
		break;

   case 'paging':
   case 'back':

         $data = ProductPledge::Listview($search_key, $search_cus_id);
         $count = ProductPledge::ListviewCount($search_key, $search_cus_id);

		 include ADMIN_LAYOUT_PATH . 'customerslip.html';
  		 #include ADMIN_LAYOUT_PATH . 'customer.html';
         exit;
         break;

   case 'delete':
   		/*
   		 * Delete from database
   		 */
   		$app->delete($uval);
        $notice = PRODUCT_PLEDGE_DELETE;
		$app->access_table = PRODUCTS;
   		$data = ProductPledge::Listview($search_key,$search_category);
		$count = ProductPledge::ListviewCount($search_key,$search_category);
		$_SESSION['success_msg'] = $notice;
   		include ADMIN_LAYOUT_PATH . 'forms/customerslip.html';
   		exit;
		break;
        

   case 'change_status':
  		/*
  		 * Change the stauts in database
  		 */
  		  $app->save($change_status_array, array('id = ' => $uval['id']));
          $notice = STATUS_SUCCESS;
          echo $change_status_array['status'].'~~'.$notice;
          exit;
        break;

    default:
		/*
		 * Make the pages with header and footer
		 */
		$data = ProductPledge::Listview($search_key, $search_cus_id);
		$count = ProductPledge::ListviewCount($search_key, $search_cus_id);
        //echo "<pre>";
       // print_r($data);
        
        include ADMIN_LAYOUT_PATH . 'common/header.html';
		include ADMIN_LAYOUT_PATH . 'customerslip.html';
		#include ADMIN_LAYOUT_PATH . 'forms/customerslip.html';
		include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>