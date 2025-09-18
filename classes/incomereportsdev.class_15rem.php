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
class IncomeReportsDev {
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
		$s_sub_branch = $args[4];

         $type_of_page = $args[5];


            if($type_of_page==1)
            {
              $search_condition  = " AND  ( P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";  
            }

            $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
//	    $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
	    
            // DSSK changed 05_SEP_2017 //
         if ((is_array($s_sub_branch)) && ($s_sub_branch != '') && ($s_sub_branch[0] > 0)) {
            foreach ($s_sub_branch as $key => $value) {
                $sub_branch_id_overall[] = $value;
            }
            $sub_branch_ids = implode(',', $sub_branch_id_overall);
            $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id IN(" . $sub_branch_ids . "))" : "";
        }
        // \.DSSK changed 05_SEP_2017 //

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( CF.date_transaction BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( CF.date_transaction >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( CF.date_transaction <= '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif(((!is_superadmin($_SESSION['session_usergroupname'])&&$s_sdate == "" && $s_edate == "" && $s_sub_branch==""))){
            // echo "sdf";exit;
                 $sess_branch_id=$_SESSION['session_user_branch_id'];
                 $query_sub_branch = "SELECT id FROM mf_sub_branch WHERE branch_id = $sess_branch_id";
                 $data_sub_id = $app->getrows($query_sub_branch);


                 $sub_branch_count=count($data_sub_id);
                 for($i=0; $i<$sub_branch_count;$i++){
                  $id[]=$data_sub_id[$i][0];
                 }
                 $a=implode(',', array_map('intval', $id));
                 //echo $a;exit;
                // echo "<pre>";print_r($id);exit;
                $search_condition .= " AND  ( P.sub_branch_id IN($a))";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }

             //echo $search_condition;exit;
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


            /*
                    Jun6 2016
            OR P.cash_flow_id=CF.id OR P.id = PR.old_loan_id OR  PT.loan_id= P.id OR PA.loan_id = P.id OR PE.old_loan_id=P.id OR PP.old_loan_id=P.id

            */



            $query = "SELECT CF.id,CF.module_type FROM mf_cash_flow_transactions as CF LEFT JOIN mf_renewal AS PR ON PR.cash_in_id = CF.id LEFT JOIN mf_product_interests AS PT ON PT.cash_flow_id = CF.id LEFT JOIN mf_product_auction AS PA ON PA.cash_in_id = CF.id LEFT JOIN mf_pay_excess_amount AS PE ON PE.cash_in_id = CF.id LEFT JOIN mf_partial_amount_payment AS PP ON PP.cash_in_id = CF.id LEFT JOIN mf_redemption AS R ON R.cash_flow_id = CF.id LEFT JOIN mf_branch AS B ON B.id = CF.branch_id
                      LEFT JOIN mf_pledge AS P ON P.cash_flow_id=CF.parent_id
                      LEFT JOIN mf_pledge AS PAZ ON PAZ.cash_flow_id=CF.id
                      LEFT JOIN mf_pledge AS PB ON PB.id = PR.old_loan_id
                      LEFT JOIN mf_product_interests AS PTA ON PTA.id = PR.old_loan_id
                      LEFT JOIN mf_product_auction AS PAA ON PAA.loan_id = P.id
                      LEFT JOIN mf_pay_excess_amount AS PEA ON PEA.old_loan_id=P.id
                      LEFT JOIN mf_partial_amount_payment AS PPA ON PPA.old_loan_id=P.id
                      LEFT JOIN mf_sub_branch AS SB ON SB.id = P.sub_branch_id
                      WHERE CF.transaction_type = 'cashin' 
                      AND CF.editable = 0 
                      AND CF.module_type !=2 
                      /*  AND ( P.status = 1)  changed 07_AUG_2017 */
                      AND CF.module_type !=4 ".$search_condition." GROUP BY CF.id ORDER BY CF.date_transaction DESC ,CF.id";

            $data = $app->getrows($query);
			$count = count($data);
          //  echo $count;
			if($count > 0) {
				foreach ($data as $key => $val) {
					if ($val['module_type'] == 2) {
						unset($data[$key]);
					}
				}
			}
            return $count;
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
			$s_sub_branch = $args[4];
            $type_of_page = $args[5];
           //echo "<pre>";print_r($args);

            if($type_of_page==1)
            {
                $search_condition  .= " AND  ( P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";  
            }

            $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
			//$search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
// DSSK changes 07_SEP_2017 //
 if((is_array($s_sub_branch))&&($s_sub_branch!='')&&($s_sub_branch[0]>0))
        {
          foreach ($s_sub_branch as $key => $value) {
            $sub_branch_id_overall[] = $value;
          }
          $sub_branch_ids = implode(',',$sub_branch_id_overall);
          $search_condition .= ($s_sub_branch != "")? "AND ( SB.id IN(".$sub_branch_ids."))":"";
        } 

// \.DSSK changes 07_SEP_2017 //
            
            $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( CF.date_transaction BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( CF.date_transaction >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( CF.date_transaction <= '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif(((!is_superadmin($_SESSION['session_usergroupname'])&&$s_sdate == "" && $s_edate == "" && $s_sub_branch==""))){
             // echo "sdf";exit;
                 $sess_branch_id=$_SESSION['session_user_branch_id'];
                 $query_sub_branch = "SELECT id FROM mf_sub_branch WHERE branch_id = $sess_branch_id";
                 $data_sub_id = $app->getrows($query_sub_branch);


                 $sub_branch_count=count($data_sub_id);
                 for($i=0; $i<$sub_branch_count;$i++){
                  $id[]=$data_sub_id[$i][0];
                 }
                 $a=implode(',', array_map('intval', $id));
                // echo $a;exit;
                // echo "<pre>";print_r($id);exit;
                $search_condition .= " AND  ( P.sub_branch_id IN($a))";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }
            // echo $_SESSION['session_user_branch_id'];exit;
             /*
              * Return the Customers list count
              */
            // echo $search_condition;exit;
            $count = self::ListviewCount($s_lid,$s_sdate,$s_edate,$s_branch,$s_sub_branch,$type_of_page);
            //echo $count;
            if ($count > 0)
               {
                    /*
                    * Return the Customers list
                    */
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging


				/*	$query = "SELECT CONCAT( SB.name,' - (',SB.sub_branch_code,')') AS sub_details, CF.id,CF.parent_id,CF.module_type,CF.date_transaction,CF.amount as amount,CF.transaction_type,(case when ( PR.cash_in_id = CF.id) then 'Renewal Interest' when ( PT.cash_flow_id = CF.id) then 'Pay Interest' when ( PE.cash_in_id = CF.id ) then 'Excess Interest' when ( PP.cash_in_id = CF.id ) then 'Partial Amount Interest' when ( R.cash_flow_id = CF.id ) then 'Reemdemed Interest' else CF.cash_purpose end) as cash_purpose,CF.branch_id,P.loan_id,P.loan_amount,DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS loan_date FROM mf_cash_flow_transactions as CF LEFT JOIN mf_renewal AS PR ON PR.cash_in_id = CF.id LEFT JOIN mf_product_interests AS PT ON PT.cash_flow_id = CF.id LEFT JOIN mf_product_auction AS PA ON PA.cash_in_id = CF.id LEFT JOIN mf_pay_excess_amount AS PE ON PE.cash_in_id = CF.id LEFT JOIN mf_partial_amount_payment AS PP ON PP.cash_in_id = CF.id LEFT JOIN mf_redemption AS R ON R.cash_flow_id = CF.id LEFT JOIN mf_branch AS B ON B.id = CF.branch_id
                                LEFT JOIN mf_pledge AS P ON P.cash_flow_id=CF.parent_id
                                LEFT JOIN mf_pledge AS PAZ ON PAZ.cash_flow_id=CF.id
                                LEFT JOIN mf_pledge AS PAX ON PAX.id = PR.old_loan_id
                                LEFT JOIN mf_product_interests AS PAT ON PAT.loan_id= P.id
                                LEFT JOIN mf_product_auction AS PAC ON PAC.loan_id = P.id
                                LEFT JOIN mf_partial_amount_payment AS PAP ON PAP.old_loan_id=P.id
                                LEFT JOIN mf_redemption AS RX ON RX.loan_id = P.id
                                LEFT JOIN mf_sub_branch AS SB ON SB.id = P.sub_branch_id
                                WHERE CF.transaction_type = 'cashin' AND CF.editable = 0 AND CF.module_type !=2 AND CF.module_type !=4 ".$search_condition." AND P.sub_branch_id IS NOT NULL ORDER BY CF.date_transaction DESC ,CF.id". Paging::get_sql_limit();
                 */
                          $query = "SELECT CONCAT( SB.name,' - (',SB.sub_branch_code,')') AS sub_details, CF.id,CF.parent_id,CF.module_type,CF.date_transaction,CF.amount as amount,CF.transaction_type,CF.id as cf_id,
                            /*(case when ( PR.cash_in_id = CF.id) then 'Renewal Interest' when ( PT.cash_flow_id = CF.id) then 'Pay Interest' when ( PE.cash_in_id = CF.id ) then 'Excess Interest' when ( PP.cash_in_id = CF.id ) then 'Partial Amount Interest' when ( R.cash_flow_id = CF.id ) then 'Reemdemed Interest' else CF.cash_purpose end) as cash_purpose,*/
                               
                                PR.cash_in_id as  pr_cashin_id,
				PT.cash_flow_id as pt_cashflow_id,
				PE.cash_in_id as pe_cashin_id,
				PP.cash_in_id as pp_cashin_id,
				R.cash_flow_id  as r_cashin_id,
				CF.cash_purpose as cf_cash_purpose,

                                CF.branch_id,P.loan_id,P.loan_amount,P.loan_date AS loan_date FROM mf_cash_flow_transactions as CF LEFT JOIN mf_renewal AS PR ON PR.cash_in_id = CF.id LEFT JOIN mf_product_interests AS PT ON PT.cash_flow_id = CF.id LEFT JOIN mf_product_auction AS PA ON PA.cash_in_id = CF.id LEFT JOIN mf_pay_excess_amount AS PE ON PE.cash_in_id = CF.id LEFT JOIN mf_partial_amount_payment AS PP ON PP.cash_in_id = CF.id LEFT JOIN mf_redemption AS R ON R.cash_flow_id = CF.id LEFT JOIN mf_branch AS B ON B.id = CF.branch_id
                                LEFT JOIN mf_pledge AS P ON P.cash_flow_id=CF.parent_id
                                LEFT JOIN mf_pledge AS PAZ ON PAZ.cash_flow_id=CF.id
                                LEFT JOIN mf_pledge AS PAX ON PAX.id = PR.old_loan_id
                                LEFT JOIN mf_product_interests AS PAT ON PAT.loan_id= P.id
                                LEFT JOIN mf_product_auction AS PAC ON PAC.loan_id = P.id
                                LEFT JOIN mf_partial_amount_payment AS PAP ON PAP.old_loan_id=P.id
                                LEFT JOIN mf_redemption AS RX ON RX.loan_id = P.id
                                LEFT JOIN mf_sub_branch AS SB ON SB.id = P.sub_branch_id
                                WHERE CF.transaction_type = 'cashin' AND CF.editable = 0 
                                AND CF.module_type !=2 
                                /* AND ( P.status = 1)   changed 07_AUG_2017 */
                                AND CF.module_type !=4 ".$search_condition." AND P.sub_branch_id IS NOT NULL ORDER BY CF.date_transaction DESC ,CF.id". Paging::get_sql_limit();               

//echo $query;
                    //echo "<pre>"; echo $query."<hr>";die;
                    // CF.module_type IN (2,3,4,6,7,8,9)
                    //OR P.cash_flow_id=CF.id OR P.id = PR.old_loan_id OR  PT.loan_id= P.id OR PA.loan_id = P.id OR PE.old_loan_id=P.id OR PP.old_loan_id=P.id OR R.loan_id = P.id

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
                $search_condition .= " AND  ( CF.date_transaction >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( CF.date_transaction <= '".date("Y-m-d",strtotime($s_edate))."')";
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
                $search_condition .= " AND  ( CF.date_transaction BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( CF.date_transaction >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( CF.date_transaction <= '".date("Y-m-d",strtotime($s_edate))."')";
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

                   /*   $query = "SELECT
                    CF.date_transaction AS id,
                    P.loan_id AS loan_id,
                    P.loan_amount AS loan_amount,
                    P.branch_id,
                    CF.date_transaction AS report_date,
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
                    ORDER BY CF.date_transaction DESC ";*/
                
                
                
                $query = " SELECT CF.date_transaction AS id, 
                                P.loan_id AS loan_id, 
                                P.loan_amount AS loan_amount, 
                                P.branch_id, 
                                CF.date_transaction AS report_date, 
                                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS pay_date, 
                                DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date, 
                                P.loan_amount AS total_loan_amount, 
                                P.service_charge AS service_charge, 
                                CF.id AS cash_flow_id, 
                                CF.cash_purpose AS purpose, 

                                CF.module_type  AS cf_model_type,
                                P.id AS p_id,
                                R.id AS r_id,
                                PR.id AS pr_id,
                                PE.id AS pe_id,
                                PP.id AS pp_id,
                                CF.amount AS cf_amount,

                               /* IF(CF.module_type = 3 AND 
                                 ( P.id IS NOT NULL 
                                       AND R.id IS NULL
                                       AND PR.id IS NULL
                                       AND PE.id IS NULL 
                                       AND PP.id IS NULL),CF.amount, 0) AS pledge_interest, */

                                CF.cash_purpose AS cf_cash_purpose,
                                PT.interest_amount AS pt_interest_amount,
                                CF.transaction_type AS cf_transaction_type,
                                PA.interest_amount AS pa_interest_amount,
                                PA.amount_difference AS pa_amount_difference,	
                                 
                            /* IF(CF.cash_purpose = 'Pay Interest' AND R.id IS NOT NULL, PT.interest_amount, 0) AS redeem_interest,
                               IF(PR.id IS NOT NULL, PT.interest_amount, 0) AS renew_interest,
                               IF(CF.module_type = 3 AND PE.id IS NOT NULL, PT.interest_amount, 0) AS excess_interest,
                               IF(CF.module_type = 3 AND PP.id IS NOT NULL, PT.interest_amount, 0) AS partial_interest, 
                               IF(CF.cash_purpose = 'Auction' && CF.transaction_type = 'cashin', PA.interest_amount, 0) AS auction_interest, 
                               IF(CF.cash_purpose = 'Auction' && CF.transaction_type = 'cashin', PA.amount_difference, 0) AS auction_profit, 
                               IF(CF.module_type = 7, CF.amount, 0) AS service_charge,
                               IF(CF.module_type = 8, CF.amount, 0) AS additional_amount,
                               IF(R.id IS NULL,amount,0) AS income */

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
                                ORDER BY CF.date_transaction DESC ";
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
			  $s_sub_branch = $args[4];
            $type_of_page =  $args[5];          
           if($type_of_page==1)
            {
              $search_condition  = " AND ( P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";  
            }

                        $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
			//$search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";

            // DSSK changes 07_SEP_2017 //
            if((is_array($s_sub_branch))&&($s_sub_branch!='')&&($s_sub_branch[0]>0))
                   {
                     foreach ($s_sub_branch as $key => $value) {
                       $sub_branch_id_overall[] = $value;
                     }
                     $sub_branch_ids = implode(',',$sub_branch_id_overall);
                     $search_condition .= ($s_sub_branch != "")? "AND ( SB.id IN(".$sub_branch_ids."))":"";
                   } 

// \.DSSK changes 07_SEP_2017 //

          $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( CF.date_transaction BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( CF.date_transaction >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( CF.date_transaction <= '".date("Y-m-d",strtotime($s_edate))."')";
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
                    CF.date_transaction AS id,
                    P.loan_id AS loan_id,
                    P.loan_amount AS loan_amount,
                    P.branch_id,CF.parent_id,
                    CF.date_transaction AS report_date,
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

                    GROUP BY CF.date_transaction,P.loan_id

                    ORDER BY CF.date_transaction DESC";*/


					/*$query = "SELECT
                    (case when ( CF.cash_purpose = 'Pay Interest') then '1' when ( CF.cash_purpose = 'Service Charge') then '2'  else 0 end) as cash_purpose_type,
                     CF.id,CF.parent_id,CF.module_type,CF.date_transaction,CF.amount as amount,
                     CF.transaction_type,CF.cash_purpose as PP,
                     (case when ( PR.cash_in_id = CF.id) then 'Renewal Interest' when ( PT.cash_flow_id = CF.id)  then 'Pay Interest' when ( PE.cash_in_id = CF.id ) then 'Excess Interest' when ( PP.cash_in_id = CF.id ) then 'Partial Amount Interest' when ( R.cash_flow_id = CF.id ) then 'Reemdemed Interest' else CF.cash_purpose end) as cash_purpose,
                     CF.branch_id,P.loan_id,P.loan_amount,DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS loan_date
                    FROM ".CASH_FLOW_TRANSACTIONS." as CF
                    LEFT JOIN ".RENEWAL." AS PR ON PR.cash_in_id = CF.id
                    LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.cash_flow_id = CF.id
                    LEFT JOIN ".PRODUCT_AUCTION." AS PA ON PA.cash_in_id = CF.id
                    LEFT JOIN ".PAY_EXCESS_AMOUNT." AS PE ON PE.cash_in_id = CF.id
                    LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." AS PP ON PP.cash_in_id = CF.id
                    LEFT JOIN ".REDEMPTION." AS R ON R.cash_flow_id = CF.id
                    INNER JOIN ". PLEDGE." AS P ON P.cash_flow_id=CF.parent_id  OR  PT.loan_id= P.id
                    LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id
                    WHERE  CF.transaction_type = 'cashin' AND CF.cash_purpose != 'Additional Charge' AND CF.editable = 0 AND CF.module_type !=2 AND CF.module_type !=4 ".$search_condition."
                    ORDER BY CF.date_transaction,CF.module_type DESC,P.loan_id,cash_purpose,CF.date_transaction DESC";
*/
             
             $query = " SELECT 
 /*(case  when ( CF.cash_purpose = 'Pay Interest') then '1'  when ( CF.cash_purpose = 'Service Charge') then '2' else 0 end) as cash_purpose_type, */
          CF.id,
          CF.parent_id,
          CF.module_type,
          CF.date_transaction,
          CF.amount as amount, 
          CF.transaction_type,
		  CF.cash_purpose as PP, 		  
		  
		  PR.cash_in_id AS pr_cashin_id,
		  PT.cash_flow_id AS pt_cashflow_id,
		  PE.cash_in_id AS pe_cashin_id,
		  PP.cash_in_id AS pp_cashin_id,
		  R.cash_flow_id  AS r_cashin_id,
		  CF.cash_purpose AS cf_cash_purpose,
		  CF.id AS cf_id,
		  
		  /*(case 
		  when ( PR.cash_in_id = CF.id) then 'Renewal Interest' 
		  when ( PT.cash_flow_id = CF.id) then 'Pay Interest' 
		  when ( PE.cash_in_id = CF.id ) then 'Excess Interest' 
		  when ( PP.cash_in_id = CF.id ) then 'Partial Amount Interest' 
		  when ( R.cash_flow_id = CF.id ) then 'Reemdemed Interest' else CF.cash_purpose end) as cash_purpose, */
 
 
                 CF.branch_id,
                 P.loan_id,
		 P.loan_amount,
		 P.loan_date AS loan_date
		 FROM ".CASH_FLOW_TRANSACTIONS." as CF
		 LEFT JOIN ".RENEWAL." AS PR ON PR.cash_in_id = CF.id 
		 LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.cash_flow_id = CF.id 
		 LEFT JOIN ".PRODUCT_AUCTION." AS PA ON PA.cash_in_id = CF.id
		 LEFT JOIN ".PAY_EXCESS_AMOUNT." AS PE ON PE.cash_in_id = CF.id 
		 LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." AS PP ON PP.cash_in_id = CF.id 
		 LEFT JOIN ".REDEMPTION." AS R ON R.cash_flow_id = CF.id 
		 INNER JOIN ". PLEDGE." AS P 
		 ON P.cash_flow_id=CF.parent_id 
		 OR PT.loan_id= P.id 
		 LEFT JOIN ".SUB_BRANCH." AS SB 
		 ON SB.id = P.sub_branch_id 
		 WHERE CF.transaction_type = 'cashin' 
		 AND CF.cash_purpose != 'Additional Charge' 
		 AND CF.editable = 0 
		 AND CF.module_type !=2 
		 AND CF.module_type !=4
                 AND ( P.status = 1)    /* changed 07_SEP_2017 */
		".$search_condition."		 
                 GROUP BY P.loan_id
                 ORDER BY P.loan_id ASC ";
                 /* ORDER BY 
                   CF.date_transaction,
                 CF.module_type DESC,
		 P.loan_id,
		 cash_purpose,
		 CF.date_transaction DESC";

                    

                    /*AUG20-16*/
                   // ORDER BY CF.date_transaction,CF.module_type DESC,CF.id,cash_purpose DESC";

                    /*Before change*/
                    //ORDER BY CF.date_transaction DESC,CF.module_type,CF.id,cash_purpose DESC";

                     //echo $query;exit;

        $result = $app->getexport($query);
       // echo "<pre>";print_r($result);exit;
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

