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
class CustomerPledgedReport {

    public function SubbranchSet($set_sb) {

        // Changed dssk sub branch privilege
        /*
         * Get Sub-Branch list
         */
//        echo $set_sb;
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

            return "AND (P.sub_branch_id IN($find_id))";
        }
    }

    public static function ListviewCount() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $args = func_get_args();

        $app = $GLOBALS['app'];


        $s_cid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_branch = $args[3];
        $s_sub_branch = $args[4];
        $s_status_type = $args[5];

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
        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND B.id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }
        
        // Sub Branch Privilege
        $set_sb = 0;
        $search_condition .= self::SubbranchSet($set_sb);
        /*
         * Return the Customers list
         */
        $query = "SELECT 
        COUNT(DISTINCT C.id)
        FROM " . PLEDGE . " as P 
        LEFT JOIN " . BRANCH . " as B ON B.id=P.branch_id
        LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id 
	LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id
        WHERE 1 AND SB.status = 1 " . $search_condition . "";
        $data = $app->getrow($query);

        return $count = intval($data[0]);
    }

    public static function Listview() {
        //Rajesh Feb 24,2021
        $args = func_get_args();

        $app = $GLOBALS['app'];
        $paging = $GLOBALS['paging'];

        $s_cid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_branch = $args[3];
        $s_sub_branch = $args[4];
        $s_status_type = $args[5];

        $search_condition = "AND (C.id <> '' )";
        $search_condition = ($s_branch != "") ? "AND ( B.id  = '" . $s_branch . "' )  " : "";
        $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id  = '" . $s_sub_branch . "' )  " : "";

        if ($s_status_type == 2) {
            $search_condition .= "AND (P.status IN(0,1) || (PA.status IN(0,1)))";
        } elseif ($s_status_type == 1) {
            //IF(PAI.status=1 || P.status=0 ,'Closed','Opened')
            $test = 0;
            $search_condition .= ($s_status_type != "") ? "AND ( PA.status  = '" . $s_status_type . "' || P.status = '" . $test . "')  " : "";
        } elseif ($s_status_type == 0) {
            $test = 1;
            $search_condition .= ($s_status_type != "") ? "AND ( PA.status  = '" . $s_status_type . "' || P.status = '" . $test . "' )  " : "";
        }

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


        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND B.id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }

        /*
         * Return the Customers list count
         */
        
        // Sub Branch Privilege
        $set_sb = 0;
        $search_condition .= self::SubbranchSet($set_sb);
        
        $count = self::ListviewCount($s_cid, $s_sdate, $s_edate, $s_branch, $s_sub_branch);


        if ($count > 0) {
            /*
             * Return the Customers list
             */
            Paging::set_max($count);
            Paging::calc(); // calculation for paging

            $query = "SELECT 
                SUM(P.loan_amount) AS total_loan_amount,
                DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                C.id AS id,
                CONCAT(SB.name,' - (',SB.sub_branch_code,')') AS sub_branch_code,
                    P.branch_id,P.status,
                C.name AS customer_name, 
                C.customer_id AS customer_id, 
                COUNT(DISTINCT P.id) AS no_of_loans,
                SUM(P.total_net_weight) AS total_weight                   
                   
                    FROM " . PLEDGE . " as P 
                    LEFT JOIN " . BRANCH . " as B ON B.id=P.branch_id
                    LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id 
                    LEFT JOIN " . PRODUCT_AUCTION . " as PA on PA.loan_id = P.id
                    LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id
                    WHERE 1 AND SB.status = 1 " . $search_condition . " 
                    GROUP BY C.id
                    ORDER BY C.id, P.id " . Paging::get_sql_limit();
            //echo "<pre>"; echo $query."<hr>";die;
            //return $app->getrows($query);
            //echo  $query;
            $preresult = $app->getexport($query);
            $result = self::listResultData($preresult);
            return $result;
        }
    }
    public static function Listview_old() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $args = func_get_args();

        $app = $GLOBALS['app'];
        $paging = $GLOBALS['paging'];

        $s_cid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_branch = $args[3];
        $s_sub_branch = $args[4];
        $s_status_type = $args[5];

        $search_condition = "AND (C.id <> '' )";
        $search_condition = ($s_branch != "") ? "AND ( B.id  = '" . $s_branch . "' )  " : "";
        $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id  = '" . $s_sub_branch . "' )  " : "";


        if ($s_status_type == 2) {
            $search_condition .= "AND (P.status IN(0,1) || (PA.status IN(0,1)))";
        } elseif ($s_status_type == 1) {
            //IF(PAI.status=1 || P.status=0 ,'Closed','Opened')
            $test = 0;
            $search_condition .= ($s_status_type != "") ? "AND ( PA.status  = '" . $s_status_type . "' || P.status = '" . $test . "')  " : "";
        } elseif ($s_status_type == 0) {
            $test = 1;
            $search_condition .= ($s_status_type != "") ? "AND ( PA.status  = '" . $s_status_type . "' || P.status = '" . $test . "' )  " : "";
        }

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


        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND B.id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }

        /*
         * Return the Customers list count
         */
        
        // Sub Branch Privilege
        $set_sb = 0;
        $search_condition .= self::SubbranchSet($set_sb);
        
        $count = self::ListviewCount($s_cid, $s_sdate, $s_edate, $s_branch, $s_sub_branch);


        if ($count > 0) {
            /*
             * Return the Customers list
             */
            Paging::set_max($count);
            Paging::calc(); // calculation for paging

            $query = "SELECT 
                    CONCAT(
                    SB.name,' - (',SB.sub_branch_code,')') AS sub_branch_code,C.id AS id,P.branch_id,P.status,
                    C.name AS customer_name, C.customer_id AS customer_id, 
                    DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS report_date,
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                    SUM(P.loan_amount) AS total_loan_amount,
                    SUM(P.total_net_weight) AS total_weight,
                    SUM(P.item_count) AS total_items,
                    COUNT(DISTINCT P.id) AS no_of_loans
                    FROM " . PLEDGE . " as P 
                    LEFT JOIN " . BRANCH . " as B ON B.id=P.branch_id
                    LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id 
                    LEFT JOIN " . PRODUCT_AUCTION . " as PA on PA.loan_id = P.id
                    LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id
                    WHERE 1 AND SB.status = 1 " . $search_condition . " 
                    GROUP BY C.id
                    ORDER BY C.id, P.id " . Paging::get_sql_limit();
            //echo "<pre>"; echo $query."<hr>";die;
            //return $app->getrows($query);
            //echo  $query;
            $preresult = $app->getexport($query);
            $result = self::listResultData($preresult);
            return $result;
        }
    }

    public static function subListviewCount() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $args = func_get_args();

        $app = $GLOBALS['app'];


        $s_cid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_branch = $args[3];
        $s_status_type = $args[4];
        $s_sub_branch = $args[5];

        $search_condition = "AND (C.id <> '' )";
        $search_condition = ($s_branch != "") ? "AND ( B.id  = '" . $s_branch . "' )  " : "";
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
        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND B.id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }

        /*
         * Return the Customers list
         */
        $query = "SELECT 
            COUNT(P.loan_id)
            FROM " . PLEDGE . " as P 
            LEFT JOIN " . BRANCH . " as B ON B.id=P.branch_id
            LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id 
            WHERE 1 " . $search_condition . " 
            GROUP BY P.loan_id ";
        $data = $app->getrow($query);
        return $count = intval($data[0]);
    }

    public static function subListview() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $args = func_get_args();

        $app = $GLOBALS['app'];
        $paging = $GLOBALS['paging'];


        $s_cid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_branch = $args[3];
        $s_status_type = $args[4];
        $s_sub_branch = $args[5];

        $search_condition = "AND (C.id <> '' )";
        $search_condition = ($s_branch != "") ? "AND ( B.id  = '" . $s_branch . "' )  " : "";
        $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id  = '" . $s_sub_branch . "' )  " : "";
        $search_condition .= ($s_cid != "") ? "AND (C.id = '" . $s_cid . "' OR C.customer_id = '" . $s_cid . "')  " : "";

        if ($s_status_type == 2) {
            $search_condition .= "AND (P.status IN(0,1) || (PAI.status IN(0,1)))";
        } elseif ($s_status_type == 1) {
            $test = 0;
            $search_condition .= ($s_status_type != "") ? "AND ( PAI.status  = '" . $s_status_type . "' || P.status = '" . $test . "')  " : "";
        } elseif ($s_status_type == 0) {
            $test = 1;
            $search_condition .= ($s_status_type != "") ? "AND ( PAI.status  = '" . $s_status_type . "' || P.status = '" . $test . "' )  " : "";
        }


        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }
        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND B.id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }

        /*
         * Return the Customers list count
         */
        $count = self::subListviewCount($s_cid, $s_sdate, $s_edate);

        // Sub Branch Privilege
         $search_condition .=  self::SubbranchSet($set_sb);

        if ($count > 0) {

            // Rajesh 26-05-2020 live query
            /*$query = "SELECT 
                    P.customer_id, P.loan_id AS loan_code, P.id AS pledge_id, 
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date, 
                    P.loan_amount,
                    P.total_net_weight AS net_weight,P.item_count,
                    
                    CONCAT(
                        C.name,' - ( ',C.id,' ) ','\n',
                        C.street,'\n',
                        C.location, '-', C.postal_code, '\n',
                        'PH:',C.contact_no,'\n',
                        'Email:',C.email,'\n'
                    ) AS customer_details,

                    GROUP_CONCAT(
                        CT.category,' - ',PRO.name,' - [ ', PRO.product_with,' , ', PRO.product_without,' ]'

                        ORDER BY CT.category DESC SEPARATOR '\n'
                    ) AS product_details,
                    SC.name AS scheme_name,
					IF(PAI.status=1 || P.status=0 ,'Closed','Opened') AS status
                    
                    FROM " . PLEDGE . " as P 
                    LEFT JOIN " . BRANCH . " as B ON B.id=P.branch_id
                    LEFT JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id 
                    LEFT JOIN " . CATEGORIES . " as CT ON CT.id = PD.category_id 
                    LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id 
                    LEFT JOIN " . SCHEME . " as SC ON P.scheme_id = SC.id 
                    LEFT JOIN " . PRODUCTS . " as PRO on PRO.id = PD.product_id					
                    LEFT JOIN " . PRODUCT_AUCTION . " as PAI ON PAI.loan_id = P.id
                    LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id
                    WHERE 1 " . $search_condition . " 
                    GROUP BY P.loan_id
                    ORDER BY C.id, P.id ";
*/

            // Rajesh 26-05-2020 staging updated query

            $query = "SELECT 
                    P.customer_id, P.loan_id AS loan_code, P.id AS pledge_id, 
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date, 
                    P.loan_amount,
                    P.total_net_weight AS net_weight,P.item_count,
                    
                    CONCAT(
                        C.name,' - ( ',C.id,' ) ','\n',
                        C.street,'\n',
                        C.location, '-', C.postal_code, '\n',
                        'PH:',C.contact_no,'\n',
                        'Email:',C.email,'\n'
                    ) AS customer_details,

                    GROUP_CONCAT(
                           IF(  PN.pre_name IS NULL, '',   PN.pre_name), ' ',IF( PRO.product_with IS NULL, '',  PRO.product_with), IF(PW.pre_name IS NULL, '', PW.pre_name),' ', PRO.name ORDER BY PRO.name DESC SEPARATOR ',') AS product_details,

                  
                    IF(PAI.status=1 || P.status=0 ,'Closed','Opened') AS status
                    
                    FROM " . PLEDGE . " as P 
                    LEFT JOIN " . BRANCH . " as B ON B.id=P.branch_id
                    LEFT JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id 
                    LEFT JOIN " . CATEGORIES . " as CT ON CT.id = PD.category_id 
                    LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id 
                    LEFT JOIN " . SCHEME . " as SC ON P.scheme_id = SC.id 
                    LEFT JOIN " . PRODUCTS . " as PRO on PRO.id = PD.product_id                 
                    LEFT JOIN " . PRODUCT_AUCTION . " as PAI ON PAI.loan_id = P.id 
                    LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id
                    LEFT JOIN " . PRENAME . " as PN ON PN.id = PD.pre_name_id 

                 LEFT JOIN ".PRODUCT_WITH." as PW ON PD.product_with = PW.id
                    WHERE 1 " . $search_condition . " 
                    GROUP BY P.loan_id
                    ORDER BY C.id, P.id ";
            $preresult = $app->getexport($query);
            $result = self::resultData($preresult);


            return $result;
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


        $s_cid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_branch = $args[3];
        $s_sub_branch = $args[4];
        $s_status_type = $args[5];

        $search_condition = "AND (C.id <> '' )";
        $search_condition = ($s_branch != "") ? "AND ( B.id  = '" . $s_branch . "' )  " : "";
        $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id  = '" . $s_sub_branch . "' )  " : "";

        if ($s_status_type == 2) {

            $search_condition .= "AND (P.status IN(0,1) || (PAI.status IN(0,1)))";
        } elseif ($s_status_type == 1) {
            //IF(PAI.status=1 || P.status=0 ,'Closed','Opened')
            $test = 0;
            $search_condition .= ($s_status_type != "") ? "AND ( PAI.status  = '" . $s_status_type . "' || P.status = '" . $test . "')  " : "";
        } elseif ($s_status_type == 0) {
            $test = 1;
            $search_condition .= ($s_status_type != "") ? "AND ( PAI.status  = '" . $s_status_type . "' || P.status = '" . $test . "' )  " : "";
        }

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
        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND B.id = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }


        /*
         * Return the Customers list count
         */
        $count = self::subListviewCount($s_cid, $s_sdate, $s_edate);

        if ($count > 0) {
            /*
             * Return the Customers list
             */

            /* $query = "SELECT 
              P.loan_id,
              DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
              P.loan_amount,
              P.total_net_weight,P.item_count,

              CONCAT(
              C.name,' - (',C.id,')',', ',
              C.street,', ',
              C.location, ' - ', C.postal_code
              ) AS customer_details,

              GROUP_CONCAT(
              CT.category,'-',PRO.name

              ORDER BY CT.category DESC SEPARATOR '  ,  '
              ) AS product_details,

              IF(PA.loan_id <> '',
              'Bidded',
              IF(PR.loan_id <> '',
              'Redeemed',
              'Open'
              )
              ) AS status_text,

              IF(PA.id <> '',
              DATE_FORMAT(PA.paid_on,'%d-%b-%y'),
              IF(PR.id <> '',
              DATE_FORMAT(PR.paid_on,'%d-%b-%y'),
              '-'
              )
              ) AS return_date,

              SC.name AS scheme_name

              FROM ".PLEDGE." as P
              LEFT JOIN ".BRANCH." as B ON B.id=P.branch_id
              LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
              LEFT JOIN ".CATEGORIES." as CT ON CT.id = PD.category_id
              LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
              LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
              LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
              LEFT JOIN ".REDEMPTION." as PR on PR.loan_id = P.id
              LEFT JOIN ".PRODUCT_AUCTION." as PA on PA.loan_id = P.id
              WHERE 1 ".$search_condition."
              GROUP BY P.loan_id
              ORDER BY C.id, P.id "; */

              //Rajesh 26-05-2020 live query

            /*$query = "SELECT 
                    C.customer_id, P.loan_id AS loan_code, P.id AS pledge_id, 
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date, 
                    P.loan_amount,
                    P.total_net_weight AS net_weight,P.item_count,
                    
                    CONCAT(
                        C.name,'\n',
                        C.street,'\n',
                        C.location, '-', C.postal_code, '\n',
                        'PH:',C.contact_no,'\n',
                        'Email:',C.email,'\n'
                    ) AS customer_details,

                    GROUP_CONCAT(
                        CT.category,' - ',PRO.name,' - [ ', PRO.product_with,' , ', PRO.product_without,' ]'

                        ORDER BY CT.category DESC SEPARATOR '\n'
                    ) AS product_details,
                    SC.name AS scheme_name,
					IF(PAI.status=1 || P.status=0 ,'Closed','Opened') AS status
                    
                    FROM " . PLEDGE . " as P 
                    LEFT JOIN " . BRANCH . " as B ON B.id=P.branch_id
                    LEFT JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id 
                    LEFT JOIN " . CATEGORIES . " as CT ON CT.id = PD.category_id 
                    LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id 
                    LEFT JOIN " . SCHEME . " as SC ON P.scheme_id = SC.id 
                    LEFT JOIN " . PRODUCTS . " as PRO on PRO.id = PD.product_id					
                    LEFT JOIN " . PRODUCT_AUCTION . " as PAI ON PAI.loan_id = P.id 
                    LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id
                    WHERE 1 and SB.status = 1 " . $search_condition . " 
                    GROUP BY P.loan_id
                    ORDER BY P.id DESC";*/

                    // Rajesh 26-05-2020 staging updated query

                    $query = "SELECT 
                    C.customer_id, P.loan_id AS loan_code, P.id AS pledge_id, 
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date, 
                    P.loan_amount,
                    P.total_net_weight AS net_weight,P.item_count,
                    
                    CONCAT(
                        C.name,'\n',
                        C.street,'\n',
                        C.location, '-', C.postal_code, '\n',
                        'PH:',C.contact_no,'\n',
                        'Email:',C.email,'\n'
                    ) AS customer_details,

                   GROUP_CONCAT(
                           IF(  PN.pre_name IS NULL, '',   PN.pre_name), ' ',IF( PRO.product_with IS NULL, '',  PRO.product_with), IF(PW.pre_name IS NULL, '', PW.pre_name),' ', PRO.name ORDER BY PRO.name DESC SEPARATOR ',') AS product_details,
                    SC.name AS scheme_name,
                    IF(PAI.status=1 || P.status=0 ,'Closed','Opened') AS status
                    
                    FROM " . PLEDGE . " as P 
                    LEFT JOIN " . BRANCH . " as B ON B.id=P.branch_id
                    LEFT JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id 
                    LEFT JOIN " . CATEGORIES . " as CT ON CT.id = PD.category_id 
                    LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id 
                    LEFT JOIN " . SCHEME . " as SC ON P.scheme_id = SC.id 
                    LEFT JOIN " . PRODUCTS . " as PRO on PRO.id = PD.product_id                 
                    LEFT JOIN " . PRODUCT_AUCTION . " as PAI ON PAI.loan_id = P.id 
                    LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id

                    LEFT JOIN " . PRENAME . " as PN ON PN.id = PD.pre_name_id 

                    LEFT JOIN ".PRODUCT_WITH." as PW ON PD.product_with = PW.id

                    WHERE 1 and SB.status = 1 " . $search_condition . " 
                    GROUP BY P.loan_id
                    ORDER BY P.id DESC";


            //echo $query;exit;
            $preresult = $app->getexport($query);
            $result = self::resultData($preresult);
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
                $weight_in = floatval($rows['net_weight']);

                $weight_in_gs = intval($weight_in);
                $weight_in_mgs = intval(floatval($weight_in - intval($weight_in)) * 1000);

                $rows['weight_in'] = $weight_in;
                $rows['weight_in_gs'] = $weight_in_gs;
                $rows['weight_in_mgs'] = $weight_in_mgs;

                $loan_id = $rows['pledge_id'];
                $loan_code = $rows['loan_code'];
                $paid_on = '-';
                $status_text = '';
                $pquery = "SELECT COUNT(id) FROM " . PRODUCT_AUCTION . " WHERE loan_id =  " . $loan_id;
                $pc = $app->getrow($pquery);
                if ((int) $pc[0] > 0) {
                    $status_text = "Bidded";

                    $pquery = "SELECT  DATE_FORMAT(paid_on,'%d-%b-%y') FROM " . PRODUCT_AUCTION . " WHERE loan_id =  " . $loan_id;
                    $pc = $app->getrow($pquery);
                    $paid_on = $pc[0];
                } else {
                    //$rquery = "SELECT COUNT(id)  FROM ".REDEMPTION." WHERE loan_id =  ".$loan_id;
                    $pquery = "SELECT COUNT(id) FROM " . RENEWAL . " WHERE old_loan_id =  " . $loan_id;
                    $pc = $app->getrow($pquery);
                    if ((int) $pc[0] > 0) {
                        $status_text = "Renewed";

                        $pquery = "SELECT  DATE_FORMAT(paid_on,'%d-%b-%y') FROM " . RENEWAL . " WHERE old_loan_id =  " . $loan_id;
                        $pc = $app->getrow($pquery);
                        $paid_on = $pc[0];
                    } else {
                        $pquery = "SELECT COUNT(id) FROM " . PAY_EXCESS_AMOUNT . " WHERE old_loan_id =  " . $loan_id;
                        $pc = $app->getrow($pquery);
                        if ((int) $pc[0] > 0) {

                            $status_text = "Excess paid";
                            $pquery = "SELECT  DATE_FORMAT(paid_on,'%d-%b-%y') FROM " . PAY_EXCESS_AMOUNT . " WHERE old_loan_id =  " . $loan_id;
                            $pc = $app->getrow($pquery);
                            $paid_on = $pc[0];
                        } else {
                            $pquery = "SELECT COUNT(id) FROM " . PARTIAL_AMOUNT_PAYMENT . " WHERE old_loan_id =  " . $loan_id;
                            $pc = $app->getrow($pquery);
                            if ((int) $pc[0] > 0) {
                                $status_text = "Partial paid";
                                $pquery = "SELECT  DATE_FORMAT(paid_on,'%d-%b-%y') FROM " . PARTIAL_AMOUNT_PAYMENT . " WHERE old_loan_id =  " . $loan_id;
                                $pc = $app->getrow($pquery);
                                $paid_on = $pc[0];
                            } else {
                                $pquery = "SELECT COUNT(id)  FROM " . REDEMPTION . " WHERE loan_id =  " . $loan_id;
                                $pc = $app->getrow($pquery);
                                if ((int) $pc[0] > 0) {
                                    $status_text = "Redeemed";
                                    $pquery = "SELECT  DATE_FORMAT(paid_on,'%d-%b-%y') FROM " . REDEMPTION . " WHERE loan_id =  " . $loan_id;
                                    $pc = $app->getrow($pquery);
                                    $paid_on = $pc[0];
                                }
                            }
                        }
                    }
                }
                if ($paid_on != '-') {
                    $rows['status'] = 'Closed';
                }
                if (@$rows['status'] != '' && $rows['status'] != 'Open') {
                    $separator = $status_text ? '-' : '';
                    $rows['status'] .= $separator . $status_text;
                }

                if (@$rows['return_date'] == '') {


                    $rows['return_date'] = $paid_on;
                }



                $result[] = $rows;
            }
        } else {
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
                /** Set Result data for the record * */
                $weight_in = floatval($rows['total_weight']);

                $weight_in_gs = intval($weight_in);
                $weight_in_mgs = intval(floatval($weight_in - intval($weight_in)) * 1000);

                $loan_amount = $data[$i]['total_loan_amount'] / $weight_in;

                $rows['weight_in'] = $weight_in;
                $rows['weight_in_gs'] = $weight_in_gs;
                $rows['weight_in_mgs'] = $weight_in_mgs;
                $rows['loan_amount'] = $loan_amount;


                $result[] = $rows;
            }
        } else {
            $result = $reports;
        }

        return $result;
    }

}
