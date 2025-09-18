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


class PreAuctionReport {
    
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
    
    /*
     * Define the required fields and return text
     */

    public static function ListviewCount() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();

        $app = $GLOBALS['app'];

        $s_cid = $args[0];
        $s_branch = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        $s_sub_branch = $args[4];
        $type_of_page = $args[5];

        if ($type_of_page == 2) {
            $search_condition .= " AND  (P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";
        }


        $s_cid = preg_replace("/mfc/i", '', $s_cid);


        $search_condition .= ($s_cid != "") ? "AND (C.id = '" . $s_cid . "' )  " : "";



        $search_condition .= " AND  ( P.status = 1)";
        $search_condition .= ($s_branch != "") ? "AND ( B.id  = '" . $s_branch . "' )  " : "";
//	    $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
        // Added 11_DEC_2017 //

        if ((is_array($s_sub_branch)) && ($s_sub_branch != '') && ($s_sub_branch[0] > 0)) {
            foreach ($s_sub_branch as $key => $value) {
                $sub_branch_id_overall[] = $value;
            }
            $sub_branch_ids = implode(',', $sub_branch_id_overall);
            $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id IN(" . $sub_branch_ids . "))" : "";
        }

        // END //

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
        }

        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND  B.id   = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }
        
        // Sub Branch Privilege
         $search_condition .=  self::SubbranchSet($set_sb);
        /*
         * Return the Customers list
         */
        $query = "SELECT 


                DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS report_date


                
                FROM " . PLEDGE . " AS P 
                LEFT JOIN " . BRANCH . " AS B ON B.id = P.branch_id
                LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id 
		LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id
                
                
                WHERE 1  " . $search_condition . " 
                GROUP BY DATE_FORMAT(P.loan_date,'%Y-%m-%d')
                ORDER BY DATE_FORMAT(P.loan_date,'%Y-%m-%d') DESC ";
        $data = $app->getrows($query);

        return $count = count($data);
    }

    public static function Listview() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();

        $app = $GLOBALS['app'];
        $paging = $GLOBALS['paging'];

        $s_cid = $args[0];
        $s_branch = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        $s_sub_branch = $args[4];
        $type_of_page = $args[5];
        if ($type_of_page == 2) {
            // $search_condition  .= " AND  (P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";  
        }

        $s_cid = preg_replace("/mfc/i", '', $s_cid);
        $search_condition .= ($s_cid != "") ? "AND (C.id = '" . $s_cid . "' )  " : "";

        $search_condition .= " AND  ( P.status = 1)";
        $search_condition .= ($s_branch != "") ? "AND ( B.id  = '" . $s_branch . "' )  " : "";
//	    $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
        // Added 11_DEC_2017 //

        if ((is_array($s_sub_branch)) && ($s_sub_branch != '') && ($s_sub_branch[0] > 0)) {
            foreach ($s_sub_branch as $key => $value) {
                $sub_branch_id_overall[] = $value;
            }
            $sub_branch_ids = implode(',', $sub_branch_id_overall);
            $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id IN(" . $sub_branch_ids . "))" : "";
        }

        // END //


        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
        }

        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND  B.id   = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }
        
        // Sub Branch Privilege
         $search_condition .=  self::SubbranchSet($set_sb);

        /*
         * Return the Customers list count
         */
        $count = self::ListviewCount($s_cid, $s_branch, $s_sdate, $s_edate, $s_sub_branch, $type_of_page);
