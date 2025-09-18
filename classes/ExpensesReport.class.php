<?php
class ExpensesReport{

    /* ========= EXPENSES-ONLY: Count ========= */
    public static function ListviewCount()
    {
        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_branch     = $args[0];
        $s_sdate      = $args[1];
        $s_edate      = $args[2];
        $s_sub_branch = $args[3];
        $type_of_page = $args[4];

        $search_condition = " ";

        if ($type_of_page == 1) {
            $search_condition .= " AND ( DATE_FORMAT(ME.created_at, '%Y-%m-%d') BETWEEN DATE_SUB(NOW(), INTERVAL 3 DAY) AND NOW() )";
        }

        $search_condition .= ($s_branch != "") ? " AND (ME.branch_id = '".$s_branch."')" : "";

        if ((is_array($s_sub_branch)) && !empty($s_sub_branch) && (int)$s_sub_branch[0] > 0) {
            $ids = implode(',', array_map('intval', $s_sub_branch));
            $search_condition .= " AND (ME.sub_branch_id IN (".$ids."))";
        }

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND (DATE(ME.created_at) BETWEEN '".date('Y-m-d', strtotime($s_sdate))."' AND '".date('Y-m-d', strtotime($s_edate))."')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND (DATE(ME.created_at) >= '".date('Y-m-d', strtotime($s_sdate))."')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND (DATE(ME.created_at) <= '".date('Y-m-d', strtotime($s_edate))."')";
        }

        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")
                ? " AND ME.branch_id = '".$_SESSION['session_user_branch_id']."'" : "";
        }

        $query = "
            SELECT COUNT(DISTINCT CONCAT(ME.branch_id,'-',ME.sub_branch_id,'-',DATE(ME.created_at)))
            FROM ".EXPENSES." AS ME
            WHERE 1 ".$search_condition;

        $data = $app->getrow($query);
        return (int)$data[0];
    }

    /* ========= EXPENSES-ONLY: List (header rows per branch-sub_branch-date) ========= */
    public static function Listview()
    {
        $args = func_get_args();
        $app   = $GLOBALS['app'];
        $paging = $GLOBALS['paging'];

        $s_branch     = $args[0];
        $s_sdate      = $args[1];
        $s_edate      = $args[2];
        $s_sub_branch = $args[3];
        $type_of_page = $args[4];

        $search_condition = " ";
        if ($type_of_page == 1) {
            $search_condition .= " AND ( DATE_FORMAT(ME.created_at, '%Y-%m-%d') BETWEEN DATE_SUB(NOW(), INTERVAL 3 DAY) AND NOW() )";
        }

        $search_condition .= ($s_branch != "") ? " AND (ME.branch_id = '".$s_branch."')" : "";

        if ((is_array($s_sub_branch)) && !empty($s_sub_branch) && (int)$s_sub_branch[0] > 0) {
            $ids = implode(',', array_map('intval', $s_sub_branch));
            $search_condition .= " AND (ME.sub_branch_id IN (".$ids."))";
        }

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND (DATE(ME.created_at) BETWEEN '".date('Y-m-d', strtotime($s_sdate))."' AND '".date('Y-m-d', strtotime($s_edate))."')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND (DATE(ME.created_at) >= '".date('Y-m-d', strtotime($s_sdate))."')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND (DATE(ME.created_at) <= '".date('Y-m-d', strtotime($s_edate))."')";
        }

        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")
                ? " AND ME.branch_id = '".$_SESSION['session_user_branch_id']."'" : "";
        }

        $count = self::ListviewCount($s_branch, $s_sdate, $s_edate, $s_sub_branch, $type_of_page);
        if ($count <= 0) { return array(); }

        Paging::set_max($count);
        Paging::calc();

        $query = "
                    SELECT 
    ME.branch_id,
    ME.sub_branch_id,
    DATE(ME.created_at) AS report_date,
    CONCAT(ME.branch_id,'-',ME.sub_branch_id) AS id,
    DATE_FORMAT(DATE(ME.created_at),'%d-%b-%Y') AS trans_date,
    SUM(IF(ME.cash_type='cashin',  ME.amount, 0)) AS total_cashin,
    SUM(IF(ME.cash_type='cashout', ME.amount, 0)) AS total_cashout,
    ME.opening_balance,
    ME.closing_balance
FROM mf_expenses AS ME
INNER JOIN (
    SELECT branch_id, sub_branch_id, MAX(DATE(created_at)) AS latest_date
    FROM mf_expenses
    WHERE 1 ".$search_condition."
    GROUP BY branch_id, sub_branch_id
) AS sq
    ON ME.branch_id = sq.branch_id
   AND ME.sub_branch_id = sq.sub_branch_id
   AND DATE(ME.created_at) = sq.latest_date
