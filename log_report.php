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
$page_title = 'Log Report';
$module_name = 'log_report';

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
$search_user_id = (isset($_GET['search_user_id'])) ? $_GET['search_user_id'] : $_POST['search_user_id'];
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
if($search_user_id != ""){
    $search_args.="search_user_id=".$search_user_id."&";
    $heading_suffix .= " and User ".User::getUserName($search_user_id);
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
        $data = LogReport::subListview($search_branch_id,$search_user_id, $search_sdate, $search_edate);
        include ADMIN_LAYOUT_PATH . "elements/sublist_". $module_name.'.html';
        
        break;

     
   case 'export_xls':
   
        //Getting Report Data
        $data = LogReport::Listexport($search_branch_id,$search_user_id, $search_sdate, $search_edate);

        //XLS Data Adding or Modification according to the requirement of excel Object class function
        if(count($data)>0){
        foreach($data as $row){
            
            
            unset($row['user_id']);
            unset($row['branch_id']);
            unset($row['report_date']);
            unset($row['group_date']);
            

            $xls_data[] = $row;
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
         
   case 'export_pdf':
   
        
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
        
        break;
        
    case 'paging':
    case 'search':
    case 'back':
    case 'refresh':
        /*
         * Make the pages with header and footer
         */     
        $data = LogReport::Listview($search_branch_id,$search_user_id, $search_sdate, $search_edate);
        include ADMIN_LAYOUT_PATH . $module_name.'.html';
        
        break;
        
    case 'preview':
    case 'view':
    case 'print_preview':

        //Getting Report Data
        $data = LogReport::Listexport($search_branch_id,$search_user_id, $search_sdate, $search_edate);
        include ADMIN_LAYOUT_PATH . 'forms/'. $module_name . '.html';
        
        break;
        
    default:
        /*
         * Make the pages with header and footer
         */     
        //$data = LogReport::Listview($search_branch_id,$search_user_id, $search_sdate, $search_edate);
        
        //if($data)
        
        include ADMIN_LAYOUT_PATH . 'common/header.html'; 
            include ADMIN_LAYOUT_PATH . $module_name.'.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}