//exit;
        if ($count > 0) {
            /*
             * Return the Customers list
             */
            Paging::set_max($count);
            Paging::calc(); // calculation for paging

            $query = "SELECT 
                DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS id,
                B.name AS branch_name,
                DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS report_date,
                DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                SUM(P.loan_amount) AS total_loan_amount,
                SUM(P.total_net_weight) AS total_weight,
                SUM(P.item_count) AS total_item,
					P.Sub_branch_id,
                
                COUNT(DISTINCT C.id) AS no_of_customer,
		CONCAT(SB.name,' - (',SB.sub_branch_code,')') AS sub_details,
                COUNT(DISTINCT P.id) AS no_of_loan

                FROM " . PLEDGE . " AS P 
                LEFT JOIN " . BRANCH . " AS B ON B.id = P.branch_id
                LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id 
                LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id
                WHERE 1 " . $search_condition . " 
                GROUP BY DATE_FORMAT(P.loan_date,'%Y-%m-%d')
                ORDER BY DATE_FORMAT(P.loan_date,'%Y-%m-%d') DESC " . Paging::get_sql_limit();
            //echo "<pre>"; echo $query."<hr>";die;
            return $app->getrows($query);
        }
    }

    public static function subListviewCount() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();

        $app = $GLOBALS['app'];

        $s_cid = $args[0];
        $s_branch = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        $s_sub_branch = $args[4];  // added 11_DEC_2017

        $s_cid = preg_replace("/mfc/i", '', $s_cid);


        $search_condition = " AND  ( P.status = 1) ";

        //Grace Period Checking - After 1 year + 7 Day Grace Period - Calculate from the date of last paid
        /* $search_condition  = " AND  (

          DATE_FORMAT(
          DATE_ADD(
          DATE_ADD(
          IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
          INTERVAL 12 MONTH)
          ,
          INTERVAL ".AUCTION_GRACE_DAYS." DAY
          ),
          '%Y-%m-%d'
          )

          ) < DATE_FORMAT(CURRENT_DATE,'%Y-%m-%d')"; */


        //Grace Period Checking - After 1 year + 0 Day Grace Period - Calculate from the date of Loan Pledged 
        /* $search_condition  = " AND  (

          DATE_FORMAT(
          DATE_ADD(
          DATE_ADD(
          P.loan_date,
          INTERVAL 12 MONTH)
          ,
          INTERVAL ".AUCTION_GRACE_DAYS." DAY
          ),
          '%Y-%m-%d'
          )

          ) < DATE_FORMAT(CURRENT_DATE,'%Y-%m-%d')"; */

        $search_condition .= ($s_cid != "") ? "AND (C.id = '" . $s_cid . "' )  " : "";




        $search_condition .= ($s_branch != "") ? "AND ( B.id  = '" . $s_branch . "' )  " : "";

        // Added 11_DEC_2017 //

        if ((is_array($s_sub_branch)) && ($s_sub_branch != '') && ($s_sub_branch[0] > 0)) {
            foreach ($s_sub_branch as $key => $value) {
                $sub_branch_id_overall[] = $value;
            }
            $sub_branch_ids = implode(',', $sub_branch_id_overall);
            $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id IN(" . $sub_branch_ids . "))" : "";
        }

        // END //


        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
        }

        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND  B.id   = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }
        
        // Sub Branch Privilege
         $search_condition .=  self::SubbranchSet($set_sb);


        /*
         * Return the Pledges list Count
         */
        $query = "SELECT DISTINCT P.id 
                FROM " . PLEDGE . " AS P 
                LEFT JOIN " . BRANCH . " AS B ON B.id = P.branch_id
                LEFT JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id 
                LEFT JOIN " . CATEGORIES . " as CT ON CT.id = PD.category_id 
                LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id 
                LEFT JOIN " . SCHEME . " as SC ON P.scheme_id = SC.id 
                LEFT JOIN " . PRODUCTS . " as PRO on PRO.id = PD.product_id
                        WHERE 1 " . $search_condition . " 
                        GROUP BY P.loan_id
                        ORDER BY id DESC ";
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


        //echo $s_sdate . "  " . $s_edate;



        $s_cid = $args[0];
        $s_branch = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        $s_sub_branch = $args[4];

        $s_cid = preg_replace("/mfc/i", '', $s_cid);

        $search_condition = " AND  ( P.status = 1)";

        //Grace Period Checking - After 1 year + 7 Day Grace Period - Calculate from the date of last paid
        /* $search_condition  = " AND  (

          DATE_FORMAT(
          DATE_ADD(
          DATE_ADD(
          IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
          INTERVAL 12 MONTH)
          ,
          INTERVAL ".AUCTION_GRACE_DAYS." DAY
          ),
          '%Y-%m-%d'
          )

          ) < DATE_FORMAT(CURRENT_DATE,'%Y-%m-%d')"; */


        //Grace Period Checking - After 1 year + 0 Day Grace Period - Calculate from the date of Loan Pledged 
        /* $search_condition  = " AND  (

          DATE_FORMAT(
          DATE_ADD(
          DATE_ADD(
          P.loan_date,
          INTERVAL 12 MONTH)
          ,
          INTERVAL ".AUCTION_GRACE_DAYS." DAY
          ),
          '%Y-%m-%d'
          )

          ) < DATE_FORMAT(CURRENT_DATE,'%Y-%m-%d')"; */



        $search_condition .= ($s_cid != "") ? "AND (C.id = '" . $s_cid . "' )  " : "";




        $search_condition .= ($s_branch != "") ? "AND ( B.id  = '" . $s_branch . "' )  " : "";


        // Added 11_DEC_2017 //

        if ((is_array($s_sub_branch)) && ($s_sub_branch != '') && ($s_sub_branch[0] > 0)) {
            foreach ($s_sub_branch as $key => $value) {
                $sub_branch_id_overall[] = $value;
            }
            $sub_branch_ids = implode(',', $sub_branch_id_overall);
            $search_condition .= ($s_sub_branch != "") ? " AND ( P.sub_branch_id IN(" . $sub_branch_ids . "))" : "";
        }

        // END //

        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
        }

        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND  B.id   = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }

        $count = self::subListviewCount($s_cid, $s_branch, $s_sdate, $s_edate, $s_sub_branch);
        
        // Sub Branch Privilege
         $search_condition .=  self::SubbranchSet($set_sb);
         
        /*
         * Return the Pledge list
         */


        if ($count > 0) {
            Paging::set_max($count);
            Paging::calc(); // calculation for paging

            // Rajesh 26-05-2020 live query
            /*$query = "SELECT DISTINCT 
            
                        B.name as branch_name,
                        DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS report_date,

                        P.loan_id AS loan_no,
                        P.loan_amount AS loan_amount,
                                                PA.serial_no,
                        
                        CONCAT(
                            C.name,'(',C.customer_id,')','<br>',
                            C.street,'<br>',
                            C.location, '-', C.postal_code, '<br>',
                            'PH:',C.contact_no,'<br>',
                            'Email:',C.email,'<br>'
                        ) AS customer_details,

                        GROUP_CONCAT(
                            CT.category,'-',PRO.name,'-[',PRO.product_with,',',PRO.product_without,']'
                            
                            ORDER BY CT.category DESC SEPARATOR '<br>'
                        ) AS product_details,
                        


                        P.total_gross_weight AS gross, 
                        P.total_net_weight AS net_weight, 
                        P.item_count AS quantity, 
                        
                        SC.name as scheme_name

                FROM " . PLEDGE . " AS P 
                LEFT JOIN " . BRANCH . " AS B ON B.id = P.branch_id
                LEFT JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id 
                LEFT JOIN " . CATEGORIES . " as CT ON CT.id = PD.category_id
                LEFT JOIN " . PRODUCT_AUCTION . " as PA ON P.id = PA.loan_id
                LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id 
                LEFT JOIN " . SCHEME . " as SC ON P.scheme_id = SC.id 
                LEFT JOIN " . PRODUCTS . " as PRO on PRO.id = PD.product_id
                        WHERE 1 " . $search_condition . " 
                        GROUP BY P.loan_id
                        ORDER BY P.loan_id ASC ";*/

                        // Rajesh 26-05-2020 staging updated query
                        $query = "SELECT DISTINCT 
            
                        B.name as branch_name,
                        DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS report_date,

                        P.loan_id AS loan_no,
                        P.loan_amount AS loan_amount,
                                                PA.serial_no,
                        
                        CONCAT(
                            C.name,'(',C.customer_id,')','<br>',
                            C.street,'<br>',
                            C.location, '-', C.postal_code, '<br>',
                            'PH:',C.contact_no,'<br>',
                            'Email:',C.email,'<br>'
                        ) AS customer_details,

                        GROUP_CONCAT(
                           IF(  PN.pre_name IS NULL, '',   PN.pre_name),  ' ',IF( PRO.product_with IS NULL, '',  PRO.product_with), IF(PW.pre_name IS NULL, '', PW.pre_name),' ', PRO.name,',' ORDER BY PRO.name DESC SEPARATOR '<br>') AS product_details,
                        


                        P.total_gross_weight AS gross, 
                        P.total_net_weight AS net_weight, 
                        P.item_count AS quantity, 
                        
                        SC.name as scheme_name

                FROM " . PLEDGE . " AS P 
                LEFT JOIN " . BRANCH . " AS B ON B.id = P.branch_id
                LEFT JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id 
                LEFT JOIN " . CATEGORIES . " as CT ON CT.id = PD.category_id
                LEFT JOIN " . PRODUCT_AUCTION . " as PA ON P.id = PA.loan_id
                LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id 
                LEFT JOIN " . SCHEME . " as SC ON P.scheme_id = SC.id 
                LEFT JOIN " . PRODUCTS . " as PRO on PRO.id = PD.product_id

                LEFT JOIN " . PRENAME . " as PN ON PN.id = PD.pre_name_id

                LEFT JOIN ".PRODUCT_WITH." as PW ON PD.product_with = PW.id 

                        WHERE 1 " . $search_condition . " 
                        GROUP BY P.loan_id
                        ORDER BY P.loan_id ASC ";
            //echo $query."<hr>";
            //exit;
            return $app->getrows($query);
        }
    }

    public static function Listexport() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];
        $s_cid = $args[0];
        $s_branch = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        $s_sub_branch = $args[4];
        $type_of_page = $args[5];
        if ($type_of_page == 2) {
            $search_condition .= " AND  (P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";
        }


        $s_cid = preg_replace("/mfc/i", '', $s_cid);


        //Grace Period Checking - After 1 year + 7 Day Grace Period - Calculate from the date of last paid
        /* $search_condition  = " AND  (

          DATE_FORMAT(
          DATE_ADD(
          DATE_ADD(
          IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
          INTERVAL 12 MONTH)
          ,
          INTERVAL ".AUCTION_GRACE_DAYS." DAY
          ),
          '%Y-%m-%d'
          )

          ) < DATE_FORMAT(CURRENT_DATE,'%Y-%m-%d')"; */


        //Grace Period Checking - After 1 year + 0 Day Grace Period - Calculate from the date of Loan Pledged 
        /* $search_condition  = " AND  (

          DATE_FORMAT(
          DATE_ADD(
          DATE_ADD(
          P.loan_date,
          INTERVAL 12 MONTH)
          ,
          INTERVAL ".AUCTION_GRACE_DAYS." DAY
          ),
          '%Y-%m-%d'
          )

          ) < DATE_FORMAT(CURRENT_DATE,'%Y-%m-%d')"; */

        $search_condition .= ($s_cid != "") ? "AND (C.id = '" . $s_cid . "' )  " : "";



        $search_condition .= " AND  ( P.status = 1)";
        $search_condition .= ($s_branch != "") ? "AND ( B.id  = '" . $s_branch . "' )  " : "";