WHERE 1 ".$search_condition."
GROUP BY ME.branch_id, ME.sub_branch_id
ORDER BY report_date DESC";

    echo $query;
        return $app->getrows($query);
    }

    /* ========= EXPENSES-ONLY: Sub Count (by particulars/rows) ========= */
    public static function subListviewCount()
{
    $args = func_get_args();
    $app = $GLOBALS['app'];

    $s_branch     = $args[0];
    $s_sdate      = $args[1];
    $s_edate      = $args[2];
    $s_sub_branch = $args[3]; // add this

    $search_condition = " ";
    $search_condition .= ($s_branch != "") ? " AND (ME.branch_id = '".$s_branch."')" : "";

    if ((is_array($s_sub_branch)) && !empty($s_sub_branch) && (int)$s_sub_branch[0] > 0) {
        $ids = implode(',', array_map('intval', $s_sub_branch));
        $search_condition .= " AND (ME.sub_branch_id IN (".$ids."))";
    }

    if ($s_sdate != "" && $s_edate != "") {
        $search_condition .= " AND (DATE(ME.created_at) BETWEEN '".date('Y-m-d', strtotime($s_sdate))."' AND '".date('Y-m-d', strtotime($s_edate))."')";
    } elseif ($s_sdate != "" && $s_edate == "") {
        $search_condition .= " AND (DATE(ME.created_at) >= '".date('Y-m-d', strtotime($s_sdate))."')";
    } elseif ($s_sdate == "" && $s_edate != "") {
        $search_condition .= " AND (DATE(ME.created_at) <= '".date('Y-m-d', strtotime($s_edate))."')";
    }

    if (!is_superadmin($_SESSION['session_usergroupname'])) {
        $search_condition .= ($_SESSION['session_user_branch_id'] != "")
            ? " AND ME.branch_id = '".$_SESSION['session_user_branch_id']."'" : "";
    }

    $query = "
        SELECT ME.id
        FROM ".EXPENSES." AS ME
        WHERE 1 ".$search_condition."
        GROUP BY ME.id";

    $rows = $app->getrows($query);
    return count($rows);
}

    /* ========= EXPENSES-ONLY: Sub List (detail rows for a branch-sub_branch-date) ========= */
    public static function subListview()
    {
        $args = func_get_args();
        $app   = $GLOBALS['app'];
        $paging = $GLOBALS['paging'];

        $s_branch     = $args[0];
        $s_sdate      = $args[1];
        $s_edate      = $args[2];
        $s_sub_branch = $args[3];
        $type_of_page = $args[4];

        $search_condition = " ";

        if ($type_of_page == 1) {
            $search_condition .= " AND ( DATE_FORMAT(ME.created_at, '%Y-%m-%d') BETWEEN DATE_SUB(NOW(), INTERVAL 3 DAY) AND NOW() )";
        }

        $search_condition .= ($s_branch != "") ? " AND (ME.branch_id = '".$s_branch."')" : "";

        if ((is_array($s_sub_branch)) && !empty($s_sub_branch) && !empty($s_sub_branch[0])) {
            $ids = implode(',', array_map('intval', $s_sub_branch));
            $search_condition .= " AND (ME.sub_branch_id IN (".$ids."))";
        }

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND (DATE(ME.created_at) BETWEEN '".date('Y-m-d', strtotime($s_sdate))."' AND '".date('Y-m-d', strtotime($s_edate))."')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND (DATE(ME.created_at) >= '".date('Y-m-d', strtotime($s_sdate))."')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND (DATE(ME.created_at) <= '".date('Y-m-d', strtotime($s_edate))."')";
        }

        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")
                ? " AND ME.branch_id = '".$_SESSION['session_user_branch_id']."'" : "";
        }

        $order_by = " ORDER BY ME.branch_id, DATE(ME.created_at), ME.id ";

        $query = "
            SELECT
                ME.id,
                DATE(ME.created_at) AS report_date,
                DATE_FORMAT(ME.created_at,'%d-%b-%Y') AS trans_date,
                ME.branch_id,
                ME.sub_branch_id,
                ME.category_id,
                ME.sub_category_id,
                ME.description AS particulars,
                ME.opening_balance,
                ME.closing_balance,
                IF(ME.cash_type='cashin',  ME.amount, 0) AS cash_in,
                IF(ME.cash_type='cashout', ME.amount, 0) AS cash_out,
                ME.amount,
                ME.cash_type
            FROM ".EXPENSES." AS ME
            WHERE 1 ".$search_condition.$order_by;

        return $app->getrows($query);
    }

    /* ========= EXPENSES-ONLY: Export ========= */
    public static function Listexport()
    {
        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_branch     = $args[0];
        $s_sdate      = $args[1];
        $s_edate      = $args[2];
        $s_sub_branch = $args[3];

        $search_condition = " ";
        $search_condition .= ($s_branch != "") ? " AND (ME.branch_id = '".$s_branch."')" : "";

        if ((is_array($s_sub_branch)) && !empty($s_sub_branch) && (int)$s_sub_branch[0] > 0) {
            $ids = implode(',', array_map('intval', $s_sub_branch));
            $search_condition .= " AND (ME.sub_branch_id IN (".$ids."))";
        }

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND (DATE(ME.created_at) BETWEEN '".date('Y-m-d', strtotime($s_sdate))."' AND '".date('Y-m-d', strtotime($s_edate))."')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND (DATE(ME.created_at) >= '".date('Y-m-d', strtotime($s_sdate))."')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND (DATE(ME.created_at) <= '".date('Y-m-d', strtotime($s_edate))."')";
        }

        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")
                ? " AND ME.branch_id = '".$_SESSION['session_user_branch_id']."'" : "";
        }

        $query = "
            SELECT
                CONCAT(ME.branch_id,'-',ME.sub_branch_id,'-',DATE(ME.created_at)) AS id,
                ME.branch_id AS branch_id,
                ME.sub_branch_id AS sub_branch_id,
                DATE(ME.created_at) AS report_date,
                DATE_FORMAT(ME.created_at,'%d-%b-%Y') AS trans_date,
                SUM(IF(ME.cash_type='cashin',  ME.amount, 0))  AS total_cashin,
                SUM(IF(ME.cash_type='cashout', ME.amount, 0)) AS total_cashout,
                SUM(ME.opening_balance) AS opening_balance,
                SUM(ME.closing_balance) AS closing_balance
            FROM ".EXPENSES." AS ME
            WHERE 1 ".$search_condition."
            GROUP BY DATE(ME.created_at), ME.branch_id, ME.sub_branch_id
            ORDER BY DATE(ME.created_at) DESC";

        return $app->getexport($query);
    }

    private static function tallyData($reports,$sub_branch_select,$sublist = true,$pledge_count,$new_array){
        $report_count = count($reports);
        if($report_count <= 0){ return array(); }

        $tally = array();
        $total_cash_in  = 0;
        $total_cash_out = 0;
        $di = 1; // row counter

        foreach($reports as $key => $rows){
            $opening_amount = floatval($rows['opening_balance']);
            $closing_amount = floatval($rows['closing_balance']);
            $cash_in  = floatval($rows['cash_in']);
            $cash_out = floatval($rows['cash_out']);
            $branch_id = $rows['branch_id'];
            $trans_date = $rows['trans_date'];
            $id = $rows['report_date'];
            $particulars = $rows['particulars'];

            $total_cash_in  += $cash_in;
            $total_cash_out += $cash_out;

            $cash_purpose_open  = CASHFLOW_PURPOSE_OPENING;
            $cash_purpose_close = CASHFLOW_PURPOSE_CLOSING;
            $cash_purpose_total = CASHFLOW_PURPOSE_TOTAL;

            if($di == 1){
                $order_id = '01';
                $tally[$id.'_'.$order_id.'_'.$cash_purpose_open] = array(
                    'id'          => $id,
                    'report_date' => $rows['report_date'],
                    'trans_date'  => $rows['trans_date'],
                    'order_id'    => $order_id,
                    'branch_id'   => $branch_id,
                    'branch_name' => $rows['branch_name'],
                    'particulars' => $cash_purpose_open,
                    'cash_in'     => $opening_amount,
                    'cash_out'    => ''
                );
            }

            $order_id = '08';
            $tally[$id.'_'.$order_id.'_'.$particulars] = array(
                'id'          => $id,
                'report_date' => $rows['report_date'],
                'trans_date'  => $rows['trans_date'],
                'order_id'    => $order_id,
                'branch_id'   => $branch_id,
                'branch_name' => $rows['branch_name'],
                'particulars' => $particulars,
                'cash_in'     => $cash_in,
                'cash_out'    => $cash_out
            );

            if(($key+1) == $report_count){
                $calc_closing = (($opening_amount+$total_cash_in)-($total_cash_out));

                $order_id = '09';
                $tally[$id.'_'.$order_id.'_'.$cash_purpose_total] = array(
                    'id'          => $id,
                    'report_date' => $rows['report_date'],
                    'trans_date'  => $rows['trans_date'],
                    'order_id'    => $order_id,
                    'branch_id'   => $branch_id,
                    'branch_name' => $rows['branch_name'],
                    'particulars' => $cash_purpose_total,
                    'cash_in'     => $total_cash_in,
                    'cash_out'    => $total_cash_out
                );

                $order_id = '12';
                $tally[$id.'_'.$order_id.'_'.$cash_purpose_close] = array(
                    'id'          => $id,
                    'report_date' => $rows['report_date'],
                    'trans_date'  => $rows['trans_date'],
                    'order_id'    => $order_id,
                    'branch_id'   => $branch_id,
                    'branch_name' => $rows['branch_name'],
                    'particulars' => $cash_purpose_close,
                    'cash_in'     => '',
                    'cash_out'    => $calc_closing
                );

                $total_cash_in  = 0;
                $total_cash_out = 0;
                $di = 0;
            }

            $di++;
        }

        ksort($tally);
        return $tally;
    }
}
?>
