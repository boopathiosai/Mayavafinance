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


class LogReport {
     /*
         * Define the required fields and return text
         */
        public static function getSubListCountQuery() {
        
        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_branch = $args[0];
        $s_user = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        
                
        $search_having = ($s_branch != "") ? "AND ( branch_id  = '".$s_branch."' )  ":"";
        $search_having .= ($s_user != "") ? "AND ( user_id  = '".$s_user."' )  ":"";
        
        
        if($s_sdate != "" && $s_edate != ""){  
            $search_having .= " AND  ( report_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))." 00:00' AND '".date("Y-m-d",strtotime($s_edate))." 23:59')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_having .= " AND  ( report_date >= '".date("Y-m-d",strtotime($s_sdate))." 00:00')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_having .= " AND  ( report_date <= '".date("Y-m-d",strtotime($s_edate))." 23:59')";
        }else{
            //$search_having .= " AND  ( report_date = '".date("Y-m-d")."')";
        }

        if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_having .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
        }
        

        $search_condition = ($s_branch != "") ? "AND ( U.branch_id  = '".$s_branch."' )  ":"";
        $search_condition .= ($s_user != "") ? "AND ( U.id  = '".$s_user."' )  ":"";
        
        
        if($s_sdate != "" && $s_edate != ""){  
            $search_condition .= " AND  (( DATE_FORMAT(J.created_datetime,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."') OR ( DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."'))";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_condition .= " AND  (( DATE_FORMAT(J.created_datetime,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."') OR ( DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."'))";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_condition .= " AND  ((DATE_FORMAT(J.created_datetime,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."') OR (DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."'))";
        }else{
            //$search_condition .= " AND  ( DATE_FORMAT(J.created_datetime,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }
	
		/*Fixed Bud ID :0010611 J.id & J.branch_id */
        if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND U.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
            $search_condition_b = $search_condition.($_SESSION['session_user_branch_id'] != "")?" AND U.id = '".$_SESSION['session_user_branch_id']."'":"";
        }
// For 3 days list - DSSK 08_JUL_2019
        if($s_sdate == "" && $s_edate == ""){ 
//            $prifix_search = " AND (J.created_datetime BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW()) || (J.modified_datetime BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW()) ";
            $serch_create_date = " where (J.created_datetime BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";
            $serch_modified_date = " where (J.modified_datetime BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";
        }else{
        //$prifix_search = " HAVING  (group_date <> '' and group_date <> '0000-00-00'  and group_date <> '1970-01-01' )  ".$search_having; 
            $prifix_search = " WHERE 1 ".$search_condition; 
        }

        //$prifix_search = " HAVING  (group_date <> '' and group_date <> '0000-00-00'  and group_date <> '1970-01-01' )  ".$search_having; 
//        $prifix_search = " WHERE 1 ".$search_condition; 
        
        $suffix_subquery = "INNER JOIN ".USER_GROUP." AS UG ON U.user_group_id = UG.id  
                            INNER JOIN ".BRANCH." AS B ON B.id = U.branch_id".$prifix_search;
            //echo "<pre>";                
            $query = "
                       ( 
                           SELECT 
                                COUNT(U.id)
                            FROM ".BRANCH." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date."
                        )
                        
                        UNION

                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".BRANCH." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                            
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".DEPARTMENT." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION

                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".DEPARTMENT." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".USERS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".USERS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".CUSTOMERS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".CUSTOMERS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".CATEGORIES." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".CATEGORIES." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".PRODUCTS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".PRODUCTS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".SCHEME." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".SCHEME." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".CASH_FLOW_TRANSACTIONS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id  AND J.editable = 1
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".CASH_FLOW_TRANSACTIONS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id AND J.editable = 1
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".PLEDGE." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".PLEDGE." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".PLEDGE." AS J 
                            JOIN ".PRODUCT_INTERESTS." AS PI ON J.id = PI.loan_id
                            INNER JOIN ".USERS." AS U ON PI.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".PLEDGE." AS J 
                            JOIN ".PRODUCT_INTERESTS." AS PI ON J.id = PI.loan_id
                            LEFT JOIN ".USERS." AS U ON PI.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".PLEDGE." AS J 
                            JOIN ".RENEWAL." AS PN ON J.id = PN.old_loan_id
                            LEFT JOIN ".USERS." AS U ON PN.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".PLEDGE." AS J 
                            JOIN ".REDEMPTION." AS PR ON J.id = PR.loan_id
                            LEFT JOIN ".USERS." AS U ON PR.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".PLEDGE." AS J 
                            JOIN ".PARTIAL_AMOUNT_PAYMENT." AS PAP ON J.id = PAP.old_loan_id
                            LEFT JOIN ".USERS." AS U ON PAP.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".PLEDGE." AS J 
                            JOIN ".PAY_EXCESS_AMOUNT." AS PEA ON J.id = PEA.old_loan_id
                            LEFT JOIN ".USERS." AS U ON PEA.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date."  
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".PLEDGE." AS J 
                            LEFT JOIN ".PRODUCT_AUCTION." AS PA ON J.id = PA.loan_id
                            LEFT JOIN ".USERS." AS U ON PA.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".PLEDGE." AS J 
                            LEFT JOIN ".PRODUCT_AUCTION." AS PA ON J.id = PA.loan_id
                            LEFT JOIN ".USERS." AS U ON PA.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )

                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".MONEY_SHORTAGE." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            LEFT JOIN ".BRANCH." AS RB ON RB.id = J.branch_id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".MONEY_SHORTAGE." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            LEFT JOIN ".BRANCH." AS RB ON RB.id = J.branch_id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".AMOUNT_TRANSFER." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            LEFT JOIN ".BRANCH." AS RB ON RB.id = J.request_branch_id 
                            LEFT JOIN ".BRANCH." AS AB ON AB.id = J.transfer_branch_id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".AMOUNT_TRANSFER." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            LEFT JOIN ".BRANCH." AS RB ON RB.id = J.request_branch_id 
                            LEFT JOIN ".BRANCH." AS AB ON AB.id = J.transfer_branch_id 
                            ".$suffix_subquery." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".AMOUNT_TRANSFER." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            LEFT JOIN ".BRANCH." AS RB ON RB.id = J.request_branch_id 
                            LEFT JOIN ".BRANCH." AS AB ON AB.id = J.transfer_branch_id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".BANK_TRANSACTION." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                COUNT(U.id)
                            FROM ".BANK_TRANSACTION." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        ";
        
        return $query;
        
        }



        public static function getSubListQuery() {
        
        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_branch = $args[0];
        $s_user = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        
                
        $search_having = ($s_branch != "") ? "AND ( branch_id  = '".$s_branch."' )  ":"";
        $search_having .= ($s_user != "") ? "AND ( user_id  = '".$s_user."' )  ":"";
        
        
        if($s_sdate != "" && $s_edate != ""){  
            $search_having .= " AND  ( report_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))." 00:00' AND '".date("Y-m-d",strtotime($s_edate))." 23:59')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_having .= " AND  ( report_date >= '".date("Y-m-d",strtotime($s_sdate))." 00:00')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_having .= " AND  ( report_date <= '".date("Y-m-d",strtotime($s_edate))." 23:59')";
        }else{
            //$search_having .= " AND  ( report_date = '".date("Y-m-d")."')";
        }

        if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_having .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
        }
        

        $search_condition = ($s_branch != "") ? "AND ( U.branch_id  = '".$s_branch."' )  ":"";
        $search_condition .= ($s_user != "") ? "AND ( U.id  = '".$s_user."' )  ":"";
        
        
        if($s_sdate != "" && $s_edate != ""){  
            $search_condition .= " AND  ( DATE_FORMAT(J.created_datetime,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_condition .= " AND  ( DATE_FORMAT(J.created_datetime,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_condition .= " AND  ( DATE_FORMAT(J.created_datetime,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
        }else{
            //$search_condition .= " AND  ( DATE_FORMAT(J.created_datetime,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }

        if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND U.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
        }

        
// FOR 3days list -DSSK 08_JUL_2019
        
        if($s_sdate == "" && $s_edate == ""){  
//             echo $created_datetime = " where  (J.created_datetime BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())"; 
//             echo $created_datetime = ""; 
//            $search_having .= " AND (J.created_datetime BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";
//            $prifix_search =  " AND (J.created_datetime BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW()) || (J.modified_datetime BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW()) ";
//            $prifix_search =  " AND (report_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW()) ";
            $serch_create_date = " where (J.created_datetime BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";
            $serch_modified_date = " where (J.modified_datetime BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";
         }else{
        // END
        $prifix_search = " HAVING  (group_date <> '' and group_date <> '0000-00-00'  and group_date <> '1970-01-01' )  ".$search_having; 
        //$prifix_search = " WHERE 1 ".$search_condition; 
//        $prifix_search =  " AND (J.created_datetime BETWEEN DATE_SUB(NOW(),INTERVAL 1000 DAY) AND NOW()) || (J.modified_datetime BETWEEN DATE_SUB(NOW(),INTERVAL 1000 DAY) AND NOW()) ";
         }
//        $prifix_search = " HAVING  (group_date <> '' and group_date <> '0000-00-00'  and group_date <> '1970-01-01' )  ".$search_having; 
        //$prifix_search = " WHERE 1 ".$search_condition; 
        
        $suffix_subquery = "INNER JOIN ".USER_GROUP." AS UG ON U.user_group_id = UG.id  
                            INNER JOIN ".BRANCH." AS B ON B.id = U.branch_id".$prifix_search;
            //echo "<pre>";                
            $query = "
                        
                       ( 
                           SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Masters' AS module,
                                'Branch' AS page,
                                'Add New' AS action,
                                CONCAT(J.name,' - (',J.id,')') AS record
                            FROM ".BRANCH." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION

                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.modified_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.modified_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,
                                'Masters' AS module,
                                'Branch' AS page,
                                'Modified' AS action,
                                CONCAT(J.name,' - (',J.id,')') AS record
                            FROM ".BRANCH." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                            
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Masters' AS module,
                                'Department' AS page,
                                'Add New' AS action,
                                CONCAT(J.department_name,' - (',J.id,')') AS record
                            FROM ".DEPARTMENT." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION

                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.modified_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.modified_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,
                                'Masters' AS module,
                                'Department' AS page,
                                'Modified' AS action,
                                CONCAT(J.department_name,' - (',J.id,')') AS record
                            FROM ".DEPARTMENT." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Masters' AS module,
                                'Users' AS page,
                                'Add New' AS action,
                                CONCAT(J.name,' - (',J.id,')') AS record
                            FROM ".USERS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.modified_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.modified_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,
                                'Masters' AS module,
                                'Users' AS page,
                                'Modified' AS action,
                                CONCAT(J.name,' - (',J.id,')') AS record
                            FROM ".USERS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Masters' AS module,
                                'Customer' AS page,
                                'Add New' AS action,
								
                                CONCAT( J.relation_name,' - (', J.id,')') AS record
                            FROM ".CUSTOMERS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date. " 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.modified_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.modified_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,
                                'Masters' AS module,
                                'Customer' AS page,
                                'Modified' AS action,
                                CONCAT( J.relation_name,' - (', J.id,')') AS record
                            FROM ".CUSTOMERS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Masters' AS module,
                                'Category' AS page,
                                'Add New' AS action,
                                CONCAT(J.category,' - (',J.id,')') AS record
                            FROM ".CATEGORIES." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.modified_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.modified_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,
                                'Masters' AS module,
                                'Category' AS page,
                                'Modified' AS action,
                                CONCAT(J.category,' - (',J.id,')') AS record
                            FROM ".CATEGORIES." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Masters' AS module,
                                'Product' AS page,
                                'Add New' AS action,
                                CONCAT(J.name,' - (',J.id,')') AS record
                            FROM ".PRODUCTS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery.$serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.modified_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.modified_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,
                                'Masters' AS module,
                                'Product' AS page,
                                'Modified' AS action,
                                CONCAT(J.name,' - (',J.id,')') AS record
                            FROM ".PRODUCTS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Masters' AS module,
                                'Scheme' AS page,
                                'Add New' AS action,
                                CONCAT(J.name,' - (',J.id,')') AS record
                            FROM ".SCHEME." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery.$serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.modified_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.modified_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,
                                'Masters' AS module,
                                'Scheme' AS page,
                                'Modified' AS action,
                                CONCAT(J.name,' - (',J.id,')') AS record
                            FROM ".SCHEME." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transaction' AS module,
                                'Cash Flow Transfer' AS page,
                                'Add New' AS action,
                                CONCAT(J.cash_purpose,' - (',J.id,')') AS record
                            FROM ".CASH_FLOW_TRANSACTIONS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id  AND J.editable = 1
                            ".$suffix_subquery.$serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.modified_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.modified_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,
                                'Transaction' AS module,
                                'Cash Flow Transfer' AS page,
                                'Modified' AS action,
                                CONCAT(J.cash_purpose,' - (',J.id,')') AS record
                            FROM ".CASH_FLOW_TRANSACTIONS." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id AND J.editable = 1
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        UNION
                        
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Product Pledge' AS page,
                                'Add New' AS action,
                                CONCAT(J.loan_id,' - (',J.id,')') AS record
                            FROM ".PLEDGE." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery.$serch_create_date." 
                        )
                        
                       /* UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.modified_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.modified_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,
                                'Transactions' AS module,
                                'Product Pledge' AS page,
                                'Modified' AS action,
                                CONCAT(J.loan_id,' - (',J.id,')') AS record
                            FROM ".PLEDGE." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )*/
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(PI.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(PI.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(PI.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Pay Interest' AS page,
                                'New Interest' AS action,
                                CONCAT(J.loan_id,' - (',PI.id,')') AS record
                            FROM ".PLEDGE." AS J 
                            JOIN ".PRODUCT_INTERESTS." AS PI ON J.id = PI.loan_id
                            INNER JOIN ".USERS." AS U ON PI.created_by = U.id 
                            ".$suffix_subquery.$serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(PI.modified_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(PI.modified_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(PI.modified_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,
                                'Transactions' AS module,
                                'Pay Interest' AS page,
                                'Modify Interest' AS action,
                                CONCAT(J.loan_id,' - (',PI.id,')') AS record
                            FROM ".PLEDGE." AS J 
                            JOIN ".PRODUCT_INTERESTS." AS PI ON J.id = PI.loan_id
                            LEFT JOIN ".USERS." AS U ON PI.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(PN.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(PN.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(PN.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Renewal' AS page,
                                'Renew Products Mod' AS action,
                                CONCAT(J.loan_id,' - (',(IF(PN.new_loan_id <> '',PN.new_loan_id,'#')),')') AS record
                            FROM ".PLEDGE." AS J 
                            JOIN ".RENEWAL." AS PN ON J.id = PN.old_loan_id
                            LEFT JOIN ".USERS." AS U ON PN.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(PR.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(PR.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(PR.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Redemption' AS page,
                                'Redeem Products' AS action,
                                CONCAT(J.loan_id,' - (',PR.id,')') AS record
                            FROM ".PLEDGE." AS J 
                            JOIN ".REDEMPTION." AS PR ON J.id = PR.loan_id
                            LEFT JOIN ".USERS." AS U ON PR.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(PAP.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(PAP.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(PAP.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Partial Amount Payment' AS page,
                                'Partial Payment' AS action,
                                CONCAT(J.loan_id,' - (',(IF(PAP.new_loan_id <> '',PAP.new_loan_id,'#')),')') AS record
                            FROM ".PLEDGE." AS J 
                            JOIN ".PARTIAL_AMOUNT_PAYMENT." AS PAP ON J.id = PAP.old_loan_id
                            LEFT JOIN ".USERS." AS U ON PAP.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(PEA.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(PEA.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(PEA.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Excess Amount To Customer' AS page,
                                'Excess Payment' AS action,
                                CONCAT(J.loan_id,' - (',(IF(PEA.new_loan_id <> '',PEA.new_loan_id,'#')),')') AS record
                            FROM ".PLEDGE." AS J 
                            JOIN ".PAY_EXCESS_AMOUNT." AS PEA ON J.id = PEA.old_loan_id
                            LEFT JOIN ".USERS." AS U ON PEA.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(PA.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(PA.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(PA.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Auction Management' AS page,
                                'Auction Bidding' AS action,
                                CONCAT(J.loan_id,' - (',PA.id,')') AS record
                            FROM ".PLEDGE." AS J 
                            LEFT JOIN ".PRODUCT_AUCTION." AS PA ON J.id = PA.loan_id
                            LEFT JOIN ".USERS." AS U ON PA.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(PA.modified_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(PA.modified_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(PA.modified_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Auction Management' AS page,
                                'Modify Auction Bidding' AS action,
                                CONCAT(J.loan_id,' - (',PA.id,')') AS record
                            FROM ".PLEDGE." AS J 
                            LEFT JOIN ".PRODUCT_AUCTION." AS PA ON J.id = PA.loan_id
                            LEFT JOIN ".USERS." AS U ON PA.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )

                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Raise Money Shortage Request' AS page,
                                'New Raise Request' AS action,
                                CONCAT(RB.name,' - (',J.id,')') AS record
                            FROM ".MONEY_SHORTAGE." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            LEFT JOIN ".BRANCH." AS RB ON RB.id = J.branch_id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.modified_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.modified_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Raise Money Shortage Request' AS page,
                                'Modified Raise Request' AS action,
                                CONCAT(RB.name,' - (',J.id,')') AS record
                            FROM ".MONEY_SHORTAGE." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            LEFT JOIN ".BRANCH." AS RB ON RB.id = J.branch_id 
                            ".$suffix_subquery. $serch_modified_date."  
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Allocate Branches' AS page,
                                'Allocate' AS action,
                                CONCAT(AB.name,' to ',RB.name) AS record
                            FROM ".AMOUNT_TRANSFER." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            LEFT JOIN ".BRANCH." AS RB ON RB.id = J.request_branch_id 
                            LEFT JOIN ".BRANCH." AS AB ON AB.id = J.transfer_branch_id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.transfer_date,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.transfer_date,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.transfer_date,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Transfer Money' AS page,
                                'Transfer' AS action,
                                CONCAT('From ',AB.name,' to ',RB.name) AS record
                            FROM ".AMOUNT_TRANSFER." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            LEFT JOIN ".BRANCH." AS RB ON RB.id = J.request_branch_id 
                            LEFT JOIN ".BRANCH." AS AB ON AB.id = J.transfer_branch_id 
                            ".$suffix_subquery." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.received_date,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.received_date,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.received_date,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Received Money' AS page,
                                'Receive' AS action,
                                CONCAT('By ',RB.name,' from ',AB.name) AS record
                            FROM ".AMOUNT_TRANSFER." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            LEFT JOIN ".BRANCH." AS RB ON RB.id = J.request_branch_id 
                            LEFT JOIN ".BRANCH." AS AB ON AB.id = J.transfer_branch_id 
                            ".$suffix_subquery." 
                        )
                        
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.created_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.created_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.created_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,                         
                                'Transactions' AS module,
                                'Bank Transaction' AS page,
                                'Add New' AS action,
                                CONCAT(B.name,' - (',J.id,')') AS record
                            FROM ".BANK_TRANSACTION." AS J 
                            LEFT JOIN ".USERS." AS U ON J.created_by = U.id 
                            ".$suffix_subquery. $serch_create_date." 
                        )
                        
                        UNION
                        
                        (
                            SELECT 
                                U.id AS user_id,
                                U.branch_id AS branch_id,
                                U.name AS user_name,
                                UG.name AS user_group,
                                B.name AS user_branch,

                                DATE_FORMAT(J.modified_datetime,'%Y-%m-%d %H:%i') AS report_date, DATE_FORMAT(J.modified_datetime,'%Y-%m-%d') AS group_date,
                                DATE_FORMAT(J.modified_datetime,'%d-%b-%Y  %I:%i %p') AS log_date_time,
                                'Transactions' AS module,
                                'Bank Transaction' AS page,
                                'Modified' AS action,
                                CONCAT(B.name,' - (',J.id,')') AS record
                            FROM ".BANK_TRANSACTION." AS J 
                            LEFT JOIN ".USERS." AS U ON J.modified_by = U.id 
                            ".$suffix_subquery. $serch_modified_date." 
                        )
                        
                        ORDER BY report_date DESC,user_branch,user_name, module, page, action ASC ";
//        echo  $query;
        return $query;
        
        }

        
        private static function getTransPeriod(){
        
            $app = $GLOBALS['app'];
            

            $query = "SELECT 
            
            DATE_FORMAT((CF.date_transaction),'%Y-%m-%d') AS sdate
            
            
            FROM ".CASH_FLOW_TRANSACTIONS." as CF 

            ORDER BY CF.id ASC LIMIT 0,1
            ";
            $sdata = $app->getrows($query);
            
            $query = "SELECT 
            
            
            DATE_FORMAT((CF.date_transaction),'%Y-%m-%d') AS edate
            
            FROM ".CASH_FLOW_TRANSACTIONS." as CF 

            ORDER BY CF.id DESC LIMIT 0,1
            ";
            $edata = $app->getrows($query);
            
            //p_arr($sdata);
            
            //return array("sdate"=>$sdata[0]['sdate'],"edate"=>$edata[0]['edate']);
            return array("sdate"=>$sdata[0]['sdate'],"edate"=>date("Y-m-d"));
        
        
        }
        
        public static function ListviewCount() {
            /*
             * Get the function parameters , if exist . And make the search condition
             */

            $args = func_get_args();

            $app = $GLOBALS['app'];
            
            $s_branch = $args[0];
            $s_user = $args[1];
            $s_sdate = $args[2];
            $s_edate = $args[3];



            
            /*
             * Return the Pledges list Count
             * /

        if($s_sdate != "" && $s_edate != ""){  
            $search_condition .= " AND  (( DATE_FORMAT(CF.created_datetime,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."') OR ( DATE_FORMAT(CF.modified_datetime,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."'))";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_condition .= " AND  (( DATE_FORMAT(CF.created_datetime,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."') OR ( DATE_FORMAT(CF.modified_datetime,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."'))";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_condition .= " AND  ((DATE_FORMAT(CF.created_datetime,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."') OR (DATE_FORMAT(CF.modified_datetime,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."'))";
        }else{
            //$search_condition .= " AND  ( DATE_FORMAT(J.created_datetime,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }

        if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
            $search_condition_b = $search_condition.($_SESSION['session_user_branch_id'] != "")?" AND CF.id = '".$_SESSION['session_user_branch_id']."'":"";
        }
                                    
            $cf_cnt = $app->getrow("SELECT COUNT(*) FROM ".CASH_FLOW_TRANSACTIONS." AS CF WHERE 1 ".$search_condition);
            $m_cnt1 = $app->getrow("SELECT COUNT(*) FROM ".BRANCH." AS CF WHERE 1 ".$search_condition_b);
            $m_cnt2 = $app->getrow("SELECT COUNT(*) FROM ".USERS." AS CF WHERE 1 ".$search_condition);
            $m_cnt3 = $app->getrow("SELECT COUNT(*) FROM ".DEPARTMENT." AS CF WHERE 1 ".$search_condition);
            $m_cnt4 = $app->getrow("SELECT COUNT(*) FROM ".CATEGORIES." AS CF WHERE 1 ".$search_condition);
            $m_cnt5 = $app->getrow("SELECT COUNT(*) FROM ".PRODUCTS." AS CF WHERE 1 ".$search_condition);
            $m_cnt6 = $app->getrow("SELECT COUNT(*) FROM ".CUSTOMERS." AS CF WHERE 1 ".$search_condition);
            $m_cnt7 = $app->getrow("SELECT COUNT(*) FROM ".SETTINGS." AS CF WHERE 1 ".$search_condition);
            $m_cnt8 = $app->getrow("SELECT COUNT(*) FROM ".SCHEME." AS CF WHERE 1 ".$search_condition);
            
            //$count  = intval((2.5*$cf_cnt[0])+ 3* ($m_cnt1[0]+$m_cnt2[0]+$m_cnt3[0]+$m_cnt4[0]+$m_cnt5[0]+$m_cnt6[0]+$m_cnt7[0]+$m_cnt8[0]));
            //return $count;
            
            $count  = intval($cf_cnt[0]+ $m_cnt1[0]+$m_cnt2[0]+$m_cnt3[0]+$m_cnt4[0]+$m_cnt5[0]+$m_cnt6[0]+$m_cnt7[0]+$m_cnt8[0]);
            return round(1.8*$count);*/
            $count = 0;
            if(!isset($_SESSION["log_report_count"]))
                $count = (int)$_SESSION["log_report_count"];
            if($count == 0){    
                $cquery = self::getSubListCountQuery($s_branch, $s_user, $s_sdate, $s_edate); 
                $crows = $app->getrows($cquery);
                
                $count = 0;
                foreach($crows as $crow){
                    $count += (int)$crow[0];
                }
                $_SESSION["log_report_count"] = $count;
            } 
            
            return $count;
       }


    public static function Listview() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_branch = $args[0];
        $s_user = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        
        $count = self::ListviewCount($s_branch, $s_user, $s_sdate, $s_edate);

        //if ($count > 0) 
        {


            
            /*$data = self::getTransPeriod();
            if($s_sdate == "") $s_sdate = $data['sdate'] ;
            if($s_edate == "") $s_edate = $data['edate'] ;
            //echo $s_sdate."  -  ".$s_edate;
            if($s_sdate != '' && $s_edate != '')
                $data = ListDateRange($s_sdate, $s_edate);
            else
                $data = array();*/
                
            //p_arr($data);
            //echo $s_sdate."  -  ".$s_edate;
              
            Paging::set_max( $count );
            Paging::calc();
            $query = self::getSubListQuery($s_branch, $s_user, $s_sdate, $s_edate). Paging::get_sql_limit();
			//echo $query;
            //echo "<pre>"; echo $query."<hr>";die;
            
            
            // calculation for paging
            return $app->getrows($query);
            
            /*$records = $app->getrows($query);
            //p_arr($records);

            
            if(count($records)>0){
            foreach($records as $record){
                $key = $record['group_date'];
                $data[$key] = $record;

                $query = self::getSubListQuery($s_branch, $s_user, $key, $key);
                $sub_records = $app->getrows($query);

                
                $user_ids = fetchFields($sub_records , 'user_id'); //array_unique();
                $branch_ids =  fetchFields($sub_records , 'branch_id'); //array_unique();
                
                $user_ids = (count($user_ids) > 0) ? array_unique($user_ids) : $user_ids;
                $branch_ids = (count($branch_ids) > 0) ? array_unique($branch_ids) : $branch_ids;

                //p_arr($user_ids);  p_arr($branch_ids);

                $data[$key]['no_of_user'] = count($user_ids);
                $data[$key]['no_of_branch'] = count($branch_ids);
                $data[$key]['no_of_record'] = count($sub_records);
            }
            }
            //p_arr($data);
            $data = array_filter($data);
            //p_arr($data);
           
            Paging::set_max( count($data) );
            Paging::calc();// calculation for paging
            
            $start = Paging::getListStart();
            $end = Paging::getPerPage();
            //echo $start ."  " . $end;
            $si=0;
            foreach($data as $key=>$val){
             
                 if($si>= ($start+$end) ) break;

                 if($si >= $start){
                    if(is_array($data)){
                       $ret_data[] = $val;
                       
                    }
                 }
                 $si++;
                 
                 
                 
            }
            
            //if(count($ret_data)>0){ array_filter($ret_data); }
            
            //p_arr($ret_data);
            
            return $ret_data;*/
        }    
        
    }
    
    
   public static function subListviewCount() {
            /*
             * Get the function parameters , if exist . And make the search condition
             */

            $args = func_get_args();

            $app = $GLOBALS['app'];
            
            $s_branch = $args[0];
            $s_user = $args[1];
            $s_sdate = $args[2];
            $s_edate = $args[3];

            /*
             * Return the Pledges list Count
             */

            $query = self::getSubListQuery($s_branch, $s_user, $s_sdate, $s_edate);
            //echo $query;
            $data = $app->getrows($query);
            
            return $count = count($data);
     
       }


    public static function subListview() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_branch = $args[0];
        $s_user = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        
        $count = self::subListviewCount($s_branch, $s_user, $s_sdate, $s_edate);

        if ($count > 0) 
        {
            Paging::set_max( $count );
            Paging::calc();// calculation for paging

            $query = self::getSubListQuery($s_branch, $s_user, $s_sdate, $s_edate);
            //echo "<pre>".$query."</pre>";
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

        $s_branch = $args[0];
        $s_user = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        
                
        $s_branch = $args[0];
        $s_user = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        
        $query = self::getSubListQuery($s_branch, $s_user, $s_sdate, $s_edate);
        //echo $query;
        //exit;
        return $app->getexport($query);


    }
    

}