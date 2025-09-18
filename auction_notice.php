<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Customer
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  --------------
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  05-Feb-2010
//**************************************************************************
// Description      :  This is the Customer php file  for Customer module,used to process the view,add,edit and delete in Customer module.
//**************************************************************************

session_start();

define ('ROOT_PATH', '');
$page_title = 'Auction Notice';
$module_name = 'auction_notice';

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = PRINT_CAT_ID;


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
$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$disp = (isset($_GET['disp'])) ? $_GET['disp'] : $_POST['disp'];

$disp = ($disp!='') ? $disp : 'content_div';

$pval = (isset($_GET['pval'])) ? $_GET['pval'] : $_POST['pval'];
$pp_pval = (isset($_GET['pp_pval'])) ? $_GET['pp_pval'] : $_POST['pp_pval'];

$serial_no = $_POST['serial_no'];
$serial_no = $pval['serial_no'];
$auction_charge = $_POST['auction_charge'];
$auction_amount = $_POST['auction_amount'];


$plist = (isset($_GET['plist'])) ? $_GET['plist'] : $_POST['plist'];

$name = $_POST['name'];
$gross_weight = $_POST['gross_weight'];
$category_id = $_POST['category_id'];
$net_weight = $_POST['net_weight'];
$market_rate = $_POST['market_rate'];
$product_id=$_POST["product_id"];
$product_name=$_POST["product_name"];
$product_with=$_POST["product_with"];
$product_without =$_POST["product_without"];
$item_count = $_POST['item_count'];


$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$uval['pa_id'] = (isset($_GET['pa_id'])) ? $_GET['pa_id'] : $_POST['pa_id'];

$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];


$uval['pledge_id'] = (isset($_GET['pledge_id'])) ? $_GET['pledge_id'] : $_POST['pledge_id'];

$loan_id = (isset($_GET['loan_id'])) ? $_GET['loan_id'] : $_POST['loan_id'];
$customer_id = (isset($_GET['customer_id'])) ? $_GET['customer_id'] : $_POST['customer_id'];
$branch_id = (isset($_GET['branch_id'])) ? $_GET['branch_id'] : $_POST['branch_id'];
$pledge_id = (isset($_GET['pledge_id'])) ? $_GET['pledge_id'] : $_POST['pledge_id'];


/*
 * Get search options
 */
$search_key = (isset($_GET['search_key'])) ? $_GET['search_key'] : $_POST['search_key'];
$search_cus_id = (isset($_GET['search_cus_id'])) ? $_GET['search_cus_id'] : $_POST['search_cus_id'];
$search_key_location = (isset($_GET['search_key_location'])) ? $_GET['search_key_location'] : $_POST['search_key_location'];
$search_fhw = (isset($_GET['search_fhw'])) ? $_GET['search_fhw'] : $_POST['search_fhw'];
$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_loan_id = (isset($_GET['search_loan_id'])) ? $_GET['search_loan_id'] : $_POST['search_loan_id'];

$search_sdate = (isset($_GET['search_sdate'])) ? $_GET['search_sdate'] : $_POST['search_sdate'];
$search_edate = (isset($_GET['search_edate'])) ? $_GET['search_edate'] : $_POST['search_edate'];

//customer id for view
$id = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];

//$search_date = NotificationLetter::getAuctionFilterDate();//getLastAuctionDate();//'2011-05-04';
//$search_sdate = ($search_sdate == '') ? $search_date : $search_sdate;
//$search_sdate = ($search_edate == '') ? date("Y-m-d") : $search_edate;


//echo "  ->".$search_date;


$search_args = "";
if($search_sdate != ""){
    $search_args.="search_sdate=".$search_sdate."&";
}

if($search_edate != ""){
    $search_args.="search_edate=".$search_edate."&";
}

if($search_key != ""){
    $search_args.="search_key=".$search_key."&";
}
if($search_cus_id != ""){
    $search_args.="search_cus_id=".$search_cus_id."&";
}
if($search_key_location != ""){
    $search_args.="search_key_location=".$search_key_location."&";
}
if($search_fhw != ""){
    $search_args.="search_fhw=".$search_fhw."&";
}
if($search_branch_id != ""){
    $search_args.="search_branch_id=".$search_branch_id."&";
}
if($search_loan_id != ""){
    $search_args.="search_loan_id=".$search_loan_id."&";
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



  case 'view':
  case 'preview':
  case 'edit':
       /*
        * Get the Customer details, if type is edit
        */
        //echo $uval['id'];
       $product_details = Auction_Notice::Detail($uval['id'],$_GET['customer_id'],$loan_id);
             $data = Auction_Notice::Listview($search_cus_id,$search_branch_id,$search_loan_id);
       $product_data = $product_details[0];
	   //echo "<pre>";
       //print_r($product_data);exit;
       
       /*$product_details = NotificationLetter::Detail($loan_id,$customer_id,$pledge_id, $branch_id);
       $data = NotificationLetter::Listview($search_cus_id,$search_branch_id,$search_loan_id);
       $product_data = $product_details;*/

       
       include ADMIN_LAYOUT_PATH . 'forms/auction_notice.html';

    break;
   case 'delete-confirm':
        /* * Get the Customer details, if type is edit*/

        $customer_data = Auction_Notice::Detail($uval['id'],$uval['pa_id']);
        //print_r($product_data); exit;
        $product_data = $customer_data;
        $customer_data = $customer_data[0];

        include ADMIN_LAYOUT_PATH . 'elements/delete_notification_letter.html';
        exit;
        break;


   case 'paging':
   case 'back':
   case 'search':
   case 'refresh':

         $data = Auction_Notice::Listview($search_sdate, $search_edate, $search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
         //$count = NotificationLetter::ListviewCount($search_sdate, $search_edate, $search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);

         include ADMIN_LAYOUT_PATH . 'auction_notice.html';
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
   case 'fetch_loan_details':
        /*
         * Fetch schama description and interst type
         */
         $app->access_table = PLEDGE;
         $loan_details = ProductPledge::Detail_Renewal($old_loan_id);
         echo $loan_details[0]['loan_amount']."~~".$loan_details[0]['pro_id']."~~".$loan_details[0]['description']."~~".$loan_details[0]['scheme_name'];
        exit;
        break;


    case 'view':
        /*
         * Make the pages with out header and footer
         */
        $data = Auction_Notice::Listview($search_sdate, $search_edate, $search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        //$count = Auction_Notice::ListviewCount($search_sdate, $search_edate, $search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        include ADMIN_LAYOUT_PATH . 'auction_notice.html';

        break;

    default:
    
        /*
         * Make the pages with header and footer
         */
        // $data = Auction_Notice::Listview($search_sdate, $search_edate, $search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        //$count = Auction_Notice::ListviewCount($search_sdate, $search_edate, $search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        $category_data = ProductCategory::getProductCategoryOptData();
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        if($uval['pledge_id']!=''){
            $product_data = Auction_Notice::ProductDetail($uval['pledge_id']);
            //$product_data = $product_data[0];
            //echo $uval['pledge_id'];
            //print_r(array_keys($product_data));
            include ADMIN_LAYOUT_PATH . 'forms/auction_notice.html';
        }else{
            include ADMIN_LAYOUT_PATH . 'auction_notice.html';
        }
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>