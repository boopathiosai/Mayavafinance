<?php session_start();
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Customer
// Programmer       :  Karthick B
//**************************************************************************
define ('ROOT_PATH', '');
$page_title = 'Ledger & Closing Report';
$module_name = 'closing_report_dev';

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = REPORTS_CAT_ID;

$menu = Privillege::getModuleDetail($_SESSION[$app->session_usertype1]);

if($app->is_logged_in() !== true) {
    if($mod == 'Ajax') {
        echo "session_timed_out";
        exit;
    } else {
        header('Location: index.php');
        exit;
    }
}

$pval = $_POST['pval'];
$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];

$search_sdate = (isset($_GET['search_sdate'])) ? $_GET['search_sdate'] : $_POST['search_sdate'];
$search_edate = (isset($_GET['search_edate'])) ? $_GET['search_edate'] : $_POST['search_edate'];
$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_customer_id = (isset($_GET['search_customer_id'])) ? $_GET['search_customer_id'] : $_POST['search_customer_id'];
$search_sub_branch_id = (isset($_GET['search_sub_branch_id'])) ? $_GET['search_sub_branch_id'] : $_POST['search_sub_branch_id'];

$search_args = "";
$heading_suffix = "";

if(!is_superadmin($_SESSION['session_usergroupname'])){
    $branch_name = $_SESSION['session_user_branch_name'];
}else{
    $branch_name = "";
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
if($search_customer_id != ""){
    $search_args.="search_customer_id=".$search_customer_id."&";
    $heading_suffix .= " for the customer ".Customer::getCustomerName($search_customer_id);
}
if($search_sub_branch_id != ""){
    $search_args.="search_sub_branch_id=".$search_sub_branch_id."&";
}

if($page_no <= 0) $page_no = 1;
if($rg <= 0) $rg = 1;
$page_no = (int)$page_no;

Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);

switch($type) {
   case 'paging':
   case 'back':
   case 'search':
        if($_POST['searchOption']!='null'){
            $search_sub_branch=explode(',',$_POST['searchOption']);
        }
        if($type=='search'){
            $type_of_page="search";
            if($search_sdate== "" && $search_edate == ""){
                $type_of_page=2;
            }
        }else if($type == 'paging'){
            $s_qry = $_SESSION['multiple_sub_branch_id'];
            $search_sub_branch = $s_qry; 
            $_POST['searchOption'] = $s_qry;
            $type_of_page = "";
        }else{
            $type_of_page=1;
        }
        $data = DailyReportDev::Listview($search_branch_id,$search_sdate,$search_edate,$search_sub_branch,$type_of_page);

        $_SESSION['sub_branch_id'] = $search_sub_branch_id;
        $_SESSION['multiple_sub_branch_id'] = $search_sub_branch;
        include ADMIN_LAYOUT_PATH . 'closing_report_dev.html';
        exit;
        break;

   case 'sublist':
        if(isset($_SESSION['multiple_sub_branch_id'])&&(!empty($_SESSION['multiple_sub_branch_id']))){
            $search_sub_branch_id = $_SESSION['multiple_sub_branch_id'];
        } else if(isset($_POST['multiple_search'])){
            $search_sub_branch_id = $_POST['multiple_search'];
        }
        $data = DailyReportDev::subListview($search_branch_id,$search_sdate,$search_edate,$search_sub_branch_id);
        include ADMIN_LAYOUT_PATH . 'elements/sublist_expenses_report.html';
        exit;
        break;

   case "export_xls":
        if(($search_sdate=='')&&($search_edate=='')&&($search_sub_branch_id=='')){
            $type_of_page =1;
        } else {
            $type_of_page = "export_xls";
        }
        $search_sub_branch_id = $_SESSION['multiple_sub_branch_id'];

        $initial=0;
        for ($i=0; $i < count($search_sub_branch_id) ; $i++) {
            if($initial==0){
                $heading_suffix_cur = " for the sub branch ".Subbranch::getSubBranchName($search_sub_branch_id[$i]);
                $initial ++;
            } else{
                $heading_suffix_cur .= " and ".Subbranch::getSubBranchName($search_sub_branch_id[$i]);
            }
        }

        $data = DailyReportDev::subListview($search_branch_id,$search_sdate,$search_edate,$search_sub_branch_id,$type_of_page);

        if(count($data)>0){
            foreach($data as $row){
                $row['particulars'] = $row['particulars'] == 'Pay interest' ? 'Interest' : $row['particulars'];
                if($row['particulars']==CASHFLOW_PURPOSE_OPENING || 
                   $row['particulars']==CASHFLOW_PURPOSE_CLOSING || 
                   $row['particulars']==CASHFLOW_PURPOSE_TOTAL){
                    $row['branch_name'] = "";
                }
                $row['cash_in'] = $row['cash_in'];
                $row['cash_out'] = $row['cash_out'];

                // ✅ Correct Closing Balance formula applied globally
                $row['closing_balance'] = ($row['opening_balance'] + $row['cash_in']) - $row['cash_out'];

                unset($row['id'], $row['report_date'], $row['branch_id'], $row['order_id'], $row['branch_name']);
                if(($row['cash_in'])||($row['cash_out'])!=0){
                    $xls_data[] = $row;
                }
            }
        }

        require_once("classes/excel.class.php");    
        $prifix = date("Ymdhis");
        $file_name = "tmp/".$module_name."_".$prifix.".xls";
        $excel = new ExcelWriter($file_name, $page_title);
        $excel->exportData($xls_data, $file_name);
        exit;
        break;

   case "export_pdf":
        $search_sub_branch_id = $_SESSION['multiple_sub_branch_id'];
        $data = DailyReportDev::subListview($search_branch_id,$search_sdate,$search_edate,$search_sub_branch_id);
        if(count($data)>0){
            foreach($data as $row){
                $row['particulars'] = $row['particulars'] == 'Pay interest' ? 'Interest' : $row['particulars'];
                if($row['particulars']==CASHFLOW_PURPOSE_OPENING || 
                   $row['particulars']==CASHFLOW_PURPOSE_CLOSING || 
                   $row['particulars']==CASHFLOW_PURPOSE_TOTAL){
                    $row['branch_name'] = "";
                }
                $row['cash_in'] = $row['cash_in'];
                $row['cash_out'] = $row['cash_out'];

                // ✅ Apply Correct Closing Balance formula
                $row['closing_balance'] = ($row['opening_balance'] + $row['cash_in']) - $row['cash_out'];

                unset($row['id'], $row['report_date'], $row['branch_id'], $row['order_id'], $row['branch_name']);
                if((($row['cash_in'])||($row['cash_out']))!=0){
                    $pdf_data[] = $row;
                }
            }
        }
        require_once("classes/pdf.class.php");    
        $prifix = date("Ymdhis");
        $file_name = "tmp/".$module_name."_".$prifix.".pdf";
        $pdf = new ExportPDF();
        $pdf->headerTitle = $page_title." - Date:".date("d-m-Y h:i:sa");
        $width = array(85,120,60,60);
        $pdf->exportData($pdf_data, $width, 7, 'fancy', array(300,400));
        $pdf->Output($file_name,'D');
        exit;
        break;

   case 'preview':
   case 'view':
   case 'print_preview':
        $search_sub_branch_id = $_SESSION['multiple_sub_branch_id'];
        $daily_report = DailyReportDev::subListview($search_branch_id,$search_sdate,$search_edate,$search_sub_branch_id);
        include ADMIN_LAYOUT_PATH . 'forms/'. $module_name . '.html';
        break;

   default:
        $page_type=1;
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'closing_report_dev.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';   
        break;
}
