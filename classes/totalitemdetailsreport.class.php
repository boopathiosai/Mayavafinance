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
class TotalItemDetailsReport {
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

                            $search_condition = ($s_branch != "")? "AND ( B.id  = '".$s_branch."' )  ":"";
                            $search_condition .= (
                                    ($s_sdate != "" && $s_edate != "") ? 
                                        " AND  ( P.loan_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')" :
                                        ($s_sdate != "" && $s_edate == "") ? 
                                            " AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."')" :
                                            ($s_sdate == "" && $s_edate != "") ? 
                                                " AND  ( P.loan_date <= '".date("Y-m-d",strtotime($s_edate))."')" :
                                                "");

                             if($_SESSION['session_usergroupname'] != 'Super Admin'){
                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                             }
                            /*
                             * Return the Customers list
                            */
							$query = "SELECT 
							DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS released_date, 
							B.name as branch_name, 
							SUM(PAI.auction_items) AS released_total_quantity,
							SUM(PAI.auction_net_weight ) As released_product_total_weight,
							SUM(PAI.auction_amount) AS amount
							FROM ".CASH_FLOW_TRANSACTIONS." AS CF
							LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
							LEFT JOIN ".REDEMPTION." as PR ON PR.cash_flow_id = CF.id
							LEFT JOIN ".PRODUCT_AUCTION." as PAI ON PAI.loan_id = PR.loan_id
							WHERE 1 ".$search_condition." 
							GROUP BY CF.branch_id, CF.date_transaction
							ORDER BY CF.date_transaction ASC ";
							$data = $app->getrows($query);
                            return $count = count($data);

     }

        public static function Listview() 
    {
                /*
                 * Get the function parameters , if exist . And make the search condition
                 */
                //Modified by karthik
                $args = func_get_args();

                $app = $GLOBALS['app'];
                $paging = $GLOBALS['paging'];  
                $s_key = $args[0];
                $s_branch = $args[0];
                $s_sdate = $args[1];
                $s_edate = $args[2];

                $search_condition = ($s_branch != "")? "AND ( B.id  = '".$s_branch."' )  ":"";
                $search_condition .= (
                ($s_sdate != "" && $s_edate != "") ? 
                    " AND  ( P.loan_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')" :
                    ($s_sdate != "" && $s_edate == "") ? 
                        " AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."')" :
                        ($s_sdate == "" && $s_edate != "") ? 
                            " AND  ( P.loan_date <= '".date("Y-m-d",strtotime($s_edate))."')" :
                            "");

                        if($_SESSION['session_usergroupname'] != 'Super Admin'){
                                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                        }
                 /*
                  * Return the Customers list count
                  */
                $count = self::ListviewCount($s_branch, $s_sdate, $s_edate);

                if ($count > 0) {
                 /*
                  * Return the Customers list
                  */
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT 
					DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS released_date, 
					B.name as branch_name, 
					SUM(PAI.auction_items) AS released_total_quantity,
					SUM(PAI.auction_net_weight) As released_product_total_weight,
					SUM(PAI.auction_amount+PR.principle_amount) AS amount
					FROM ".CASH_FLOW_TRANSACTIONS." AS CF
					LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
					LEFT JOIN ".REDEMPTION." as PR ON PR.cash_flow_id = CF.id
					LEFT JOIN ".PRODUCT_AUCTION." as PAI ON PAI.loan_id = PR.loan_id
					WHERE 1 ".$search_condition." 
					GROUP BY CF.branch_id, CF.date_transaction
					ORDER BY CF.date_transaction ASC ". Paging::get_sql_limit();
                    return $app->getrows($query);
                }
    }

            public static function Listexport() 
            {
                /*
                 * Get the function parameters , if exist . And make the search condition
                 */
                //Modified by karthik
                $args = func_get_args();

                $app = $GLOBALS['app'];
                $paging = $GLOBALS['paging'];  
                $s_key = $args[0];
                $s_branch = $args[0];
                $s_sdate = $args[1];
                $s_edate = $args[2];

				$search_condition = ($s_branch != "")? "AND ( B.id  = '".$s_branch."' )  ":"";
				$search_condition .= (
				($s_sdate != "" && $s_edate != "") ? 
				" AND  ( P.loan_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')" :
				($s_sdate != "" && $s_edate == "") ? 
				" AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."')" :
				($s_sdate == "" && $s_edate != "") ? 
				" AND  ( P.loan_date <= '".date("Y-m-d",strtotime($s_edate))."')" :
				"");

                if($_SESSION['session_usergroupname'] != 'Super Admin'){
                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                }
                 /*
                  * Return the Customers list count
                  */
                $count = self::ListviewCount($s_branch, $s_sdate, $s_edate);

                if ($count > 0) {
                 /*
                  * Return the Customers list
                  */
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT P.loan_id,DATE_FORMAT( P.loan_date, '%d-%M-%y' ) AS loan_date,
					P.loan_amount,
					PT.interest_amount,
					PT.additional_charge,
					PT.interest_days,
					DATE_FORMAT( DATE_ADD( IF( PT.paid_on <> '', PT.paid_on, P.loan_date ) , INTERVAL 1
                    MONTH ) , '%d-%M-%y' ) AS due_date,
					DATE_FORMAT( PT.paid_on, '%d-%M-%y' ) AS paid_date,
					SC.name AS scheme_name,
					IF(P.status=0, 'Closed', 'Opened' ) AS paid_status
					FROM ".PRODUCT_INTERESTS." AS PT
                    LEFT JOIN ". PLEDGE." AS P ON PT.loan_id = P.id
					LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
                    LEFT JOIN ".SCHEME." AS SC ON P.scheme_id = SC.id
                    WHERE 1 ".$search_condition." 
                    ORDER BY P.loan_id";
				
                    return $app->getexport($query);
                }

            }

}
