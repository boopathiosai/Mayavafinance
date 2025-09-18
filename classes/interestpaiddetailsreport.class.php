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
class InterestPaidDetailsReport {
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
                            " AND  ( PI.paid_on BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')" :
                            ($s_sdate != "" && $s_edate == "") ? 
                            " AND  ( PI.paid_on >= '".date("Y-m-d",strtotime($s_sdate))."')" :
                            ($s_sdate == "" && $s_edate != "") ? 
                            " AND  ( PI.paid_on <= '".date("Y-m-d",strtotime($s_edate))."')" :
                            "");

                             if(!is_superadmin($_SESSION['session_usergroupname'])){
                             $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                             }
                            /*
                             * Return the Customers list
                            */
                            $query="SELECT DATE_FORMAT(PI.paid_on,'%d-%b-%Y') as paid_date,B.name,SUM(PI.interest_amount) AS total_amount 
                            FROM ".PRODUCT_INTERESTS." AS PI
                            LEFT JOIN ".BRANCH." AS B ON B.id = PI.branch_id
                            WHERE 1  ".$search_condition." 
                            GROUP BY PI.branch_id,PI.paid_on Order By PI.paid_on DESC ";
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
                            " AND  ( PI.paid_on BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')" :
                            ($s_sdate != "" && $s_edate == "") ? 
                            " AND  ( PI.paid_on >= '".date("Y-m-d",strtotime($s_sdate))."')" :
                            ($s_sdate == "" && $s_edate != "") ? 
                            " AND  ( PI.paid_on <= '".date("Y-m-d",strtotime($s_edate))."')" :
                            "");
                if(!is_superadmin($_SESSION['session_usergroupname'])){
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
                    $query="SELECT DATE_FORMAT(PI.paid_on,'%d-%b-%Y') as paid_date,B.name,SUM(PI.interest_amount) AS total_amount 
                    FROM ".PRODUCT_INTERESTS." AS PI
                    LEFT JOIN ".BRANCH." AS B ON B.id = PI.branch_id 
                    WHERE 1  ".$search_condition." 
                    GROUP BY PI.branch_id,PI.paid_on Order By PI.paid_on DESC ". Paging::get_sql_limit();
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
                            " AND  (PI.paid_on BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')" :
                            ($s_sdate != "" && $s_edate == "") ? 
                            " AND  (PI.paid_on >= '".date("Y-m-d",strtotime($s_sdate))."')" :
                            ($s_sdate == "" && $s_edate != "") ? 
                            " AND  (PI.paid_on <= '".date("Y-m-d",strtotime($s_edate))."')" :
                            "");

                if(!is_superadmin($_SESSION['session_usergroupname'])){
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
                    $query="SELECT DATE_FORMAT(PI.paid_on,'%d-%b-%Y') as paid_date,B.name,SUM(PI.interest_amount) AS total_amount 
                    FROM ".PRODUCT_INTERESTS." AS PI
                    LEFT JOIN ".BRANCH." AS B ON B.id = PI.branch_id 
                    WHERE 1  ".$search_condition." 
                    GROUP BY PI.branch_id,PI.paid_on Order By PI.paid_on DESC "; 
                    return $app->getexport($query);
                }

            }

}
