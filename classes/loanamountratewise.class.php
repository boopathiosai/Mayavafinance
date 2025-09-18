<?php

//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Customer class
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Karthik B
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  29-Oct-2011
//**************************************************************************
// Description      :  This is the Customer class  for Mayavafinance.
//**************************************************************************
class LoanAmountRateWise {

    public function SubbranchSet($set_sb) {

        // Changed dssk sub branch privilege
        /*
         * Get Sub-Branch list
         */
        $app = $GLOBALS['app'];
        if ($_SESSION['session_usertype'] != 1 && $set_sb == 0) {
            $query_sub_branch = "SELECT id, branch_id, sub_branch_code FROM mf_sub_branch WHERE status = 1 AND branch_id  = " . $_SESSION['session_user_branch_id'];
            $data = $app->getrows($query_sub_branch);
            $sub_count = count($data);
            $find_id = "";
            for ($i = 0; $i < $sub_count; $i++) {
                $find_id .= $data[$i]['id'] . ',';
            }
            $find_id = rtrim($find_id, ',');

            return ($find_id == "" ? "" : " AND (P.sub_branch_id IN($find_id))");
        }
    }

    public static function ListviewCount() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $args = func_get_args();

        $app = $GLOBALS['app'];
        $s_sdate = $args[0];
        $s_edate = $args[1];
        $s_sloan_amt = $args[2];
        $s_eloan_amt = $args[3];
        $s_sloan_rate = $args[4];
        $s_eloan_rate = $args[5];
        $s_cid = $args[6];
        $s_branch = $args[7];
        $s_sub_branch = $args[8];
        $search_sloancount = $args[9];
        $search_eloancount = $args[10];

