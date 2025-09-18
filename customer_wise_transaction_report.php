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
$page_title = 'Customer Wise Transaction Report';
$module_name = 'customer_wise_transaction_report';

include ROOT_PATH . 'includes/config.php';


$_SESSION["mcatid"] = REPORTS_CAT_ID;



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
/*
 * Get search options
 */

$search_customer_id = (isset($_GET['search_customer_id'])) ? $_GET['search_customer_id'] : $_POST['search_customer_id'];
$search_sdate = (isset($_GET['search_sdate'])) ? $_GET['search_sdate'] : $_POST['search_sdate'];
$search_edate = (isset($_GET['search_edate'])) ? $_GET['search_edate'] : $_POST['search_edate'];

$search_args = "";

if($search_sdate != ""){
    $search_args.="search_sdate=".$search_sdate."&";
    $heading_suffix .= " from ".date("d/M/Y",strtotime($search_sdate));
}
if($search_edate != ""){
    $search_args.="search_edate=".$search_edate."&";
    $heading_suffix .= " till ".date("d/M/Y",strtotime($search_edate));
}
if($search_customer_id != ""){
    $search_args.="search_customer_id=".$search_customer_id."&";
    $heading_suffix .= " for the customer ".Customer::getCustomerName($search_customer_id);
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
   case 'search':

         $data = CustomerWiseTransactionReport::Listview($search_customer_id, $search_sdate, $search_edate);

         include ADMIN_LAYOUT_PATH . 'customer_wise_transaction_report.html';
         exit;
         break;

   case 'sublist':

         $data = CustomerWiseTransactionReport::subListview($search_customer_id, $search_sdate, $search_edate);

         include ADMIN_LAYOUT_PATH . 'elements/sublist_customer_wise_transaction_report.html';
         exit;
         break;
         
   case "export_xls":

       //Getting Report Data
        $data = CustomerWiseTransactionReport::Listexport($search_customer_id, $search_sdate, $search_edate);

        //XLS Data Adding or Modification according to the requirement of excel Object class function
        if(count($data)>0){
        foreach($data as $row){
            
            
            $row['pleged_amount'] = convertToCurrency($row['pleged_amount']);
            $row['interest_amount'] = convertToCurrency($row['interest_amount']);
            $row['renewal_amount'] = convertToCurrency($row['renewal_amount']);
            $row['redeam_amount'] = convertToCurrency($row['redeam_amount']);
            $row['partial_amount'] = convertToCurrency($row['partial_amount']);
            $row['excess_amount'] = convertToCurrency($row['excess_amount']);
            $row['excess_interest'] = convertToCurrency($row['excess_interest']);
            $row['auction_amount'] = convertToCurrency($row['auction_amount']);
            $row['auction_lose'] = convertToCurrency($row['auction_lose']);
            
            
            
            
            unset($row['customer_id']);
            unset($row['loan_id']);
            unset($row['customer_no']);
            unset($row['cf_ids']);

            $xls_data[] = $row;
        } }

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
        $excel->exportData($xls_data, $file_name);
        exit;
    
    
    case "export_pdf":
        
       //Getting Report Data
        $data = CustomerWiseTransactionReport::Listexport($search_customer_id, $search_sdate, $search_edate);
        
        //PDF Data Adding or Modification according to the requirement of Pdf Object class function
        if(count($data)>0){
        foreach($data as $row){
            
            
            $row['pleged_amount'] = convertToCurrency($row['pleged_amount']);
            $row['interest_amount'] = convertToCurrency($row['interest_amount']);
            $row['renewal_amount'] = convertToCurrency($row['renewal_amount']);
            $row['redeam_amount'] = convertToCurrency($row['redeam_amount']);
            $row['partial_amount'] = convertToCurrency($row['partial_amount']);
            $row['excess_amount'] = convertToCurrency($row['excess_amount']);
            $row['excess_interest'] = convertToCurrency($row['excess_interest']);
            $row['auction_amount'] = convertToCurrency($row['auction_amount']);
            $row['auction_lose'] = convertToCurrency($row['auction_lose']);
            
            
            
            unset($row['branch_id']);
            unset($row['customer_id']);
            unset($row['loan_id']);
            unset($row['customer_no']);
            unset($row['cf_ids']);

            $pdf_data[] = $row;
        } }

        
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
        $width = array(30,20,150,150,80,40,40,40,40,40,40,40,40,40,40);
        
        //Display Type 1- basic 2-improved 3-fancy
        //Page Size String Like 'A0', 'A10' || Array array(width,height)
        $pdf->exportData($pdf_data, $width, 10, 'fancy', array(850,600));
        
        //Export the pdf report
        $pdf->Output($file_name,'D');
        exit;
       
     break;
     
    case 'preview':
    case 'view':
    case 'print_preview':

        //Getting Report Data
        
        $data = CustomerWiseTransactionReport::Listexport($search_customer_id, $search_sdate, $search_edate);
        include ADMIN_LAYOUT_PATH . 'forms/'. $module_name . '.html';
        
        break;

    default:
        /*
         * Make the pages with header and footer
         */
        $data = CustomerWiseTransactionReport::Listview($search_customer_id, $search_sdate, $search_edate);
        $count = CustomerWiseTransactionReport::ListviewCount($search_customer_id, $search_sdate, $search_edate);

        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'customer_wise_transaction_report.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}