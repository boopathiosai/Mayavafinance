<?php

//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Expenses Report (Dev)
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  (Converted by Assistant from DailyReportDev)
// Last Modified    :  --
//**************************************************************************
// Description      :  ExpensesReportDev - All queries purely from mf_expenses.
//**************************************************************************

class ExpensesReportDev {
    /* ---------------------------
    * Sub-Branch helper methods
    * ---------------------------
    * NOTE: made these static so calls like self::SubbranchSet(...) work.
    */
    public static function SubbranchSet($set_sb) { 
        $app = $GLOBALS['app'];
        if ($_SESSION['session_usertype'] != 1 && $set_sb == 0) {
            $query_sub_branch = "SELECT id FROM mf_sub_branch WHERE status = 1 AND branch_id = " . (int)$_SESSION['session_user_branch_id'];
            $data = $app->getrows($query_sub_branch);
            $ids = [];
            foreach ($data as $row) { $ids[] = $row['id']; }
            $find_id = implode(',', $ids);
            return ($find_id == "" ? "" : " AND (E.sub_branch_id IN($find_id))");
        }
        return "";
    }

    public static function SubbranchSet_default($set_sb) {
        $app = $GLOBALS['app'];
        if ($_SESSION['session_usertype'] != 1 && $set_sb == 0) {
            $query_sub_branch = "SELECT id FROM mf_sub_branch WHERE status = 1 AND branch_id = " . (int)$_SESSION['session_user_branch_id'];
            $data = $app->getrows($query_sub_branch);
            $ids = [];
            foreach ($data as $row) { $ids[] = $row['id']; }
            return implode(',', $ids);
        }
        return "";
    }

    public static function SubbranchData($set_sb) {
        $app = $GLOBALS['app'];
        if ($_SESSION['session_usertype'] != 1 && $set_sb == 0) {
            $query_sub_branch = "SELECT id FROM mf_sub_branch WHERE status = 1 AND branch_id = " . (int)$_SESSION['session_user_branch_id'];
            $data = $app->getrows($query_sub_branch);
            $ids = [];
            foreach ($data as $row) { $ids[] = $row['id']; }
            $find_id = implode(',', $ids);
            return ($find_id == "" ? "" : " AND (E.sub_branch_id IN($find_id))");
        }
        return "";
    }

    public static function SubbranchStatus($set_sb) {
        $app = $GLOBALS['app'];
        if ($_SESSION['session_usertype'] != 1 && $set_sb == 0) {
            $query_sub_branch = "SELECT id FROM mf_sub_branch WHERE status = 0 AND branch_id = " . (int)$_SESSION['session_user_branch_id'];
            $data = $app->getrows($query_sub_branch);
            return (count($data) != 0) ? 1 : 0;
        }
        return 0;
    }

