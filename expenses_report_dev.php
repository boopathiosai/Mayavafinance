<?php session_start();
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
define ('ROOT_PATH', '');
$page_title = 'Expenses Report';
$module_name = 'expenses_report_dev';

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
// commented DSSK - 05_MAY_2017 //
/*if(!is_accessible($menu))
{
        //echo"notaccessible";
        header('Location: index.php');
        exit;
}*/
// DSSK - END//

/*
 * Get values from js
 */
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
$search_category_id = (isset($_GET['search_category_id'])) ? $_GET['search_category_id'] : $_POST['search_category_id'];
$search_sub_category_id = (isset($_GET['search_sub_category_id'])) ? $_GET['search_sub_category_id'] : $_POST['search_sub_category_id'];

$search_args = "";
$heading_suffix = "";

if(!is_superadmin($_SESSION['session_usergroupname'])){
    $branch_name = $_SESSION['session_user_branch_name'];
} else {
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
if($search_sub_branch_id != ""){
    $search_args.="search_sub_branch_id=".$search_sub_branch_id."&";
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

// expenses_report_dev.php
if (isset($_POST['action']) && $_POST['action'] == "get_sub_branches") {
    $branch_id = isset($_POST['branch_id']) ? (int) $_POST['branch_id'] : 0;

    echo "<option value=''>-- Select Sub Branch --</option>";

    if ($branch_id > 0) {
        $query = "SELECT id, name, license_no 
                  FROM " . SUB_BRANCH . " 
                  WHERE status = '1' 
                  AND branch_id = '" . $branch_id . "' 
                  ORDER BY name ASC";

        $rows = $app->getrows($query);

        if (!empty($rows)) {
            foreach ($rows as $row) {
                echo "<option value='".$row['id']."'>".$row['name']."</option>";
            }
        }
    }
    exit;
}
if (isset($_POST['action']) && $_POST['action'] == "get_sub_categories") {
    global $app;
    $category_id = (int) $_POST['category_id'];

    $query = "SELECT id, name 
              FROM mf_expenses_sub_category 
              WHERE category_id = ".$category_id." 
              ORDER BY name ASC";

    $rows = $app->getrows($query);

    echo "<option value=''>-- Select Sub Category --</option>";
    foreach ($rows as $row) {
        echo "<option value='".$row['id']."'>".$row['name']."</option>";
    }
    exit;
}


switch($type) {
   case 'paging':
   case 'back':
   case 'search':
        if($_POST['searchOption']!='null'){
            $search_sub_branch=explode(',',$_POST['searchOption']);
        }

        if($type=='search') {
            $type_of_page="search";
            if($search_sdate== "" && $search_edate == ""){
                $type_of_page=2;
            }
        } else if($type == 'paging'){
            $s_qry = $_SESSION['multiple_sub_branch_id'];
            $search_sub_branch = $s_qry; 
            $_POST['searchOption'] = $s_qry;
            $type_of_page = "";
        } else {
            $type_of_page=1;
        }
             
        $data = ExpensesReportDev::Listview($search_branch_id,$search_sdate,$search_edate,$search_sub_branch,$search_category_id,$search_sub_category_id,$type_of_page);

        $_SESSION['sub_branch_id'] = $search_sub_branch_id;
        $_SESSION['multiple_sub_branch_id'] = $search_sub_branch;
        $_SESSION['category_id'] = $search_category_id;
        $_SESSION['sub_category_id'] = $search_sub_category_id;
        include ADMIN_LAYOUT_PATH . 'expenses_report_dev.html';
        exit;
        break;

case 'sublist':
    /* $search_sub_branch = isset($_POST['sub_branch_id']) ? (int)$_POST['sub_branch_id'] : 0;
    $search_sdate      = isset($_POST['sdate']) ? $_POST['sdate'] : "";
    $search_edate      = isset($_POST['edate']) ? $_POST['edate'] : "";
    $search_category   = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $search_subcat     = isset($_POST['sub_category_id']) ? (int)$_POST['sub_category_id'] : 0; */
   

    $startDate = isset($_POST['search_sdate']) ? $_POST['search_sdate'] : 0;
    $endDate = isset($_POST['search_edate']) ? $_POST['search_edate'] : 0;
    $branchId = isset($_POST['search_branch_id']) ? (int)$_POST['search_branch_id'] : 0;
    $subBranchId = isset($_POST['search_sub_branch_id']) ? (int)$_POST['search_sub_branch_id'] : 0;
    $search_category   = isset($_POST['search_category_id']) ? (int)$_POST['search_category_id'] : 0;
    $search_subcat     = isset($_POST['search_sub_category_id']) ? (int)$_POST['search_sub_category_id'] : 0; 

    $data = ExpensesReportDev::subListview(
        $startDate,
        $endDate,
        $branchId,
        $subBranchId,
        $search_category,
        $search_subcat
    );

    include ADMIN_LAYOUT_PATH . 'elements/sublist_expenses_report.html';
    exit;

case "export_xls":

    // inputs (ensure proper casting)
    $search_sub_branch_id   = !empty($_SESSION['sub_branch_id']) ? (int)$_SESSION['sub_branch_id'] : 0;
    $search_category_id     = isset($_SESSION['category_id']) ? (int)$_SESSION['category_id'] : 0;
    $search_sub_category_id = isset($_SESSION['sub_category_id']) ? (int)$_SESSION['sub_category_id'] : 0;

    $branch_name     = Branch::getBranchName($search_branch_id);
    $sub_branch_name = Subbranch::getSubBranchName($search_sub_branch_id);

    // Excel headers
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=expenses_report_" . date("YmdHis") . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<table border='1' cellspacing='0' cellpadding='5' style='width:100%; border-collapse:collapse;'>";

    echo "<tr><td colspan='7' style='font-size:16px; font-weight:bold; text-align:center;'>"
        . "Expenses Report (From " . htmlspecialchars($search_sdate) . " To " . htmlspecialchars($search_edate) . ")"
        . "</td></tr>";

    echo "<tr><td colspan='7' style='font-size:14px; font-weight:bold;'>Branch: " . htmlspecialchars($branch_name) . "</td></tr>";
    echo "<tr><td colspan='7' style='font-size:14px; font-weight:bold;'>Sub Branch: " . htmlspecialchars($sub_branch_name) . "</td></tr>";
    echo "<tr><td colspan='7' style='height:20px;'>&nbsp;</td></tr>";

    // app DB handle (used to fetch manual opening_balance rows)
    $app = $GLOBALS['app'];

    // ✅ transactions (detailed rows)
    $rows = ExpensesReportDev::subListview(
        $search_sdate,
        $search_edate,
        $search_branch_id,
        $search_sub_branch_id,
        $search_category_id,
        $search_sub_category_id
    );

    // =========================
    // ✅ Build opening_by_date map (grouped by normalized sub-branch name)
    // =========================
    $opening_by_date = array();

    // normalize function for Tamil/English name differences
    function normalize_name($name) {
        return preg_replace('/[^A-Za-z0-9\p{L}]/u', '', mb_strtolower($name));
    }

    // get normalized name of selected sub-branch
    $current_normalized = normalize_name($sub_branch_name);

    // sanitize dates for SQL
    $s_date_sql = ($search_sdate) ? date("Y-m-d", strtotime($search_sdate)) : null;
    $e_date_sql = ($search_edate) ? date("Y-m-d", strtotime($search_edate)) : null;

    // fetch ALL manual opening rows for this branch (not filtered by sub_branch yet)
    $filters = " WHERE COALESCE(opening_balance,0) <> 0 ";
    if ($search_branch_id) {
        $filters .= " AND branch_id = " . (int)$search_branch_id;
    }
    if ($s_date_sql && $e_date_sql) {
        $filters .= " AND DATE(expenses_date) BETWEEN '".$s_date_sql."' AND '".$e_date_sql."'";
    } elseif ($s_date_sql) {
        $filters .= " AND DATE(expenses_date) >= '".$s_date_sql."'";
    } elseif ($e_date_sql) {
        $filters .= " AND DATE(expenses_date) <= '".$e_date_sql."'";
    }

    $open_rows = $app->getrows("
        SELECT DATE(expenses_date) AS dt, opening_balance, sub_branch_id
        FROM mf_expenses
        {$filters}
        ORDER BY DATE(expenses_date) ASC, id ASC
    ");

    if (!empty($open_rows)) {
        foreach ($open_rows as $orow) {
            $sub_name   = Subbranch::getSubBranchName($orow['sub_branch_id']);
            $normalized = normalize_name($sub_name);
            // only take if belongs to same normalized group
            if ($normalized === $current_normalized) {
                $d = date("d-m-Y", strtotime($orow['dt']));
                $opening_by_date[$d] = (float)$orow['opening_balance'];
            }
        }
    }

    // ✅ If no manual openings found, fallback to Listview()
    $global_opening = 0.0;
    if (empty($opening_by_date)) {
        $lv = ExpensesReportDev::Listview(
            $search_branch_id,
            $search_sdate,
            $search_edate,
            $search_sub_branch_id,
            $search_category_id,
            $search_sub_category_id,
            2
        );
        if (!empty($lv) && isset($lv[0]['opening_balance'])) {
            $global_opening = (float)$lv[0]['opening_balance'];
        }
    }

    // Table header
    echo "<tr style='font-weight:bold; background:#f2f2f2;'>"
        . "<th>Date</th>"
        . "<th style='text-align:right;'>Opening Balance (Rs.)</th>"
        . "<th>Category</th>"
        . "<th>Sub Category</th>"
        . "<th>Description</th>"
        . "<th style='text-align:right;'>Cash In (Rs.)</th>"
        . "<th style='text-align:right;'>Cash Out (Rs.)</th>"
        . "</tr>";

    $total_in  = 0.0;
    $total_out = 0.0;
    $last_date = "";
    $first_row_date = "";

    // get first transaction date (for closing calc)
    if (!empty($rows) && isset($rows[0]['report_date'])) {
        $first_row_date = date("d-m-Y", strtotime($rows[0]['report_date']));
    }

    // select starting opening
    $running_opening_for_closing = $global_opening;
    if (!empty($opening_by_date) && $first_row_date !== "" && isset($opening_by_date[$first_row_date])) {
        $running_opening_for_closing = (float)$opening_by_date[$first_row_date];
    } elseif (!empty($opening_by_date) && $first_row_date === "") {
        // fallback to earliest manual opening
        reset($opening_by_date);
        $running_opening_for_closing = (float)current($opening_by_date);
    }

    foreach ($rows as $row) {

        $date        = isset($row['report_date']) ? date("d-m-Y", strtotime($row['report_date'])) : "";
        $category    = isset($row['category_name']) ? $row['category_name'] : "";
        $subcategory = isset($row['sub_category_name']) ? $row['sub_category_name'] : "";
        $desc        = isset($row['description']) ? $row['description'] : "";
        $cash_in     = isset($row['cash_in']) ? (float)$row['cash_in'] : 0.0;
        $cash_out    = isset($row['cash_out']) ? (float)$row['cash_out'] : 0.0;

        // Opening balance shown only once per date
        $opening_col = "";
        if ($last_date !== $date) {
            if (isset($opening_by_date[$date])) {
                $opening_col = number_format($opening_by_date[$date], 2);
            } elseif ($date === $first_row_date && $running_opening_for_closing != 0) {
                $opening_col = number_format($running_opening_for_closing, 2);
            }
            $last_date = $date;
        }

        echo "<tr>"
            . "<td>" . htmlspecialchars($date) . "</td>"
            . "<td style='text-align:right;'>" . $opening_col . "</td>"
            . "<td>" . htmlspecialchars($category) . "</td>"
            . "<td>" . htmlspecialchars($subcategory) . "</td>"
            . "<td>" . htmlspecialchars($desc) . "</td>"
            . "<td style='text-align:right;'>" . number_format($cash_in, 2) . "</td>"
            . "<td style='text-align:right;'>" . number_format($cash_out, 2) . "</td>"
            . "</tr>";

        $total_in  += $cash_in;
        $total_out += $cash_out;
    }

    // Totals row
    echo "<tr style='font-weight:bold; background:#e6ffe6;'>"
        . "<td colspan='5'>Total</td>"
        . "<td style='text-align:right;'>" . number_format($total_in, 2) . "</td>"
        . "<td style='text-align:right;'>" . number_format($total_out, 2) . "</td>"
        . "</tr>";

    // Closing balance: starting opening + totals
    $closing_balance = $running_opening_for_closing + $total_in - $total_out;

    echo "<tr style='font-weight:bold;'>"
        . "<td colspan='6'>Closing Balance</td>"
        . "<td style='text-align:right;'>" . number_format($closing_balance, 2) . "</td>"
        . "</tr>";

    echo "</table>";
    exit;
    break;

    
// case "export_pdf":

//     require_once("classes/pdf.class.php");

//     $branch_name     = Branch::getBranchName($search_branch_id);
//     $sub_branch_name = Subbranch::getSubBranchName($search_sub_branch_id);

//     $rows = ExpensesReportDev::subListview(
//         $search_sdate,
//         $search_edate,
//         $search_branch_id,
//         $search_sub_branch_id,
//         $search_category_id,
//         $search_sub_category_id
//     );

//     $global_opening = !empty($rows[0]['opening_balance']) ? (float)$rows[0]['opening_balance'] : 0.0;
//     $total_in  = 0.0;
//     $total_out = 0.0;

//     $prifix = date("YmdHis");

//     // --- PDF INIT ---
//     $pdf = new ExportPDF('P','mm','A4');
//     $pdf->SetAutoPageBreak(true, 20);
//     $pdf->headerTitle = "Daily Report for the sub branch {$sub_branch_name} - Date:" . date("d-m-Y h:i:sa");

//     // Check font file
//     if (file_exists(__DIR__."/classes/font/madurai.php")) {
//         $pdf->AddFont('madurai', '', 'madurai.php');
//         $pdf->SetFont('madurai','',10);
//     } else {
//         $pdf->SetFont('Arial','',10); // fallback if font missing
//     }

//     $pdf->AliasNbPages();
//     $pdf->AddPage();

//     // --- Tamil + English mixed cell helper ---
//     function writeMixedCell($pdf, $w, $h, $text, $border=1, $ln=0, $align='L', $fill=false) {
//         preg_match_all('/([\x{0B80}-\x{0BFF}]+|[^\x{0B80}-\x{0BFF}]+)/u', $text, $parts);

//         $startX = $pdf->GetX();
//         $startY = $pdf->GetY();
//         $curX   = $startX;

//         foreach ($parts[0] as $part) {
//             if (preg_match('/[\x{0B80}-\x{0BFF}]/u', $part)) {
//                 $pdf->SetFont('madurai','',10); // Tamil
//             } else {
//                 $pdf->SetFont('Arial','',10);   // English
//             }
//             $pdf->SetXY($curX, $startY);
//             $pdf->Cell($pdf->GetStringWidth($part), $h, $part, 0, 0, $align, $fill);
//             $curX += $pdf->GetStringWidth($part);
//         }

//         // Final border
//         $pdf->SetXY($startX, $startY);
//         $pdf->Cell($w, $h, "", $border, $ln, $align, $fill);
//     }

//     // Column widths
//     $w = [
//         'date' => 25,
//         'cat'  => 35,
//         'sub'  => 35,
//         'desc' => 55,
//         'in'   => 20,
//         'out'  => 20
//     ];

//     // Table header
//     function printTableHeader($pdf, $w) {
//         $pdf->SetFont('Arial', 'B', 10);
//         $pdf->SetFillColor(128, 0, 128);
//         $pdf->SetTextColor(255);
//         $pdf->Cell($w['date'], 10, " Date", 1, 0, 'C', true);
//         $pdf->Cell($w['cat'], 10, "Category", 1, 0, 'C', true);
//         $pdf->Cell($w['sub'], 10, "Sub Category", 1, 0, 'C', true);
//         $pdf->Cell($w['desc'], 10, "Particulars", 1, 0, 'C', true);
//         $pdf->Cell($w['in'], 10, "Cash In", 1, 0, 'C', true);
//         $pdf->Cell($w['out'], 10, "Cash Out", 1, 1, 'C', true);
//         $pdf->SetTextColor(0);

//         // restore body font
//         if (in_array('madurai', $pdf->fonts)) {
//             $pdf->SetFont('madurai','',10);
//         } else {
//             $pdf->SetFont('Arial','',10);
//         }
//     }

//     printTableHeader($pdf, $w);

//     // Opening Balance
//     $pdf->SetFillColor(240,240,250);
//     $pdf->Cell($w['date'] + $w['cat'] + $w['sub'] + $w['desc'], 8, "Opening Balance", 1, 0, 'L', true);
//     $pdf->Cell($w['in'], 8, number_format($global_opening, 2), 1, 0, 'R', true);
//     $pdf->Cell($w['out'], 8, "0.00", 1, 1, 'R', true);

//     // Data rows
//     $fill = false;
//     $lineHeight = 8;
// /*     echo "<pre>";
// print_r( $rows); echo "</pre>"; exit; */
//     foreach ($rows as $row) {
//         if ($pdf->GetY() + $lineHeight > ($pdf->h - 20)) {
//             $pdf->AddPage();
//             printTableHeader($pdf, $w);
//         }

//         $date = (!empty($row['report_date']) && $row['report_date'] != '0000-00-00') 
//                   ? date("d-M-Y", strtotime($row['report_date'])) : '';

//         $category    = isset($row['category_name'])     ? $row['category_name']     : '';
//         $subcategory = isset($row['sub_category_name']) ? $row['sub_category_name'] : '';
//         $desc        = isset($row['description'])       ? $row['description']       : '';
//         $cash_in     = isset($row['cash_in'])  ? (float)$row['cash_in']  : 0.0;
//         $cash_out    = isset($row['cash_out']) ? (float)$row['cash_out'] : 0.0;

//         $pdf->SetFillColor($fill ? 245 : 255, 245, 255);

//         $pdf->Cell($w['date'], $lineHeight, $date, 1, 0, 'C', true);
//         writeMixedCell($pdf, $w['cat'], $lineHeight, $category, 1, 0, 'L', true);
//         writeMixedCell($pdf, $w['sub'], $lineHeight, $subcategory, 1, 0, 'L', true);
//         writeMixedCell($pdf, $w['desc'], $lineHeight, $desc, 1, 0, 'L', true);
//         $pdf->Cell($w['in'], $lineHeight, number_format($cash_in, 2), 1, 0, 'R', true);
//         $pdf->Cell($w['out'], $lineHeight, number_format($cash_out, 2), 1, 1, 'R', true);

//         $total_in  += $cash_in;
//         $total_out += $cash_out;
//         $fill = !$fill;
//     }

//     // TOTAL
//     $pdf->SetFillColor(200,255,200);
//     $pdf->SetFont('Arial','B',10);
//     $pdf->Cell($w['date'] + $w['cat'] + $w['sub'] + $w['desc'], 8, "TOTAL", 1, 0, 'R', true);
//     $pdf->Cell($w['in'], 8, number_format($total_in, 2), 1, 0, 'R', true);
//     $pdf->Cell($w['out'], 8, number_format($total_out, 2), 1, 1, 'R', true);

//     // Closing Balance
//     $closing_balance = $global_opening + $total_in - $total_out;
//     $pdf->SetFillColor(255, 200, 200);
//     $pdf->Cell($w['date'] + $w['cat'] + $w['sub'] + $w['desc'], 8, "Closing Balance", 1, 0, 'R', true);
//     $pdf->Cell($w['in'], 8, number_format($closing_balance, 2), 1, 0, 'R', true);
//     $pdf->Cell($w['out'], 8, "", 1, 1, 'R', true);
// $prifix = preg_replace('/[^A-Za-z0-9_\-]/', '', $prifix); // only safe chars
// $downloadName = "REPORT_{$prifix}.pdf";

// if (ob_get_length()) {
//     ob_end_clean(); // clear buffer
// }

// header('Content-Type: application/pdf');
// header('Content-Disposition: attachment; filename="' . $downloadName . '"');
// $pdf->Output('D', $downloadName);   
// exit;

// break;


case "export_pdf":
    $startDate = isset($_GET['search_sdate']) ? $_GET['search_sdate'] : 0;
    $endDate   = isset($_GET['search_edate']) ? $_GET['search_edate'] : 0;
    $branchId  = isset($_GET['search_branch_id']) ? (int)$_GET['search_branch_id'] : 0;
    $search_sub_branch_id   = isset($_GET['search_sub_branch_id']) ? (int)$_GET['search_sub_branch_id'] : 0;
    $search_category_id     = isset($_GET['search_category_id']) ? (int)$_GET['search_category_id'] : 0;
    $search_sub_category_id = isset($_GET['search_sub_category_id']) ? (int)$_GET['search_sub_category_id'] : 0;

    // 🔹 1. Manual opening balance fetch (same as Excel)
 $opening_by_date = [];
$sql = "
    SELECT DATE(expenses_date) AS dt, COALESCE(opening_balance,0) AS opening_balance, sub_branch_id
    FROM mf_expenses
    WHERE branch_id = '{$branchId}'
      AND DATE(expenses_date) BETWEEN '".date('Y-m-d', strtotime($startDate))."' 
                                  AND '".date('Y-m-d', strtotime($endDate))."'
    ORDER BY dt, sub_branch_id
";

$rows = $app->getrows($sql);

foreach ($rows as $r) {
    $opening_by_date[$r['dt']][$r['sub_branch_id']] = (float)$r['opening_balance'];
//     echo "<pre>";
// print_r($opening_by_date);
// echo "</pre>";
// exit;
}

    // 🔹 2. Fallback global opening balance (Listview)
    $balances = ExpensesReportDev::Listview(
        $branchId, $startDate, $endDate,
        $search_sub_branch_id, $search_category_id, $search_sub_category_id,
        2
    );
    $balance_map = [];
    foreach ($balances as $bal) {
        $key = $bal['branch_id'].'_'.$bal['license_no'];
        $balance_map[$key] = (float)$bal['opening_balance'];
    }

    // 🔹 3. Get main data
    $data = ExpensesReportDev::subListview(
        $startDate, $endDate, $branchId,
        $search_sub_branch_id, $search_category_id, $search_sub_category_id
    );

    $pdf_data = [];
    if (!empty($data)) {
        $grouped_data = [];
        foreach ($data as $row) {
            $grouped_data[$row['report_date']][] = $row;
        }

        foreach ($grouped_data as $report_date => $rows) {
            $row0 = $rows[0];
            $sub_branch_id = $row0['sub_branch_id'];

            // 🔹 Opening balance priority: manual → fallback Listview → 0
            if (isset($opening_by_date[$report_date][$sub_branch_id])) {
                $opening_balance = $opening_by_date[$report_date][$sub_branch_id];
            } else {
                $key = $row0['branch_id'].'_'.$row0['license_no'];
                $opening_balance = isset($balance_map[$key]) ? $balance_map[$key] : 0;
            }

            // Opening balance row
            $pdf_data[] = [
                "date" => "",
                "category" => "",
                "sub_category" => "",
                "description" => "Opening Balance ($report_date)",
                "cash_in" => number_format($opening_balance, 2),
                "cash_out" => ""
            ];

            $total_in = 0; $total_out = 0;
            foreach ($rows as $r) {
                $total_in  += (float)$r['cash_in'];
                $total_out += (float)$r['cash_out'];

                $pdf_data[] = [
                    "date" => $report_date,
                    "category" => $r['category_name'],
                    "sub_category" => $r['sub_category_name'],
                    "description" => $r['description'],
                    "cash_in" => number_format((float)$r['cash_in'], 2),
                    "cash_out" => number_format((float)$r['cash_out'], 2)
                ];
            }

            // TOTAL row
            $pdf_data[] = [
                "date" => "",
                "category" => "",
                "sub_category" => "",
                "description" => "TOTAL",
                "cash_in" => number_format($total_in, 2),
                "cash_out" => number_format($total_out, 2)
            ];

            // Closing balance
            $closing_balance = $opening_balance + $total_in - $total_out;
            $pdf_data[] = [
                "date" => "",
                "category" => "",
                "sub_category" => "",
                "description" => "Closing Balance",
                "cash_in" => number_format($closing_balance, 2),
                "cash_out" => ""
            ];

            // Spacer row
            $pdf_data[] = ["date"=>"","category"=>"","sub_category"=>"","description"=>"","cash_in"=>"","cash_out"=>""];
        }
    }

    // 🔹 Export PDF
    require_once("classes/pdf.class.php");
    $prifix = date("YmdHis");
    $file_name = "tmp/".$module_name."_".$prifix.".pdf";

    $pdf = new ExportPDF();
    $pdf->SetAutoPageBreak(false, 0);

    if ($_SESSION['session_usertype'] == 1) {
        $heading_suffix_cur .= Branch::getBranchName($branchId);
    }

    if (empty($heading_suffix)) {
        $pdf->headerTitle = $page_title." - Date: ".date("d-m-Y h:i:sa");
    } elseif ($startDate == $endDate) {
        $pdf->headerTitle = $page_title." (FROM ".$startDate." To ".$endDate.") ".$heading_suffix_cur." - Date: ".date("d-m-Y h:i:sa");
    } else {
        $pdf->headerTitle = $page_title." ".$heading_suffix_cur." - Date: ".date("d-m-Y h:i:sa");
    }

    $width = [30, 35, 35, 50, 30, 30];
    $header = ['Date', "Category", "Sub Category", "Description", "Cash In", "Cash Out"];

    $pdf->exportData($pdf_data, $width, 6, 'fancy', 'A2', $header);

    if (ob_get_length()) ob_end_clean();
    $pdf->Output($file_name, 'D');
    exit;
break;






case 'preview':
case 'view':
case 'print_preview':

    $search_branch_id       = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
    $search_sub_branch_id   = isset($_SESSION['multiple_sub_branch_id']) ? $_SESSION['multiple_sub_branch_id'] : [];
    $search_category_id     = isset($_SESSION['category_id']) ? (int)$_SESSION['category_id'] : 0;
    $search_sub_category_id = isset($_SESSION['sub_category_id']) ? (int)$_SESSION['sub_category_id'] : 0;

   $expenses_report = [];

if (is_array($search_sub_branch_id) && count($search_sub_branch_id) > 0) {
    foreach ($search_sub_branch_id as $sbid) {
        $rows = ExpensesReportDev::subListview(
            $search_sdate,
            $search_edate,
            $search_branch_id,
            (int)$sbid,   // ✅ SUB-BRANCH ID pass pannanum
            $search_category_id,
            $search_sub_category_id
        );
        // echo "<pre>";
        // print_r($rows);
        // echo "</pre>";
        //  exit;
        if (!is_array($rows)) {
            $rows = [];
        }

        $expenses_report = array_merge($expenses_report, $rows);
        echo "<pre>";
        print_r($expenses_report);
        echo "</pre>";
         exit;
    }
} else {
    // Branch only selected → fetch all sub-branches
    $rows = ExpensesReportDev::subListview(
        $search_sdate,
        $search_edate,
        $search_branch_id,
        0,   // ✅ 0 means "all sub-branches" (if your function supports)
        $search_category_id,
        $search_sub_category_id
    );

    echo "<pre>";
        print_r($rows);
        echo "</pre>";
         exit;

    if (!is_array($rows)) {
        $rows = [];
    }

    $expenses_report = array_merge($expenses_report, $rows);
}
    // 2️⃣ Opening balance fetch for main & sub-branch
    $opening_by_date = [];

// Branch condition
$branch_condition = $search_branch_id > 0 ? "branch_id = '{$search_branch_id}'" : "1=1";

// Sub-branch condition
$sub_branch_condition = "";
if (is_array($search_sub_branch_id) && count($search_sub_branch_id) > 0 && !in_array(0, $search_sub_branch_id)) {
    $sub_branch_list = implode(',', array_map('intval', $search_sub_branch_id));
    $sub_branch_condition = " AND sub_branch_id IN ($sub_branch_list)";
}

$sql = "
    SELECT DATE(expenses_date) AS dt, COALESCE(opening_balance,0) AS opening_balance, sub_branch_id
    FROM mf_expenses
    WHERE $branch_condition
    AND DATE(expenses_date) BETWEEN '".date('Y-m-d', strtotime($search_sdate))."'
                                AND '".date('Y-m-d', strtotime($search_edate))."'
    $sub_branch_condition
    ORDER BY dt, sub_branch_id
";
//     echo "<pre>";
//    print_r($sql);   
//     exit;
    $rows = $app->getrows($sql);
    if (!is_array($rows)) $rows = [];

    foreach ($rows as $r) {
        $opening_by_date[$r['dt']][$r['sub_branch_id']] = (float)$r['opening_balance'];
//         echo "<pre>";
//    print_r($opening_by_date);   
//     exit;
    }

    // 3️⃣ Calculate closing balance per sub-branch & date
    $closing_by_date = [];
    foreach ($expenses_report as $row) {
        $date = $row['report_date'];
        $sub_branch = $row['sub_branch_id'];
        $opening = isset($opening_by_date[$date][$sub_branch]) ? $opening_by_date[$date][$sub_branch] : 0;

        $total_in = isset($closing_by_date[$date][$sub_branch]['cash_in']) ? $closing_by_date[$date][$sub_branch]['cash_in'] : 0;
        $total_out = isset($closing_by_date[$date][$sub_branch]['cash_out']) ? $closing_by_date[$date][$sub_branch]['cash_out'] : 0;

        $total_in += (float)$row['cash_in'];
        $total_out += (float)$row['cash_out'];

        $closing_by_date[$date][$sub_branch] = [
            'cash_in' => $total_in,
            'cash_out' => $total_out,
            'closing_balance' => $opening + $total_in - $total_out
        ];
    }

    // 4️⃣ Pass all to template
    include ADMIN_LAYOUT_PATH . 'forms/' . $module_name . '.html';

break;





    default:
        /*
         * Make the pages with header and footer
         */
        $page_type=1;
        // $data = ExpensesReportDev::Listview($search_branch_id,$search_sdate,$search_edate,$sub_branch,$search_category_id,$search_sub_category_id,$page_type);
        
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'expenses_report_dev.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}