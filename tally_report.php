<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE                   
// Module           :  Customer          
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Vinothkuamr V
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  13-AUG-2019
//**************************************************************************
// Description      :  This is the Customer php file  for Customer module,used to process the view,add,edit and delete in Customer module.
//**************************************************************************

session_start();

define ('ROOT_PATH', '');
$page_title = 'Tally Report';
$module_name = 'tally_report';

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
//echo "<pre>";  print_r($_POST);
$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_sub_branch_id = (isset($_GET['search_sub_branch_id'])) ? $_GET['search_sub_branch_id'] : $_POST['search_sub_branch_id'];
$search_sdate = (isset($_GET['search_sdate'])) ? $_GET['search_sdate'] : $_POST['search_sdate'];
$search_edate = (isset($_GET['search_edate'])) ? $_GET['search_edate'] : $_POST['search_edate'];
$search_type = (isset($_GET['search_type'])) ? $_GET['search_type'] : $_POST['search_type'];
$search_interest_type = (isset($_GET['search_interest_type'])) ? $_GET['search_interest_type'] : $_POST['search_interest_type'];

/*echo $search_branch_id."</br>";
echo $search_sub_branch_id."</br>";
echo $search_sdate."<br>";
echo $search_edate."</br>";
echo $search_type;

exit;*/

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

if($search_sub_branch_id != ""){
    $search_args.="search_sub_branch_id=".$search_sub_branch_id."&";
    $heading_suffix .= " for the branch ".Branch::getBranchName($search_branch_id);
}
if($search_type != ""){
    $search_args.="search_type=".$search_type."&";
    //$heading_suffix .= " for the branch ".Branch::getBranchName($search_branch_id);
}