    /*
    * ---------------------------------------
    * List view (header rows by date/branch)
    * ---------------------------------------
    * This returns aggregated header rows expected by the HTML:
    * fields: id (concat), branch_id, report_date, trans_date, sub_details, branch_name
    */
    public static function ListviewCount() {
        $args = func_get_args();
        $app = $GLOBALS['app'];
        $s_branch      = isset($args[0]) ? $args[0] : "";
        $s_sdate       = isset($args[1]) ? $args[1] : "";
        $s_edate       = isset($args[2]) ? $args[2] : "";
        $s_sub_branch  = isset($args[3]) ? $args[3] : "";
        $type_of_page  = isset($args[4]) ? $args[4] : "";

        $search_condition = "";

        // Default recent (3 day) if quick view/page type
        if ($type_of_page == 1 || ($s_sdate != "" && $s_edate == "")) {
            $search_condition = " AND (E.expenses_date BETWEEN DATE_SUB(NOW(), INTERVAL 3 DAY) AND NOW())";
        }

        $search_condition .= ($s_branch != "") ? " AND (E.branch_id = '" . addslashes($s_branch) . "')" : "";

        if (is_array($s_sub_branch) && !empty($s_sub_branch) && (int)$s_sub_branch[0] > 0) {
            $ids = array_map('intval', $s_sub_branch);
            $search_condition .= " AND (E.sub_branch_id IN (" . implode(',', $ids) . "))";
        } elseif (!empty($s_sub_branch) && !is_array($s_sub_branch)) {
            $search_condition .= " AND (E.sub_branch_id = '" . addslashes($s_sub_branch) . "')";
        }

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND (DATE(E.expenses_date) BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND (DATE(E.expenses_date) >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND (DATE(E.expenses_date) <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        }

        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= (!empty($_SESSION['session_user_branch_id'])) ? " AND E.branch_id = '" . (int)$_SESSION['session_user_branch_id'] . "'" : "";
        }

        // add sub-branch privilege filter
        $set_sb = 0;
        $search_condition .= self::SubbranchSet($set_sb);

        $query = "
            SELECT COUNT(*) AS cnt
            FROM (
                SELECT 1
                FROM mf_expenses AS E
                LEFT JOIN mf_branch AS B ON B.id = E.branch_id
                LEFT JOIN mf_sub_branch AS SB ON SB.id = E.sub_branch_id
                WHERE 1 " . $search_condition . "
                GROUP BY B.id, SB.id, DATE(E.expenses_date)
            ) t
        ";

        $data = $app->getrow($query);
        return isset($data[0]) ? (int)$data[0] : 0;
    }

public static function Listview() {
    $args = func_get_args();
    $app = $GLOBALS['app'];

    $s_branch = isset($args[0]) ? $args[0] : "";
    $s_sdate = isset($args[1]) ? $args[1] : "";
    $s_edate = isset($args[2]) ? $args[2] : "";
    $s_sub_branch = isset($args[3]) ? $args[3] : "";
    $s_category = isset($args[4]) ? $args[4] : 0;
    $s_sub_category = isset($args[5]) ? $args[5] : 0;
    $type_of_page = isset($args[6]) ? $args[6] : 1;

    $search_condition = "";

    // Default quick range
    $start_date = date('Y-m-d', strtotime("-1 days"));
    $end_date   = date('Y-m-d', strtotime("+1 days"));
    if ($type_of_page == 1 || ($s_sdate == "" && $s_edate == "")) {
        $search_condition .= " AND (DATE(E.expenses_date) BETWEEN '".$start_date."' AND '".$end_date."')";
    }

    // branch filter
    if (!empty($s_branch)) {
        $search_condition .= " AND (E.branch_id = '" . addslashes($s_branch) . "')";
    }

    // sub-branch filter (license based)
    if (!empty($s_sub_branch)) {
        $s_sub_branch = (array)$s_sub_branch;
        $s_sub_branch = array_filter($s_sub_branch);

        if (!empty($s_sub_branch)) {
            $rows = $app->getrows("
                SELECT DISTINCT license_no 
                FROM mf_sub_branch 
                WHERE id IN(".implode(',', array_map('intval',$s_sub_branch)).")
            ");
            $license_nos = array_column($rows, "license_no");
            if (!empty($license_nos)) {
                $license_list = implode("','", array_map("addslashes", $license_nos));
                $search_condition .= " AND (E.license_no IN('".$license_list."'))";
            }
        }
    }

    // category filter
    if (!empty($s_category)) {
        $search_condition .= " AND E.category_id = " . (int)$s_category;
    }
    if (!empty($s_sub_category)) {
        $search_condition .= " AND E.sub_category_id = " . (int)$s_sub_category;
    }

    // date filter
    if (!empty($s_sdate) && !empty($s_edate)) {
        $search_condition .= " AND (DATE(E.expenses_date) BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
    } elseif (!empty($s_sdate)) {
        $search_condition .= " AND (DATE(E.expenses_date) >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
    } elseif (!empty($s_edate)) {
        $search_condition .= " AND (DATE(E.expenses_date) <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
    }

    if (!is_superadmin($_SESSION['session_usergroupname'])) {
        $search_condition .= (!empty($_SESSION['session_user_branch_id'])) ? " AND E.branch_id = '" . (int)$_SESSION['session_user_branch_id'] . "'" : "";
    }

    $query = "
        SELECT 
            B.id AS branch_id,     
            DATE(MIN(E.expenses_date)) AS report_date_min,
            DATE(MAX(E.expenses_date)) AS report_date,
            CONCAT(SB.license_no,'-',SB.id,'-',DATE(MAX(E.expenses_date))) AS id,
            DATE_FORMAT(MAX(E.expenses_date),'%d-%b-%Y') AS trans_date,
            SB.name AS sub_branch_name,
            CONCAT(SB.name,' - (',COALESCE(SB.sub_branch_code,''),')') AS sub_details,
            SB.id AS sub_branch_id,
            SB.license_no,
            B.name AS branch_name,

            -- ✅ Opening balance with category + subcategory filter
            (
                SELECT E2.opening_balance
                FROM mf_expenses E2
                WHERE E2.branch_id = B.id
                  AND E2.license_no = SB.license_no
                  AND DATE(E2.expenses_date) >= '" . date("Y-m-d", strtotime($s_sdate)) . "'
                  " . (!empty($s_category) ? " AND E2.category_id = " . (int)$s_category : "") . "
                  " . (!empty($s_sub_category) ? " AND E2.sub_category_id = " . (int)$s_sub_category : "") . "
                ORDER BY E2.expenses_date ASC, E2.id DESC
                LIMIT 1
            ) AS opening_balance,

            SUM(CASE WHEN E.cash_type = 'Cash In' THEN E.amount ELSE 0 END) AS cash_in,
            SUM(CASE WHEN E.cash_type = 'Cash Out' THEN E.amount ELSE 0 END) AS cash_out,

            (
                (
                    SELECT E2.opening_balance
                    FROM mf_expenses E2
                    WHERE E2.branch_id = B.id
                      AND E2.license_no = SB.license_no
                      AND DATE(E2.expenses_date) >= '" . date("Y-m-d", strtotime($s_sdate)) . "'
                      " . (!empty($s_category) ? " AND E2.category_id = " . (int)$s_category : "") . "
                      " . (!empty($s_sub_category) ? " AND E2.sub_category_id = " . (int)$s_sub_category : "") . "
                    ORDER BY E2.expenses_date ASC, E2.id DESC
                    LIMIT 1
                )
                + SUM(CASE WHEN E.cash_type = 'Cash In' THEN E.amount ELSE 0 END)
                - SUM(CASE WHEN E.cash_type = 'Cash Out' THEN E.amount ELSE 0 END)
            ) AS closing_balance

        FROM mf_expenses AS E
        LEFT JOIN mf_branch AS B ON B.id = E.branch_id
        LEFT JOIN mf_sub_branch AS SB ON SB.id = E.sub_branch_id
        WHERE 1 ".$search_condition."
        GROUP BY B.id, SB.license_no 
        ORDER BY MAX(E.expenses_date) DESC
    ";
    //  echo "<pre>"; print_r($query); 
    

    return $app->getrows($query);
}


    /*
    * subListview: returns detailed expense rows for a selected branch + date (used by ajax/sublist expansion, export and preview)
    */
public static function subListview($sdate = NULL, $edate = NULL, $branch_id = 0, $sub_branch_id = 0, $category_id = 0, $sub_category_id = 0) {
    global $app;

    $condition = "";
    if (!empty($sdate)) {
        $condition .= " AND DATE(E.expenses_date) >= '" . date('Y-m-d', strtotime($sdate)) . "'";
    }
    // print_r($sdate);
    // exit;
    if (!empty($edate)) {
        $condition .= " AND DATE(E.expenses_date) <= '" . date('Y-m-d', strtotime($edate)) . "'";
    }
    if (!empty($branch_id)) {
        $condition .= " AND E.branch_id = " . (int)$branch_id;
    }
    if (!empty($sub_branch_id)) {
        $data = $app->getrow("SELECT license_no FROM mf_sub_branch WHERE id = ".(int)$sub_branch_id);
        $condition .= " AND E.license_no = '" . $data['license_no']."'";
    }
    
    if (!empty($category_id)) {         
        $condition .= " AND E.category_id = " . (int)$category_id;
    }
    if (!empty($sub_category_id)) {
        $condition .= " AND E.sub_category_id = " . (int)$sub_category_id;
    }

$sql = "SELECT
            E.category_id,
            E.sub_category_id,
            E.license_no,
            E.sub_branch_id,
            DATE(E.expenses_date) AS report_date,
            B.name AS branch_name,
            SB.name AS sub_branch_name,
            E.description,
            C.name AS category_name,
            SC.name AS sub_category_name,
            E.opening_balance,   -- ✅ include here
            (CASE WHEN E.cash_type = 'Cash In'  THEN E.amount ELSE 0 END) AS cash_in,
            (CASE WHEN E.cash_type = 'Cash Out' THEN E.amount ELSE 0 END) AS cash_out  
        FROM mf_expenses AS E
        LEFT JOIN mf_branch AS B ON B.id = E.branch_id
        LEFT JOIN mf_sub_branch AS SB ON SB.license_no = E.license_no
        LEFT JOIN mf_expenses_category AS C ON C.id = E.category_id
        LEFT JOIN mf_expenses_sub_category AS SC ON SC.id = E.sub_category_id
        WHERE 1=1 ".$condition."
        GROUP BY 
            E.id,
            E.branch_id,
            E.license_no,            
            E.category_id, 
            E.sub_category_id, 
            E.expenses_date
        ORDER BY report_date ";




    // Debug SQL
    // echo $sql; exit;

        // echo "<pre>"; print_r($sql); die;

    return $app->getrows($sql);
}


    /*
    * Export listing
    */
    public static function Listexport() {
        $args         = func_get_args();
        $app          = $GLOBALS['app'];
        $s_branch     = isset($args[0]) ? $args[0] : "";
        $s_sdate      = isset($args[1]) ? $args[1] : "";
        $s_edate      = isset($args[2]) ? $args[2] : "";
        $s_sub_branch = isset($args[3]) ? $args[3] : "";

        $search_condition = "";

        // Default last 3 days
        if ($s_sdate == "" && $s_edate == "") {
            $search_condition .= " AND (E.expenses_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";
        }

        if ($s_branch != "") {
            $search_condition .= " AND (E.branch_id = '" . addslashes($s_branch) . "')";
        }

        if (is_array($s_sub_branch) && count($s_sub_branch) > 0 && $s_sub_branch[0] > 0) {
            $sub_branch_ids = implode(',', array_map('intval', $s_sub_branch));
            $search_condition .= " AND (E.sub_branch_id IN(" . $sub_branch_ids . "))";
        }

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND (DATE(E.expenses_date) BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND (DATE(E.expenses_date) >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND (DATE(E.expenses_date) <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        }

        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            if (!empty($_SESSION['session_user_branch_id'])) {
                $search_condition .= " AND E.branch_id = '" . (int)$_SESSION['session_user_branch_id'] . "'";
            }
        }

        $query = "SELECT 
                    CONCAT(B.id,'-',DATE(E.expenses_date)) AS id,
                    B.id AS branch_id,
                    DATE(E.expenses_date) AS report_date,
                    DATE_FORMAT(DATE(E.expenses_date),'%d-%b-%Y') AS trans_date,
                    SUM(E.amount) AS total_expenses,
                    COUNT(E.id) AS no_of_records,
                    B.name AS branch_name
                FROM mf_expenses AS E
                LEFT JOIN mf_branch AS B ON B.id = E.branch_id
                LEFT JOIN mf_sub_branch AS SB ON SB.id = E.sub_branch_id
                WHERE 1 " . $search_condition . "
                GROUP BY DATE(E.expenses_date), B.id
                ORDER BY DATE(E.expenses_date) DESC";

        return $app->getrows($query);
    }

    // private static function tallyData($reports) {
    //     // kept for compatibility with other code; original logic expects fields opening_balance/closing_balance/amnt
    //     $tally = array();
    //     $total_cash_out = 0;
    //     $di = 1;

    //     foreach ($reports as $key => $rows) {
    //         $opening_amount = floatval(isset($rows['opening_balance']) ? $rows['opening_balance'] : 0);
    //         $closing_amount = floatval(isset($rows['closing_balance']) ? $rows['closing_balance'] : 0);
    //         $id             = isset($rows['report_date']) ? $rows['report_date'] : (isset($rows['created_at']) ? $rows['created_at'] : '');

    //         if ($di == 1) {
    //             $tally[$id . '_01_Opening']['particulars'] = 'Opening Balance';
    //             $tally[$id . '_01_Opening']['cash_in']     = $opening_amount;
    //             $tally[$id . '_01_Opening']['cash_out']    = '';
    //         }

    //         $amt = isset($rows['amnt']) ? $rows['amnt'] : (isset($rows['amount']) ? $rows['amount'] : 0);

    //         $tally[$id . '_05_Expenses']['particulars'] = 'Expenses';
    //         $tally[$id . '_05_Expenses']['cash_in']     = '';
    //         $tally[$id . '_05_Expenses']['cash_out']    = $amt;
    //         $total_cash_out += $amt;

    //         if (($key + 1) == count($reports)) {
    //             $closing_amount = ($opening_amount - $total_cash_out);
    //             $tally[$id . '_09_Total']['particulars']  = 'Total';
    //             $tally[$id . '_09_Total']['cash_in']      = '';
    //             $tally[$id . '_09_Total']['cash_out']     = $total_cash_out;

    //             $tally[$id . '_12_Closing']['particulars'] = 'Closing Balance';
    //             $tally[$id . '_12_Closing']['cash_in']     = '';
    //             $tally[$id . '_12_Closing']['cash_out']    = $closing_amount;
    //         }

    //         $di++;
    //     }

    //     if (!empty($tally)) ksort($tally);
    //     return $tally;
    // }

}

?>
