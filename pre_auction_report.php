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
$page_title = 'Pre-Auction Report';
$module_name = 'pre_auction_report';

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
$serial_no = ($serial_no == '') ? 1 : $serial_no;

 

/*
 * Get search options
 */

$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_sdate = (isset($_GET['search_sdate'])) ? $_GET['search_sdate'] : $_POST['search_sdate'];
$search_edate = (isset($_GET['search_edate'])) ? $_GET['search_edate'] : $_POST['search_edate'];
$search_customer_id = (isset($_GET['search_customer_id'])) ? $_GET['search_customer_id'] : $_POST['search_customer_id'];
$search_sub_branch_id = (isset($_GET['search_sub_branch_id'])) ? $_GET['search_sub_branch_id'] : $_POST['search_sub_branch_id'];

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
if($search_customer_id != ""){
    $search_args.="search_customer_id=".$search_customer_id."&";
    $heading_suffix .= " for the customer ".Customer::getCustomerName($search_customer_id);
}
if($search_sub_branch_id != ""){
	$search_args.="search_sub_branch_id=".$search_sub_branch_id."&";
    $heading_suffix .= " for the sub branch ".Subbranch::getSubBranchName($search_sub_branch_id);
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



switch($type) {

    case 'sublist':
        /*
         * Make the pages with header and footer
         */     
        $data = PreAuctionReport::subListview($search_customer_id, $search_branch_id, $search_sdate, $search_edate);
        include ADMIN_LAYOUT_PATH . "elements/sublist_" . $module_name.'.html';
        
        break;
     
   case 'export_xls':   
       
        // Added 11_DEC_2017 -DSSK //
       if(($search_sdate=='')&&($search_edate=='')&&($search_sub_branch_id==''))
          {
            //echo "prin";
            //exit;
            $type_of_page =2;
          }
          else{
            //echo "dfgdf";exit;
            $type_of_page = "export_xls";
          }
		
        $search_sub_branch_id = $_SESSION['multiple_sub_branch_id'];


             $initial=0;
        for ($i=0; $i < count($search_sub_branch_id) ; $i++) {

          if($initial==0)
          {
            $heading_suffix_cur = " for the sub branch ".Subbranch::getSubBranchName($search_sub_branch_id[$i]);
            $initial ++;
            //echo $heading_suffix_cur;exit;
          }
          else{
            $heading_suffix_cur .= " and ".Subbranch::getSubBranchName($search_sub_branch_id[$i]);
          }


        } 
       
       // end -Added 11_DEC_2017 -DSSK //      
        //Getting Report Data
        $data = PreAuctionReport::Listexport($search_customer_id, $search_branch_id, $search_sdate, $search_edate,$search_sub_branch_id,$type_of_page);
        //XLS Data Adding or Modification according to the requirement of XLS Object class function      
//       echo "<pre>"; print_r($data); exit;
        if(count($data)>0){
        foreach($data as $key => $row){
//$row['loan_amount'] = convertToCurrency($row['loan_amount']);
            $net_weight = floatval($row['net_weight']);
            $weight_in_gs =  intval($net_weight);
            $weight_in_mgs =  intval(floatval($net_weight - intval($net_weight)) * 1000);
//			$xls_row['branch_name'] = $row['branch_name'];
			$xls_row['serial_no'] = $key+1;
			$xls_row['loan_no'] = $row['loan_no'];
			$xls_row['loan_date'] = $row['report_date'];			          
                        $xls_row['loan_amount'] = convertToCurrency($row['loan_amount']);
                        $xls_row['customer_name'] = $row['cust_name'].','.$row['cust_relation'].'-'.$row['cust_rel_name']; // Changed(Cust name, relation name) DSSK 22_NOV_2017 
			$xls_row['customer_details'] = $row['customer_details'].', Phone.No-'.$row['cont_no'];
                        $xls_row['product_details'] = $row['product_details'];
			$xls_row['quantity'] = $row['quantity'];
			$xls_row['net_weight'] = $weight_in_gs." G & ".$weight_in_mgs." Mg";
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
   
        
        //Getting Report Data
        $data = PreAuctionReport::Listexport($search_customer_id, $search_branch_id, $search_sdate, $search_edate,$search_sub_branch_id);
		
        //PDF Data Adding or Modification according to the requirement of Pdf Object class function
        if(count($data)>0){
        foreach($data as $key => $row){
			$pdf_row['branch_name'] = $row['branch_name'];
			$pdf_row['serial_no'] = $key+1;
			$pdf_row['loan_no'] = $row['loan_no'];   
            $pdf_row['loan_date'] = $row['report_date'];			        
            $pdf_row['loan_amount'] = convertToCurrency($row['loan_amount']);			
            
            $net_weight = floatval($row['net_weight']);

						$str_customer_details="";

							for($i=0; $i<strlen($row['customer_details']);$i++)
							{
								$sep_str_customer_details = substr($row['customer_details'],$i,130);
								$str_customer_details .= $sep_str_customer_details."\n";
								$i=$i+130;							
							}

							$row['customer_details']=$str_customer_details;

			$pdf_row['customer_details'] = $row['customer_details'];
            $pdf_row['product_details'] = $row['product_details'];
			$pdf_row['quantity'] = $row['quantity'];
			
            
            $weight_in_gs =  intval($net_weight);
            $weight_in_mgs =  intval(floatval($net_weight - intval($net_weight)) * 1000);
            
            $pdf_row['net_weight'] = $weight_in_gs." G & ".$weight_in_mgs." Mg";
			
			unset($row['report_date']);


			



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
        $width = array(30,30,30,30,30,200,120,50);
        
        //Display Type 1- basic 2-improved 3-fancy
        //Page Size String Like 'A0', 'A10' || Array array(width,height)
        $pdf->exportData($pdf_data, $width, 30, 'fancy', array(600,500));
        
        //Export the pdf report
        $pdf->Output($file_name,'D');
        
        exit;
        
        break;
        
    case 'paging':
    case 'search':
        /*
         * Make the pages with header and footer
         */
         /* Added 11_DEC_2017 -DSSK */        
         if($_POST['searchOption']!='null'){
                $search_sub_branch=explode(',',$_POST['searchOption']);            
            }
            
         if($type=='search')
         {
          $type_of_page="search";
          if($search_sdate== "" && $search_edate == ""){
              $type_of_page=2;
          }
         }else if($type=='paging'){
             $_POST['searchOption'] = $_SESSION['multiple_sub_branch_id'];
             $search_sub_branch=explode(',',$_SESSION['multiple_sub_branch_id']);      
             $type_of_page = "";
         }else{
           $type_of_page=2;
         }
          /* end- Added 11_DEC_2017 -DSSK */
        $data = PreAuctionReport::Listview($search_customer_id, $search_branch_id, $search_sdate, $search_edate,$search_sub_branch);
        
         $_SESSION['sub_branch_id'] = $search_sub_branch_id; // Added 11_DEC_2017 -DSSK
         $_SESSION['multiple_sub_branch_id'] = $search_sub_branch; // Added 11_DEC_2017 -DSSK
        include ADMIN_LAYOUT_PATH . $module_name.'.html';
        
        break;
        
    case 'preview':
    case 'view':
    case 'print_preview':

        //Getting Report Data
        /* Added 06_DEC_2017 -DSSK */
        
         $search_sub_branch_id = $_SESSION['multiple_sub_branch_id'];

         $initial=0;

        for ($i=0; $i < count($search_sub_branch_id) ; $i++) {

          if($initial==0)
          {
            $heading_suffix_cur = " for the sub branch ".Subbranch::getSubBranchName($search_sub_branch_id[$i]);
            $initial ++;
            //echo $heading_suffix_cur;exit;
          }
          else{
            $heading_suffix_cur .= " and ".Subbranch::getSubBranchName($search_sub_branch_id[$i]);
          }


        }

        if($search_sdate == "" && $search_edate == "" ){
                $page_type = 2;   
            }
         /* end- Added 06_DEC_2017 -DSSK */
        $data = PreAuctionReport::Listexport($search_customer_id, $search_branch_id, $search_sdate, $search_edate,$search_sub_branch_id);
        include ADMIN_LAYOUT_PATH . 'forms/'. $module_name . '.html';
        
        break;
        
    default:
        /*
         * Make the pages with header and footer
         */   
         $type_of_page=2;
        $data = PreAuctionReport::Listview($search_customer_id, $search_branch_id, $search_sdate, $search_edate,$type_of_page);
        
        //if($data)
        
        include ADMIN_LAYOUT_PATH . 'common/header.html'; 
            include ADMIN_LAYOUT_PATH . $module_name.'.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>