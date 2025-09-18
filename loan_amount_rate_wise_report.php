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
$page_title = 'Loan Amount Rate Wise  Report';
$module_name = 'loan_amount_rate_wise_report';

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
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];
$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];



/*
 * Get search options
 */
$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_sdate = (isset($_GET['search_sdate'])) ? $_GET['search_sdate'] : $_POST['search_sdate'];
$search_edate = (isset($_GET['search_edate'])) ? $_GET['search_edate'] : $_POST['search_edate'];
//loan amount  
$search_sloanamt = (isset($_GET['search_sloanamt'])) ? $_GET['search_sloanamt'] : $_POST['search_sloanamt'];
$search_eloanamt = (isset($_GET['search_eloanamt'])) ? $_GET['search_eloanamt'] : $_POST['search_eloanamt'];

//loan rate
$search_sloanrate = (isset($_GET['search_sloanrate'])) ? $_GET['search_sloanrate'] : $_POST['search_sloanrate'];
$search_eloanrate = (isset($_GET['search_eloanrate'])) ? $_GET['search_eloanrate'] : $_POST['search_eloanrate'];

$search_customer_id = (isset($_GET['search_customer_id'])) ? $_GET['search_customer_id'] : $_POST['search_customer_id'];

$search_sub_branch_id = (isset($_GET['search_sub_branch_id'])) ? $_GET['search_sub_branch_id'] : $_POST['search_sub_branch_id'];
$search_sloancount = (isset($_GET['search_sloancount'])) ? $_GET['search_sloancount'] : $_POST['search_sloancount'];
$search_eloancount = (isset($_GET['search_eloancount'])) ? $_GET['search_eloancount'] : $_POST['search_eloancount'];

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


if($search_sloanamt != ""){
    $search_args.="search_sloanamt=".$search_sloanamt."&";
    $heading_suffix .= " Amount ".$search_sloanamt;
}
if($search_eloanamt != ""){
    $search_args.="search_eloanamt=".$search_eloanamt."&";
    $heading_suffix .= " - ".$search_eloanamt;
}

if($search_sloanrate != ""){
    $search_args.="search_sloanrate=".$search_sloanrate."&";
    $heading_suffix .= " Rate ".$search_sloanrate;
}
if($search_eloanrate != ""){
    $search_args.="search_eloanrate=".$search_eloanrate."&";
    $heading_suffix .= " - ".$search_eloanrate;
}
if ($search_sloancount != "") {
    $search_args .= "search_sloancount=" . $search_sloancount . "&";
    $heading_suffix .= " - " . $search_sloancount;
}
if ($search_eloancount != "") {
    $search_args .= "search_eloancount=" . $search_eloancount . "&";
    $heading_suffix .= " - " . $search_eloancount;
}

