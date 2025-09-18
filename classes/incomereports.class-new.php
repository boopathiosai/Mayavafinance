<?php
 //**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE
 // Module           :  CUSTOMERS class
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Karthik B
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  29-Oct-2011
 //**************************************************************************
 // Description      :  This is the CUSTOMERS class  for Mayavafinance.
//**************************************************************************
class IncomeReports {
     /*
         * Define the required fields and return text
         */
        public static function ListviewCount()
        {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $args = func_get_args();

        $app = $GLOBALS['app'];


        $s_lid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_branch = $args[3];



        $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";
                $search_condition .= ($s_branch != "")? "AND ( P.branch_id  = '".$s_branch."' )  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
             }


        /*
         * Return the Customers list
         */
        $query = "SELECT

        P.id

        FROM ". PLEDGE." AS P
        LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.loan_id = P.id
        LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
        LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
        LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
        WHERE 1  ".$search_condition."
        GROUP BY P.id
        ORDER BY P.id ";
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

            $s_lid = $args[0];
            $s_sdate = $args[1];
            $s_edate = $args[2];
                $s_branch = $args[3];

                        $search_condition .= ($s_branch != "")? "AND ( P.branch_id  = '".$s_branch."' )  ":"";
            $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
             }

             /*
              * Return the Customers list count
              */
            $count = self::ListviewCount($s_lid,$s_sdate,$s_edate,$s_branch);

            if ($count > 0)
               {
                    /*
                    * Return the Customers list
                    */
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging

                    $query = "SELECT

                    P.id AS id,
                    P.loan_id AS loan_id,
                    P.loan_amount AS loan_amount,
                                        P.branch_id,
                    CONCAT(C.name,' - (',C.customer_id,')') AS customer_name, C.customer_id AS customer_id,

                    DATE_FORMAT(PT.paid_on,'%Y-%m-%d') AS report_date,
                    DATE_FORMAT(PT.paid_on,'%%d-%b-%Y') AS pay_date,
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,

                    P.loan_amount AS total_loan_amount,
                    P.service_charge AS service_charge,

                    SUM(PT.principle_amount) AS total_principle,
                    SUM(PT.interest_amount) AS total_interest,
                    SUM(PT.additional_charge) AS additional_charges,
                    SUM(PT.total_paid_amount) AS total_amount,
                    SUM(PT.wavier_amount) AS total_wavier,
                    COUNT(DISTINCT P.id) AS no_of_loans


                    FROM ". PLEDGE." AS P
                    LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.loan_id = P.id
                    LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
                    LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                    LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
                    WHERE 1  ".$search_condition."
                    GROUP BY P.id
                    ORDER BY P.id ". Paging::get_sql_limit();
                    //echo $query;
                    return $app->getrows($query);

               }
        }


        public static function subListviewCount()
        {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $args = func_get_args();

        $app = $GLOBALS['app'];

        $s_lid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];




        $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
             }


        /*
         * Return the Customers list
         */
        $query = "SELECT
        DISTINCT
        PT.id

        FROM ". PLEDGE." AS P
        LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.loan_id = P.id
        LEFT JOIN ".BRANCH." as B ON B.id=P.branch_id
        LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
        LEFT JOIN ".CATEGORIES." as CT ON CT.id = PD.category_id
        LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
        LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
        LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
        LEFT JOIN ".REDEMPTION." as PR on PR.loan_id = P.id
        LEFT JOIN ".PRODUCT_AUCTION." as PA on PA.loan_id = P.id
        WHERE 1 ".$search_condition."
        GROUP BY P.id
        ";


        $data = $app->getrows($query);
        return $count = count($data);
       }

        public static function subListview()
        {
            /*
             * Get the function parameters , if exist . And make the search condition
             */
            //Modified by karthik
            $args = func_get_args();

            $app = $GLOBALS['app'];
            $paging = $GLOBALS['paging'];

        $s_lid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];




        $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
             }


             /*
              * Return the Customers list count
              */
            $count = self::subListviewCount($s_lid,$s_sdate,$s_edate);

            if ($count > 0)
               {
                    /*
                    * Return the Customers list
                    */
                    //Paging::set_max( $count );
                    //Paging::calc();// calculation for paging

                   $query = "SELECT
                    DISTINCT
                        P.loan_id,P.id AS interest_loan_id,
                        C.id AS customer_id,
                        SC.name AS scheme_name,

                        CONCAT(
                            DATE_FORMAT(
                                DATE_SUB(PT.paid_on,
                                INTERVAL (PT.interest_days + PT.wavier_days) DAY
                                ),'%d-%b-%y'
                            ),' To ',
                            DATE_FORMAT(
                                PT.paid_on, '%d-%b-%y'
                            )
                        ) AS interest_period,
                        IF(CF.id=PAI.cash_in_id,
                            PAI.auction_amount,
                            0.00
                        ) AS auction_amount,

                        IF(CF.id=PAO.cash_out_id,
                            (-1 * PAO.amount_difference),
                            0.00
                        ) AS auction_lose,

                        PT.pay_interest_details AS interest_details,

                        DATE_FORMAT( PT.paid_on, '%d-%b-%Y' ) AS paid_on,
                        PT.principle_amount,
                        PT.interest_amount AS total_interest,
                        PR.interest_amount AS redem_interest_amount,
                        P.loan_amount,
                        P.service_charge AS service_charge,
                        PT.additional_charge,
                        PT.interest_days,
                        PT.interest_rate,
                        DATE_FORMAT( P.loan_date, '%d-%b-%Y' ) AS loan_date,
                        PT.wavier_amount,
                        IF(P.status=0, 'Closed', 'Opened' ) AS loan_status

                    FROM ". PLEDGE." AS P
                    INNER JOIN ".CASH_FLOW_TRANSACTIONS." AS CF
                    LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.loan_id = P.id
                    LEFT JOIN ".BRANCH." as B ON B.id=P.branch_id
                    LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
                    LEFT JOIN ".CATEGORIES." as CT ON CT.id = PD.category_id
                    LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                    LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
                    LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
                    LEFT JOIN ".REDEMPTION." as PR on PR.loan_id = P.id
                    LEFT JOIN ".PRODUCT_AUCTION." as PAI ON PAI.cash_in_id = CF.id
                    LEFT JOIN ".PRODUCT_AUCTION." as PAO ON PAO.cash_out_id = CF.id
                    WHERE 1 ".$search_condition."
                    GROUP BY P.id
                    ORDER BY P.id ";
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

        $s_lid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];

        $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
             }



             /*
              * Return the Customers list count
              */
            $count = self::subListviewCount($s_lid,$s_sdate,$s_edate);

            if ($count > 0)
            {
             /*
              * Return the Customers list
              */
				  $total_income = round($data[$i]['total_interest'] + $data[$i]['additional_charges'] + $data[$i]['service_charge'],0);

                  $query = "SELECT
                            DISTINCT
                            DATE_FORMAT( P.loan_date, '%d-%b-%Y' ) AS loan_date,
                            P.loan_id,
                            C.name as customer_name,
                            P.loan_amount, SUM(PT.interest_amount) AS Interest_Amount , PT.additional_charge AS Additional_charge ,P.service_charge AS Service_charge


                            FROM ". PLEDGE." AS P
                            LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.loan_id = P.id
                            LEFT JOIN ".BRANCH." as B ON B.id=P.branch_id
                            LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
                            LEFT JOIN ".CATEGORIES." as CT ON CT.id = PD.category_id
                            LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                            LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
                            LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
                            LEFT JOIN ".REDEMPTION." as PR on PR.loan_id = P.id
                            LEFT JOIN ".PRODUCT_AUCTION." as PA on PA.loan_id = P.id
                            WHERE 1 ".$search_condition."
                            GROUP BY P.id
                            ORDER BY P.id ";

				
                return $app->getexport($query);
            }

            }
}
