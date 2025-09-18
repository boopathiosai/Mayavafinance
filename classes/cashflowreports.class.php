<?php

//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Pledge class
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Subash S
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  15-Jun-2011
//**************************************************************************
// Description      :  This is the Product Pledging class  for Mayavafinance.
//**************************************************************************


class CashFlowReports {
    /*
     * Define the required fields and return text
     */

    public static function ListviewCount() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $args = func_get_args();

        $app = $GLOBALS['app'];



        $s_branch = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_trans_type = $args[3];
        $s_trans_key = $args[4];
        $type_of_page = $args[5]; // Added by DSSK 05_MAR_2018


        $search_condition = ($s_trans_key != "") ? " AND  ( CF.cash_purpose LIKE '%" . $s_trans_key . "%')" : "";
        // Added by DSSK 05_MAR_2018
        if ($type_of_page == 1) {
            $search_condition .= " AND  (CF.date_transaction BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";
        }
        // END    
        $search_condition .= ($s_trans_type != "") ? "AND (CF.transaction_type LIKE '" . $s_trans_type . "')" : "";
        $search_condition .= ($s_branch != "") ? "AND ( CF.branch_id  = '" . $s_branch . "' )  " : "";

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }


        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND  CF.branch_id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }

        /*
         * Return the Customers list
         */
        $query = "SELECT

                DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date

                FROM " . CASH_FLOW_TRANSACTIONS . " as CF
                LEFT JOIN " . BRANCH . " AS B ON B.id = CF.branch_id

                WHERE 1 " . $search_condition . " AND CF.parent_id = 0
                GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'), B.id, CF.cash_purpose
                ORDER BY CF.date_transaction, B.name DESC ";

        $data = $app->getrows($query);

        return $count = count($data);
    }

    public static function Listview() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $args = func_get_args();

        $app = $GLOBALS['app'];
        $paging = $GLOBALS['paging'];

        $s_branch = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_trans_type = $args[3];
        $s_trans_key = $args[4];
        $type_of_page = $args[5];


        $search_condition = ($s_trans_key != "") ? " AND  ( CF.cash_purpose LIKE '%" . $s_trans_key . "%')" : "";
        // Added by DSSK 05_MAR_2018
        if ($type_of_page == 1 || ($s_sdate == "" && $s_edate == "")) {
            $search_condition .= " AND  (CF.date_transaction BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";
        }
        // END
        $search_condition .= ($s_trans_type != "") ? "AND (CF.transaction_type LIKE '" . $s_trans_type . "')" : "";
        $search_condition .= ($s_branch != "") ? "AND ( CF.branch_id  = '" . $s_branch . "' )  " : "";


        $sub_search_condition = ($s_trans_key != "") ? " AND  ( sCF.cash_purpose LIKE '%" . $s_trans_key . "%')" : "";
        $sub_search_condition .= ($s_trans_type != "") ? "AND (sCF.transaction_type LIKE '" . $s_trans_type . "')" : "";

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }


        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND  CF.branch_id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }

        /*
         * Return the Customers list count
         */
        $count = self::ListviewCount($s_branch, $s_sdate, $s_edate, $s_trans_type, $s_trans_key, $type_of_page);

        if ($count > 0) {
            /*
             * Return the Customers list
             */
            Paging::set_max($count);
            Paging::calc(); // calculation for paging

            $query = "SELECT SUM(CF.amount) as amount,P.loan_id,group_concat(CF.id) as purpose_id,CF.transaction_type,CF.closing_balance,CF.opening_balance,
                CONCAT(B.id,'-',DATE_FORMAT(CF.date_transaction,'%Y-%m-%d')) AS id,
                DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                B.id AS branch_id,
                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,
                B.name AS branch_name,CF.cash_purpose,
                '" . addslashes($search_condition) . "' AS search_condition
                FROM " . CASH_FLOW_TRANSACTIONS . " as CF
                LEFT JOIN " . BRANCH . " AS B ON B.id = CF.branch_id
                LEFT JOIN " . PLEDGE . " AS P ON P.branch_id = B.id AND P.cash_flow_id = CF.id
                WHERE 1 " . $search_condition . " AND CF.parent_id = 0
                GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'), CF.cash_purpose, B.id
                ORDER BY CF.date_transaction ASC " . Paging::get_sql_limit();
            //echo "<pre>"; echo $query."<hr>";die;
            //return $app->getrows($query);

            $preresult = $app->getrows($query);
            $result = self::listResultData($preresult);


            return $result;
        }
    }

    public static function subListviewCount() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();

        $app = $GLOBALS['app'];

        $s_branch = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_trans_type = $args[3];
        $s_trans_key = $args[4];


        $search_condition = ($s_trans_key != "") ? " AND  ( CF.cash_purpose LIKE '%" . $s_trans_key . "%')" : "";
        $search_condition .= ($s_trans_type != "") ? "AND (CF.transaction_type LIKE '" . $s_trans_type . "')" : "";
        $search_condition .= ($s_branch != "") ? "AND ( CF.branch_id  = '" . $s_branch . "' )  " : "";

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }

        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND  CF.branch_id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }


        /*
         * Return the Pledges list Count
         */
        $query = "SELECT CF.id

                FROM " . CASH_FLOW_TRANSACTIONS . " as CF
                LEFT JOIN " . BRANCH . " AS B ON B.id = CF.branch_id

                WHERE 1 " . $search_condition . "
                ORDER BY CF.date_transaction, B.name DESC";

        $data = $app->getrows($query);


        //$dates = self::ListDateRange('2011-10-01','2011-11-08');
        //print_r($dates);


        return $count = count($data);
    }

    public static function subListview() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_branch = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_trans_type = $args[3];
        $s_trans_key = $args[4];


        $search_condition = ($s_trans_key != "") ? " AND  ( CF.cash_purpose LIKE '%" . $s_trans_key . "%')" : "";
        $search_condition .= ($s_trans_type != "") ? "AND (CF.transaction_type LIKE '" . $s_trans_type . "')" : "";
        $search_condition .= ($s_branch != "") ? "AND ( CF.branch_id  = '" . $s_branch . "' )  " : "";

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }
        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND  CF.branch_id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }

        $count = self::subListviewCount($s_branch, $s_sdate, $s_edate, $s_trans_type, $s_trans_key);
        /*
         * Return the Pledge list
         */

        /*
          (SELECT OCF.opening_balance FROM  ".CASH_FLOW_TRANSACTIONS." AS OCF WHERE OCF.branch_id = P.branch_id  AND DATE_FORMAT(OCF.date_transaction,'%Y-%m-%d') = DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') ORDER BY OCF.date_transaction DESC LIMIT 0,1 )  AS sub_opening_balance,
          (SELECT CCF.closing_balance FROM  ".CASH_FLOW_TRANSACTIONS." AS CCF WHERE CCF.branch_id = P.branch_id  AND DATE_FORMAT(CCF.date_transaction,'%Y-%m-%d') = DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') ORDER BY CCF.date_transaction DESC LIMIT 0,1 )  AS sub_closing_balance,
         */

        if ($count > 0) {
            Paging::set_max($count);
            Paging::calc(); // calculation for paging
            $query = "SELECT
                        B.name as branch_name, CF.id AS cash_flow_id,
                        CONCAT(B.name,'-',CF.id) AS branch_name1,
                        DATE_FORMAT(CF.date_transaction,'%d-%b-%Y %I:%i %p') AS report_date,
                        CF.opening_balance AS opening_balance,
                        CF.transaction_type AS type,
                        IF(CF.transaction_type = 'cashin', CF.amount, 0.00) AS cash_in,
                        IF(CF.transaction_type = 'cashout', CF.amount, 0.00) AS cash_out,
                        CF.cash_purpose AS purpose,
                        IF(CF.transaction_type = 'cashin', CF.cash_purpose, '-') AS cashin_purpose,
                        IF(CF.transaction_type = 'cashout', CF.cash_purpose, '-') AS cashout_purpose,
                        CF.closing_balance  AS closing_balance

                FROM " . CASH_FLOW_TRANSACTIONS . " as CF
                LEFT JOIN " . BRANCH . " AS B ON B.id = CF.branch_id

                WHERE 1 " . $search_condition . "
                ORDER BY CF.date_transaction, B.name DESC";
            //echo $query."<hr>";
            //exit;
            return $app->getrows($query);
        }
    }

    public static function Listexport() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $args = func_get_args();

        $app = $GLOBALS['app'];
        $paging = $GLOBALS['paging'];

        $s_branch = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_trans_type = $args[3];
        $s_trans_key = $args[4];
        $type_of_page = $args[5];

        if ($s_sdate == "" && $s_edate == "") {
//            $search_condition .= " AND  (P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())"; // Command by DSSK for slow query issue 19_SEP_2018
            $search_condition .= " AND  (CF.date_transaction BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";
        }
        $search_condition .= ($s_trans_key != "") ? " AND  ( CF.cash_purpose LIKE '%" . $s_trans_key . "%')" : "";

        $search_condition .= ($s_trans_type != "") ? "AND (CF.transaction_type LIKE '" . $s_trans_type . "')" : "";
        $search_condition .= ($s_branch != "") ? "AND ( CF.branch_id  = '" . $s_branch . "' )  " : "";


        $sub_search_condition = ($s_trans_key != "") ? " AND  ( sCF.cash_purpose LIKE '%" . $s_trans_key . "%')" : "";
        $sub_search_condition .= ($s_trans_type != "") ? "AND (sCF.transaction_type LIKE '" . $s_trans_type . "')" : "";

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }


        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND  CF.branch_id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }
        // Added by DSSK 05_MAR_2018
        if ($type_of_page == 1) {
            $search_condition_aa = " AND  (CF.date_transaction BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";
        } else {
            $search_condition_aa = "";
        }
        // END
        /*
         * Return the Customers list count
         */
        $count = self::ListviewCount($s_branch, $s_sdate, $s_edate, $s_trans_type, $s_trans_key, $type_of_page);

        if ($count > 0) {
            /*
             * Return the Customers list
             */
            Paging::set_max($count);
            Paging::calc(); // calculation for paging

            $query = "SELECT CF.amount as amount,P.loan_id as loan_id,CF.id as purpose_id,CF.transaction_type,CF.opening_balance,CF.closing_balance,
                CONCAT(B.id,'-',DATE_FORMAT(CF.date_transaction,'%Y-%m-%d')) AS id,
                DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                B.id AS branch_id,CF.module_type,
                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,
                B.name AS branch_name,CF.cash_purpose,PA.amount_to_pay,PA.auction_amount,PA.profit_status,
                /*Added by manimaran*/ R.interest_amount as interest,/*END*/
                '" . addslashes($search_condition) . "' AS search_condition
                FROM " . CASH_FLOW_TRANSACTIONS . " as CF
                LEFT JOIN " . BRANCH . " AS B ON B.id = CF.branch_id
        LEFT JOIN " . RENEWAL . " AS PR ON PR.cash_in_id = CF.id
        LEFT JOIN " . PRODUCT_INTERESTS . " AS PT ON PT.cash_flow_id = CF.id
        LEFT JOIN " . PRODUCT_AUCTION . " AS PA ON PA.cash_in_id = CF.id
        LEFT JOIN " . PAY_EXCESS_AMOUNT . " AS PE ON PE.cash_in_id = CF.id
        LEFT JOIN " . PARTIAL_AMOUNT_PAYMENT . " AS PP ON PP.cash_in_id = CF.id
        LEFT JOIN " . REDEMPTION . " AS R ON R.cash_flow_id = CF.id
        LEFT JOIN " . PLEDGE . " AS P ON P.cash_flow_id=CF.id             
            OR P.id = PR.old_loan_id 
            OR PT.loan_id= P.id 
            OR PA.loan_id = P.id 
            OR PE.old_loan_id=P.id 
            OR PP.old_loan_id=P.id            
                WHERE 1 " . $search_condition . $search_condition_aa . " AND CF.parent_id = 0
                GROUP BY CF.id, B.id
                ORDER BY CF.date_transaction ASC";
//                echo $query;exit;
           // $preresult = $app->getexport($query);
            //echo ""
            // Changed by DSSK for slowness 19_SEP_2018            
            /*$resultCount = count($preresult);
            if ($resultCount == 0) {
                $preresult[] = '';
            }*/

            //print_r($query); exit;

            $preresult = $app->getrows($query);
            $result = self::listResultData($preresult);

            //$result = self::listResultData($preresult);


            return $result;
        }
    }

    private static function resultData($reports) {

        global $app;

        $result = array();

        if (count($reports) > 0) {

            $dpi = 0;
            $di = 1;

            foreach ($reports as $rows) {
                /** Set Result data for the record * */
                $opening_amount = floatval($rows['opening_balance']);
                $closing_amount = floatval($rows['closing_balance']);

                $cash_in = floatval($rows['cash_in']);
                $cash_out = floatval($rows['cash_out']);

                $cashin_purpose = $rows['cashin_purpose'];
                $cashout_purpose = $rows['cashout_purpose'];
                $purpose = $rows['purpose'];


                $cash_purpose = '';


                $loan_id = 0;
                $pquery = "SELECT id FROM " . PLEDGE . " WHERE cash_flow_id = " . $rows['cash_flow_id'];
                $pcid = $app->getrow($pquery);
                if ((int) $pcid[0] > 0) {
                    $cash_purpose = CASHFLOW_PURPOSE_PLEDGED;
                    $loan_id = (int) $pcid[0];
                } else {

                    $rquery = "SELECT loan_id FROM " . REDEMPTION . " WHERE cash_flow_id =  " . $rows['cash_flow_id'];
                    $rcid = $app->getrow($rquery);
                    if ((int) $rcid[0] > 0) {
                        $cash_purpose = CASHFLOW_PURPOSE_REDEEMED;
                        $loan_id = (int) $rcid[0];
                    } else {

                        $apquery = "SELECT loan_id FROM " . PRODUCT_AUCTION . " WHERE cash_in_id =  " . $rows['cash_flow_id'];
                        $apcid = $app->getrow($apquery);
                        if ((int) $apcid[0] > 0) {
                            $cash_purpose = AUCTION_AMOUNT;
                            $loan_id = (int) $apcid[0];
                        } else {

                            $alquery = "SELECT loan_id FROM " . PRODUCT_AUCTION . " WHERE cash_out_id = " . $rows['cash_flow_id'];
                            $alcid = $app->getrow($alquery);
                            if ((int) $alcid[0] > 0) {
                                $cash_purpose = AUCTION_AMOUNT_LOSS;
                                $loan_id = (int) $alcid[0];
                            } else {

                                //
                                $piquery = "SELECT loan_id FROM " . PRODUCT_INTERESTS . " WHERE cash_flow_id = " . $rows['cash_flow_id'];
                                $picid = $app->getrow($piquery);
                                if ((int) $picid[0] > 0) {
                                    $cash_purpose = PAY_INTEREST;
                                    $loan_id = (int) $picid[0];
                                }
                            }
                        }
                    }
                }

                if ((int) $loan_id > 0) {
                    $pcquery = "SELECT loan_id FROM mf_pledge WHERE id = " . (int) $loan_id;
                    $pcode = $app->getrow($pcquery);
                    $loan_code = $pcode[0];
                }
                if ($cash_purpose != '' && (int) $loan_id > 0 && (int) $cash_in > 0)
                    $cashin_purpose = $cash_purpose . " - (Loan ID : " . $loan_code . ")";

                if ($cash_purpose != '' && (int) $loan_id > 0 && (int) $cash_out > 0)
                    $cashout_purpose = $cash_purpose . " - (Loan ID : " . $loan_code . ")";

                $rows['cashin_purpose'] = $cashin_purpose;
                $rows['cashout_purpose'] = $cashout_purpose;
                $rows['loan_code'] = $loan_code;
                $rows['opening_amount'] = $opening_amount;
                $rows['closing_amount'] = $closing_amount;

                $rows['cash_in'] = $cash_in;
                $rows['cash_out'] = $cash_out;

                $result[] = $rows;
            }
        }else {
            $result = $reports;
        }

        return $result;
    }

    private static function listResultData($reports) {

        global $app;

        $result = array();

        if (count($reports) > 0) {

            $dpi = 0;
            $di = 1;

            foreach ($reports as $rows) {


                $purpose_id = explode(',', $rows['purpose_id']);

                $purpose_count = count($purpose_id);

                if ($purpose_count > 1) {
                    $reduce_val = '';
                    foreach ($purpose_id as $key => $val) {

                        $parent_id = $val + 1;
                        $purpose_query = "SELECT SUM(sCF.amount) as amount FROM " . CASH_FLOW_TRANSACTIONS . " AS sCF WHERE (parent_id =  " . $val . " OR parent_id =  " . $parent_id . ") AND sCF.branch_id = " . $rows['branch_id'] . " AND DATE_FORMAT(sCF.date_transaction,'%Y-%m-%d') = DATE_FORMAT('" . $rows['report_date'] . "','%Y-%m-%d')AND sCF.transaction_type = 'cashin' GROUP BY DATE_FORMAT(sCF.date_transaction,'%Y-%m-%d') LIMIT 0,1";



                        $purpose_row = $app->getrow($purpose_query);

                        $reduce_val = $purpose_row['amount'] + $reduce_val;
                    }
                } else {

                    if ($rows['cash_purpose'] == "Auction") {
                        $parent_id = $rows['purpose_id'];
                        $auc_loan_query = "SELECT SUM(P.loan_amount) as amount FROM " . PRODUCT_AUCTION . " AS PA
										  LEFT JOIN " . PLEDGE . " AS P ON P.id = PA.loan_id WHERE PA.cash_in_id = " . $parent_id;

                        $auc_loan_row = $app->getrow($auc_loan_query);
                        $auc_loan_val = $auc_loan_row[0];
                        $margin_auc_amount = $rows['auction_amount'];
                    }
                }



                /////*-- Edited By Vinoth *////


                $rows['loan_amount'] = floatval($rows['amount']);
                $rows['opening_balance'] = ($closing_to_opening) ? $closing_to_opening : $rows['opening_balance'];

                if (($rows['profit_status'] == 1 || $rows['profit_status'] == 0) && ($rows['cash_purpose'] == 'Auction')) {

                    //$rows['amount'] = $rows['auction_amount'] - $rows['amount_to_pay'];
                    $rows['cash_purpose'] = 'Auction Sale :- Principle';
                    $rows['amount'] = $auc_loan_val;
                    $rows['loan_amount'] = $rows['amount'];
                } elseif ($rows['cash_purpose'] == 'Auction') {

                    $rows['cash_purpose'] = 'Auction Sale :- Principle';
                    $rows['amount'] = $auc_loan_val;
                    //$rows['amount'] = $rows['amount_to_pay'] - $rows['auction_amount'];
                    //$rows['transaction_type'] = 'cashout';
                    $rows['loan_amount'] = $rows['amount'];
                }

                if ($rows['cash_purpose'] == 'Auction Service Charge') {
                    if ($auc_loan_val > $margin_auc_amount)
                        $rows['amount'] = $auc_loan_val - $margin_auc_amount;
                    elseif ($margin_auc_amount > $auc_loan_val)
                        $rows['amount'] = $margin_auc_amount - $auc_loan_val;

                    $rows['cash_purpose'] = 'Auction Sale :- Margin';
                    $rows['transaction_type'] = 'cashin';
                    $rows['loan_amount'] = $rows['amount'];
                }


                $trans_amount = floatval($rows['amount']);
                $transaction_type = $rows['transaction_type'];


                if ($transaction_type == 'cashin') {
                    $rows['cash_in'] = $trans_amount + $reduce_val;
                    $cash_in = floatval($rows['cash_in']);
                    $rows['amount'] = $cash_in;
                    $closing_balance = $rows['opening_balance'] + $cash_in;
                }

                if ($transaction_type == 'cashout') {
                    $rows['cash_out'] = $trans_amount - $reduce_val;
                    $cash_out = floatval($rows['cash_out']);
                    $rows['amount'] = $cash_out;
                    $closing_balance = $rows['opening_balance'] - $cash_out;
                }


                $rows['closing_balance'] = floatval($closing_balance);
                $closing_to_opening = $closing_balance;

                $result[] = $rows;
            }
        } else {
            $result = $reports;
        }

        return array_reverse($result);
    }

}
