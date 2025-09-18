<?php

header('Location: expenses_report_dev.php');          
exit;
?> 
<?php 

session_start();
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Expenses Report
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Karthick B
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  29-Oct-2011
//**************************************************************************
// Description      :  This is the Expenses Report php file for Expenses module,used to process the view,add,edit and delete in Expenses module.
//**************************************************************************
define('ROOT_PATH', '');
$page_title = 'Expenses Report';
$module_name = 'expenses_report';

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
$search_category_id = (isset($_GET['search_category_id'])) ? $_GET['search_category_id'] : $_POST['search_category_id'];
$search_sub_category_id = (isset($_GET['search_sub_category_id'])) ? $_GET['search_sub_category_id'] : $_POST['search_sub_category_id'];

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
if ($search_sub_branch_id != "") {
    $search_args .= "search_sub_branch_id=" . $search_sub_branch_id . "&";
}
if ($search_category_id != "") {
    $search_args .= "search_category_id=" . $search_category_id . "&";
    $heading_suffix .= " for the category " . ExpenseCategory::getCategoryName($search_category_id);
}
if ($search_sub_category_id != "") {
    $search_args .= "search_sub_category_id=" . $search_sub_category_id . "&";
    $heading_suffix .= " for the sub category " . ExpenseSubCategory::getSubCategoryName($search_sub_category_id);
}

if ($page_no <= 0) {
    $page_no = 1;
}
if ($rg <= 0) {
    $rg = 1;
}
$page_no = (int) $page_no;

/*
 * Assign limit to display records
 */
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);

