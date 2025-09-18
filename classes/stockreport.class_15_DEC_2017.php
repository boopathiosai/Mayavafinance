<?php
 //**************************************************************************
  //**************************************************************************
  // Project          :  MAYAVA FINANCE
  // Module           :  Pledge class
  // Domain           :  http://wwa.mayavafinance.com
  // Programmer       :  
  // Inprogress       :  \\offlinev5\Mayavafinance
  // Backup           :  \\dataserver\EIT\Projects\In Progress\
  // Last Modified    :  15-Jun-2011
  //**************************************************************************
  // Description      :  This is the Product Pledging class  for Mayavafinance.
 //**************************************************************************


class StockReport {
        /*
         * Define the required fields and return text
         */


        public static function ListDateRange($strDateFrom,$strDateTo) {
          // takes two dates formatted as YYYY-MM-DD and creates an
          // inclusive array of the dates between the from and to dates.

          // could test validity of dates here but I'm already doing
          // that in the main script

          $aryRange=array();

          $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
          $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

          if ($iDateTo>=$iDateFrom) {
            array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry

            while ($iDateFrom<$iDateTo) {
              $iDateFrom+=86400; // add 24 hours
              array_push($aryRange,date('Y-m-d',$iDateFrom));
            }
          }
          return $aryRange;
        }

    public static function ListviewCount() 
    {
    /*
     * Get the function parameters , if exist . And make the search condition
     */
    //Modified by karthik
    $args = func_get_args();

    $app = $GLOBALS['app'];
   
        $s_cid = $args[0];
        $s_branch = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
	$s_sub_branch = $args[4];
        $type_of_page = $args[5];
        
            if($type_of_page==2)
            {
                $search_condition  .= " AND  (P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";  
                           
            } 

        $s_cid = preg_replace("/mfc/i",'',$s_cid);
        
        //$search_condition = "AND (C.id <> '' AND CF.branch_id <> '' )";
        $search_condition .= ($s_cid != "") ? "AND (C.id = '".$s_cid."' )  ":"";
        
        $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
//	$search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
        
         // Added 05_DEC_2017 //
        
        if((is_array($s_sub_branch))&&($s_sub_branch!='')&&($s_sub_branch[0]>0))
        {
          foreach ($s_sub_branch as $key => $value) {
            $sub_branch_id_overall[] = $value;
          }
          $sub_branch_ids = implode(',',$sub_branch_id_overall);
          $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id IN(".$sub_branch_ids."))":"";
        }
        
        // END //

        if($s_sdate != "" && $s_edate != ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
        }else{
            //$search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }


         if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
         }
    /*
     * Return the Customers list
     */
     $query = "SELECT 
                    COUNT(DISTINCT CONCAT(CF.branch_id,'-', CF.date_transaction))
                    FROM ".CASH_FLOW_TRANSACTIONS." AS CF
					LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
					LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id
                    WHERE P.item_count AND CF.editable = 0 AND CF.module_type IN (1,2,4,5) ".$search_condition."";
    $data = $app->getrow($query);

    return $count = intval($data[0]);


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

        $s_cid = $args[0];
        $s_branch = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
	$s_sub_branch = $args[4]; 
	$type_of_page = $args[5];
            if($type_of_page == 2)
            {
                $search_condition  .= " AND  (P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";  
                           
            } 
        
        $s_cid = preg_replace("/mfc/i",'',$s_cid);
        
        //$search_condition = "AND (C.id <> '' AND CF.branch_id <> '' )";
        //$search_condition .= ($s_cid != "") ? "AND (C.id = '".$s_cid."' )  ":"";
        
        $search_condition .= ($s_branch != "")? " AND ( CF.branch_id  = '".$s_branch."' )  ":"";
//	$search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
        
         // Added 05_DEC_2017 //
        