//	    $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
        // Added 05_DEC_2017 //

        if ((is_array($s_sub_branch)) && ($s_sub_branch != '') && ($s_sub_branch[0] > 0)) {
            foreach ($s_sub_branch as $key => $value) {
                $sub_branch_id_overall[] = $value;
            }
            $sub_branch_ids = implode(',', $sub_branch_id_overall);
            $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id IN(" . $sub_branch_ids . "))" : "";
        }

        // END //


        if ($s_sdate != "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } elseif ($s_sdate != "" && $s_edate == "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '" . date("Y-m-d", strtotime($s_sdate)) . "')";
        } elseif ($s_sdate == "" && $s_edate != "") {
            $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '" . date("Y-m-d", strtotime($s_edate)) . "')";
        } else {
            //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
        }




        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND  B.id   = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }
        
        // Sub Branch Privilege
         $search_condition .=  self::SubbranchSet($set_sb);

        //echo "src = ".$search_condition;

        $count = self::subListviewCount($s_cid, $s_branch, $s_sdate, $s_edate, $s_sub_branch, $type_of_page);
        /*
         * Return the Pledge list
         */
        if ($count > 0) {

            // Rajesh 26-05-2020 live query
            /*$query = "SELECT DISTINCT             
                        B.name as branch_name,
                        PA.serial_no,
                        DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS report_date,PA.serial_no,
                        P.loan_id AS loan_no,
                        P.loan_amount AS loan_amount,
                        C.name As cust_name,C.id As cust_id,
                        C.relationship_category As cust_relation, C.relation_name AS cust_rel_name,
                        C.contact_no AS cont_no,
                        CONCAT(                            
                            C.street,', ',
                            C.location, ' - ', C.postal_code
                        ) AS customer_details,

                        GROUP_CONCAT(
                            CT.category,'-',PRO.name

                            ORDER BY CT.category DESC SEPARATOR '  ,  '
                        ) AS product_details,                       
                        
                        P.item_count AS quantity, 
                        P.total_net_weight AS net_weight                        

                        FROM " . PLEDGE . " as P 
                        LEFT JOIN " . BRANCH . " as B ON P.branch_id = B.id
                        LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id
                        LEFT JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id
                        LEFT JOIN " . PRODUCT_AUCTION . " as PA ON P.id = PA.loan_id
                        LEFT JOIN " . CATEGORIES . " as CT ON CT.id = PD.category_id
                        LEFT JOIN " . PRODUCTS . " as PRO on PRO.id = PD.product_id
                        LEFT JOIN " . PRODUCT_INTERESTS . " as PT ON P.id = PT.loan_id 
                        LEFT JOIN " . SCHEME . " AS SC  ON P.scheme_id = SC.id
			LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id
                        WHERE 1 " . $search_condition . " 
                        GROUP BY P.loan_id
                        ORDER BY P.id ASC ";*/
            // Rajesh 26-05-2020 staging updated query

            $query = "SELECT DISTINCT             
                        B.name as branch_name,
                        PA.serial_no,
                        DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS report_date,PA.serial_no,
                        P.loan_id AS loan_no,
                        P.loan_amount AS loan_amount,
                        C.name As cust_name,C.id As cust_id,
                        C.relationship_category As cust_relation, C.relation_name AS cust_rel_name,
                        C.contact_no AS cont_no,
                        CONCAT(                            
                            C.street,', ',
                            C.location, ' - ', C.postal_code
                        ) AS customer_details,

                        GROUP_CONCAT(
                           IF(  PN.pre_name IS NULL, '',   PN.pre_name),  ' ',IF( PRO.product_with IS NULL, '',  PRO.product_with), IF(PW.pre_name IS NULL, '', PW.pre_name),' ', PRO.name  ORDER BY PRO.name DESC SEPARATOR ', ') AS product_details,
                                          
                        
                        P.item_count AS quantity, 
                        P.total_net_weight AS net_weight                        

                        FROM " . PLEDGE . " as P 
                        LEFT JOIN " . BRANCH . " as B ON P.branch_id = B.id
                        LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id
                        LEFT JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id
                        LEFT JOIN " . PRODUCT_AUCTION . " as PA ON P.id = PA.loan_id
                        LEFT JOIN " . CATEGORIES . " as CT ON CT.id = PD.category_id
                        LEFT JOIN " . PRODUCTS . " as PRO on PRO.id = PD.product_id
                        LEFT JOIN " . PRODUCT_INTERESTS . " as PT ON P.id = PT.loan_id 
                        LEFT JOIN " . SCHEME . " AS SC  ON P.scheme_id = SC.id
      LEFT JOIN " . SUB_BRANCH . " AS SB ON SB.id = P.sub_branch_id

      LEFT JOIN " . PRENAME . " as PN ON PN.id = PD.pre_name_id

      LEFT JOIN ".PRODUCT_WITH." as PW ON PW.id = PD.product_with
      
                        WHERE 1 " . $search_condition . " 
                        GROUP BY P.loan_id
                        ORDER BY P.id ASC ";
            //echo $query;
            //exit;
            $preresult = $app->getexport($query);
            //echo "<pre>";print_r($preresult);
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
                $result[] = $rows;
            }
        } else {
            $result = $reports;
        }

        return $result;
    }

}