switch ($type) {
    case 'paging':
    case 'back':
    case 'search':
        if ($_POST['searchOption'] != 'null') {
            $search_sub_branch = explode(',', $_POST['searchOption']);
        }

        if ($type == 'search') {
            $type_of_page = "search";
        } else {
            $type_of_page = 1;
        }

        $data = ExpensesReport::Listview($search_branch_id, $search_sdate, $search_edate, $search_sub_branch, $search_category_id, $search_sub_category_id, $type_of_page);

        $_SESSION['sub_branch_id'] = $search_sub_branch_id;
        $_SESSION['multiple_sub_branch_id'] = $search_sub_branch;
        $_SESSION['category_id'] = $search_category_id;
        $_SESSION['sub_category_id'] = $search_sub_category_id;
        include ADMIN_LAYOUT_PATH . 'expenses_report.html';
        exit;
        break;

    case 'sublist':
    $data = [];
    if (isset($_SESSION['multiple_sub_branch_id']) && !empty($_SESSION['multiple_sub_branch_id'])) {
        $sub_branch_ids = $_SESSION['multiple_sub_branch_id'];
    } elseif (!empty($_POST['multiple_search'])) {
        $sub_branch_ids = $_POST['multiple_search'];
    } else {
        $sub_branch_ids = [$search_sub_branch_id];
    }

    foreach ($sub_branch_ids as $sbid) {
        $subData = ExpensesReport::subListview(
            $search_branch_id,
            $search_sdate,
            $search_edate,
            $sbid,   // send single id here
            $search_category_id,
            $search_sub_category_id
        );
        $data = array_merge($data, $subData);
    }

    include ADMIN_LAYOUT_PATH . 'elements/sublist_expenses_report.html';
    exit;
    break;

    case "export_xls":
        if (($search_sdate == '') && ($search_edate == '') && ($search_sub_branch_id == '')) {
            $type_of_page = 1;
        } else {
            $type_of_page = "export_xls";    
        }

        $search_sub_branch_id = $_SESSION['multiple_sub_branch_id'];
        $search_category_id = $_SESSION['category_id'];
        $search_sub_category_id = $_SESSION['sub_category_id'];

        $initial = 0;
        for ($i = 0; $i < count($search_sub_branch_id); $i++) {
            if ($initial == 0) {
                $heading_suffix_cur = " for the sub branch " . Subbranch::getSubBranchName($search_sub_branch_id[$i]);
                $initial ++;
            } else {
                $heading_suffix_cur .= " and " . Subbranch::getSubBranchName($search_sub_branch_id[$i]);
            }
        }

        $data = ExpensesReport::subListview($search_branch_id, $search_sdate, $search_edate, $search_sub_branch_id, $search_category_id, $search_sub_category_id, $type_of_page);

        //XLS Data Adding or Modification according to the requirement of XLS Object class function
        if (count($data) > 0) {
            $i = 0;
            foreach ($data as $row) {
                if (($search_sdate == '') && ($search_edate == '')) {
                    $row['expense_date'] = $row['expense_date'];
                } elseif ($search_sdate == $search_edate) {
                    $row['expense_date'] = '';
                    unset($row['expense_date']);
                } else {
                    $row['expense_date'] = $row['expense_date'];
                }

                unset($row['id']);
                unset($row['branch_id']);
                unset($row['sub_branch_id']);
                unset($row['category_id']);
                unset($row['sub_category_id']);
                unset($row['created_at']);
                unset($row['created_by']);
                unset($row['modified_at']);
                unset($row['modified_by']);
                
                if ($row['amount'] != 0) {
                    $xls_data[] = $row;
                }
                $i++;
            }
        }

        require_once("classes/excel.class.php");

        //File Name Setting
        $prifix = date("Ymdhis");
        $file_name = "tmp/" . $module_name . "_" . $prifix . ".xls";

        //Report Title
        if ($heading_suffix == "")
            $title = $page_title;
        elseif ($search_sdate == $search_edate)
            $title = $page_title . "(FROM " . $search_sdate . " To " . $search_edate . ")" . $heading_suffix_cur;
        else
            $title = $page_title . " " . $heading_suffix_cur;

        //Excel Object
        $excel = new ExcelWriter($file_name, $title);

        //Export the XLS Report
        $excel->exportData($xls_data, $file_name);
        exit;
        break;

    case "export_pdf":
        $search_sub_branch_id = $_SESSION['multiple_sub_branch_id'];
        $search_category_id = $_SESSION['category_id'];
        $search_sub_category_id = $_SESSION['sub_category_id'];

        $initial = 0;
        for ($i = 0; $i < count($search_sub_branch_id); $i++) {
            if ($initial == 0) {
                $heading_suffix_cur = " for the sub branch " . Subbranch::getSubBranchName($search_sub_branch_id[$i]);
                $initial ++;
            } else {
                $heading_suffix_cur .= " and " . Subbranch::getSubBranchName($search_sub_branch_id[$i]);
            }
        }
        
        $data = ExpensesReport::subListview($search_branch_id, $search_sdate, $search_edate, $search_sub_branch_id, $search_category_id, $search_sub_category_id);

        //PDF Data Adding or Modification according to the requirement of Pdf Object class function
        if (count($data) > 0) {
            $i = 0;
            foreach ($data as $row) {
                if (($search_sdate == '') && ($search_edate == '')) {
                    $row['expense_date'] = $row['expense_date'];
                } elseif ($search_sdate == $search_edate) {
                    $row['expense_date'] = '';
                    unset($row['expense_date']);
                } else {
                    $row['expense_date'] = $row['expense_date'];
                }

                unset($row['id']);
                unset($row['branch_id']);
                unset($row['sub_branch_id']);
                unset($row['category_id']);
                unset($row['sub_category_id']);
                unset($row['created_at']);
                unset($row['created_by']);
                unset($row['modified_at']);
                unset($row['modified_by']);

                if ($row['amount'] != 0) {
                    $pdf_data[] = $row;
                }
                $i++;
            }
        }

        //Class Include 
        require_once("classes/pdf.class.php");

        //File Name Setup
        $prifix = date("Ymdhis");
        $file_name = "tmp/" . $module_name . "_" . $prifix . ".pdf";

        //Pdf Object
        $pdf = new ExportPDF();

        //Report Title
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
        $search_sub_branch_id = $_SESSION['multiple_sub_branch_id'];
        $search_category_id = $_SESSION['category_id'];
        $search_sub_category_id = $_SESSION['sub_category_id'];

        $initial = 0;
        for ($i = 0; $i < count($search_sub_branch_id); $i++) {
            if ($initial == 0) {
                $heading_suffix_cur = " for the sub branch " . Subbranch::getSubBranchName($search_sub_branch_id[$i]);
                $initial ++;
            } else {
                $heading_suffix_cur .= " and " . Subbranch::getSubBranchName($search_sub_branch_id[$i]);
            }
        }

        $expenses_report = ExpensesReport::subListview($search_branch_id, $search_sdate, $search_edate, $search_sub_branch_id, $search_category_id, $search_sub_category_id);
        
        include ADMIN_LAYOUT_PATH . 'forms/' . $module_name . '.html';
        break;

    default:
        /*
         * Make the pages with header and footer
         */
        $page_type = 1;
        $data = ExpensesReport::Listview($search_branch_id, $search_sdate, $search_edate, $sub_branch, $search_category_id, $search_sub_category_id, $page_type);

        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'expenses_report.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}