        if((is_array($s_sub_branch))&&($s_sub_branch!='')&&($s_sub_branch[0]>0))
        {
          foreach ($s_sub_branch as $key => $value) {
            $sub_branch_id_overall[] = $value;
          }
          $sub_branch_ids = implode(',',$sub_branch_id_overall);
          $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id IN(".$sub_branch_ids."))":"";
        }
        
        // END //

        if($s_sdate != "" && $s_edate != ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
        }else{
            //$search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }


         if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
         }

         /*
          * Return the Customers list count
          */
         $count = self::ListviewCount($s_cid,$s_branch , $s_sdate,$s_edate,$s_sub_branch,$type_of_page);

        if ($count > 0) 
           {
                /*
                * Return the Customers list
                */
                Paging::set_max( $count );
                Paging::calc();// calculation for paging
               
                               
                 $query = "SELECT 
                        
                CONCAT(B.id,'-',DATE_FORMAT(CF.date_transaction,'%Y-%m-%d')) AS id,
                CONCAT(C.name,'- (',C.customer_id,')') AS customer_name,
                C.customer_id,
                B.name AS branch_name,
                B.id AS branch_id,
                SB.sub_branch_code AS sub_details,
                CF.id AS cash_flow_id,
                DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date_new,
                DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,
                (
                    COALESCE(SUM(P.loan_amount),0.00)
                ) AS amount_out,

                (
                COALESCE(SUM(P.item_count),0.00)
                )
                AS item_in,

                (
                COALESCE(SUM(P.total_net_weight),0.00)
                )
                AS weight_in,
                COUNT(DISTINCT C.id) AS no_of_customer,
                COUNT(DISTINCT P.id) AS no_of_issued,
                P.sub_branch_id AS sub_branch_id 
                FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
                LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
                LEFT JOIN ".CUSTOMERS." as C ON P.customer_id = C.id 
		LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id
                WHERE P.item_count > 0 AND CF.editable = 0 ".$search_condition." 
                GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'), B.id 
                ORDER BY CF.date_transaction, B.name 
                ". Paging::get_sql_limit();
                
                
                //echo "<pre>"; echo $query."<hr>";die;

                //$tmp_arr = $app->getrows($query);
                //return $tmp_arr;
                
                $preresult = $app->getrows($query);				
                $result = self::listResultData($preresult);                
                return $result;


                

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
        $s_sub_branch = $args[4]; 

        $s_cid = preg_replace("/mfc/i",'',$s_cid);
        
        //$search_condition = "AND (C.id <> '' AND CF.branch_id <> '' )";
        $search_condition .= ($s_cid != "") ? "AND (C.id = '".$s_cid."' )  ":"";
        
        $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
        
        
        // Added 05_DEC_2017 //
        
        if((is_array($s_sub_branch))&&($s_sub_branch!='')&&($s_sub_branch[0]>0))
        {
          foreach ($s_sub_branch as $key => $value) {
            $sub_branch_id_overall[] = $value;
          }
          $sub_branch_ids = implode(',',$sub_branch_id_overall);
          $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id IN(".$sub_branch_ids."))":"";
        }
        
        // END //

        if($s_sdate != "" && $s_edate != ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
        }else{
            //$search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }
        if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  CF.branch_id  =  '".$_SESSION['session_user_branch_id']."'":"";
        }


        /*
         * Return the Pledges list Count
         */
        $query = "SELECT   COUNT(DISTINCT CONCAT(CF.branch_id,'-', CF.date_transaction))

                    FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                    LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
                    WHERE CF.editable = 0  ".$search_condition;

        $data = $app->getrow($query);


        //$dates = self::ListDateRange('2011-10-01','2011-11-08');
        //print_r($dates);


        return $count = intval($data[0]);
     
    }

    public static function subListview() {

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

        $s_cid = preg_replace("/mfc/i",'',$s_cid);
        
        //$search_condition = " AND (C.id <> '' AND CF.branch_id <> '' )";
        $search_condition .= ($s_cid != "") ? "AND (C.id = '".$s_cid."' )  ":"";
        
        $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
        
        // Added 05_DEC_2017 //
        
        if((is_array($s_sub_branch))&&($s_sub_branch!='')&&($s_sub_branch[0]>0))
        {
          foreach ($s_sub_branch as $key => $value) {
            $sub_branch_id_overall[] = $value;
          }
          $sub_branch_ids = implode(',',$sub_branch_id_overall);
          $search_condition .= ($s_sub_branch != "")? " AND ( P.sub_branch_id IN(".$sub_branch_ids."))":"";
        }
        
        // END //

        if($s_sdate != "" && $s_edate != ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
        }else{
            //$search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') = '".date("Y-m-d")."')";
        }

        if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
        }

        $count = self::subListviewCount($s_cid, $s_branch, $s_sdate, $s_edate, $s_sub_branch);
        /*
         * Return the Pledge list
         */
         
        
        if ($count > 0) {

            
            
            $query = "SELECT 
                            
                    DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS id,
                    CONCAT(C.name,'- (',C.customer_id,')') AS customer_name,
                    C.customer_id,
                    C.name as customer_name,
                    B.name AS branch_name,

                    CF.opening_balance AS  opening_balance,
                    CF.closing_balance AS  closing_balance,

                    P.loan_id AS loan_code,
                    P.id AS pledge_id,
                    B.id AS branch_id,
                    CF.id AS cash_flow_id,
                    
                    DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                    DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,

                    CF.cash_purpose AS process_type,
                            
                            
                    IF(P.id <> '',
                        P.loan_amount,
                        '0.00'
                    ) AS amount_out,

                    IF(P.id <> '',
                        P.item_count,
                        '0'
                    ) AS item_in,
            

            
                    IF(P.id <> '',
                        P.total_net_weight,
                        '0'
                    ) AS weight_in,
                    P.sub_branch_id AS sub_branch_id
            
                    FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                    LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
                    LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
                    LEFT JOIN ".CUSTOMERS." as C ON P.customer_id = C.id 

                    WHERE CF.editable = 0 ".$search_condition." 
                    ORDER BY CF.date_transaction ";
            
            $preresult = $app->getrows($query);
            $result = self::resultData($preresult);
            return $result;


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
//        echo "<pre>"; print_r($args);
//        foreach ($s_sub_branch as $key => $value1) {
//            echo "<pre>"; print_r($value1);
//          }
        $s_cid = preg_replace("/mfc/i",'',$s_cid);
        
        //$search_condition = " AND (C.id <> '' AND CF.branch_id <> '' )";
        $search_condition .= ($s_cid != "") ? "AND (C.id = '".$s_cid."' )  ":"";
        
        $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
//	$search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
        
        // Added 05_DEC_2017 //
        
        if((is_array($s_sub_branch))&&($s_sub_branch != '')&&($s_sub_branch[0] > 0))
        {
          foreach ($s_sub_branch as $key => $value) {
            $sub_branch_id_overall[] = $value;
          }
          $sub_branch_ids = implode(',',$sub_branch_id_overall);
          $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id IN(".$sub_branch_ids."))":"";
        }
        
        // END //
        
        if($s_sdate != "" && $s_edate != ""){  
            $search_condition .= " AND  ( CF.date_transaction BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_condition .= " AND  ( CF.date_transaction >= '".date("Y-m-d",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_condition .= " AND  ( CF.date_transaction <= '".date("Y-m-d",strtotime($s_edate))."')";
        }else{
            //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
        }


        if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
        }
        
        //echo "src = ".$search_condition;

        $count = self::subListviewCount($s_cid, $s_branch, $s_sdate, $s_edate);
        
        /*
         * Return the Pledge list
         */

          
          if ($count > 0) 
          {
   

				 $query = "SELECT 
                        
                CONCAT(B.id,'-',DATE_FORMAT(CF.date_transaction,'%Y-%m-%d')) AS id,
                CONCAT(C.name,'- (',C.customer_id,')') AS customer_name,
                C.customer_id,
                B.name AS branch_name,
                B.id AS branch_id,
                
                CF.id AS cash_flow_id,CF.module_type,
		GROUP_CONCAT(DISTINCT CF.cash_purpose) AS process_type,
                DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date_new,
                DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,
                (
                    COALESCE(SUM(P.loan_amount),0.00)
                ) AS amount_out,

                (
                COALESCE(SUM(P.item_count),0.00)
                )
                AS item_in,

                (
                COALESCE(SUM(P.total_net_weight),0.00)
                )
                AS weight_in,
                COUNT(DISTINCT C.id) AS no_of_customer,
                COUNT(DISTINCT P.id) AS no_of_issued,
                P.sub_branch_id As sub_branch_id /* Added 05_DEC_2017 */
                FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
                LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
                LEFT JOIN ".CUSTOMERS." as C ON P.customer_id = C.id 
		LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id
                WHERE CF.editable = 0 ".$search_condition." AND CF.module_type IN(1,2,4)
                GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d')
                ORDER BY CF.date_transaction,CF.module_type DESC";
				//echo $query;die;

                    $preresult = $app->getexport($query);
                    
                    $result = self::listResultData($preresult);
                    return $result;
//                    return $preresult;
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
            
            $branch_id = $rows['branch_id'];
            $cash_flow_id = $rows['cash_flow_id'];
            $report_date = $rows['report_date'];
            $loan_id = $rows['pledge_id'];
            $cash_purpose = $rows['process_type'];
            if($rows['sub_branch_id'] != ''){
              $where = " AND P.sub_branch_id=".$rows['sub_branch_id'];
            }else{
                $where = " ";
            }
            
            if($loan_id <= 0){
            
                $rquery = "SELECT R.loan_id FROM ".REDEMPTION." AS R left join ".PLEDGE." as P on R.loan_id=P.id WHERE R.cash_flow_id =  ".$cash_flow_id.$where;
                $rcid = $app->getrow($rquery);
                if((int)$rcid[0] > 0){
                  $cash_purpose = CASHFLOW_PURPOSE_REDEEMED;
                  $loan_id = (int)$rcid[0];  
                }else{
                
                    $apquery = "SELECT R.loan_id FROM ".PRODUCT_AUCTION." AS R left join ".PLEDGE." as P on R.loan_id=P.id  WHERE R.cash_in_id =  ".$cash_flow_id.$where;
                    $apcid = $app->getrow($apquery);
                    if((int)$apcid[0] > 0){
                      $cash_purpose = AUCTION_AMOUNT;
                      $loan_id = (int)$apcid[0];  
                    }else{
                    
                        $alquery = "SELECT R.loan_id FROM ".PRODUCT_AUCTION." AS R left join ".PLEDGE." as P on R.loan_id=P.id WHERE  R.cash_out_id = ".$cash_flow_id.$where;
                        $alcid = $app->getrow($alquery);
                        if((int)$alcid[0] > 0){
                          $cash_purpose = AUCTION_AMOUNT_LOSS;
                          $loan_id = (int)$alcid[0];  
                        }else{
                            
                           
                        }
                    
                    }
                }
                if($loan_id > 0){
                    $ai_qry = "SELECT COALESCE(SUM(P.loan_amount),0.00) AS amount_in, COALESCE(SUM(P.item_count)) AS item_out, COALESCE(SUM(P.total_net_weight)) AS weight_out, C.customer_id, C.name as customer_name,  DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date, P.loan_id as loan_code FROM ".PLEDGE." AS P LEFT JOIN ".CUSTOMERS." as C ON P.customer_id = C.id WHERE P.id = ".$loan_id."";
                    $aires = $app->getrow($ai_qry);
                    $rows['amount_in'] = $aires[0];
                    $rows['item_out'] = $aires[1];
                    $rows['weight_out'] = $aires[2];
                    $rows['customer_id'] = $aires[3];
                    $rows['customer_name'] = $aires[4];
                    $rows['loan_date'] = $aires[5];
                    $rows['loan_code'] = $aires[6];
                }
            
            }else{
                $cash_purpose = CASHFLOW_PURPOSE_PLEDGED;
            }
            
           if($loan_id > 0){
          
            
            $amount_in = floatval(htmlentities(stripslashes($rows['amount_in']), ENT_QUOTES));
            $amount_out = floatval(htmlentities(stripslashes($rows['amount_out']), ENT_QUOTES));
            
            $weight_in = floatval(htmlentities(stripslashes($rows['weight_in']), ENT_QUOTES));
            $weight_out = floatval(htmlentities(stripslashes($rows['weight_out']), ENT_QUOTES));
            
            $weight_in_gs =  intval($weight_in);
            $weight_in_mgs =  intval(floatval($weight_in - intval($weight_in)) * 1000);
            $weight_out_gs =  intval($weight_out);
            $weight_out_mgs =  intval(floatval($weight_out - intval($weight_out)) * 1000);
            
            
            $item_in = floatval(htmlentities(stripslashes($rows['item_in']), ENT_QUOTES));
            $item_out = floatval(htmlentities(stripslashes($rows['item_out']), ENT_QUOTES));
            
            //$opening_amount = floatval(htmlentities(stripslashes($rows['opening_balance']), ENT_QUOTES));
            //$closing_amount = floatval(htmlentities(stripslashes($rows['closing_balance']), ENT_QUOTES));
            
          
            $rows['amount_out'] = $amount_out;
            $rows['amount_in'] = $amount_in;
            $rows['weight_in'] = $weight_in;
            $rows['weight_out'] = $weight_out;
            $rows['weight_in_gs'] = $weight_in_gs;
            $rows['weight_in_mgs'] = $weight_in_mgs;
            $rows['weight_out_gs'] = $weight_out_gs;
            $rows['weight_out_mgs'] = $weight_out_mgs;

            $rows['item_in'] = $item_in;
            $rows['item_out'] = $item_out;
          
            //$rows['opening_amount'] = $opening_amount;
            //$rows['closing_amount'] = $closing_amount;
          
            $result[] = $rows;
            }
          }
        }else{
          $result = $reports;
        }
        
        return $result;
        
    }
    
    private static function listResultData($reports){
       
       global $app;
       
       $result = array();
//       echo "<pre>";print_r($reports);echo "</pre>";exit;
       if(count($reports)>0){
  
          $dpi = 0;
          $di = 1;
          
          foreach($reports as $rows)
          {
            /** Set Result data for the record **/
			
            $branch_id = $rows['branch_id'];
            $report_date = $rows['report_date'];   
             $where = "DATE_FORMAT(R.paid_on,'%Y-%m-%d') =  '".$report_date."' AND R.branch_id = ".$branch_id."";
            if($rows['sub_branch_id'] != ''){
              $where .= " AND P.sub_branch_id=".$rows['sub_branch_id'];
            }
            
//            $ao_qry = "SELECT COALESCE(SUM(principle_amount)), GROUP_CONCAT(loan_id SEPARATOR ',') AS redeem_id, COUNT(loan_id) AS rcount  FROM ".REDEMPTION." WHERE DATE_FORMAT(paid_on,'%Y-%m-%d') =  '".$report_date."' AND branch_id = ".$branch_id." GROUP BY DATE_FORMAT(paid_on,'%Y-%m-%d'), branch_id"; // loan_id = ".$loan_id;
            $ao_qry = "SELECT COALESCE(SUM(R.principle_amount)), GROUP_CONCAT(R.loan_id SEPARATOR ',') AS redeem_id, COUNT(R.loan_id) AS rcount  FROM ".REDEMPTION." AS R left join ".PLEDGE." as P on R.loan_id=P.id WHERE  ".$where." GROUP BY DATE_FORMAT(R.paid_on,'%Y-%m-%d'), R.branch_id"; // loan_id = ".$loan_id;
            $ao_res = $app->getrow($ao_qry);
            $rows['amount_in'] = (int)$ao_res[0];
            $redeem_id = $ao_res[1];
            $no_of_return = (int)$ao_res[2];
			
            
//            $ao_qry = "SELECT COALESCE(SUM(auction_amount)), GROUP_CONCAT(loan_id SEPARATOR ',') AS auction_id, COUNT(loan_id) AS acount  FROM ".PRODUCT_AUCTION." WHERE DATE_FORMAT(paid_on,'%Y-%m-%d') =  '".$report_date."' AND branch_id = ".$branch_id." GROUP BY DATE_FORMAT(paid_on,'%Y-%m-%d'), branch_id"; // loan_id = ".$loan_id;
            $ao_qry = "SELECT COALESCE(SUM(R.auction_amount)), GROUP_CONCAT(R.loan_id SEPARATOR ',') AS auction_id, COUNT(R.loan_id) AS acount  FROM ".PRODUCT_AUCTION." AS R left join ".PLEDGE." as P on R.loan_id=P.id WHERE  ".$where."  GROUP BY DATE_FORMAT(R.paid_on,'%Y-%m-%d'), R.branch_id"; // loan_id = ".$loan_id;
            $ao_res = $app->getrow($ao_qry);
            $rows['amount_in'] += (int)$ao_res[0];
            $auction_id = $ao_res[1];
            $no_of_return += (int)$ao_res[2];

            $io_loanids = array();
            $io_loanids = array_merge(explode(',',$redeem_id),explode(',',$auction_id));			
			$io_loanids = array_filter($io_loanids);
			$count_loanids = count($io_loanids);

			$rows['no_of_return'] = $no_of_return;
            
            
            //$ao_qry = "SELECT COALESCE(SUM(item_count)), COALESCE(SUM(total_net_weight)) AS wi_out FROM ".PLEDGE." WHERE id IN (".implode(',',$io_loanids).")";
			if($count_loanids > 0) {

				if($rows['module_type'] == 1) {
					$ao_qry = "SELECT COALESCE(SUM(item_count)), COALESCE(SUM(total_net_weight)) AS wi_out FROM ".PLEDGE." AS P LEFT JOIN ".CASH_FLOW_TRANSACTIONS." AS CF ON CF.id=P.cash_flow_id WHERE P.id IN (".implode(',',$io_loanids).")";
				} else {
					$ao_qry = "SELECT COALESCE(SUM(item_count)), COALESCE(SUM(total_net_weight)) AS wi_out FROM ".PLEDGE." AS P LEFT JOIN ".CASH_FLOW_TRANSACTIONS." AS CF ON CF.id=P.cash_flow_id WHERE P.id IN (".implode(',',$io_loanids).")";
				}
				

				$ao_res = $app->getrow($ao_qry);
				$rows['item_out'] = $ao_res[0];
				$rows['weight_out'] = $ao_res[1];
			}
				
            $item_in = floatval($rows['no_of_issued']);
            $item_out = floatval($rows['no_of_return']);
            
            if($item_in > 0 || $item_out > 0)
            {
 

            $ow_query = "SELECT COALESCE(SUM(sP.total_net_weight),0.00), COUNT(DISTINCT sP.id) AS opening_item , COALESCE(SUM(sP.loan_amount),0.00) as opening_amount
                FROM ".PLEDGE." AS sP 
                WHERE sP.branch_id = ".$rows['branch_id']." AND DATE_FORMAT(sP.loan_date ,'%Y-%m-%d') < DATE_FORMAT('".$rows['report_date']."','%Y-%m-%d')";
                
            $cw_query = "SELECT COALESCE(SUM(sP.total_net_weight),0.00), COUNT(DISTINCT sP.id) AS closing_item , COALESCE(SUM(sP.loan_amount),0.00) as closing_amount
                FROM ".PLEDGE." AS sP 
                WHERE sP.branch_id = ".$rows['branch_id']." AND DATE_FORMAT(sP.loan_date ,'%Y-%m-%d') < DATE_FORMAT((DATE_ADD('".$rows['report_date']."', INTERVAL 1 DAY)) ,'%Y-%m-%d')";

            $ow_row = $app->getrow($ow_query);
            $cw_row = $app->getrow($cw_query);
    
            $rows['opening_weight'] = $ow_row[0];
            $rows['closing_weight'] = $cw_row[0]; 
			$rows['opening_item'] = $ow_row[1];
            $rows['closing_item'] = $cw_row[1];

            $rows['opening_balance'] = $ow_row[2];
            $rows['closing_balance'] = $cw_row[2];
          			            
			/*if($rows['module_type'] == 1) 
				$amount_in = 0;
            else */
			    $amount_in = floatval($rows['amount_in']);

            $amount_out = floatval($rows['amount_out']);
            $weight_in = floatval($rows['weight_in']);
            $weight_out = floatval($rows['weight_out']);
            
            $weight_in_gs =  intval($weight_in);
            $weight_in_mgs =  intval(floatval($weight_in - intval($weight_in)) * 1000);
            $weight_out_gs =  intval($weight_out);
            $weight_out_mgs =  intval(floatval($weight_out - intval($weight_out)) * 1000);
            
            $opening_weight = ($closing_to_opening_weight) ? $closing_to_opening_weight : floatval($rows['opening_weight']);
            //$closing_weight = floatval($rows['closing_weight']);
			
			if($weight_in > 0) 
				$closing_weight = $opening_weight+$weight_in;
			
			if($weight_out > 0) 
				$closing_weight = $closing_weight - $weight_out;


            $opening_weight_gs =  intval($opening_weight);
            $opening_weight_mgs =  intval(floatval($opening_weight - intval($opening_weight)) * 1000);
            $closing_weight_gs =  intval($closing_weight);
            $closing_weight_mgs =  intval(floatval($closing_weight - intval($closing_weight)) * 1000);
            
            $pitem_in = floatval($rows['item_in']);
            $pitem_out = floatval($rows['item_out']);
            
            //$opening_amount = floatval($rows['opening_balance']);
            //$closing_amount = floatval($rows['closing_balance']);
			
			$opening_amount = $closing_to_opening_amount? $closing_to_opening_amount : 0;
			if($amount_out > 0) 
				$closing_amount += $amount_out;

			if($amount_in > 0) 
				$closing_amount = $closing_amount - $amount_in;

			
            $opening_item = ($closing_item) ? $closing_item : 0;
            $closing_item+= floatval($rows['no_of_issued'])- floatval($rows['no_of_return']);
            
            $item_in = floatval($rows['no_of_issued']);
            $item_out = floatval($rows['no_of_return']);
           if($item_out != 0){
               $rows['process_type'] = "Pledged Loan,Redeemed Loan";
           }
          
            $rows['amount_out'] = $amount_out;
            $rows['amount_in'] = $amount_in;
            $rows['weight_in'] = $weight_in;
            $rows['weight_out'] = $weight_out;
            $rows['weight_in_gs'] = $weight_in_gs;
            $rows['weight_in_mgs'] = $weight_in_mgs;
            $rows['weight_out_gs'] = $weight_out_gs;
            $rows['weight_out_mgs'] = $weight_out_mgs;
            $rows['item_in'] = $item_in;
            $rows['item_out'] = $item_out;
	    $rows['opening_item'] = $opening_item;
            $rows['closing_item'] = $closing_item;
            $rows['opening_amount'] = $opening_amount;
            $rows['closing_amount'] = $closing_amount;            
            $rows['opening_weight'] = $opening_weight;
            $rows['closing_weight'] = $closing_weight;
	    $closing_to_opening_weight = $closing_weight;
	    $closing_to_opening_amount = $closing_amount;
            $rows['opening_weight_gs'] = $opening_weight_gs;
            $rows['opening_weight_mgs'] = $opening_weight_mgs;
            $rows['closing_weight_gs'] = $closing_weight_gs;
            $rows['closing_weight_mgs'] = $closing_weight_mgs;
            
           
                $result[] = $rows;
                }
				$di++;

          }
        }else{
          $result = $reports;
        }
    	//exit;
		$result = array_reverse($result);
        return $result;
        
    }

    

}
