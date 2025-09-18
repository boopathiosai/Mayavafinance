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
             
            $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }


        /*
         * Return the Customers list
         */
            /*$query = "SELECT
            COUNT(DISTINCT P.id)
            FROM ".CASH_FLOW_TRANSACTIONS." as CF 
            INNER JOIN ". PLEDGE." AS P ON CF.id = P.cash_flow_id 
            LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.loan_id = P.id
            LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
            LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
            LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
            WHERE 1 ".$search_condition."";*/
            $query = "SELECT
            COUNT(DISTINCT DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'))
            FROM ".CASH_FLOW_TRANSACTIONS." as CF
            LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.cash_flow_id = CF.id
            LEFT JOIN ". PLEDGE." AS P ON P.id = PT.loan_id
			LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
            WHERE  CF.transaction_type = 'cashin' AND CF.editable = 0  ".$search_condition."";

            $data = $app->getrow($query);
            return $count = (int)$data[0];
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

            $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";

            $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
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

                    /*$query = "SELECT
                    P.id AS id,
                    P.loan_id AS loan_id,
                    P.loan_amount AS loan_amount,
                    P.branch_id,
                    CONCAT(C.name,' - (',C.customer_id,')') AS customer_name, C.customer_id AS customer_id,
                    DATE_FORMAT(PT.paid_on,'%Y-%m-%d') AS report_date,
                    DATE_FORMAT(PT.paid_on,'%d-%b-%Y') AS pay_date,
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                    P.loan_amount AS total_loan_amount,
                    P.service_charge AS service_charge,
                    PT.cash_flow_id,
                    SUM(PT.principle_amount) AS total_principle,
                    SUM(PT.interest_amount) AS total_interest,
                    SUM(PT.additional_charge) AS additional_charges,
                    SUM(PT.total_paid_amount) AS total_amount,
                    SUM(PT.wavier_amount) AS total_wavier,
                    COUNT(DISTINCT P.id) AS no_of_loans,    
                    IF(PA.status=1  >0 || P.status=0 ,'Closed','Opened') AS status                    
                    FROM ".CASH_FLOW_TRANSACTIONS." as CF 
                    INNER JOIN ". PLEDGE." AS P ON CF.id = P.cash_flow_id
                    LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.loan_id = P.id
                    LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
                    LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                    LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
                    LEFT JOIN ".PRODUCT_AUCTION." as PA on PA.loan_id = P.id
                    WHERE 1  ".$search_condition."
                    GROUP BY P.id
                    ORDER BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') DESC ". Paging::get_sql_limit();*/
                    //echo "<pre>"; echo $query."<hr>";die;
                    //echo "<pre>";
                    $query = "SELECT
                    DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS id,
                    P.loan_id AS loan_id,
                    P.loan_amount AS loan_amount,
                    P.branch_id,
                    DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                    DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS pay_date,
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                    P.loan_amount AS total_loan_amount,
                    P.service_charge AS service_charge,
                    CF.id AS cash_flow_id,
                    SUM(IF(CF.module_type = 3 AND (P.id IS NOT NULL AND R.id IS NULL AND PR.id IS NULL AND PE.id IS NULL AND PP.id IS NULL), CF.amount, 0)) AS pledge_interest,
                    SUM(IF(CF.module_type = 3 AND R.id IS NOT NULL, PT.interest_amount, 0)) AS redeem_interest,
                    SUM(IF(CF.module_type = 3 AND PR.id IS NOT NULL, PT.interest_amount, 0)) AS renew_interest,
                    SUM(IF(CF.module_type = 3 AND PE.id IS NOT NULL, PT.interest_amount, 0)) AS excess_interest,
                    SUM(IF(CF.module_type = 3 AND PP.id IS NOT NULL, PT.interest_amount, 0)) AS partial_interest,
                    SUM(IF(CF.cash_purpose = 'Auction' && CF.transaction_type = 'cashin', PA.interest_amount, 0)) AS auction_interest,
                    SUM(IF(CF.cash_purpose = 'Auction' && CF.transaction_type = 'cashin', PA.amount_difference, 0)) AS auction_profit,
                    SUM(IF(CF.module_type = 7, CF.amount, 0)) AS service_charge,
                    SUM(IF(CF.module_type = 8, CF.amount, 0)) AS additional_amount,
                    SUM(amount) AS income
                    
                    FROM ".CASH_FLOW_TRANSACTIONS." as CF
                    
                    LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.cash_flow_id = CF.id
                    LEFT JOIN ".PRODUCT_AUCTION." AS PA ON PA.cash_in_id = CF.id 
                    LEFT JOIN ". PLEDGE." AS P ON P.id = PT.loan_id
					LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
                    LEFT JOIN ".REDEMPTION." AS R ON PT.id = R.pay_id
                    LEFT JOIN ".RENEWAL." AS PR ON PT.id = PR.pay_id
                    LEFT JOIN ".PAY_EXCESS_AMOUNT." AS PE ON PT.id = PE.pay_id
                    LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." AS PP ON PT.id = PP.pay_id
                    
                    WHERE CF.transaction_type = 'cashin' AND CF.editable = 0  ".$search_condition." 
                    
                    GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') 
                    
                    ORDER BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') DESC ". Paging::get_sql_limit();
                    
                    //echo "<pre>"; echo $query."<hr>";die;
                    // CF.module_type IN (2,3,4,6,7,8,9)
                    
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
		 $s_branch = $args[3];

            $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";




        $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  CF.date_transaction = '".date("Y-m-d",strtotime($s_sdate))."'";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }


        /*
         * Return the Customers list
         */
        /*$query = "SELECT
        DISTINCT
        PT.id
        FROM ".CASH_FLOW_TRANSACTIONS." as CF 
        INNER JOIN ". PLEDGE." AS P ON CF.id = P.cash_flow_id
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
        ";*/
        
            $query = "SELECT
            COUNT(DISTINCT CF.id)
            FROM ".CASH_FLOW_TRANSACTIONS." as CF
            LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.cash_flow_id = CF.id
            LEFT JOIN ". PLEDGE." AS P ON P.id = PT.loan_id
			LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
            WHERE  CF.transaction_type = 'cashin'  AND CF.editable = 0 ".$search_condition."";



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
			 $s_branch = $args[3];

			 $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
            $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
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

                  /* $query = "SELECT
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
                        PT.interest_amount,
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
                        ORDER BY P.id ";*/
                        
                   /* $query = "SELECT
                    DISTINCT
                        P.loan_id AS loan_code,
                        P.id AS pledge_id,
                        B.name AS branch_name,
                        PT.cash_flow_id,
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
                        PT.id AS pay_id,

                        DATE_FORMAT( PT.paid_on, '%d-%b-%Y' ) AS paid_on,
                        PT.principle_amount AS principle_amount,
                        PT.interest_amount AS interest_amount,

                        P.loan_amount AS loan_amount,
                        P.service_charge AS service_charge,
                        PT.additional_charge,
                        PT.interest_days,
                        PT.interest_rate,
                        DATE_FORMAT( P.loan_date, '%d-%b-%Y' ) AS loan_date,
                        PT.wavier_amount,
                        IF(P.status=1, 'Opened', IF(PAI.loan_id > 0 OR PAO.loan_id > 0,'Auctioned','Closed') ) AS loan_status

                        FROM ". PLEDGE." AS P
                        INNER JOIN ".CASH_FLOW_TRANSACTIONS." AS CF ON P.cash_flow_id = CF.id
                        LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.loan_id = P.id
                        LEFT JOIN ".BRANCH." as B ON B.id=P.branch_id
                        LEFT JOIN ".PRODUCT_AUCTION." as PAI ON PAI.loan_id = P.id
                        LEFT JOIN ".PRODUCT_AUCTION." as PAO ON PAO.loan_id = P.id
                        WHERE 1 ".$search_condition."
                        ORDER BY P.id ";*/
                      // echo "<pre>";
                      $query = "SELECT
                    DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS id,
                    P.loan_id AS loan_id,
                    P.loan_amount AS loan_amount,
                    P.branch_id,
                    DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                    DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS pay_date,
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                    P.loan_amount AS total_loan_amount,
                    P.service_charge AS service_charge,
                    CF.id AS cash_flow_id,
                    CF.cash_purpose AS purpose,
                    IF(CF.module_type = 3 AND (P.id IS NOT NULL AND R.id IS NULL AND PR.id IS NULL AND PE.id IS NULL AND PP.id IS NULL), CF.amount, 0) AS pledge_interest,
                    IF(CF.cash_purpose = 'Pay Interest' AND R.id IS NOT NULL, PT.interest_amount, 0) AS redeem_interest,
                    IF(PR.id IS NOT NULL, PT.interest_amount, 0) AS renew_interest,
                    IF(CF.module_type = 3 AND PE.id IS NOT NULL, PT.interest_amount, 0) AS excess_interest,
                    IF(CF.module_type = 3 AND PP.id IS NOT NULL, PT.interest_amount, 0) AS partial_interest,
                    IF(CF.cash_purpose = 'Auction' && CF.transaction_type = 'cashin', PA.interest_amount, 0) AS auction_interest,
                    IF(CF.cash_purpose = 'Auction' && CF.transaction_type = 'cashin', PA.amount_difference, 0) AS auction_profit,
                    IF(CF.module_type = 7, CF.amount, 0) AS service_charge,
                    IF(CF.module_type = 8, CF.amount, 0) AS additional_amount,
                    IF(R.id IS NULL,amount,0) AS income
                    
                    FROM ".CASH_FLOW_TRANSACTIONS." as CF
                    LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.cash_flow_id = CF.id
                    LEFT JOIN ".PRODUCT_AUCTION." AS PA ON PA.cash_in_id = CF.id 
                    LEFT JOIN ". PLEDGE." AS P ON P.id = PT.loan_id
					LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
                    LEFT JOIN ".REDEMPTION." AS R ON PT.id = R.pay_id
                    LEFT JOIN ".RENEWAL." AS PR ON PT.id = PR.pay_id
                    LEFT JOIN ".PAY_EXCESS_AMOUNT." AS PE ON PT.id = PE.pay_id
                    LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." AS PP ON PT.id = PP.pay_id
                    WHERE CF.transaction_type = 'cashin' AND CF.editable = 0  ".$search_condition." 
                    ORDER BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') DESC ";
                   // echo "</pre>";
                    $result =  $app->getrows($query);    
                    //$preresult = $app->getrows($query);
                    //$result = self::resultData($preresult);
                    return $result;
                    
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
			 $s_branch = $args[3];

            $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";

          $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }



             /*
              * Return the Customers list count
              */
            //$count = self::subListviewCount($s_lid,$s_sdate,$s_edate);

             /*
              * Return the Customers list
              */
                  

                  /*$query = "SELECT

                    P.id AS id,
                    P.loan_id AS loan_id,
                    P.loan_amount AS loan_amount,
                                        P.branch_id,
                    CONCAT(C.name,' - (',C.customer_id,')') AS customer_name, C.customer_id AS customer_id,

                    DATE_FORMAT(PT.paid_on,'%Y-%m-%d') AS report_date,
                    DATE_FORMAT(PT.paid_on,'%%d-%b-%Y') AS pay_date,
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,

                    P.loan_amount AS total_loan_amount,
                    P.service_charge AS Service_charge,

                    SUM(PT.principle_amount) AS total_principle,
                    SUM(PT.interest_amount) AS Interest_Amount,
                    SUM(PT.additional_charge) AS Additional_charge,
                    SUM(PT.total_paid_amount) AS total_amount,
                    SUM(PT.wavier_amount) AS total_wavier,
                    COUNT(DISTINCT P.id) AS no_of_loans

                    FROM ". PLEDGE." AS P
                    INNER JOIN ".CASH_FLOW_TRANSACTIONS." AS CF
                    INNER JOIN ".PRODUCT_INTERESTS." AS PT ON PT.loan_id = P.id
                    LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
                    LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                    LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
                    WHERE 1  ".$search_condition."
                    GROUP BY P.id
                    ORDER BY P.id ";*/
                    
                   /*$query = "SELECT
                    DISTINCT
                        P.loan_id AS loan_code,
                        P.id AS pledge_id,
                        B.name AS branch_name,
                        PT.cash_flow_id,
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
                        PT.id AS pay_id,

                        DATE_FORMAT( PT.paid_on, '%d-%b-%Y' ) AS paid_on,
                        PT.principle_amount AS principle_amount,
                        PT.interest_amount AS interest_amount,
                        
                        P.loan_amount AS loan_amount,
                        P.service_charge AS service_charge,
                        PT.additional_charge,
                        PT.interest_days,
                        PT.interest_rate,
                        DATE_FORMAT( P.loan_date, '%d-%b-%Y' ) AS loan_date,
                        PT.wavier_amount,
                        IF(P.status=1, 'Opened', IF(PAI.loan_id > 0 OR PAO.loan_id > 0,'Auctioned','Closed') ) AS loan_status

                        FROM ". PLEDGE." AS P
                        INNER JOIN ".CASH_FLOW_TRANSACTIONS." AS CF ON P.cash_flow_id = CF.id
                        LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.loan_id = P.id
                        LEFT JOIN ".BRANCH." as B ON B.id=P.branch_id
                        LEFT JOIN ".PRODUCT_AUCTION." as PAI ON PAI.loan_id = P.id
                        LEFT JOIN ".PRODUCT_AUCTION." as PAO ON PAO.loan_id = P.id
                        WHERE 1 ".$search_condition."
                        ORDER BY P.id ";

        $preresult = $app->getexport($query);
        $result = self::resultData($preresult);*/
        
            /*$query = "SELECT
                    DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS id,
                    P.loan_id AS loan_id,
                    P.loan_amount AS loan_amount,
                    P.branch_id,
                    DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                    DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS pay_date,
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                    P.loan_amount AS total_loan_amount,
                    P.service_charge AS service_charge,
                    CF.id AS cash_flow_id,
                    CF.cash_purpose AS purpose,
                    if(CF.module_type = 3 AND P.id IS NOT NULL, CF.amount, 0) AS pledge_interest,
                    if(CF.module_type = 3 AND PR.id IS NOT NULL , PT.interest_amount, 0) AS redeem_interest,
                    if(CF.module_type = 3 AND R.id IS NOT NULL , PT.interest_amount, 0) AS renew_interest,
                    if(CF.module_type = 3 AND PE.id IS NOT NULL , PT.interest_amount, 0) AS excess_interest,
                    if(CF.module_type = 3 AND PP.id IS NOT NULL , PT.interest_amount, 0) AS partial_interest,
                    if(CF.module_type = 6, PA.interest_amount, 0) AS auction_interest,
                    if(CF.module_type = 6, PA.amount_difference, 0) AS auction_profit,
                    if(CF.module_type = 7, CF.amount, 0) AS service_charge,
                    if(CF.module_type = 8, CF.amount, 0) AS additional_amount,                    
                    amount AS income
                    
                    FROM ".CASH_FLOW_TRANSACTIONS." as CF
                    LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.cash_flow_id = CF.id
                    LEFT JOIN ".PRODUCT_AUCTION." AS PA ON PA.cash_in_id = CF.id 
                    LEFT JOIN ". PLEDGE." AS P ON P.id = PT.loan_id
                    LEFT JOIN ".REDEMPTION." AS R ON PT.id = R.pay_id
                    LEFT JOIN ".RENEWAL." AS PR ON PT.id = PR.pay_id
                    LEFT JOIN ".PAY_EXCESS_AMOUNT." AS PE ON PT.id = PE.pay_id
                    LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." AS PP ON PT.id = PP.pay_id
                    WHERE CF.transaction_type = 'cashin' AND CF.editable = 0  ".$search_condition." 
                    ORDER BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') DESC ";*/

					$query = "SELECT
                    DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS id,
                    P.loan_id AS loan_id,
                    P.loan_amount AS loan_amount,
                    P.branch_id,CF.parent_id,
                    DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                    DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS pay_date,
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                    P.loan_amount AS total_loan_amount,
                    P.service_charge AS service_charge,
					CF.module_type,
                    CF.id AS cash_flow_id,
                    SUM(IF(CF.module_type = 3 AND (P.id IS NOT NULL AND R.id IS NULL AND PR.id IS NULL AND PE.id IS NULL AND PP.id IS NULL), CF.amount, 0)) AS pledge_interest,
                    SUM(IF(CF.module_type = 3 AND R.id IS NOT NULL, PT.interest_amount, 0)) AS redeem_interest,
                    SUM(IF(CF.module_type = 3 AND PR.id IS NOT NULL, PT.interest_amount, 0)) AS renew_interest,
                    SUM(IF(CF.module_type = 3 AND PE.id IS NOT NULL, PT.interest_amount, 0)) AS excess_interest,
                    SUM(IF(CF.module_type = 3 AND PP.id IS NOT NULL, PT.interest_amount, 0)) AS partial_interest,
                    SUM(IF(CF.cash_purpose = 'Auction' && CF.transaction_type = 'cashin', PA.interest_amount, 0)) AS auction_interest,
                    SUM(IF(CF.cash_purpose = 'Auction' && CF.transaction_type = 'cashin', PA.amount_difference, 0)) AS auction_profit,
                    SUM(IF(CF.module_type = 7, CF.amount, 0)) AS service_charge,
                    SUM(IF(CF.module_type = 8, CF.amount, 0)) AS additional_amount,
                    SUM(amount) AS income
                    
                    FROM ".CASH_FLOW_TRANSACTIONS." as CF                    
                    LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.cash_flow_id = CF.id
                    LEFT JOIN ".PRODUCT_AUCTION." AS PA ON PA.cash_in_id = CF.id 
                    LEFT JOIN ". PLEDGE." AS P ON P.id = PT.loan_id
					LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
                    LEFT JOIN ".REDEMPTION." AS R ON PT.id = R.pay_id
                    LEFT JOIN ".RENEWAL." AS PR ON PT.id = PR.pay_id
                    LEFT JOIN ".PAY_EXCESS_AMOUNT." AS PE ON PT.id = PE.pay_id
                    LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." AS PP ON PT.id = PP.pay_id
                    
                    WHERE CF.transaction_type = 'cashin' AND CF.editable = 0 ".$search_condition." 
                    
                    GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'),P.loan_id
                    
                    ORDER BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') DESC";
					
        $result = $app->getexport($query);

          if(count($result) > 0) 
          {
                return $result;
          }
    
    }
    
    private static function resultData($reports){
       
       global $app;
       
       $result = array();
       
       if(count($reports)>0){
  
          $dpi = 0;
          $di = 1;
          
          foreach($reports as $rows)
          {
            /** Set Result data for the record **/
            
            
                $change_status = ($rows['status'] == 1) ? 0 : 1;                
                $pay_interest_details = unserialize($rows['interest_details']);
                $interest_loan_id = $rows['interest_loan_id'];
                //calculate total interest
                /*$query = "SELECT SUM(interest_amount) AS interest FROM ".PRODUCT_INTERESTS." WHERE loan_id ='".$interest_loan_id."' GROUP BY loan_id";
                $interest_data = $app->getrows($query);
                $total_interest = round($interest_data[0]['interest']);*/
                
                $loan_id = $rows['pledge_id'];
                
                $pquery = "SELECT  loan_id,cash_in_id, cash_out_id, interest_amount, auction_charge, auction_amount,amount_difference FROM ".PRODUCT_AUCTION." WHERE loan_id =  ".$loan_id;
                $auc_det = $app->getrow($pquery);
                if((int)$auc_det[0] > 0){
                    $rows['auction_amount'] = (int)$auc_det[5];
                    if((int)$auc_det[6] < 0){
                        $rows['auction_lose'] = -1 * (int)$auc_det[6];
                    }else{
                        $rows['auction_lose'] = 0;
                    }
                    $rows['loan_status'] = 'Auctioned';
                
                }
                
                $chash_id = $rows['cash_flow_id'];
                $pay_id = $rows['pay_id'];
                

                    $query = "SELECT id FROM ".RENEWAL." WHERE pay_id ='".$pay_id."'";
                    $rei_ids = $app->getrow($query);
                    $i_id = $rei_ids[0];
                    if($i_id > 0){
                        $rows['renew_interest_amount'] = $rows['interest_amount'];
                    }else{
                        $query = "SELECT id FROM ".PARTIAL_AMOUNT_PAYMENT." WHERE pay_id ='".$pay_id."'";
                        $ppi_ids = $app->getrow($query);
                        $i_id = $ppi_ids[0];
                        if($i_id > 0){
                            $rows['partial_interest_amount'] = $rows['interest_amount'];
                        }else{
                            $query = "SELECT id FROM ".PAY_EXCESS_AMOUNT." WHERE pay_id ='".$pay_id."'";
                            $pei_ids = $app->getrow($query);
                            $i_id = $pei_ids[0];
                            if($i_id > 0){
                                $rows['excess_interest_amount'] = $rows['interest_amount'];
                            }else{
                                
                                $query = "SELECT id FROM ".REDEMPTION." WHERE pay_id ='".$pay_id."'";
                                $ri_ids = $app->getrow($query);
                                $i_id = $ri_ids[0];
                                if($i_id > 0){
                                    $rows['redeem_interest_amount'] = $rows['interest_amount'];
                                }else{
                                
                                $rows['normal_interest_amount'] = $rows['interest_amount'];
                            }

                        }
                    }

            
                }
                      
            
          //print_r($rows);
            $result[] = $rows;
          }
          
        }else{
          $result = $reports;
        }
        
        return $result;
        
    }
}
