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
$page_title = 'Customer Pledged Report';
$module_name = 'customer_pledged_report';

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

$search_customer_id = (isset($_GET['search_customer_id'])) ? $_GET['search_customer_id'] : $_POST['search_customer_id'];

$search_sdate = (isset($_GET['search_sdate'])) ? $_GET['search_sdate'] : $_POST['search_sdate'];
$search_edate = (isset($_GET['search_edate'])) ? $_GET['search_edate'] : $_POST['search_edate'];

$search_sub_branch_id = (isset($_GET['search_sub_branch_id'])) ? $_GET['search_sub_branch_id'] : $_POST['search_sub_branch_id'];

$search_status_type = (isset($_GET['search_status_type'])) ? $_GET['search_status_type'] : $_POST['search_status_type'];


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
if($search_status_type != ""){
	$search_args.="search_status_type=".$search_status_type."&";
    
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

         $data = CustomerPledgedReport::Listview($search_customer_id,$search_sdate,$search_edate,$search_branch_id,$search_sub_branch_id,$search_status_type);
         include ADMIN_LAYOUT_PATH . $module_name.'.html';
         exit;
         break;

   case 'sublist':
   
         $data = CustomerPledgedReport::subListview($search_customer_id,$search_sdate,$search_edate,$search_branch_id,$search_status_type, $search_sub_branch_id);
         include ADMIN_LAYOUT_PATH . 'elements/sublist_'.$module_name.'.html';
         exit;
         break;

    case 'preview':
    case 'view':
    case 'print_preview':

        //Getting Report Data
        $data = CustomerPledgedReport::Listexport($search_customer_id,$search_sdate,$search_edate,$search_branch_id,$search_sub_branch_id,$search_status_type);
        include ADMIN_LAYOUT_PATH . 'forms/'. $module_name . '.html';
        
        break;

    case "export_xls":

        //Getting Report Data
        $data = CustomerPledgedReport::Listexport($search_customer_id,$search_sdate,$search_edate,$search_branch_id,$search_sub_branch_id,$search_status_type);

        //XLS Data Adding or Modification according to the requirement of excel Object class function
        if(count($data)>0){
        foreach($data as $row){


            /*$row['loan_amount'] = convertToCurrency($row['loan_amount']);
                              

            $weight_in = floatval($row['total_net_weight']);                
            

            $weight_in_gs =  intval($weight_in);
            $weight_in_mgs =  intval(floatval($weight_in - intval($weight_in)) * 1000);*/
			$row['net_weight'] = $row['weight_in_gs']." G & ".$row['weight_in_mgs']." Mg";
            $xls_row['customer_id'] = $row['customer_id'];
			$xls_row['loan_id'] = $row['loan_code'];
			$xls_row['loan_amount'] = convertToCurrency($row['loan_amount']);
			$xls_row['customer_details'] = $row['customer_details'];
			$xls_row['product_details'] = $row['product_details'];
			$xls_row['quantiy'] = $row['net_weight'];
			$xls_row['status'] = $row['status'];
			$xls_row['return_date'] = $row['return_date'];
            
           
            
            unset($row['pledge_id']);
            unset($row['weight_in']);
            unset($row['weight_in_gs']);
            unset($row['weight_in_mgs']);
            unset($row['scheme_name']);

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

        //Other Common page and header styles 
        $other["column_count"] = count($data[0]);

        //Excel Object
        $excel = new ExcelWriter($file_name, $title);

        //Column styles sheet information
        $column_style = array(
        "color"=>"#000000"
        );


        //Export the XLS Report
        $excel->exportData($xls_data, $file_name);
         exit;
    break;


    case "export_pdf":
        
        //Getting Report Data
        $data = CustomerPledgedReport::Listexport($search_customer_id,$search_sdate,$search_edate,$search_branch_id,$search_sub_branch_id,$search_status_type);

        //PDF Data Adding or Modification according to the requirement of Pdf Object class function
        if(count($data)>0){
        foreach($data as $row){


            /*$row['loan_amount'] = convertToCurrency($row['loan_amount']);
            

            $weight_in = floatval($row['total_net_weight']);
                                
                            $str_customer_details="";

                            for($i=0; $i<strlen($row['customer_details']);$i++)
                            {
                                $sep_str_customer_details = substr($row['customer_details'],$i,130);
                                $str_customer_details .= $sep_str_customer_details."\n";
                                $i=$i+130;                          
                            }

                            $row['customer_details']=$str_customer_details;

            $weight_in_gs =  intval($weight_in);
            $weight_in_mgs =  intval(floatval($weight_in - intval($weight_in)) * 1000);

            $row['total_net_weight'] = $weight_in_gs." G & ".$weight_in_mgs." Mg";
            
            unset($row['scheme_name']);*/
            
            $row['net_weight'] = $row['weight_in_gs']." G & ".$row['weight_in_mgs']." Mg";

            $pdf_row['customer_id'] = $row['customer_id'];
			$pdf_row['loan_id'] = $row['loan_code'];
			$pdf_row['loan_amount'] = convertToCurrency($row['loan_amount']);
			$pdf_row['customer_details'] = $row['customer_details'];
			$pdf_row['product_details'] = $row['product_details'];
			$pdf_row['quantiy'] = $row['net_weight'];
			$pdf_row['status'] = $row['status'];
			$pdf_row['return_date'] = $row['return_date'];
            
            unset($row['pledge_id']);
            unset($row['weight_in']);
            unset($row['weight_in_gs']);
            unset($row['weight_in_mgs']);
            unset($row['scheme_name']);

        


            $pdf_data[] = $pdf_row;
        }
        }

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
        $width = array(30,30,25,120,90,30,50,30);
        
        //Display Type 1- basic 2-improved 3-fancy
        //Page Size String Like 'A0', 'A10' || Array array(width,height)
        $pdf->exportData($pdf_data, $width, 40, 'fancy', array(420,550));
        
        //Export the pdf report
        $pdf->Output($file_name,'D');
        
        exit;
       
    break;



    default:
        #Rajesh Mar 02, 2021
        //Getting Default list data
        //$data = CustomerPledgedReport::Listview($search_customer_id,$search_sdate,$search_edate,$search_branch_id);
       // $data = array();
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . $module_name.'.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        
        break;
}