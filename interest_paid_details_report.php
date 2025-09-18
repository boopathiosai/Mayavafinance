<?php
session_start();
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Customer
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Karthick B
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  29-Oct-2011
//**************************************************************************
// Description      :  This is the Customer php file  for Customer module,used to process the view,add,edit and delete in Customer module.
//**************************************************************************
define ('ROOT_PATH', '');
$page_title = 'INTEREST PAID DETAILS REPORT';
$module_name = 'interest_paid_details_report';

include ROOT_PATH . 'includes/config.php';


$_SESSION["mcatid"] = REPORTS_CAT_ID;

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

$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_sdate = (isset($_GET['search_sdate'])) ? $_GET['search_sdate'] : $_POST['search_sdate'];
$search_edate = (isset($_GET['search_edate'])) ? $_GET['search_edate'] : $_POST['search_edate'];

$search_args = "";
$heading_suffix = "";


if($search_sdate != ""){
    $search_args.="search_sdate=".$search_sdate."&";
    $heading_suffix .= " from ".date("d/M/Y",strtotime($search_sdate));
}
if($search_edate != ""){
    $search_args.="search_edate=".$search_edate."&";
    $heading_suffix .= " till ".date("d/M/Y",strtotime($search_edate));
}
if($search_branch_id != ""){
    $search_args.="search_branch_id=".$search_branch_id."&";
    $heading_suffix .= " for the branch ".Branch::getBranchName($search_branch_id);
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



/*
 * Assign limit to display records
 */
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);


switch($type) {
   case 'paging':
   case 'back':

         $data = InterestPaidDetailsReport::Listview($search_branch_id,$search_sdate,$search_edate);

         $count = InterestPaidDetailsReport::ListviewCount($search_branch_id,$search_sdate,$search_edate);

  		 include ADMIN_LAYOUT_PATH . 'interest_paid_details_report.html';
         exit;
         break;

		case 'fetch_customer_details':
  		/*
  		 * Fetch schama description and interst type
  		 */
				$customer_details = Customer::Detail($customer_id);
			echo $customer_details[0]['name']."~~".$customer_details[0]['street']."~~".$customer_details[0]['location']."~~";
			echo $customer_details[0]['postal_code']."~~".$customer_details[0]['contact_no'];
			// echo $customer_details[0][1]."~~".$customer_details[0]['street']."~~".$customer_details[0]['location']."~~";
			//	echo $customer_details[0]['postal_code']."~~".$customer_details[0]['contact_no'];
        exit;
        break;


  case "export_xls":
 //Getting Report Data
        $data = InterestPaidDetailsReport::Listexport($s_branch,$s_sdate,$s_edate);
        require_once("classes/excel.class.php");    
       
        //File NAme Setting
        
		$prifix = date("Ymdhis");
        $file_name = "tmp/".$module_name."_".$prifix.".xls";

        //Report Title
        if($heading_suffix == "")
            $title = $page_title;
        else
            $title = $page_title . " - ".$heading_suffix;
        
        //Excel Object
        $excel = new ExcelWriter($file_name, $title);
        
        //Export the XLS Report
        $excel->exportData($data, $file_name);
         exit;
  break;

	case "export_pdf":
	     //Getting Report Data
        $data = InterestPaidDetailsReport::Listexport($search_branch_id,$search_sdate,$search_edate);
     require_once("classes/pdf.class.php");    
        
        //File Name Setup
        $prifix = date("Ymdhis");
        $file_name = "tmp/".$module_name."_".$prifix.".pdf";
        
        //Pdf Object
        $pdf = new ExportPDF();
        
        //Report Title

        if($heading_suffix == "")
            $pdf->headerTitle = $page_title;
        else
            $pdf->headerTitle = $page_title . " - " . $heading_suffix;
        
        //Report Column Width
        $width = array(25,25,50,40,70,60,40,40,40,50,50);
        
        //Display Type 1- basic 2-improved 3-fancy
        //Page Size String Like 'A0', 'A10' || Array array(width,height)
        $pdf->exportData($data, $width, 7, 'fancy', array(560,600));
        
        //Export the pdf report
        $pdf->Output($file_name,'D');
        
        exit;

		case 'preview':
    case 'view':
    case 'print_preview':

        //Getting Report Data
        $interest_paid_details_report = InterestPaidDetailsReport::Listexport($search_branch_id, $search_sdate, $search_edate);
        include ADMIN_LAYOUT_PATH . 'forms/'. $module_name . '.html';
        break;
 
    default:
		/*
		 * Make the pages with header and footer
		 */
		$data = InterestPaidDetailsReport::Listview($search_branch_id,$search_sdate,$search_edate);
		$count = InterestPaidDetailsReport::ListviewCount($search_branch_id,$search_sdate,$search_edate);
        include ADMIN_LAYOUT_PATH . 'common/header.html';
		include ADMIN_LAYOUT_PATH . 'interest_paid_details_report.html';
		include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}