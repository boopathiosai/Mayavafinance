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
error_reporting();
define ('ROOT_PATH', '');
$page_title = 'Summary Reports';
$module_name = 'summary_reports';

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
 

// This code is to block the page which isonly accessiple by Super Admin
if($_SESSION['session_usergroupname'] != 'Super Admin'){

    $_SESSION['success_msg'] = NO_PRIVILEDGE;
    header('Location: index.php');
    exit;
}

/*
 * Get values from js
 */
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];

$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];


 

/*
 * Get search options
 */

$search_wise = (isset($_GET['search_wise'])) ? $_GET['search_wise'] : $_POST['search_wise'];

$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_sdate = (isset($_GET['search_sdate'])) ? $_GET['search_sdate'] : $_POST['search_sdate'];
$search_edate = (isset($_GET['search_edate'])) ? $_GET['search_edate'] : $_POST['search_edate'];


$search_args = "";
$heading_suffix = "";


$search_wise = (trim($search_wise) != '') ? $search_wise :'items';

if($search_wise != ""){
    $search_args.="search_wise=".$search_wise."&";
    //$heading_suffix .= " - ".ucwords(preg_replace("/_/"," ",$search_wise));
    $page_title .= " - ".ucwords(preg_replace("/_/"," ",$search_wise));
}
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


//$page_title .= ($heading_suffix != "") ? " - ".$heading_suffix : "";


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


//echo "$search_wise, $search_branch_id, $search_sdate, $search_edate";

 

switch($type) {

     
   case 'export_xls':
   
        //Getting Report Data
        $data = SummaryReports::Listexport($search_wise, $search_branch_id, $search_sdate, $search_edate);
        $expot_data = SummaryReports::getExportData($search_wise, $data);

        //XLS Data Adding or Modification according to the requirement of Excel Object class function
        $xls_data = $expot_data["data"];


        require_once("classes/excel.class.php");    
        
        //File NAme Setting
        $prifix = date("Ymdhis");
        $file_name = "tmp/".$module_name."_".$prifix.".xls";

        //Report Title
        if($heading_suffix == "")
            $title = $page_title." - Date:".date("d-m-Y h:i:sa");
        else
            $title = $page_title . " - ".$heading_suffix." - Date:".date("d-m-Y h:i:sa");
        
        //Excel Object
        $excel = new ExcelWriter($file_name, $title);
        

      
        //Export the XLS Report
        $excel->exportData($xls_data, $file_name);

         exit;
         break;
         
   case 'export_pdf':
   
        
        //Getting Report Data
        $data = SummaryReports::Listexport($search_wise, $search_branch_id, $search_sdate, $search_edate);
        $expot_data = SummaryReports::getExportData($search_wise, $data);

		$expot_data = array_reverse($expot_data);
        
        //PDF Data Adding or Modification according to the requirement of Pdf Object class function
        $pdf_data = $expot_data["data"];
        
        require_once("classes/pdf.class.php");    
        
        //File Name Setup
        $prifix = date("Ymdhis");
        $file_name = "tmp/".$module_name."_".$prifix.".pdf";
        
        //Pdf Object
        $pdf = new ExportPDF();
        
        //Report Title

        if($heading_suffix == "")
            $pdf->headerTitle = $page_title." - Date:".date("d-m-Y h:i:sa");
        else
            $pdf->headerTitle = $page_title . " - " . $heading_suffix." - Date:".date("d-m-Y h:i:sa");
        
        
        $width = $expot_data["width"];
        $pwidth = $expot_data["pwidth"];
        
        //Display Type 1- basic 2-improved 3-fancy
        //Page Size String Like 'A0', 'A10' || Array array(width,height)
        $pdf->exportData($pdf_data, $width, 8, 'fancy', array($pwidth,400));
        
        //Export the pdf report
        $pdf->Output($file_name,'D');
        
        exit;
        
        break;
        
        
    case 'paging':
        /*
         * Make the pages with header and footer
         */     
        $data = SummaryReports::Listview($search_wise , $search_branch_id, $search_sdate, $search_edate);
        include ADMIN_LAYOUT_PATH . $module_name.'_'.$search_wise .'.html';
        
        break;
        
    case 'sublist':
        /*
         * Make the pages with header and footer
         */     
        $data = SummaryReports::subListview($search_wise , $search_branch_id, $search_sdate, $search_edate);
        include ADMIN_LAYOUT_PATH . "elements/sublist_" .$module_name.'_'.$search_wise .'.html';
        
        break;
        
    case 'preview':
    case 'print_preview':

        //Getting Report Data
        $data = SummaryReports::Listexport($search_wise, $search_branch_id, $search_sdate, $search_edate);	
		if($search_wise == 'pay_interest') {
			$data_expo = SummaryReports::getExportData($search_wise, $data);
			$data = $data_expo['data'];
		} else {
			$data = array_reverse($data);
		}

        include ADMIN_LAYOUT_PATH . 'forms/'. $module_name.'_'.$search_wise .'.html';
        
        break;
    case 'search':
        /*
         * Make the pages without header and footer
         */     
        $data = SummaryReports::Listview($search_wise, $search_branch_id, $search_sdate, $search_edate);
        include ADMIN_LAYOUT_PATH . $module_name.'_'.$search_wise .'.html';
        break;
    
    default:
        /*
         * Make the pages with header and footer
         */  
        /*if($search_wise=="items"){
        
        $pledged_data = SummaryReports::Listview("pledged_item", $search_branch_id, $search_sdate, $search_edate);
        $released_data = SummaryReports::Listview("released_item", $search_branch_id, $search_sdate, $search_edate);
        
        $data = array_merge($pledged_data, $released_data);


        }
        elseif($search_wise=="cashflow"){
        
        $cash_in_data = SummaryReports::Listview("cash_in", $search_branch_id, $search_sdate, $search_edate);
        $cash_out_data = SummaryReports::Listview("cash_out", $search_branch_id, $search_sdate, $search_edate);
        
        $data1 = array_merge($cash_in_data, $cash_out_data);
        $data = array_merge_sum_by_date($cash_in_data, $cash_out_data);
        
        }
        else{
        
        $data = SummaryReports::Listview($search_wise, $search_branch_id, $search_sdate, $search_edate);
        
        }*/
        //echo "$search_wise, $search_branch_id, $search_sdate, $search_edate";
       //$data = SummaryReports::Listview($search_wise, $search_branch_id, $search_sdate, $search_edate);
               // p_arr($data);
       
        include ADMIN_LAYOUT_PATH . 'common/header.html'; 
        include ADMIN_LAYOUT_PATH . $module_name.'_'.$search_wise .'.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        
        break;
}

?>