if($search_interest_type != ""){
    $search_args.="search_interest_type=".$search_interest_type;
    //$heading_suffix .= " for the branch ".Branch::getBranchName($search_branch_id);
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




 

switch($type) {


    case 'sublist':
        /*
         * Make the pages with header and footer
         */     
        $data = TallyReport::subListview($search_branch_id,$search_user_id, $search_sdate, $search_edate);
        include ADMIN_LAYOUT_PATH . "elements/sublist_". $module_name.'.html';
        
        break;

     
   case 'export_xls':
   
        //Getting Report Data
        //$data = TallyReport::Listexport($search_branch_id,$search_user_id, $search_sdate, $search_edate);
        //$data = TallyReport::Listview($search_branch_id,$search_user_id, $search_sdate, $search_edate);
		$forxls = 'xls';
		$data = TallyReport::Listview($search_branch_id,$search_sub_branch_id, $search_sdate, $search_edate, $search_type,$forxls);
		// echo "<pre>";
		// print_r($data);
	
        //XLS Data Adding or Modification according to the requirement of excel Object class function
        if(count($data)>0){
			if($search_type == 2){
				foreach($data as $rows){
					
					foreach($rows as $key => $rowa){
						
						if($key == 'loan_id'){$from_loan_id = (int)filter_var($rowa, FILTER_SANITIZE_NUMBER_INT);}
						if($key == 'max_loanid'){$to_loan_id = (int)filter_var($rowa, FILTER_SANITIZE_NUMBER_INT);}				
						
						$narration = "Being cash paid to Jewell Mortgage loan(no:".$from_loan_id."-".$to_loan_id.")";
						if($key == 'loan_date'){
							//$date = date_create($rowa);
							$row['Date'] = date('d-m-Y', strtotime($rowa));
						}
						if($key == 'pay_no'){
							//$row['Payment No'] = $rowa;
							$row['Payment No'] = '';
						}
						if($key == 'cash'){
							$row['Bank/Cash'] = $rowa;
						}
						if($key == 'acc'){
							$row['Debit Account'] = $rowa;
						}
						if($key == 'totalAmount'){
							$row['Amount'] = $rowa;
						}
						if($key == 'chk_no'){
							$row['Cheque No'] = $rowa;
						}
						if($key == 'chk_date'){
							$row['Cheque Date'] = $rowa;
						}
						$row['Narration'] = $narration;
						if($key == 'sub_branch_code'){
							$row['Sub Branch Code'] = $rowa;
						}
						// $row[$key] = $rowa;
						if(is_numeric($key)){unset($row[$key]);}
					}

					$xls_data[] = $row;
				}
			}else if($search_type == 1){
				foreach($data as $rows){
					
					foreach($rows as $key => $rowa){
						if($key == 'loan_id'){$from_loan_id = (int)filter_var($rowa, FILTER_SANITIZE_NUMBER_INT);}
						if($key == 'max_loanid'){$to_loan_id = (int)filter_var($rowa, FILTER_SANITIZE_NUMBER_INT);}	
						if($key =='count'){
							$count = $rowa;
						}
						$credit_acc_p = "Jewell Mortgage Loan";
						$credit_amnt_narrat = "Being cash received from Jewell Mortgage loan(Total-$count)";
						$credit_acc_i = "Interest Income";
						$credit_acc_a = "Interest Income";
						$credit_interest_narrat = "Being Interest received from Jewell Mortgage loan";
						$credit_actual_interest_narrat = "Being Interest received from Jewell Mortgage loan";
						if($key =='principle_amount'){
							$principle_amount = $rowa;
						}
						if($key == 'interest_amount'){
							$interest_amount = $rowa;
						}
						$narration = "Being cash paid to Jewell Mortgage loan(no:".$from_loan_id."-".$to_loan_id.")";
						
						if($key == 'paid_on'){
							//$date = date_create($rowa);
							// $row['Date'] = date('d-m-Y', strtotime($rowa));
							$pdate = date('d-m-Y', strtotime($rowa));
						}
						if($key == 'pay_no'){
							// $row['Payment No'] = $rowa;
							//$payment_no = $rowa;
						}
						if($key == 'cash'){
							// $row['Bank/Cash'] = $rowa;
							$bank_cash = $rowa;
						}
						//$row['Debit Account'] = $credit_amnt;
						if($key == 'principle_amount'){
							// $row['Debit Account'] = $rowa;
							$p_amnt = $rowa;
							$p_acc = $credit_acc_p;
						}
						if($key == 'interest_amount'){
							// $row['Debit Account'] = $rowa;
							$i_amnt = $rowa;
							$i_acc = $credit_acc_i;
						}
						if($key == 'actual_interest_amount'){
							// $row['Debit Account'] = $rowa;
							$ai_amnt = $rowa;
							$ai_acc = $credit_acc_a;
						}
						if($key == 'totalAmount'){
							// $row['Amount'] = $rowa;
							$amount = $rowa;
						}
						if($key == 'chk_no'){
							// $row['Cheque No'] = $rowa;
							$chk_no = $rowa;
						}
						if($key == 'chk_date'){
							// $row['Cheque Date'] = $rowa;
							$chk_date = $rowa;
						}
						if($key == 'sub_branch_code'){
							$sub_branch_code = $rowa;
							// $row['Sub Branch Code'] = $rowa;
						}
						// $row['Narration'] = $credit_amnt_narrat;
						$narration = $credit_amnt_narrat;
						// $row[$key] = $rowa;
						$row['Date']  = $pdate;
						$row['Rec No'] = $payment_no;
						$row['Bank/Cash'] = $bank_cash;
						$row['Credit Account'] = $p_acc;
						$row['Amount'] = $p_amnt;
						$row['Cheque No'] = $chk_no;
						$row['Cheque Date'] = $chk_date;
						$row['Narration'] = $narration;
						$row['Sub Branch Code'] = $sub_branch_code;
						if(is_numeric($key)){unset($row[$key]);}
						
						$row1['Date']  = $pdate;
						$row1['Rec No'] = $payment_no;
						$row1['Bank/Cash'] = $bank_cash;
						$row1['Credit Account'] = $i_acc;
						$row1['Amount'] = $i_amnt;
						$row1['Cheque No'] = $chk_no;
						$row1['Cheque Date'] = $chk_date;
						$row1['Narration'] = $credit_interest_narrat;
						$row1['Sub Branch Code'] = $sub_branch_code;
						if(is_numeric($key)){unset($row1[$key]);}

						$row2['Date']  = $pdate;
						$row2['Rec No'] = $payment_no;
						$row2['Bank/Cash'] = $bank_cash;
						$row2['Credit Account'] = $ai_acc;
						$row2['Amount'] = $ai_amnt;
						$row2['Cheque No'] = $chk_no;
						$row2['Cheque Date'] = $chk_date;
						$row2['Narration'] = $credit_actual_interest_narrat;
						$row2['Sub Branch Code'] = $sub_branch_code;
						if(is_numeric($key)){unset($row2[$key]);}
						//
					}
					$xls_data[] = $row;
					if($search_interest_type == 1){
						$xls_data[] = $row1; // interest
					}
					if($search_interest_type == 2){
						$xls_data[] = $row2;	// actual interest
					}						

					
					
				}
			}
		}
		
        require_once("classes/excel.class.php");    
        
        //File NAme Setting
        $prifix = date("Ymdhis");
        $file_name = "tmp/".$module_name."_".$prifix.".xls";

        //Report Title
        if($heading_suffix == ""){
            //$title = $page_title." - Date:".date("d-m-Y h:i:sa");
        }else{
            //$title = $page_title . " - ".$heading_suffix." - Date:".date("d-m-Y h:i:sa");
		}
        
        //Excel Object
        $excel = new ExcelWriter($file_name, -1);
		
        //Export the XLS Report
        $excel->exportData($xls_data, $file_name);

         exit;
         break;
         
 /*  case 'export_pdf':
   
        
        //Getting Report Data
        $data = LogReport::Listexport($search_branch_id,$search_user_id, $search_sdate, $search_edate);
        //PDF Data Adding or Modification according to the requirement of Pdf Object class function
        if(count($data)>0){
        foreach($data as $row){
            
            unset($row['user_id']);
            unset($row['branch_id']);
            unset($row['report_date']);
            unset($row['group_date']);
            

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
            $pdf->headerTitle = $page_title." - Date:".date("d-m-Y h:i:sa");
        else
            $pdf->headerTitle = $page_title . " - " . $heading_suffix." - Date:".date("d-m-Y h:i:sa");
        
        //Report Column Width
        $width = array(60, 30, 50,50,50,100,40,60,60);
        
        //Display Type 1- basic 2-improved 3-fancy
        //Page Size String Like 'A0', 'A10' || Array array(width,height)
        $pdf->exportData($pdf_data, $width, 7, 'fancy', array(460,300));
        
        //Export the pdf report
        $pdf->Output($file_name,'D');
        
        exit;
        
        break;*/
        
    case 'paging':
    case 'search':
    case 'back':
    case 'refresh':
        /*
         * Make the pages with header and footer
         */     
        $data = TallyReport::Listview($search_branch_id,$search_sub_branch_id, $search_sdate, $search_edate, $search_type);

       /* echo '<pre>'; print_r($data); exit;*/
        include ADMIN_LAYOUT_PATH . $module_name.'.html';
        
        break;
        
    case 'preview':
    case 'view':
    case 'print_preview':

        //Getting Report Data
        $data = TallyReport::Listexport($search_branch_id,$search_user_id, $search_sdate, $search_edate);
        include ADMIN_LAYOUT_PATH . 'forms/'. $module_name . '.html';
        
        break;
        
    default:
        /*
         * Make the pages with header and footer
         */     
        //$data = TallyReport::Listview($search_branch_id,$search_sub_branch_id, $search_sdate, $search_edate, $search_type);
        
        //if($data)
        
        include ADMIN_LAYOUT_PATH . 'common/header.html'; 
            include ADMIN_LAYOUT_PATH . $module_name.'.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}
