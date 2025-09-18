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
class CustomerWiseReport {
    
        public static function ListviewCount() {
                            /*
                             * Get the function parameters , if exist . And make the search condition
                             */
							//Modified by karthik
                            $args = func_get_args();

                            $app = $GLOBALS['app'];
                            $s_key = $args[0];
                            $s_branch = $args[0];
							$s_sdate = $args[1];
							$s_edate = $args[2];

                            $search_condition = ($s_branch != "")? "AND ( B.branch_id  = '".$s_branch."' )  ":"";
							
							$search_condition .= (
							($s_sdate != "" && $s_edate != "") ? 
							" AND  ( P.loan_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."'" :
							($s_sdate != "" && $s_edate == "") ? 
							" AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."'" :
							($s_sdate == "" && $s_edate != "") ? 
							" AND  ( P.loan_date <= '".date("Y-m-d",strtotime($s_edate))."'" :
							"");

							 if($_SESSION['session_usergroupname'] != 'Super Admin'){
								 $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
							 }
                            /*
                             * Return the Customers list
                             */
							$query = "SELECT P.loan_id,DATE_FORMAT(P.loan_date,'%d-%b-%Y %I:%i %p') as loan_date,P.loan_amount, 
							PRO.name as descrption,PI.	interest_amount,PI.paid_on,PI.pay_interest_details,PEX.excess_amount,PAMT.partial_amount,PAMT.created_datetime
							FROM ".PLEDGE." as P 
							LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
							LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id 
							LEFT JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id 
							LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
							LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
							LEFT JOIN ".PRODUCT_INTERESTS." as PI on PI.loan_id = P.id
							LEFT JOIN ".REDEMPTION." as RD on RD.loan_id = P.id
							LEFT JOIN ".PAY_EXCESS_AMOUNT." as PEX on PEX.old_loan_id = P.id
							LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." as PAMT on PAMT.old_loan_id = P.id
							WHERE 1 ".$search_condition." 
							GROUP BY P.loan_id
							ORDER BY P.loan_id";
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

				$search_condition = ($s_branch != "")? "AND ( B.branch_id  = '".$s_branch."' )  ":"";
				$search_condition .= (
				($s_sdate != "" && $s_edate != "") ? 
				" AND  ( P.loan_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."'" :
				($s_sdate != "" && $s_edate == "") ? 
				" AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."'" :
				($s_sdate == "" && $s_edate != "") ? 
				" AND  ( P.loan_date <= '".date("Y-m-d",strtotime($s_edate))."'" :
				"");

                if($_SESSION['session_usergroupname'] != 'Super Admin'){
                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                }
                 /*
                  * Return the Customers list count
                  */
                $count = self::ListviewCount($s_key, $s_cus_id,$search_fhw,$search_key_location,$s_branch);

                if ($count > 0) {
                 /*
                  * Return the Customers list
                  */
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
		            
					$query = "SELECT P.loan_id,DATE_FORMAT(P.loan_date,'%d-%b-%Y %I:%i %p') as loan_date,P.loan_amount, 
					PRO.name as descrption,PI.	interest_amount,PI.paid_on,PEX.excess_amount,PAMT.partial_amount,PAMT.created_datetime
					FROM ".PLEDGE." as P 
					LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
					LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id 
					LEFT JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id 
					LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
					LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
					LEFT JOIN ".PRODUCT_INTERESTS." as PI on PI.loan_id = P.id
					LEFT JOIN ".REDEMPTION." as RD on RD.loan_id = P.id
					LEFT JOIN ".PAY_EXCESS_AMOUNT." as PEX on PEX.old_loan_id = P.id
					LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." as PAMT on PAMT.old_loan_id = P.id
					WHERE 1 ".$search_condition." 
					GROUP BY P.loan_id
					ORDER BY P.loan_id ". Paging::get_sql_limit();
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

				$search_condition = ($s_branch != "")? "AND ( B.branch_id  = '".$s_branch."' )  ":"";
				$search_condition .= (
				($s_sdate != "" && $s_edate != "") ? 
				" AND  ( P.loan_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."'" :
				($s_sdate != "" && $s_edate == "") ? 
				" AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."'" :
				($s_sdate == "" && $s_edate != "") ? 
				" AND  ( P.loan_date <= '".date("Y-m-d",strtotime($s_edate))."'" :
				"");
                if($_SESSION['session_usergroupname'] != 'Super Admin'){
                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                }
                 /*
                  * Return the Customers list count
                  */
                $count = self::ListviewCount($s_key, $s_cus_id,$search_fhw,$search_key_location,$s_branch);

                if ($count > 0) {
                 /*
                  * Return the Customers list
                  */
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
					$query = "SELECT P.loan_id,DATE_FORMAT(P.loan_date,'%d-%b-%Y %I:%i %p') as loan_date,P.loan_amount, 
					PRO.name as descrption,PI.	interest_amount,PI.paid_on,PEX.excess_amount,PAMT.partial_amount,
					PAMT.created_datetime
					FROM ".PLEDGE." as P 
					LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
					LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id 
					LEFT JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id 
					LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
					LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
					LEFT JOIN ".PRODUCT_INTERESTS." as PI on PI.loan_id = P.id
					LEFT JOIN ".REDEMPTION." as RD on RD.loan_id = P.id
					LEFT JOIN ".PAY_EXCESS_AMOUNT." as PEX on PEX.old_loan_id = P.id
					LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." as PAMT on PAMT.old_loan_id = P.id
					WHERE 1 ".$search_condition." 
					GROUP BY P.loan_id
					ORDER BY P.loan_id ". Paging::get_sql_limit();
					return $app->getexport($query);
                }

			}
	

}
