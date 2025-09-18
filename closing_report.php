<?php

header('Location: closing_report_dev.php');
exit;
?> 
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
define('ROOT_PATH', '');
$page_title = 'Closing Report';
$module_name = 'closing_report';

include ROOT_PATH . 'includes/config.php';


$_SESSION["mcatid"] = REPORTS_CAT_ID;

$menu = Privillege::getModuleDetail($_SESSION[$app->session_usertype1]);

/*
 * Redirect to index page if not logged in
 */
if ($app->is_logged_in() !== true) {

    if ($mod == 'Ajax') {
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
if (!is_accessible($menu)) {
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
$page_no = (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg = (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] = (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];


$search_sdate = (isset($_GET['search_sdate'])) ? $_GET['search_sdate'] : $_POST['search_sdate'];
$search_edate = (isset($_GET['search_edate'])) ? $_GET['search_edate'] : $_POST['search_edate'];
$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_customer_id = (isset($_GET['search_customer_id'])) ? $_GET['search_customer_id'] : $_POST['search_customer_id'];
$search_sub_branch_id = (isset($_GET['search_sub_branch_id'])) ? $_GET['search_sub_branch_id'] : $_POST['search_sub_branch_id'];

$search_args = "";
$heading_suffix = "";


if ($search_sdate != "") {
    $search_args .= "search_sdate=" . $search_sdate . "&";
    $heading_suffix .= " from " . date("d/M/Y", strtotime($search_sdate));
}
if ($search_edate != "") {
    $search_args .= "search_edate=" . $search_edate . "&";
    $heading_suffix .= " till " . date("d/M/Y", strtotime($search_edate));
}
if ($search_branch_id != "") {
    $search_args .= "search_branch_id=" . $search_branch_id . "&";
    $heading_suffix .= " for the branch " . Branch::getBranchName($search_branch_id);
}
if ($search_customer_id != "") {
    $search_args .= "search_customer_id=" . $search_customer_id . "&";
    $heading_suffix .= " for the customer " . Customer::getCustomerName($search_customer_id);
}
if ($search_sub_branch_id != "") {
    // echo "<pre>";print_r($search_sub_branch_id);exit;
    $search_args .= "search_sub_branch_id=" . $search_sub_branch_id . "&";
    // $heading_suffix .= " for the sub branch ".Subbranch::getSubBranchName($search_sub_branch_id);
}


if ($page_no <= 0) {
    $page_no = 1;
}
if ($rg <= 0) {
    $rg = 1;
}
$page_no = (int) $page_no;

/*
 * Class Instances
 */



/*
 * Assign limit to display records
 */
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);


switch ($type) {
    case 'paging':
    case 'back':
    case 'search':
        // echo "<pre>";print_r($_POST['searchOption']);exit;
        if ($_POST['searchOption'] != 'null') {
            $search_sub_branch = explode(',', $_POST['searchOption']);
        }

        if ($type == 'search') {
            $type_of_page = "search";
        } else {
            $type_of_page = 1;
        }

        //echo"<pre>";print_r($a);
        // exit;

        $data = DailyReport::Listview($search_branch_id, $search_sdate, $search_edate, $search_sub_branch, $type_of_page);

        $_SESSION['sub_branch_id'] = $search_sub_branch_id;
        $_SESSION['multiple_sub_branch_id'] = $search_sub_branch;
        include ADMIN_LAYOUT_PATH . 'expenses_report.html';     
        exit;      
        break;

    case 'sublist':
        // echo "<pre>";print_r(explode(',',$_POST['multiple_search']));exit(0);
        //echo "<pre>";print_r($_SESSION);
        if (isset($_SESSION['multiple_sub_branch_id']) && (!empty($_SESSION['multiple_sub_branch_id']))) {
            $search_sub_branch_id = $_SESSION['multiple_sub_branch_id'];
        } else if (isset($_POST['multiple_search'])) {
            $search_sub_branch_id = $_POST['multiple_search'];
        }

        $data = DailyReport::subListview($search_branch_id, $search_sdate, $search_edate, $search_sub_branch_id);
        // echo '<pre>';
        //             print_r($data);exit;

        include ADMIN_LAYOUT_PATH . 'elements/sublist_expenses_report.html';
        exit;
        break;    

    case "export_xls":
        //Getting Report Data
        //echo "<pre>";print_r($_SESSION);exit;

        if (($search_sdate == '') && ($search_edate == '') && ($search_sub_branch_id == '')) {
            //echo "prin";
            //exit;
            $type_of_page = 1;
        } else {
            //echo "dfgdf";exit;
            $type_of_page = "export_xls";
        }

        $search_sub_branch_id = $_SESSION['multiple_sub_branch_id'];


        $initial = 0;
        for ($i = 0; $i < count($search_sub_branch_id); $i++) {

            if ($initial == 0) {
                $heading_suffix_cur = " for the sub branch " . Subbranch::getSubBranchName($search_sub_branch_id[$i]);
                $initial ++;
                //echo $heading_suffix_cur;exit;
            } else {
                $heading_suffix_cur .= " and " . Subbranch::getSubBranchName($search_sub_branch_id[$i]);
            }
        }
//        echo $search_branch_id;
//        echo $search_sdate;
//        echo $search_edate; 
//        echo "<pre>";
//        print_r($search_sub_branch_id);
//        echo $type_of_page;
        $data = DailyReport::subListview($search_branch_id, $search_sdate, $search_edate, $search_sub_branch_id, $type_of_page);
//       echo "<pre>";print_r($data);
        //XLS Data Adding or Modification according to the requirement of XLS Object class function
        if (count($data) > 0) {


            /* $xls_data[] = array(
              "date"=>$data[0]['date'],
              "particulars"=>'Opening Balance',
              "cash_in"=>convertToCurrency($data[0]['opening_balance'],false),
              "cash_out"=>0
              ); */
            // $cash_in = 0;
            // $cash_out = 0;

            $old_branch_name = $data[0]['branch_name'];
            $i = 0;
            // echo "<pre>";print_r($data);exit;
            foreach ($data as $row) {


                /* $cash_in += floatval($row['cash_in']);
                  $cash_out += floatval($row['cash_out']); */
                $row['particulars'] = $row['particulars'] == 'Pay interest' ? 'Interest' : $row['particulars'];
                if ($row['particulars'] == CASHFLOW_PURPOSE_OPENING || $row['particulars'] == CASHFLOW_PURPOSE_CLOSING || $row['particulars'] == CASHFLOW_PURPOSE_TOTAL) {
                    $row['branch_name'] = "";
                }
                /*
                  remove comment for zero value if variable has no zero
                  $row['cash_in'] = convertToCurrency($row['cash_in'],false);
                  $row['cash_out'] = convertToCurrency($row['cash_out'],false);
                 */
                $row['cash_in'] = $row['cash_in'];
                $row['cash_out'] = $row['cash_out'];
                // echo "haha".$search_sdate."<br>hehehe".$search_edate;exit;
                //$row['trans_date'] = (($search_sdate==$search_edate)||(($search_sdate=='')||($search_edate==''))) ? '' : $row['trans_date'];
                if (($search_sdate == '') && ($search_edate == '')) {
                    $row['trans_date'] = $row['trans_date'];
                } elseif ($search_sdate == $search_edate) {
                    $row['trans_date'] = '';
                    unset($row['trans_date']);
                } else {
                    $row['trans_date'] = $row['trans_date'];
                }
                // if($search_sdate==$search_edate)
                // {
                //     $row['trans_date']="";
                // }

                unset($row['id']);
                unset($row['report_date']);
                unset($row['branch_id']);
                unset($row['order_id']);
                unset($row['branch_name']);
                if (($row['cash_in']) || ($row['cash_out']) != 0) {

                    $xls_data[] = $row;
                }


                $i++;
            }
        }
//        echo "<pre>";
//        print_r($xls_data);
//        exit;

        /* $xls_data[] = array(
          "date"=>'',
          "particulars"=>'TOTAL',
          "cash_in"=>convertToCurrency($cash_in,false),
          "cash_out"=>convertToCurrency($cash_out,false)
          );

          $xls_data[] = array(
          "date"=>'',
          "particulars"=>'Closing Balance',
          "cash_in"=>0.00,
          "cash_out"=>convertToCurrency($data[count($data)-1]['closing_balance'],false)
          ); */
        //echo '<pre>';
        //print_r($xls_data);

        require_once("classes/excel.class.php");

        //File Name Setting

        $prifix = date("Ymdhis");
        $file_name = "tmp/" . $module_name . "_" . $prifix . ".xls";

        //echo "kkasd". $heading_suffix_cur;exit;
        //Report Title
        if ($heading_suffix == "")
            $title = $page_title;
        elseif ($search_sdate == $search_edate)
            $title = $page_title . "(FROM " . $search_sdate . " To " . $search_edate . ")" . $heading_suffix_cur;
        else
            $title = $page_title . " " . $heading_suffix_cur;
        //$title = $page_title . " - ".$heading_suffix;
        //Excel Object
        $excel = new ExcelWriter($file_name, $title);

        //Export the XLS Report
        //  echo "<pre>";print_r($xls_data);exit;
        $excel->exportData($xls_data, $file_name);
        exit;
        break;





    case "export_pdf":
        //Getting Report Data

        $search_sub_branch_id = $_SESSION['multiple_sub_branch_id'];


        $initial = 0;
        for ($i = 0; $i < count($search_sub_branch_id); $i++) {

            if ($initial == 0) {
                $heading_suffix_cur = " for the sub branch " . Subbranch::getSubBranchName($search_sub_branch_id[$i]);
                $initial ++;
                //echo $heading_suffix_cur;exit;
            } else {
                $heading_suffix_cur .= " and " . Subbranch::getSubBranchName($search_sub_branch_id[$i]);
            }
        }
        $data = DailyReport::subListview($search_branch_id, $search_sdate, $search_edate, $search_sub_branch_id);
        //$data = DailyReport::Listexport($search_branch_id,$search_sdate,$search_edate,$search_sub_branch_id);
        //PDF Data Adding or Modification according to the requirement of Pdf Object class function
        if (count($data) > 0) {

            /* $pdf_data[] = array(
              "date"=>$data[0]['date'],
              "branch"=>'',
              "particulars"=>'Opening Balance',
              "cash_in"=>convertToCurrency($data[0]['opening_balance'],false),
              "cash_out"=>0
              ); */
            $cash_in = 0;
            $cash_out = 0;
            $old_branch_name = $data[0]['branch_name'];
            $i = 0;
            foreach ($data as $row) {


                /* if($old_branch_name != $row['branch_name']){

                  $pdf_data[] = array(
                  "date"=>'',
                  "branch"=>$old_branch_name ,
                  "particulars"=>'TOTAL',
                  "cash_in"=>convertToCurrency($cash_in,false),
                  "cash_out"=>convertToCurrency($cash_out,false)
                  );

                  $pdf_data[] = array(
                  "date"=>'',
                  "branch"=>$old_branch_name ,
                  "particulars"=>'Closing Balance',
                  "cash_in"=>0.00,
                  "cash_out"=>convertToCurrency($data[$i-1]['closing_balance'],false)
                  );
                  $old_branch_name = $row['branch_name'];

                  }

                  $cash_in += floatval($row['cash_in']);
                  $cash_out += floatval($row['cash_out']); */
                $row['particulars'] = $row['particulars'] == 'Pay interest' ? 'Interest' : $row['particulars'];
                if ($row['particulars'] == CASHFLOW_PURPOSE_OPENING || $row['particulars'] == CASHFLOW_PURPOSE_CLOSING || $row['particulars'] == CASHFLOW_PURPOSE_TOTAL) {
                    $row['branch_name'] = "";
                }
                /* $row['cash_in'] = convertToCurrency($row['cash_in'],false);
                  $row['cash_out'] = convertToCurrency($row['cash_out'],false); */

                if (($search_sdate == '') && ($search_edate == '')) {
                    $row['trans_date'] = $row['trans_date'];
                } elseif ($search_sdate == $search_edate) {
                    $row['trans_date'] = '';
                    unset($row['trans_date']);
                } else {
                    $row['trans_date'] = $row['trans_date'];
                }

                $row['cash_in'] = $row['cash_in'];
                $row['cash_out'] = $row['cash_out'];

                unset($row['id']);
                unset($row['report_date']);
                unset($row['branch_id']);
                unset($row['order_id']);
                unset($row['branch_name']);


                if ((($row['cash_in']) || ($row['cash_out'])) != 0) {

                    $pdf_data[] = $row;
                }
                $i++;
            }
        }

        /* $query = "SELECT sCF.closing_balance 
          FROM ".CASH_FLOW_TRANSACTIONS." AS sCF
          WHERE sCF.branch_id = B.id AND  sCF.date_transaction = '".$data[count($data)-1]['date']."'
          ORDER BY sCF.date_transaction DESC
          LIMIT 0,1 */


        /* $pdf_data[] = array(
          "date"=>'',
          "branch"=>'',
          "particulars"=>'TOTAL',
          "cash_in"=>convertToCurrency($cash_in,false),
          "cash_out"=>convertToCurrency($cash_out,false)
          );

          $pdf_data[] = array(
          "date"=>'',
          "branch"=>'',
          "particulars"=>'Closing Balance',
          "cash_in"=>0.00,
          "cash_out"=>convertToCurrency($data[count($data)-1]['closing_balance'],false)
          ); */

        //Class Include 
        require_once("classes/pdf.class.php");

        //File Name Setup
        $prifix = date("Ymdhis");
        $file_name = "tmp/" . $module_name . "_" . $prifix . ".pdf";

        //Pdf Object
        $pdf = new ExportPDF();

        //Report Title
        // if($heading_suffix == "")
        //     $pdf->headerTitle = $page_title;
        // else
        //    // $pdf->headerTitle = $page_title . " - " . $heading_suffix;
        //      $pdf->headerTitle = $page_title;


        if ($heading_suffix == "")
            $pdf->headerTitle = $page_title;
        elseif ($search_sdate == $search_edate)
            $pdf->headerTitle = $page_title . "(FROM " . $search_sdate . " To " . $search_edate . ")" . $heading_suffix_cur;
        else
            $pdf->headerTitle = $page_title . " " . $heading_suffix_cur;

        //Report Column Width
        $width = array(85, 120, 60, 60);

        //Display Type 1- basic 2-improved 3-fancy
        //Page Size String Like 'A0', 'A10' || Array array(width,height)
        $pdf->exportData($pdf_data, $width, 7, 'fancy', array(300, 400));

        //Export the pdf report
        $pdf->Output($file_name, 'D');

        exit;
        break;

    case 'preview':
    case 'view':
    case 'print_preview':

        //Getting Report Data
        $search_sub_branch_id = $_SESSION['multiple_sub_branch_id'];

        $initial = 0;

        for ($i = 0; $i < count($search_sub_branch_id); $i++) {

            if ($initial == 0) {
                $heading_suffix_cur = " for the sub branch " . Subbranch::getSubBranchName($search_sub_branch_id[$i]);
                $initial ++;
                //echo $heading_suffix_cur;exit;
            } else {
                $heading_suffix_cur .= " and " . Subbranch::getSubBranchName($search_sub_branch_id[$i]);
            }
        }


        $daily_report = DailyReport::subListview($search_branch_id, $search_sdate, $search_edate, $search_sub_branch_id);
        //$daily_report = DailyReport::Listexport($search_branch_id,$search_sdate,$search_edate,$search_sub_branch_id);
        //p_arr($daily_report);
        include ADMIN_LAYOUT_PATH . 'forms/' . $module_name . '.html';

        break;

    default:
        /*
         * Make the pages with header and footer
         */
        $page_type = 1;
        $data = DailyReport::Listview($search_branch_id, $search_sdate, $search_edate, $sub_branch, $page_type);


        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'closing_report.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}