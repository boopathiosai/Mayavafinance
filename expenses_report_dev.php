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
if($search_category_id != ""){
    $search_args .= "search_category_id=".$search_category_id."&";

    $category = $app->getrow("SELECT name FROM ".EXPENSES_CATEGORY." WHERE id=".intval($search_category_id)." LIMIT 1");
    $heading_suffix .= " for the category ".($category ? $category['name'] : '');
}

if($search_sub_category_id != ""){
    $search_args .= "search_sub_category_id=".$search_sub_category_id."&";

    $sub_category = $app->getrow("SELECT name FROM ".EXPENSES_SUB_CATEGORY." WHERE id=".intval($search_sub_category_id)." LIMIT 1");
    $heading_suffix .= " for the sub-category ".($sub_category ? $sub_category['name'] : '');
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

    echo "<tr><td colspan='8' style='font-size:16px; font-weight:bold; text-align:center;'>"
        . "Expenses Report (From " . htmlspecialchars($search_sdate) . " To " . htmlspecialchars($search_edate) . ")"
        . "</td></tr>";

    echo "<tr><td colspan='8' style='font-size:14px; font-weight:bold;'>Branch: " . htmlspecialchars($branch_name) . "</td></tr>";
    echo "<tr><td colspan='8' style='font-size:14px; font-weight:bold;'>Sub Branch: " . htmlspecialchars($sub_branch_name) . "</td></tr>";
    echo "<tr><td colspan='8' style='height:20px;'>&nbsp;</td></tr>";

    $app = $GLOBALS['app'];

    // ✅ Get detailed rows (with OB column from DB)
    $rows = ExpensesReportDev::subListview(
        $search_sdate,
        $search_edate,
        $search_branch_id,
        $search_sub_branch_id,
        $search_category_id,
        $search_sub_category_id
    );

    // =========================
    // ✅ Table Header
    // =========================
    echo "<tr style='font-weight:bold; background:#f2f2f2;'>"
        . "<th>Date</th>"
        . "<th style='text-align:right;'>Opening Balance (Rs.)</th>"
        . "<th>Category</th>"
        . "<th>Sub Category</th>"
        . "<th>Description</th>"
        . "<th style='text-align:right;'>Cash In (Rs.)</th>"
        . "<th style='text-align:right;'>Cash Out (Rs.)</th>"
        . "<th style='text-align:right;'>Closing Balance (Rs.)</th>"
        . "</tr>";

    // =========================
    // ✅ Loop Rows (DB OB + calculate CB)
    // =========================
    $total_in  = 0.0;
    $total_out = 0.0;
    $closing_balance = 0.0;

    foreach ($rows as $row) {
        $date        = isset($row['report_date']) ? date("d-m-Y", strtotime($row['report_date'])) : "";
        $category    = isset($row['category_name']) ? $row['category_name'] : "";
        $subcategory = isset($row['sub_category_name']) ? $row['sub_category_name'] : "";
        $desc        = isset($row['description']) ? $row['description'] : "";
        $cash_in     = isset($row['cash_in']) ? (float)$row['cash_in'] : 0.0;
        $cash_out    = isset($row['cash_out']) ? (float)$row['cash_out'] : 0.0;

        // ✅ Fetch OB from DB row itself
        $opening_balance = isset($row['opening_balance']) ? (float)$row['opening_balance'] : 0.0;

        // ✅ Calculate CB for this row
        $closing_balance = $opening_balance + $cash_in - $cash_out;

        echo "<tr>"
            . "<td>" . htmlspecialchars($date) . "</td>"
            . "<td style='text-align:right;'>" . number_format($opening_balance, 2) . "</td>"
            . "<td>" . htmlspecialchars($category) . "</td>"
            . "<td>" . htmlspecialchars($subcategory) . "</td>"
            . "<td>" . htmlspecialchars($desc) . "</td>"
            . "<td style='text-align:right;'>" . number_format($cash_in, 2) . "</td>"
            . "<td style='text-align:right;'>" . number_format($cash_out, 2) . "</td>"
            . "<td style='text-align:right;'>" . number_format($closing_balance, 2) . "</td>"
            . "</tr>";

        $total_in  += $cash_in;
        $total_out += $cash_out;
    }

    // ✅ Totals
    echo "<tr style='font-weight:bold; background:#e6ffe6;'>"
        . "<td colspan='5' align='right'>Total</td>"
        . "<td style='text-align:right;'>" . number_format($total_in, 2) . "</td>"
        . "<td style='text-align:right;'>" . number_format($total_out, 2) . "</td>"
        . "<td>&nbsp;</td>"
        . "</tr>";

    // ✅ Final Closing Balance (from last row CB)
    echo "<tr style='font-weight:bold; background:#ddd;'>"
        . "<td colspan='7' align='right'>Closing Balance :</td>"
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
   
    $app = $GLOBALS['app'];

    // 🔹 Detailed rows strictly filtered inside query
    $rows = ExpensesReportDev::subListview(
            $search_sdate,
            $search_edate,
            $search_branch_id,
            $search_sub_branch_id,
            $search_category_id,
            $search_sub_category_id
        );
    // ECHO "<pre>";
    // print_r($search_sdate);
    // echo "</pre>";
    // exit;


    // =========================
    // Build opening_by_date strictly for selected category/sub-category
    // =========================
    $s_date_sql = $sdate ? date("Y-m-d", strtotime($sdate)) : null;
    $e_date_sql = $edate ? date("Y-m-d", strtotime($edate)) : null;

    $filters = " WHERE COALESCE(opening_balance,0) <> 0 ";
    if ($branchId) $filters .= " AND branch_id = {$branchId}";
    if ($search_sub_branch_id) $filters .= " AND sub_branch_id = {$search_sub_branch_id}";
    if ($search_category_id) $filters .= " AND category_id = {$search_category_id}"; // ✅ strict filter
    if ($search_sub_category_id) $filters .= " AND sub_category_id = {$search_sub_category_id}"; // ✅ strict filter
    if ($s_date_sql && $e_date_sql) {
        $filters .= " AND DATE(expenses_date) BETWEEN '{$s_date_sql}' AND '{$e_date_sql}'";
    } elseif ($s_date_sql) {
        $filters .= " AND DATE(expenses_date) >= '{$s_date_sql}'";
    } elseif ($e_date_sql) {
        $filters .= " AND DATE(expenses_date) <= '{$e_date_sql}'";
    }


    // ECHO "<pre>";
    // print_r($search_category_id);
    // echo "</pre>";
    // exit;
    $open_rows = $app->getrows("
        SELECT DATE(expenses_date) AS dt, opening_balance, category_id, sub_category_id
        FROM mf_expenses
        {$filters}
        ORDER BY DATE(expenses_date) ASC, id ASC
    ");

    // ECHO "<pre>";
    // print_r("
    //     SELECT DATE(expenses_date) AS dt, opening_balance, category_id, sub_category_id
    //     FROM mf_expenses
    //     {$filters}
    //     ORDER BY DATE(expenses_date) ASC, id ASC");

    // echo "</pre>";
    // exit;

    // ✅ $open_rows la kooda only selected category/sub-category data varum
    $opening_by_date = [];
    if (!empty($open_rows)) {
        foreach ($open_rows as $orow) {
            $d = date("d-m-Y", strtotime($orow['dt']));
            $opening_by_date[$d] = (float)$orow['opening_balance'];
        }
    }


        // fallback
        $global_opening = $opening_balance;

        // =========================
        // Build PDF data rows
        // =========================
        $pdf_data = [];
        $grand_in = $grand_out = 0;

        if (!empty($rows)) {
            foreach ($rows as $r) {
                $date        = date("d-m-Y", strtotime($r['report_date']));
                $category    = $r['category_name'];
                $subcategory = $r['sub_category_name'];
                $desc        = $r['description'];
                $cash_in     = (float)$r['cash_in'];
                $cash_out    = (float)$r['cash_out'];
                $opening     = isset($r['opening_balance']) ? (float)$r['opening_balance'] : 0.0;

                $closing = $opening + $cash_in - $cash_out;

                $pdf_data[] = [
                    "date"         => $date,
                    "category"     => $category,
                    "sub_category" => $subcategory,
                    "description"  => $desc,
                    "opening"      => number_format($opening, 2),
                    "cash_in"      => number_format($cash_in, 2),
                    "cash_out"     => number_format($cash_out, 2),
                    "Closing Balance" => number_format($closing, 2),
                ];

                $grand_in  += $cash_in;
                $grand_out += $cash_out;
            }

            $pdf_data[] = [
                "date"         => "",
                "category"     => "",
                "sub_category" => "",
                "description"  => "TOTAL",
                "opening"      => "",
                "cash_in"      => number_format($grand_in, 2),
                "cash_out"     => number_format($grand_out, 2),
                "Closing Balance" => ""
            ];
        }

        // 🔹 Export PDF
        require_once("classes/pdf.class.php");
        $prifix = date("YmdHis");
        $file_name = "tmp/".$module_name."_".$prifix.".pdf";

        $pdf = new ExportPDF();
        $pdf->SetAutoPageBreak(false, 0);

        $branchName = Branch::getBranchName($branchId);
        $pdf->headerTitle = "Expenses Report (FROM $sdate To $edate) $branchName - Date: " . date("d-m-Y h:i:sa");

        $pdf->Ln(5);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(0,6,"Opening Balance : ".number_format($opening_balance,2),0,1);
        $pdf->Cell(0,6,"Closing Balance : ".number_format($closing_balance,2),0,1);
        $pdf->Ln(3);

        $width  = [20, 25, 25, 40, 30, 25, 25, 30];
        $header = ['Date','Category','Sub Category','Description','Opening','Cash In','Cash Out','Closing Balance'];

        $pdf->exportData($pdf_data, $width, 6, 'fancy', 'A1', $header);

        if (ob_get_length()) ob_end_clean();
        $pdf->Output($file_name, 'D');
        exit;
    break;







case 'preview':
case 'view':
case 'print_preview':

    // $search_branch_id       = isset($_SESSION['branch_id']) ? (int)$_SESSION['branch_id'] : 0;
    // $search_sub_branch_id   = isset($_SESSION['multiple_sub_branch_id']) ? $_SESSION['multiple_sub_branch_id'] : [];
    // $search_category_id     = isset($_SESSION['category_id']) ? (int)$_SESSION['category_id'] : 0;
    // $search_sub_category_id = isset($_SESSION['sub_category_id']) ? (int)$_SESSION['sub_category_id'] : 0;

    // $search_sdate = isset($_SESSION['search_sdate']) ? $_SESSION['search_sdate'] : '';
    // $search_edate = isset($_SESSION['search_edate']) ? $_SESSION['search_edate'] : '';

    $expenses_report = [];

    if (is_array($search_sub_branch_id) && count($search_sub_branch_id) > 0) {
        foreach ($search_sub_branch_id as $sbid) {
            $rows = ExpensesReportDev::subListview(
                $search_sdate,
                $search_edate,
                $search_branch_id,
                (int)$sbid,
                $search_category_id,
                $search_sub_category_id
            );
            if (!is_array($rows)) $rows = [];
            $expenses_report = array_merge($expenses_report, $rows);
        }
    } else {
        $rows = ExpensesReportDev::subListview(
            $search_sdate,
            $search_edate,
            $search_branch_id,
            0,
            $search_category_id,
            $search_sub_category_id
        );
        if (!is_array($rows)) $rows = [];
        $expenses_report = array_merge($expenses_report, $rows);
    }

    // 🔹 Opening balance fetch (include previous dates for correct opening)
    $opening_by_date = [];

    $branch_condition = $search_branch_id > 0 ? "branch_id = '{$search_branch_id}'" : "1=1";

    $sub_branch_condition = "";
    if (is_array($search_sub_branch_id) && count($search_sub_branch_id) > 0 && !in_array(0, $search_sub_branch_id)) {
        $sub_branch_list = implode(',', array_map('intval', $search_sub_branch_id));
        $sub_branch_condition = " AND sub_branch_id IN ($sub_branch_list)";
    }

    $category_condition = $search_category_id > 0 ? " AND category_id = {$search_category_id}" : "";
    $sub_category_condition = $search_sub_category_id > 0 ? " AND sub_category_id = {$search_sub_category_id}" : "";

    // ✅ include previous date records for opening balance calculation
    $sql = "
        SELECT DATE(expenses_date) AS dt, COALESCE(opening_balance,0) AS opening_balance, category_id, sub_branch_id
        FROM mf_expenses
        WHERE $branch_condition
        AND DATE(expenses_date) <= '".date('Y-m-d', strtotime($search_edate))."'
        $sub_branch_condition
        $category_condition
        $sub_category_condition
        ORDER BY dt, sub_branch_id
    ";

//     echo "<pre>";
// print_r($sql); 
// echo "</pre>";
// exit;

    $rows = $app->getrows($sql);
    if (!is_array($rows)) $rows = [];

    foreach ($rows as $r) {
        $opening_by_date[$r['dt']][$r['sub_branch_id']] = (float)$r['opening_balance'];
    }

    // 🔹 Closing balance calculation for selected date range
    $closing_by_date = [];
    foreach ($expenses_report as $row) {
        $date = $row['report_date'];
        $sub_branch = $row['sub_branch_id'];

        // ✅ Opening from previous available date if exists
        $opening_dates = array_keys($opening_by_date);
        $opening = 0;
        foreach ($opening_dates as $od) {
            if ($od <= $date && isset($opening_by_date[$od][$sub_branch])) {
                $opening = (float)$opening_by_date[$od][$sub_branch];
            }
        }

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