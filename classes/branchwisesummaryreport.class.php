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
class BranchWiseSummaryReport {
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
                            if($s_sdate != "" && $s_edate != ""){  
                            $search_condition .= " AND  ( P.loan_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
                            }elseif($s_sdate != "" && $s_edate == ""){  
                            $search_condition .= " AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."')";
                            }elseif($s_sdate == "" && $s_edate != ""){ 
                            $search_condition .= " AND  ( P.loan_date <= '".date("Y-m-d",strtotime($s_edate))."')";
                            }else{
                            //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
                            }

                             if(!is_superadmin($_SESSION['session_usergroupname'])){
                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                             }
                            /*
                             * Return the Customers list
                            */
                            $query="SELECT DATE_FORMAT(P.loan_date,'%d-%b-%Y') as loan_date, MAX(P.loan_id) AS from_loan_id, MIN( P.loan_id ) AS to_loan_id, B.name, SUM( P.total_net_weight) AS total_quantityweight, SUM( P.item_count ) AS total_item, SUM( P.loan_amount ) AS total_amount
                            FROM ".PLEDGE." AS P
                            LEFT JOIN ".BRANCH." AS B ON B.id = P.branch_id
                            WHERE 1 ".$search_condition."
                            GROUP BY P.branch_id,P.loan_date
                            Order By P.loan_date DESC ";
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
                if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( P.loan_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
                }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."')";
                }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( P.loan_date <= '".date("Y-m-d",strtotime($s_edate))."')";
                }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
                }
                if(!is_superadmin($_SESSION['session_usergroupname'])){
                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                }
                 /*
                  * Return the Customers list count
                  */
                $count = self::ListviewCount($s_branch,$s_sdate,$s_edate);

                if ($count > 0) {
                 /*
                  * Return the Customers list
                  */
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query="SELECT DATE_FORMAT(P.loan_date,'%d-%b-%Y') as loan_date, MAX(P.loan_id) AS from_loan_id, MIN( P.loan_id ) AS to_loan_id, B.name, SUM( P.total_net_weight) AS total_quantityweight, SUM( P.item_count ) AS total_item, SUM( P.loan_amount ) AS total_amount
                    FROM ".PLEDGE." AS P
                    LEFT JOIN ".BRANCH." AS B ON B.id = P.branch_id
                    WHERE 1 ".$search_condition."
                    GROUP BY P.branch_id,P.loan_date
                    Order By P.loan_date DESC ". Paging::get_sql_limit();
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
                if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( P.loan_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
                }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."')";
                }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( P.loan_date <= '".date("Y-m-d",strtotime($s_edate))."')";
                }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
                }

                if(!is_superadmin($_SESSION['session_usergroupname'])){
                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                }
                 /*
                  * Return the Customers list count
                  */
                $count = self::ListviewCount($s_branch,$s_sdate,$s_edate);

                if ($count > 0) {
                 /*
                  * Return the Customers list
                  */
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query="SELECT DATE_FORMAT(P.loan_date,'%d-%b-%Y') as loan_date,CONCAT(MAX(P.loan_id),MIN(P.loan_id)) AS loan_id, B.name as branch_name, SUM(P.total_net_weight) AS total_quantity_weight, SUM( P.item_count ) AS total_item,SUM( P.loan_amount ) AS total_amount
                    FROM ".PLEDGE." AS P
                    LEFT JOIN ".BRANCH." AS B ON B.id = P.branch_id
                    WHERE 1 ".$search_condition."
                    GROUP BY P.branch_id,P.loan_date
                    Order By P.loan_date DESC"; 
                    return $app->getexport($query);
                }

            }

}