if($search_customer_id != ""){
    $search_args.="search_customer_id=".$search_customer_id."&";
    $heading_suffix .= " for the customer ".Customer::getCustomerName($search_customer_id);
}
if($search_branch_id != ""){
    $search_args.="search_branch_id=".$search_branch_id."&";
    $heading_suffix .= " for the branch ".Branch::getBranchName($search_branch_id);
}
if($search_sub_branch_id != ""){
	$search_args.="search_sub_branch_id=".$search_sub_branch_id."&";
    $heading_suffix .= " for the sub branch ".Subbranch::getSubBranchName($search_sub_branch_id);
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

//echo $type;
switch($type) {
   case 'paging':
   case 'search': 
   case 'refresh': 

        $data = LoanAmountRateWise::Listview($search_sdate, $search_edate, $search_sloanamt,$search_eloanamt,$search_sloanrate,$search_eloanrate,$search_customer_id,$search_branch_id,$search_sub_branch_id, $search_sloancount, $search_eloancount);
        $count = LoanAmountRateWise::ListviewCount($search_sdate, $search_edate, $search_sloanamt,$search_eloanamt,$search_sloanrate,$search_eloanrate,$search_customer_id,$search_branch_id,$search_sub_branch_id, $search_sloancount, $search_eloancount);
        include ADMIN_LAYOUT_PATH . 'loan_amount_rate_wise_report.html';
        exit;
    break;


   case 'sublist':
         $data = LoanAmountRateWise::subListview($search_sdate, $search_edate, $search_sloanamt,$search_eloanamt,$search_sloanrate,$search_eloanrate,$search_customer_id,$search_branch_id,$search_sub_branch_id, $search_sloancount, $search_eloancount);
//         $data = LoanAmountRateWise::subListview($search_sdate, $search_edate, $search_sloanamt,$search_eloanamt,$search_sloanrate,$search_eloanrate,$search_customer_id,$search_sub_branch_id);
         include ADMIN_LAYOUT_PATH . 'elements/sublist_'.$module_name.'.html';
         exit;
         break;

    case "export_xls":
        
        //Getting Report Data
        $data = LoanAmountRateWise::Listexport($search_sdate, $search_edate, $search_sloanamt,$search_eloanamt,$search_sloanrate,$search_eloanrate,$search_customer_id,$search_branch_id,$search_sub_branch_id, $search_sloancount, $search_eloancount);
 
        //XLS Data Adding or Modification according to the requirement of excel Object class function
        if(count($data)>0){
        foreach($data as $row){
                 $weight_in = floatval($row['pledged_weight']);
                 $weight_in_gs =  intval($weight_in);
                 $weight_in_mgs =  intval(floatval($weight_in - intval($weight_in)) * 1000);
                 $row['pledged_weight'] = $weight_in_gs. ' Gms '. $weight_in_mgs . ' Mg';
				 $pledged_weight = $weight_in_gs.'.'.$weight_in_mgs;

				 if($weight_in_gs > 0 || $weight_in_mgs >0)
	                 $loan_rate  = $row['loan_amount']/$pledged_weight;

                 $row['loan_amount']  = convertToCurrency($row['loan_amount']);
                 //$row['loan_rate']  = convertToCurrency($row['loan_rate']);
                $row['loan_rate'] = convertToCurrency($loan_rate);

				$xls_row['loan_date'] = $row['loan_date'];
				$xls_row['loan_id'] = $row['loan_id'];
				$xls_row['customer_id'] = $row['customer_id'];
				$xls_row['customer_name'] = $row['customer_name'];
				$xls_row['total_weight'] = $row['pledged_weight'];
				$xls_row['loan_amount'] = $row['loan_amount'];
                //$xls_row['scheme_name'] = $row['scheme_name'];
                $xls_row['no_of_loans'] = $row['no_of_loans'];
				$xls_row['loan_rate'] = $row['loan_rate'];
                 
                $xls_data[] = $xls_row;
        } }

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


    case "export_pdf":
        
        //Getting Report Data
      $data = LoanAmountRateWise::Listexport($search_sdate, $search_edate, $search_sloanamt,$search_eloanamt,$search_sloanrate,$search_eloanrate,$search_customer_id,$search_branch_id,$search_sub_branch_id, $search_sloancount, $search_eloancount);

        //PDF Data Adding or Modification according to the requirement of Pdf Object class function
        if(count($data)>0){
            // echo "<pre>";
            // print_r($data);
        foreach($data as $row){
         
                $weight_in = floatval($row['pledged_weight']);
                $weight_in_gs =  intval($weight_in);
                $weight_in_mgs =  intval(floatval($weight_in - intval($weight_in)) * 1000);
                $row['pledged_weight'] = $weight_in_gs. ' Gms '. $weight_in_mgs . ' Mg';

				$pledged_weight = $weight_in_gs.'.'.$weight_in_mgs;
				if($weight_in_gs > 0 || $weight_in_mgs >0)
					$loan_rate  = $row['loan_amount']/$pledged_weight;

                $row['loan_amount']  = convertToCurrency($row['loan_amount']);
               // $row['loan_rate']  = convertToCurrency($row['loan_rate']);
               $row['loan_rate'] = convertToCurrency($loan_rate);

                $pdf_row['loan_date'] = $row['loan_date'];
				$pdf_row['loan_id'] = $row['loan_id'];
				$pdf_row['customer_id'] = $row['customer_id'];
				$pdf_row['customer_name'] = $row['customer_name'];
				$pdf_row['total_weight'] = $row['pledged_weight'];
				$pdf_row['loan_amount'] = $row['loan_amount'];
                $pdf_row['no_of_loans'] = $row['no_of_loans'];
			//	$pdf_row['scheme_name'] = $row['scheme_name'];
				$pdf_row['loan_rate'] = $row['loan_rate'];
                 
                $pdf_data[] = $pdf_row;
        } }

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
        
        //Report Column Width
        $width = array(30,30,30,50,50,50,60,60);
        
        //Display Type 1- basic 2-improved 3-fancy
        //Page Size String Like 'A0', 'A10' || Array array(width,height)
        $pdf->exportData($pdf_data, $width, 7, 'fancy', array(380,300));
        
        //Export the pdf report
        $pdf->Output($file_name,'D');
        
        exit;
       
    break;

    case 'preview':
    case 'view':
    case 'print_preview':

        //Getting Report Data
        $loan_amt_rate_report = LoanAmountRateWise::Listexport($search_sdate, $search_edate, $search_sloanamt,$search_eloanamt,$search_sloanrate,$search_eloanrate,$search_customer_id,$search_branch_id,$search_sub_branch_id, $search_sloancount, $search_eloancount);
        include ADMIN_LAYOUT_PATH . 'forms/'. $module_name . '.html';
        break;


    default:
        /*
         * Make the pages with header and footer
         */
        $data = LoanAmountRateWise::Listview($search_sdate, $search_edate, $search_sloanamt,$search_eloanamt,$search_sloanrate,$search_eloanrate,$search_customer_id,$search_branch_id);
        $count = LoanAmountRateWise::ListviewCount($search_sdate, $search_edate, $search_sloanamt,$search_eloanamt,$search_sloanrate,$search_eloanrate,$search_customer_id,$search_branch_id);

        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'loan_amount_rate_wise_report.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}