        $search_condition = "AND (C.id <> '' )";
        $search_condition .= ($s_branch != "") ? "AND ( P.branch_id  = '" . $s_branch . "' )  " : "";
        $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id  = '" . $s_sub_branch . "' )  " : "";
        $search_condition .= ($s_cid != "") ? "AND (C.id = '" . $s_cid . "' OR C.customer_id = '" . $s_cid . "')  " : "";

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }
 
        // if ($s_sloan_amt != "" && $s_eloan_amt != "") {
        //     $search_condition .= " AND  P.loan_amount BETWEEN '" . $s_sloan_amt . "' AND '" . $s_eloan_amt . "'";
        // } elseif ($s_sloan_amt != "" && $s_eloan_amt == "") {
        //     $search_condition .= " AND  P.loan_amount >= '" . $s_sloan_amt . "'";
        // } elseif ($s_sloan_amt == "" && $s_eloan_amt != "") {
        //     $search_condition .= " AND  P.loan_amount <= '" . $s_eloan_amt . "'";
        // }

        if ($s_sloan_rate != "" && $s_eloan_rate != "") {
            $search_condition .= " AND  (P.loan_amount/P.total_net_weight) BETWEEN '" . $s_sloan_rate . "' AND '" . $s_eloan_rate . "'";
        } elseif ($s_sloan_rate != "" && $s_eloan_rate == "") {
            $search_condition .= " AND  (P.loan_amount/P.total_net_weight) >= '" . $s_sloan_rate . "'";
        } elseif ($s_sloan_rate == "" && $s_eloan_rate != "") {
            $search_condition .= " AND  (P.loan_amount/P.total_net_weight) <= '" . $s_eloan_rate . "'";
        }

        $loan_amount_condition = self::SubQuery($s_sloan_amt, $s_eloan_amt, $search_sloancount, $search_eloancount, $s_cid, $s_branch, $s_sub_branch, $s_sdate, $s_edate);
        if ($_SESSION['session_usergroupname'] != 'Super Admin') {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND B.id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }

        // Sub Branch Privilege
        $set_sb = 0;
        $search_condition .= self::SubbranchSet($set_sb);

        /*
         * Return the Customers list
         */
        $query = "SELECT 
                C.id AS id
                
                FROM " . PLEDGE . " AS P 
                LEFT JOIN " . BRANCH . " AS B ON P.branch_id = B.id 
                LEFT JOIN " . SCHEME . " AS SC ON P.scheme_id = SC.id 
                LEFT JOIN " . CUSTOMERS . " AS C ON C.id = P.customer_id
                LEFT JOIN " . PRODUCT_AUCTION . " as PA on PA.loan_id = P.id
                LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id
                WHERE P.status=1  " . $search_condition . " 
                GROUP BY C.id". $loan_amount_condition;
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
        $s_sdate = $args[0];
        $s_edate = $args[1];
        $s_sloan_amt = $args[2];
        $s_eloan_amt = $args[3];
        $s_sloan_rate = $args[4];
        $s_eloan_rate = $args[5];
        $s_cid = $args[6];
        $s_branch = $args[7];
        $s_sub_branch = $args[8];
        $search_sloancount = $args[9];
        $search_eloancount = $args[10];

        $search_condition = "AND (C.id <> '' )";
        $search_condition .= ($s_branch != "") ? "AND ( P.branch_id  = '" . $s_branch . "' )  " : "";
        $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id  = '" . $s_sub_branch . "' )  " : "";
        $search_condition .= ($s_cid != "") ? "AND (C.id = '" . $s_cid . "' OR C.customer_id = '" . $s_cid . "')  " : "";


        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }

        // if ($s_sloan_amt != "" && $s_eloan_amt != "") {
        //     $search_condition .= " AND  P.loan_amount BETWEEN '" . $s_sloan_amt . "' AND '" . $s_eloan_amt . "'";
        // } elseif ($s_sloan_amt != "" && $s_eloan_amt == "") {
        //     $search_condition .= " AND  P.loan_amount >= '" . $s_sloan_amt . "'";
        // } elseif ($s_sloan_amt == "" && $s_eloan_amt != "") {
        //     $search_condition .= " AND  P.loan_amount <= '" . $s_eloan_amt . "'";
        // }

        if ($s_sloan_rate != "" && $s_eloan_rate != "") {
            $search_condition .= " AND  (P.loan_amount/P.total_net_weight) BETWEEN '" . $s_sloan_rate . "' AND '" . $s_eloan_rate . "'";
        } elseif ($s_sloan_rate != "" && $s_eloan_rate == "") {
            $search_condition .= " AND (P.loan_amount/P.total_net_weight) >= '" . $s_sloan_rate . "'";
        } elseif ($s_sloan_rate == "" && $s_eloan_rate != "") {
            $search_condition .= " AND (P.loan_amount/P.total_net_weight) <= '" . $s_eloan_rate . "'";
        }
       
        if ($_SESSION['session_usergroupname'] != 'Super Admin') {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND B.id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }
        // Sub Branch Privilege
        $set_sb = 0;
        $search_condition .= self::SubbranchSet($set_sb);
        $loan_amount_condition = self::SubQuery($s_sloan_amt, $s_eloan_amt, $search_sloancount, $search_eloancount,$s_cid, $s_branch, $s_sub_branch, $s_sdate, $s_edate);
        /*
         * Return the Customers list count
         */
        $count = self::ListviewCount($s_sdate, $s_edate, $s_sloan_amt, $s_eloan_amt, $s_sloan_rate, $s_eloan_rate, $s_cid, $s_branch, $s_sub_branch, $search_sloancount, $search_eloancount);

        if ($count > 0) {
            /*
             * Return the Customers list
             */
            Paging::set_max($count);
            Paging::calc(); // calculation for paging

            $query = "SELECT 
                    C.id AS id,P.branch_id,
                    CONCAT(C.name,' - (', C.customer_id , ')') AS customer_name, 
                    C.customer_id AS customer_id, 
                    DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS report_date,
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                    SUM(P.loan_amount) AS total_loan_amount,
                    SUM(SC.loan_rate) AS total_loan_rate,
                    SUM(P.total_net_weight) AS total_weight,
                    SUM(P.item_count) AS total_items,
                    COUNT(DISTINCT P.id) AS no_of_loans,CONCAT(
                    SB.name,' - (',SB.sub_branch_code,')') AS sub_details                    
                    FROM " . PLEDGE . " AS P 
                    LEFT JOIN " . BRANCH . " AS B ON P.branch_id = B.id 
                    LEFT JOIN " . SCHEME . " AS SC ON P.scheme_id = SC.id 
                    LEFT JOIN " . CUSTOMERS . " AS C ON C.id = P.customer_id
                    LEFT JOIN " . PRODUCT_AUCTION . " as PA on PA.loan_id = P.id
		            LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id
                    WHERE P.status=1 AND SB.status = 1 " . $search_condition . " 
                    GROUP BY C.id". $loan_amount_condition."
                    ORDER BY C.id, P.id " .Paging::get_sql_limit();
            //echo "<pre>"; echo $query; 
            return $app->getrows($query);
        }
    }

    public static function subListviewCount() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $app = $GLOBALS['app'];


        $args = func_get_args();
        $s_sdate = $args[0];
        $s_edate = $args[1];
        $s_sloan_amt = $args[2];
        $s_eloan_amt = $args[3];
        $s_sloan_rate = $args[4];
        $s_eloan_rate = $args[5];
        $s_cid = $args[6];
        $s_branch = $args[7];
        $s_sub_branch = $args[8];
        $search_sloancount = $args[9];
        $search_eloancount = $args[10];
        $search_condition = "AND (C.id <> '' )";
        $search_condition .= ($s_branch != "") ? "AND ( P.branch_id  = '" . $s_branch . "' )  " : "";
        $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id  = '" . $s_sub_branch . "' )  " : "";
        $search_condition .= ($s_cid != "") ? "AND (C.id = '" . $s_cid . "' OR C.customer_id = '" . $s_cid . "')  " : "";


        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }

        // if ($s_sloan_amt != "" && $s_eloan_amt != "") {
        //     $search_condition .= " AND  P.loan_amount BETWEEN '" . $s_sloan_amt . "' AND '" . $s_eloan_amt . "'";
        // } elseif ($s_sloan_amt != "" && $s_eloan_amt == "") {
        //     $search_condition .= " AND  P.loan_amount >= '" . $s_sloan_amt . "'";
        // } elseif ($s_sloan_amt == "" && $s_eloan_amt != "") {
        //     $search_condition .= " AND  P.loan_amount <= '" . $s_eloan_amt . "'";
        // }

        // if ($s_sloan_rate != "" && $s_eloan_rate != "") {
        //     $search_condition .= " AND  (P.loan_amount/P.total_net_weight) BETWEEN '" . $s_sloan_rate . "' AND '" . $s_eloan_rate . "'";
        // } elseif ($s_sloan_rate != "" && $s_eloan_rate == "") {
        //     $search_condition .= " AND  (P.loan_amount/P.total_net_weight) >= '" . $s_sloan_rate . "'";
        // } elseif ($s_sloan_rate == "" && $s_eloan_rate != "") {
        //     $search_condition .= " AND  (P.loan_amount/P.total_net_weight) <= '" . $s_eloan_rate . "'";
        // }

        if ($_SESSION['session_usergroupname'] != 'Super Admin') {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND B.id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }
        // Sub Branch Privilege
        $set_sb = 0;
        $search_condition .= self::SubbranchSet($set_sb);
        $loan_amount_condition = self::SubQuery($s_sloan_amt, $s_eloan_amt, $search_sloancount, $search_eloancount,$s_cid, $s_branch, $s_sub_branch, $s_sdate, $s_edate);
        /*
         * Return the loan list
         */

        $query = "SELECT 
            P.loan_id,C.customer_id,C.name AS customer_name,P.loan_amount,  DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date ,
            COALESCE(P.total_gross_weight,0.00) AS pledged_weight,SC.loan_rate,
            SC.name AS scheme_name 
            FROM " . PLEDGE . " AS P 
            LEFT JOIN " . BRANCH . " AS B ON P.branch_id = B.id 
            LEFT JOIN " . SCHEME . " AS SC ON P.scheme_id = SC.id 
            LEFT JOIN " . CUSTOMERS . " AS C ON C.id = P.customer_id 
            WHERE P.status=1  " . $search_condition . " 
            ORDER BY P.loan_id DESC ";

        ///echo $query;
        $data = $app->getrows($query);
        return $count = count($data);
    }

    public static function subListview() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $app = $GLOBALS['app'];

        $args = func_get_args();
        $s_sdate = $args[0];
        $s_edate = $args[1];
        $s_sloan_amt = $args[2];
        $s_eloan_amt = $args[3];
        $s_sloan_rate = $args[4];
        $s_eloan_rate = $args[5];
        $s_cid = $args[6];
        $s_branch = $args[7];
        $s_sub_branch = $args[8];
        $search_sloancount = $args[9];
        $search_eloancount = $args[10];
