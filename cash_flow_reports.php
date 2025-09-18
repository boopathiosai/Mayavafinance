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
$page_title = 'Cash Flow Transaction Report';
$module_name = 'cash_flow_reports';

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

$search_trans_key = (isset($_GET['search_trans_key'])) ? $_GET['search_trans_key'] : $_POST['search_trans_key'];
$search_trans_type = (isset($_GET['search_trans_type'])) ? $_GET['search_trans_type'] : $_POST['search_trans_type'];
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
if($search_trans_key != ""){
    $search_args.="search_trans_key=".$search_trans_key."&";
    $heading_suffix .= " for the keyword ".$search_trans_key;
}
if($search_trans_type != ""){
    $search_args.="search_trans_type=".$search_trans_type."&";
    $heading_suffix .= " for the type ".$search_trans_type;
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

$app->access_table = CASH_FLOW_TRANSACTIONS;

/*
 * Assign limit to display records
 */
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);


 //echo $search_branch_id.", ".$search_sdate.", ".$search_edate.", ".$search_trans_type.", ".$search_trans_key;

switch($type) {

     
   case 'export_xls':
            // Added By DSSK 05_MAR_2018
       if(($search_sdate=='')&&($search_edate==''))
          {
            //echo "prin";
            //exit;
            $type_of_page =1;
          }
          else{
            //echo "dfgdf";exit;
            $type_of_page = "export_xls";
          }
        // END 
   
        //Getting Report Data
        $data = CashFlowReports::Listexport($search_branch_id, $search_sdate, $search_edate, $search_trans_type, $search_trans_key,$type_of_page);

        //XLS Data Adding or Modification according to the requirement of XLS Object class function
        if(count($data)>0){
		
			
        foreach($data as $row){
            
            /*$row['opening_balance'] = convertToCurrency($row['opening_balance']);
            $row['closing_balance'] = convertToCurrency($row['closing_balance']);
            
            $row['cash_in'] = convertToCurrency($row['cash_in']);
            $row['cash_out'] = convertToCurrency($row['cash_out']);*/
			
            $xls_row['trans_date'] = $row['trans_date'];
			$xls_row['branch_name'] = $row['branch_name'];
			$auction_pr = '';
			
			//assigning loan id in cash purpose
			if($row['module_type'] == 2 && $row['loan_id'] =='') {
				$purpose_id = $row['purpose_id'];
				$cf_id_query = "SELECT GROUP_CONCAT(P.loan_id ORDER BY P.id ASC SEPARATOR ' - ') as loan_id FROM ".PLEDGE." AS P INNER JOIN ".REDEMPTION." AS R ON P.id=R.loan_id  WHERE  R.cash_flow_id IN (".$purpose_id.')';
				$cf_data = $app->getexport($cf_id_query);
				$row['loan_id'] = $cf_data[0]['loan_id'];
			}
			
			if($row['profit_status'] == 1 && $row['cash_purpose'] == 'Auction') 
				$auction_pr = ' - Profit';
			elseif($row['cash_purpose'] == 'Auction')
				$auction_pr = ' - Loss';

			if($row['module_type'] == 1 || $row['module_type'] == 2) 
				$row['cash_purpose'] = $row['cash_purpose']."(Loan ID :".$row['loan_id'].")";
		
			$xls_row['cash_purpose'] = $row['cash_purpose'].$auction_pr;
			$xls_row['amount'] = $row['loan_amount'] ? $row['loan_amount'] : 0;
			$xls_row['cash_in'] = $row['cash_in'] ? $row['cash_in'] : 0;
			$xls_row['cash_out'] = $row['cash_out'] ? $row['cash_out'] : 0;
			$xls_row['opening_balance'] = $row['opening_balance'] ? $row['opening_balance'] : 0;
			$xls_row['closing_balance'] = $row['closing_balance'] ? $row['closing_balance'] : 0;
            $xls_data[] = $xls_row;
        }
        }
		
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
     // Added By DSSK 05_MAR_2018
       if(($search_sdate=='')&&($search_edate=='')&&($search_sub_branch_id==''))
          {
            //echo "prin";
            //exit;
            $type_of_page =1;
          }
          else{
            //echo "dfgdf";exit;
            $type_of_page = "export_xls";
          }
        // END 
        
        //Getting Report Data
        $data = CashFlowReports::Listexport($search_branch_id, $search_sdate, $search_edate, $search_trans_type, $search_trans_key,$type_of_page);;
        
        //PDF Data Adding or Modification according to the requirement of Pdf Object class function
        if(count($data)>0){
			//echo '<pre>';
			//print_r($data);
			//exit;
        foreach($data as $row){
            
            
            /*$row['opening_balance'] = convertToCurrency($row['opening_balance']);
            $row['closing_balance'] = convertToCurrency($row['closing_balance']);
            
            $row['cash_in'] = convertToCurrency($row['cash_in']);
            $row['cash_out'] = convertToCurrency($row['cash_out']);*/
            
            
            $pdf_row['trans_date'] = $row['trans_date'];
			$pdf_row['branch_name'] = $row['branch_name'];
			$auction_pr = '';
			
			//assigning loan id in cash purpose
			if($row['module_type'] == 2 && $row['loan_id'] =='') {
				$purpose_id = $row['purpose_id'];
				$cf_id_query = "SELECT P.loan_id FROM ".PLEDGE." AS P INNER JOIN ".REDEMPTION." AS R ON P.id=R.loan_id  WHERE R.cash_flow_id IN (".$purpose_id.")";
				$cf_data = $app->getexport($cf_id_query);
				$row['loan_id'] = $cf_data[0]['loan_id'];
			}
			
			if($row['profit_status'] == 1 && $row['cash_purpose'] == 'Auction') 
				$auction_pr = ' - Profit';
			elseif($row['cash_purpose'] == 'Auction')
				$auction_pr = ' - Loss';
			
			if($row['module_type'] == 1 || $row['module_type'] == 2) 
				$row['cash_purpose'] = $row['cash_purpose']."(Loan ID :".$row['loan_id'].")";
		
			$pdf_row['cash_purpose'] = $row['cash_purpose'].$auction_pr;
			$pdf_row['amount'] = $row['loan_amount'] ? $row['loan_amount'] : 0;
			$pdf_row['cash_in'] = $row['cash_in'] ? $row['cash_in'] : 0;
			$pdf_row['cash_out'] = $row['cash_out'] ? $row['cash_out'] : 0;
			$pdf_row['opening_balance'] = $row['opening_balance'] ? $row['opening_balance'] : 0;
			$pdf_row['closing_balance'] = $row['closing_balance'] ? $row['closing_balance'] : 0;
            $pdf_data[] = $pdf_row;
        }}

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
            $pdf->headerTitle = $page_title . " - ".$heading_suffix." - Date:".date("d-m-Y h:i:sa");
        
        //Report Column Width
        $width = array(40, 60, 120, 40, 40, 40,40,40);
        
        //Display Type 1- basic 2-improved 3-fancy
        //Page Size String Like 'A0', 'A10' || Array array(width,height)
        $pdf->exportData($pdf_data, $width, 7, 'fancy', array(450,300));
        
        //Export the pdf report
        $pdf->Output($file_name,'D');
        
        exit;
        
        break;
        
    case 'paging':
    case 'search':
        /*
         * Make the pages with header and footer
         */     
        
         // Added by DSSK 05-MAR-2018
          if($type=='search')
         {
          $type_of_page="search";
          if($search_sdate== "" && $search_edate == ""){
              $type_of_page=2;
          }
         }else if($type=='paging'){                              
             $type_of_page = "";
         }else{
           $type_of_page=1;
         }
   // END
        $data = CashFlowReports::Listview($search_branch_id, $search_sdate, $search_edate, $search_trans_type, $search_trans_key,$type_of_page);;
        include ADMIN_LAYOUT_PATH . $module_name.'.html';
        
        break;
        
    case 'sublist':
     /*
      * Make the pages with header and footer
      */     
     $data = CashFlowReports::subListview($search_branch_id, $search_sdate, $search_edate, $search_trans_type, $search_trans_key);;
     include ADMIN_LAYOUT_PATH . 'elements/sublist_'.$module_name.'.html';

    break;
    
    case 'preview':
    case 'view':
    case 'print_preview':

        //Getting Report Data
        $stock_report = CashFlowReports::Listexport($search_branch_id, $search_sdate, $search_edate, $search_trans_type, $search_trans_key);;
        include ADMIN_LAYOUT_PATH . 'forms/'. $module_name . '.html';
        
        break;
        
    default:
        /*
         * Make the pages with header and footer
         */   
        $type_of_page=1; // Added DSSK 05_MAR_2018
        //$data = CashFlowReports::Listview($search_branch_id, $search_sdate, $search_edate, $search_trans_type, $search_trans_key,$type_of_page);;
        
        //if($data)
        
        include ADMIN_LAYOUT_PATH . 'common/header.html'; 
        include ADMIN_LAYOUT_PATH . $module_name.'.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