//        print_r($args);

        $search_condition = "AND (C.id <> '' )";
        $search_condition .= ($s_branch != "") ? "AND ( P.branch_id  = '" . $s_branch . "' )  " : "";
        $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id  = '" . $s_sub_branch . "' )  " : "";
        $search_condition .= ($s_cid != "") ? "AND (C.id = '" . $s_cid . "' OR C.customer_id = '" . $s_cid . "')  " : "";

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }

        // if ($s_sloan_amt != "" && $s_eloan_amt != "") {
        //     $search_condition .= " AND  P.loan_amount BETWEEN '" . $s_sloan_amt . "' AND '" . $s_eloan_amt . "'";
        // } elseif ($s_sloan_amt != "" && $s_eloan_amt == "") {
        //     $search_condition .= " AND  P.loan_amount >= '" . $s_sloan_amt . "'";
        // } elseif ($s_sloan_amt == "" && $s_eloan_amt != "") {
        //     $search_condition .= " AND  P.loan_amount <= '" . $s_eloan_amt . "'";
        // }

        // if ($s_sloan_rate != "" && $s_eloan_rate != "") {
        //     $search_condition .= " AND  (P.loan_amount/P.total_net_weight) BETWEEN '" . $s_sloan_rate . "' AND '" . $s_eloan_rate . "'";
        // } elseif ($s_sloan_rate != "" && $s_eloan_rate == "") {
        //     $search_condition .= " AND  (P.loan_amount/P.total_net_weight) >= '" . $s_sloan_rate . "'";
        // } elseif ($s_sloan_rate == "" && $s_eloan_rate != "") {
        //     $search_condition .= " AND  (P.loan_amount/P.total_net_weight) <= '" . $s_eloan_rate . "'";
        // }



        if ($_SESSION['session_usergroupname'] != 'Super Admin') {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND B.id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }

        // Sub Branch Privilege
        $set_sb = 0;
        $search_condition .= self::SubbranchSet($set_sb);

        /*
         * Return the Customers list count
         */
        $count = self::subListviewCount($s_sdate, $s_edate, $s_sloan_amt, $s_eloan_amt, $s_sloan_rate, $s_eloan_rate, $s_cid);

        if ($count > 0) {
            /*
             * Return the loan list
             */
            Paging::set_max($count);
            Paging::calc(); // calculation for paging
            $query = "SELECT 
                P.loan_id,C.customer_id,C.name AS customer_name,P.loan_amount,  DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date ,
                COALESCE(P.total_net_weight,0.00) AS pledged_weight,SC.loan_rate,P.item_count,
                SC.name AS scheme_name,
                IF(PA.status=1 || P.status=0 ,'Closed','Opened') AS status
                FROM " . PLEDGE . " AS P 
                LEFT JOIN " . BRANCH . " AS B ON P.branch_id = B.id 
                LEFT JOIN " . SCHEME . " AS SC ON P.scheme_id = SC.id 
                LEFT JOIN " . CUSTOMERS . " AS C ON C.id = P.customer_id 
                LEFT JOIN " . PRODUCT_AUCTION . " as PA on PA.loan_id = P.id
                LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id
                WHERE P.status=1  " . $search_condition . " 
                ORDER BY P.loan_date DESC ";

            return $app->getrows($query);
        }
    }

    public static function Listexport() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik


        $app = $GLOBALS['app'];

        $args = func_get_args();
        $s_sdate = $args[0];
        $s_edate = $args[1];
        $s_sloan_amt = $args[2];
        $s_eloan_amt = $args[3];
        $s_sloan_rate = $args[4];
        $s_eloan_rate = $args[5];
        $s_cid = $args[6];
        $s_branch = $args[7];
        $s_sub_branch = $args[8];
        $search_sloancount = $args[9];
        $search_eloancount = $args[10];
       
        $search_condition = "AND (C.id <> '' )";
        $search_condition .= ($s_branch != "") ? "AND ( P.branch_id  = '" . $s_branch . "' )  " : "";
        $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id  = '" . $s_sub_branch . "' )  " : "";
        $search_condition .= ($s_cid != "") ? "AND (C.id = '" . $s_cid . "' OR C.customer_id = '" . $s_cid . "')  " : "";


        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }

        // if ($s_sloan_amt != "" && $s_eloan_amt != "") {
        //     $search_condition .= " AND  P.loan_amount BETWEEN '" . $s_sloan_amt . "' AND '" . $s_eloan_amt . "'";
        // } elseif ($s_sloan_amt != "" && $s_eloan_amt == "") {
        //     $search_condition .= " AND  P.loan_amount >= '" . $s_sloan_amt . "'";
        // } elseif ($s_sloan_amt == "" && $s_eloan_amt != "") {
        //     $search_condition .= " AND  P.loan_amount <= '" . $s_eloan_amt . "'";
        // }

        /* if($s_sloan_rate != "" && $s_eloan_rate != ""){  
          $search_condition .= " AND  SC.loan_rate BETWEEN '".$s_sloan_rate."' AND '".$s_eloan_rate."'";
          }elseif($s_sloan_rate != "" && $s_eloan_rate == ""){
          $search_condition .= " AND  SC.loan_rate >= '".$s_sloan_rate."'";
          }elseif($s_sloan_rate == "" && $s_eloan_rate != ""){
          $search_condition .= " AND  SC.loan_rate <= '".$s_eloan_rate."'";
          } */

        if ($s_sloan_rate != "" && $s_eloan_rate != "") {
            $search_condition .= " AND  (P.loan_amount/P.total_net_weight) BETWEEN '" . $s_sloan_rate . "' AND '" . $s_eloan_rate . "'";
        } elseif ($s_sloan_rate != "" && $s_eloan_rate == "") {
            $search_condition .= " AND  (P.loan_amount/P.total_net_weight) >= '" . $s_sloan_rate . "'";
        } elseif ($s_sloan_rate == "" && $s_eloan_rate != "") {
            $search_condition .= " AND  (P.loan_amount/P.total_net_weight) <= '" . $s_eloan_rate . "'";
        }



        if ($_SESSION['session_usergroupname'] != 'Super Admin') {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND B.id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }

        // Sub Branch Privilege
        $set_sb = 0;
        $search_condition .= self::SubbranchSet($set_sb);

        /*
         * Return the Customers list count
         */
        $count = self::ListviewCount($s_sdate, $s_edate, $s_sloan_amt, $s_eloan_amt, $s_sloan_rate, $s_eloan_rate, $s_cid, $s_branch, $s_sub_branch, $search_sloancount, $search_eloancount);
        $loan_amount_condition = self::SubQuery($s_sloan_amt, $s_eloan_amt, $search_sloancount, $search_eloancount, $s_cid, $s_branch, $s_sub_branch, $s_sdate, $s_edate);
        // echo $loan_amount_condition;
        // echo "<br>";
        if ($count > 0) {
            /*
             * Return the loan list  
             */
            //,P.item_count

            Paging::set_max($count);
            Paging::calc(); // calculation for paging
            $query = "SELECT 
                P.loan_id,C.customer_id,C.name AS customer_name,
                  SUM(P.loan_amount) AS loan_amount, DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date ,   SUM(P.total_net_weight) AS pledged_weight,SC.loan_rate,
                COUNT(DISTINCT P.id) AS no_of_loans
                FROM " . PLEDGE . " AS P 
                LEFT JOIN " . BRANCH . " AS B ON P.branch_id = B.id 
                LEFT JOIN " . SCHEME . " AS SC ON P.scheme_id = SC.id 
                LEFT JOIN " . CUSTOMERS . " AS C ON C.id = P.customer_id
				LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id
                WHERE P.status=1 AND SB.status = 1 " . $search_condition . " 
                GROUP BY C.id" . $loan_amount_condition . "
            
                ORDER BY P.loan_date DESC ";

            $preresult = $app->getexport($query);
            $result = self::resultData($preresult);

           // echo   $query;
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
                $result[] = $rows;
            }
        } else {
            $result = $reports;
        }

        return $result;
    }
    public function SubQuery($s_sloan_amt, $s_eloan_amt, $search_sloancount, $search_eloancount,$s_cid, $s_branch, $s_sub_branch, $s_sdate, $s_edate)
    {
        if ($s_sloan_amt != "" && $s_eloan_amt != "") {
            $loan_condition = "  BETWEEN '" . $s_sloan_amt . "' AND '" . $s_eloan_amt . "'";
        } elseif ($s_sloan_amt != "" && $s_eloan_amt == "") {
            $loan_condition = "  >= '" . $s_sloan_amt . "'";
        } elseif ($s_sloan_amt == "" && $s_eloan_amt != "") {
            $loan_condition = " <= '" . $s_eloan_amt . "'";
        }

        $loan_count_condition = '';
        if ($search_sloancount != "" && $search_eloancount != "") {
            $loan_count_condition = " AND COUNT(P.id) BETWEEN '" . $search_sloancount . "' AND '" . $search_eloancount . "'";
        } elseif ($search_sloancount != "" && $search_eloancount == "") {
            $loan_count_condition = " AND COUNT(P.id) >= '" . $search_sloancount . "'";
        } elseif ($search_sloancount == "" && $search_eloancount != "") {
            $loan_count_condition = " AND COUNT(P.id) <= '" . $search_eloancount . "'";
        }
        // echo "loan count".     $loan_count_condition;
        // echo "<br>";

        if ($s_sloan_amt != '' || $s_eloan_amt != "") {
            $search_condition_loan = ($s_branch != "") ? "AND ( subP.branch_id  = '" . $s_branch . "' )  " : "";
            $search_condition_loan .= ($s_sub_branch != "") ? "AND ( subP.sub_branch_id  = '" . $s_sub_branch . "' )  " : "";
            //   $search_condition_loan .= ($s_cid != "") ? "AND (C.id = '" . $s_cid . "' OR C.customer_id = '" . $s_cid . "')  " : "";

            if ($s_sdate != "" && $s_edate != "") {
                $search_condition_loan .= " AND  ( DATE_FORMAT(subP.loan_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
            } elseif ($s_sdate != "" && $s_edate == "") {
                $search_condition_loan .= " AND  ( DATE_FORMAT(subP.loan_date,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
            } elseif ($s_sdate == "" && $s_edate != "") {
                $search_condition_loan .= " AND  ( DATE_FORMAT(subP.loan_date,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
            } else {
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
            if ($s_cid == '') {
                $customerSearch = "subP.customer_id = C.id AND";
            } else {
                $customerSearch = "subP.customer_id =" . $s_cid . " AND ";
            }
            $loan_amount_condition = " HAVING
                (
                    SELECT SUM(subP.loan_amount) 
                    FROM mf_pledge AS subP 
                    WHERE  " . $customerSearch . "
                     subP.status = 1
                   " .  $search_condition_loan . "
              ) " . $loan_condition;
        } else {
            $loan_amount_condition = '';
        }
        if ($loan_count_condition != '' || $search_condition_loan != '') {
            $loan_amount_condition = " HAVING
                (
                    SELECT SUM(subP.loan_amount) 
                    FROM mf_pledge AS subP 
                    WHERE  " . $customerSearch . "
                     subP.status = 1
                   " .  $search_condition_loan . "
              ) " . $loan_condition;
        }

        if ($search_sloancount != "" || $search_eloancount != "") {
            $loan_amount_condition .=   $loan_count_condition;
        }
        return $loan_amount_condition;
    }


}
