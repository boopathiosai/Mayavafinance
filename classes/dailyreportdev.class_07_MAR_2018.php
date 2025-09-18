<?php //**************************************************************************
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
class DailyReportDev {
     /*
         * Define the required fields and return text
         */
    public static function ListviewCount()
    {
    /*
     * Get the function parameters , if exist . And make the search condition
     */
    $args = func_get_args();
    $app = $GLOBALS['app'];
    $s_branch = $args[0];
    $s_sdate = $args[1];
    $s_edate = $args[2];
    $s_sub_branch = $args[3];
    $type_of_page = $args[4];
        if($type_of_page==1)
        {
          $search_condition  = " AND  ( CF.date_transaction BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";  
        }

        $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
    $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";

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
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
         }
    /*
     * Return the Customers list
     */
        $query = "SELECT
        COUNT(DISTINCT CONCAT(B.id,'-',CF.date_transaction))
        FROM ".CASH_FLOW_TRANSACTIONS." as CF
        LEFT JOIN ". BRANCH." AS B ON B.id = CF.branch_id
        WHERE 1  ".$search_condition;
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
        $s_branch = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_sub_branch = $args[3];
        $type_of_page = $args[4];
            if($type_of_page==1)
            {
                $search_condition  .= " AND  (P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";  
                           
            }

        $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
    //$search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
        if((is_array($s_sub_branch))&&($s_sub_branch!='')&&($s_sub_branch[0]>0))
        {
          foreach ($s_sub_branch as $key => $value) {
            $sub_branch_id_overall[] = $value;
          }
          $sub_branch_ids = implode(',',$sub_branch_id_overall);
          $search_condition .= ($s_sub_branch != "")? "AND ( SB.id IN(".$sub_branch_ids."))":"";
        }
        // elseif(count($s_sub_branch)<=1)
        // {
        //     $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch[0]."' )  ":"";
        // }

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
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
         }

         /*
          * Return the Customers list count
          */
       // $count = self::ListviewCount($s_branch , $s_sdate,$s_edate);
        $count = self::ListviewCount($s_branch , $s_sdate,$s_edate,$sub_branch,$type_of_page);
//echo $count;exit;
        if ($count > 0)
           {
                /*
                * Return the Customers list
                */
                Paging::set_max( $count );
                Paging::calc();// calculation for paging
                $query = "SELECT
                CONCAT(B.id,'-',CF.date_transaction) AS id,
                B.id AS branch_id,
                CF.date_transaction AS report_date,
                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,
                SUM(P.loan_amount) AS total_pledged,
                COUNT(DISTINCT C.id) AS no_of_customer,
                COUNT(DISTINCT P.id) AS no_of_issued,
                CONCAT( SB.name,' - (',SB.sub_branch_code,')') AS sub_details,
                B.name AS branch_name,P.sub_branch_id
                FROM ".CASH_FLOW_TRANSACTIONS." as CF
                LEFT JOIN ". BRANCH." AS B ON B.id = CF.branch_id
                LEFT JOIN ". PLEDGE." AS P ON CF.id = P.cash_flow_id
                LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                LEFT JOIN ".SUB_BRANCH." AS SB ON SB.branch_id = CF.branch_id
                WHERE 1 				
               /* AND (P.status = 1)   changed 07_AUG_2017 */
				 ".$search_condition."
                GROUP BY CF.date_transaction, B.id
                ORDER BY CF.date_transaction DESC ". Paging::get_sql_limit();
                return $app->getrows($query);

           }
    }

    public static function subListviewCount()
    {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
         $args = func_get_args();
        $app = $GLOBALS['app'];
        $s_branch = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";

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
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
         }
        /*
         * Return the Customers list
         */
        $query = "SELECT
                CF.id
                FROM ".CASH_FLOW_TRANSACTIONS." as CF
                LEFT JOIN ". BRANCH." AS B ON B.id = CF.branch_id
                LEFT JOIN ". PLEDGE." AS P ON CF.id = P.cash_flow_id
                LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                LEFT JOIN ".REDEMPTION." as PR ON CF.id = PR.cash_flow_id
                LEFT JOIN ".PRODUCT_AUCTION." as PAI ON CF.id = PAI.cash_in_id
                WHERE 1  ".$search_condition."
                GROUP BY CF.cash_purpose";
        $data = $app->getrows($query);
        return $count = count($data);
     }


    public static function subListview()
    {
            /*
             * Get the function parameters , if exist . And make the search condition
             */
      $args = func_get_args();
      $app = $GLOBALS['app'];
      $paging = $GLOBALS['paging'];
      $s_branch = $args[0];
      $s_sdate = $args[1];
      $s_edate = $args[2];
      $s_sub_branch =$args[3];
      $type_of_page =$args[4];
      
       if($type_of_page==1){
         $search_condition  .= " AND  ( CF.date_transaction BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())"; 
       }

        $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";

        if((is_array($s_sub_branch))&&($s_sub_branch!='')&&(count($s_sub_branch)>0)&&(!empty($s_sub_branch[0])))
        {
          $brnch = 0;
          
          foreach ($s_sub_branch as $key => $value) {
            $sub_branch_id_overall[] = $value;
            
            if($value == "undefined"){
                $brnch = 1;
            }
            
          }
         //echo $brnch;
         //echo "coming";
         if($brnch == 11){
          $sub_branch_ids = implode(',',$sub_branch_id_overall); 
          
          $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id IN(".$sub_branch_ids."))":"";
          $get_sub_brnch = " OR P.cash_flow_id=CF.id 
                           /*  OR PT.loan_id= P.id  
                             OR R.loan_id = P.id 
                             OR PA.loan_id = P.id  */      
                             LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id";         
          
          }  else {            
        $get_sub_brnch =" LEFT JOIN ".PLEDGE." AS P2 ON  P2.cash_flow_id=CF.id 
        LEFT JOIN ".PLEDGE." AS P3 ON  PT.loan_id= P3.id 
        LEFT JOIN ".PLEDGE." AS P4 ON  R.loan_id = P4.id 
        LEFT JOIN ".PLEDGE." AS P5 ON  PA.loan_id = P5.id  LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id";  
              
          }
}
        //$search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";

        if($s_sdate != "" && $s_edate != ""){
            $search_condition .= " AND  ( CF.date_transaction BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){
            $search_condition .= " AND  ( CF.date_transaction >= '".date("Y-m-d",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){
            $search_condition .= " AND  ( CF.date_transaction <= '".date("Y-m-d",strtotime($s_edate))."')";
        }else{
            //$search_condition .= " AND  ( report_date = '".date("Y-m-d")."')";
        }

        if(!is_superadmin($_SESSION['session_usergroupname'])){
        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
        }

        $having_query = "
        HAVING 1
        ".$search_condition." ";
        $where_query = "";
        $where_query .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
       // $where_query .= ($s_sub_branch != "")? "AND ( PL.sub_branch_id  = '".$s_sub_branch."' )  ":"";

        if($s_sdate != "" && $s_edate != ""){
            $where_query .= " AND  ( CF.date_transaction BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){
            $where_query .= " AND  ( CF.date_transaction >= '".date("Y-m-d",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){
            $where_query .= " AND  ( CF.date_transaction <= '".date("Y-m-d",strtotime($s_edate))."')";
        }else{
            //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
        }

        if(!is_superadmin($_SESSION['session_usergroupname'])){
            $where_query .= ($_SESSION['session_user_branch_id'] != "")?" AND  CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
        }

        /*manimaran*/
         // if($s_sub_branch != ""){

         //  $sub_branch_join="LEFT JOIN mf_sub_branch AS SB ON SB.id = PL.sub_branch_id";
         //  $sub_branch_select_field="SB.id AS sub_branch_id,";
         // }
         /*Manimaran*/
        $order_by = " ORDER BY branch_id,report_date,order_id,CF.id";
        /*
           Before Modification query 07-Sep-2016

         $query = "SELECT
         CF.id AS id,
         CF.date_transaction AS report_date,
         DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,
         CF.id AS cash_flow_id,
         CF.parent_id AS cash_parent_id,
         1 AS order_id,
         B.id as branch_id,
         B.name as branch_name,
         CF.cash_purpose AS particulars,
         CF.opening_balance AS opening_balance,
         CF.closing_balance AS closing_balance,
         IF(CF.transaction_type = 'cashin', CF.amount, 0) AS cash_in,
         IF(CF.transaction_type = 'cashout', CF.amount, 0) AS cash_out,PA.profit_status,PA.amount_to_pay
         FROM ".CASH_FLOW_TRANSACTIONS." as CF
         LEFT JOIN ". BRANCH." AS B ON B.id = CF.branch_id
         LEFT JOIN ". PRODUCT_AUCTION." AS PA ON PA.cash_in_id = CF.id WHERE module_type != 11 ".$having_query." ".$order_by;

       */

        /*AFTER MOFICATION  08-sep-2016 */
        /* $query = "SELECT
         DISTINCT CF.id AS id,
         CF.date_transaction AS report_date,
         DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,
         CF.id AS cash_flow_id,
         CF.parent_id AS cash_parent_id,
         1 AS order_id,
         B.id as Branch_id,
         B.name as branch_name,
         CF.cash_purpose AS particulars,
         CF.opening_balance AS opening_balance,
         CF.closing_balance AS closing_balance,
         IF(CF.transaction_type = 'cashin', CF.amount, 0) AS cash_in,
         IF(CF.transaction_type = 'cashout', CF.amount, 0) AS cash_out,PA.profit_status,PA.amount_to_pay
         FROM ".CASH_FLOW_TRANSACTIONS." as CF
         LEFT JOIN ". BRANCH." AS B ON B.id = CF.branch_id
         LEFT JOIN  ".PRODUCT_INTERESTS." AS PT ON PT.cash_flow_id = CF.id
         LEFT JOIN ".REDEMPTION." AS R ON R.cash_flow_id = CF.id
         LEFT JOIN ". PRODUCT_AUCTION." AS PA ON PA.cash_in_id = CF.id
         LEFT JOIN ".PLEDGE." AS P ON P.cash_flow_id=CF.parent_id OR P.cash_flow_id=CF.id OR  PT.loan_id= P.id  OR R.loan_id = P.id OR  PA.loan_id = P.id
         LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id
         WHERE  module_type != 11 ".$search_condition." ".$order_by; */
         
         
    // DSSK - 05_MAY_2017 //     
      $query = "SELECT
         DISTINCT CF.id AS id,
         CF.date_transaction AS report_date,
         DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,
         CF.id AS cash_flow_id,
         CF.parent_id AS cash_parent_id,
         1 AS order_id,
         B.id as Branch_id,
         B.name as branch_name,
         CF.cash_purpose AS particulars,
         CF.opening_balance AS opening_balance,
         CF.closing_balance AS closing_balance,
         CF.transaction_type AS type,
         CF.amount AS amnt,
         PA.profit_status,PA.amount_to_pay
         FROM ".CASH_FLOW_TRANSACTIONS." as CF             

         LEFT JOIN ". BRANCH." AS B ON B.id = CF.branch_id
         LEFT JOIN  ".PRODUCT_INTERESTS." AS PT ON PT.cash_flow_id = CF.id
         LEFT JOIN ".REDEMPTION." AS R ON R.cash_flow_id = CF.id
         LEFT JOIN ". PRODUCT_AUCTION." AS PA ON PA.cash_in_id = CF.id
         LEFT JOIN ".PLEDGE." AS P ON P.cash_flow_id=CF.parent_id
         ".$get_sub_brnch."  		  
         WHERE  module_type != 11 
         /* AND P.redeem_status != 0
		 AND (P.status != 0)   changed 07_AUG_2017  */  	  
		  ".$search_condition." ".$order_by; 
          
         // echo $query; exit;
      
         /*
          BEFORE MODIFICATION QUERY(19-SEp-2016)
         WHERE CF.editable = 0 AND  CF.module_type !=4 AND module_type != 11 ".$search_condition." ".$order_by;

         */
         //$test=$query

        if(is_array($query)){
            $result = array();
            foreach($query as $qry){
                $res = $app->getrows($qry);
                foreach($res as $r){
                    $result[] = $r;
                }
            }

            return $result;

        }else{

         /*
              * Return the Customers list count
              */
            //$count = self::subListviewCount($s_branch, $s_sdate, $s_edate);
            $preresult = $app->getrows($query);
            $jj=1;
//            echo count($preresult);
            // echo "<pre>";print_r($preresult);
            // exit;
            if(is_array($preresult))
            {
                foreach ($preresult as $key => $count_qry) {
                    $test1[]=$key_combine[$key][$pcid[2]];
                      $pquery = "SELECT id, loan_id,sub_branch_id FROM ".PLEDGE." WHERE cash_flow_id = ".$count_qry['cash_flow_id'];
                        //echo $pquery = "SELECT id, loan_id,sub_branch_id FROM ".PLEDGE." WHERE cash_flow_id = 33190";
                        // $cas[]=$count_qry['cash_flow_id'];
                     $pcid = $app->getrow($pquery);
                        //echo (int)$pcid[0];
                if((int)$pcid[0] > 0)
                {

                  $cash_purpose = CASHFLOW_PURPOSE_PLEDGED;
                  $loan_id = (int)$pcid[0];
                  $loan_code = (int)$pcid[1];
                  $order_id = '02';
				  //$ax[]="SELECT id, loan_id,sub_branch_id FROM ".PLEDGE." WHERE cash_flow_id = ".$count_qry['cash_flow_id'];
                  $key_combine[]=array($pcid[2]=>$pcid[1]);
                  $dpi++;

                   /* if($key_combine[$key][$pcid[2]] == $key_combine[$jj][$pcid[2]])
                     {
                        $array_1[]=$pcid[1];
                            //$jj++;
                     }
                    elseif($key_combine[$key][$pcid[2]] != $key_combine[$jj][$pcid[2]]){
                       $array_2[]=$pcid[1];
                    }
					*/
                }
              //$count_res[]=$count_qry['particulars'];
              if($count_qry['particulars']=='Redeemed Loan'){
                $count_res[]=$count_qry['particulars'];
              }
              else if($count_qry['particulars']=='Pledged Loan'){
                $count_res_pledge[]=$count_qry['particulars'];
              }

            }
            $jj++;
            }
			$new_array=array();
			//echo "<pre>";print_r($ax);exit;
        if(!empty($key_combine)){    // DSSK changed 12_SEP_2017 //  
            foreach ($key_combine as $key => $value) {
            	foreach ($value as $key1 => $key_value) {
            		//print_r($key_value);
            		$new_array[$key1][] = $key_value;

            	}

            	# code...
            }
             } // \.DSSK changed 12_SEP_2017 //
 //              echo '<pre>';
 //   //  // //       // $cd = split(",", $a[12]);
 //   //  // //       print_r($gg);
 //       print_r($array_1);
 // exit;

            $result = self::tallyData($preresult,$s_sub_branch,$count_res,$count_res_pledge,$new_array);
            $count = count($result);
            if($count > 0)
            {
             /*
              * Return the Customers list
              */
                return $result;
            }
        }
    }


    public static function Listexport()
            {
                /*
                 * Get the function parameters , if exist . And make the search condition
                 */
                 $args = func_get_args();
                $app = $GLOBALS['app'];
                $paging = $GLOBALS['paging'];
                $s_branch = $args[0];
                $s_sdate = $args[1];
                $s_edate = $args[2];
                $s_sub_branch = $args[3];

       /* $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
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
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
         }*/

        $search_condition .= ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
          if((is_array($s_sub_branch))&&($s_sub_branch!='')&&($s_sub_branch[0]>0))
        {
          foreach ($s_sub_branch as $key => $value) {
            $sub_branch_id_overall[] = $value;
          }
          $sub_branch_ids = implode(',',$sub_branch_id_overall);
          $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id IN(".$sub_branch_ids."))":"";
        }

      // old code
      //  $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";

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
        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
        }
        $having_query = "
        HAVING 1
        ".$search_condition." ";

    //$order_by = "  ORDER BY CF.id DESC ";
                 /*
                  * Return the Customers list count
                  */
               //$count = self::subListviewCount($s_branch, $s_sdate, $s_edate);
                //$aquery = self::getQuery($s_branch, $s_sdate, $s_edate);
                //$query = $aquery['s'];

                 $query = "SELECT
                CONCAT(B.id,'-',CF.date_transaction) AS id,
                B.id AS branch_id,
                CF.date_transaction AS report_date,
                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,
                SUM(P.loan_amount) AS total_pledged,
                COUNT(DISTINCT C.id) AS no_of_customer,
                COUNT(DISTINCT P.id) AS no_of_issued,
                B.name AS branch_name
                FROM ".CASH_FLOW_TRANSACTIONS." as CF
                LEFT JOIN ". BRANCH." AS B ON B.id = CF.branch_id
                LEFT JOIN ". PLEDGE." AS P ON CF.id = P.cash_flow_id
                LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id
                WHERE 1  ".$search_condition."
                GROUP BY CF.date_transaction, B.id
                ORDER BY CF.date_transaction DESC";

              //echo $query;exit;
                if(is_array($query)){
                    $result = array();
                    foreach($query as $qry){
                        $res = $app->getrows($qry);
                        foreach($res as $r){
                            $result[] = $r;
                        }
                    }
                    return $result;
                }else{
                //$result = $app->getrows($query);
                $preresult = $app->getexport($query);
        foreach($preresult as $val) {
          $search_branch_id = $val['branch_id'];
          $search_sdate = $val['trans_date'];
          $search_edate = $val['trans_date'];
          $result[] = self::subListview($search_branch_id,$search_sdate,$search_edate,$s_sub_branch);
        }
                //$result = self::exportData($preresult, false);
                $count = count($result);

                if ($count > 0) {
          $rtn_result = array();
          foreach($result as $array)

          {
            foreach($array as $val)
            {
              array_push($rtn_result, $val);
            }
          }
          return array_reverse($rtn_result);
          }

                }
            }
  private static function tallyData($reports,$sub_branch_select,$sublist = true,$pledge_count,$new_array){
      global $app;
      $pledge_loan_count=count($pledge_count);
      $report_count = count($reports);
       if($report_count > 0){
          $tally = array();
          $pledge_amount = 0;
          $pledge_loanids = '';
          $inipledge_loanids = '';
          $redeem_amount = 0;
          $redeem_count = 0;
          $pledge_count = 0;
          $auction_amount = 0;
          $auction_amount_profit = 0;
          $auction_amount_lose = 0;
          $interest_amount = 0;
          $prebranch_id = 0;
          $pretrans_date = $rows[0]['trans_date'];
          $total_cash_in = 0;
          $total_cash_out = 0;
          $other_total =  array();
          $ttl_loan_amnt1 = count($reports);
          $ttl_loan_loop_count = 0;
          $dpi = 0;         
          $di = 1;      
          $jo=1;
          $cash_purpose_old = "";
          foreach($reports as $key => $rows)
          {       
               /* Changes 28_APR_2017 */
                if($rows['type'] == 'cashin'){
                    $rows['cash_in'] = $rows['amnt']; 
                }elseif($rows['type'] == 'cashout'){
                     $rows['cash_out'] = $rows['amnt']; 
                }
                /* End */
                $opening_amount = floatval($rows['opening_balance']);
                $closing_amount = floatval($rows['closing_balance']);
                $total_cash_in += (int)$rows['cash_in'];
                $total_cash_out += (int)$rows['cash_out'];
                $cash_purpose_open =  CASHFLOW_PURPOSE_OPENING;
                $cash_purpose_close =  CASHFLOW_PURPOSE_CLOSING;

//                $cash_purpose_total = $rows['particulars'] == 'Auction' ? 'Auction amount' : CASHFLOW_PURPOSE_TOTAL;
                $cash_purpose_total = $rows['particulars'] == 'Auction' ? 'Title' : CASHFLOW_PURPOSE_TOTAL;
                $cash_in = floatval($rows['cash_in']);
                $cash_out = floatval($rows['cash_out']);
                $id = $rows['report_date'];

                $branch_id = $rows['branch_id'];
                $trans_date = $rows['trans_date'];

                if($di == 1){
                    $order_id = '01';
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_open]['id'] = $id;
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_open]['report_date'] = $rows['report_date'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_open]['trans_date'] = $rows['trans_date'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_open]['order_id'] = $order_id;
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_open]['branch_id'] = $rows['branch_id'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_open]['branch_name'] = $rows['branch_name'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_open]['particulars'] = $cash_purpose_open;
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_open]['cash_in'] = $opening_amount;

                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_open]['cash_out'] = '';
                     $open_to_cal_cls = $opening_amount;

                }             

                $loan_count++;
                if(($key+1) == $report_count)
                {                   
                    $closing_amount = (($open_to_cal_cls+$total_cash_in)-($total_cash_out));
                    $order_id = '09';
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_total]['id'] = $id;
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_total]['report_date'] = $rows['report_date'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_total]['trans_date'] = $rows['trans_date'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_total]['order_id'] = $order_id;
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_total]['branch_id'] = $rows['branch_id'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_total]['branch_name'] = $rows['branch_name'];
                    if($rows['particulars']=="Auction")
                    {
                        $count_auction = "(".$loan_count.")";
                    }
//                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_total]['particulars'] = $cash_purpose_total.$count_auction;
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_total]['particulars'] = $cash_purpose_total;
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_total]['cash_in'] = $total_cash_in;
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_total]['cash_out'] = $total_cash_out;

                    $order_id = '12';
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_close]['id'] = $id;
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_close]['report_date'] = $rows['report_date'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_close]['trans_date'] = $rows['trans_date'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_close]['order_id'] = $order_id;
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_close]['branch_id'] = $rows['branch_id'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_close]['branch_name'] = $rows['branch_name'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_close]['particulars'] = $cash_purpose_close;
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_close]['cash_in'] = '';
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose_close]['cash_out'] = $closing_amount;


                    $pledge_amount = 0;
                    $pledge_loanids = '';
                    $redeem_amount = 0;
                    $pledge_count = 0;
//                    $redeem_count = 0;
                    $auction_amount = 0;
                    $auction_amount_profit = 0;
                    $auction_amount_lose = 0;
                    //$interest_amount = 0;
                    $prebranch_id = 0;
                    $pretrans_date = 0;
                    $total_cash_in = 0;
                    $total_cash_out = 0;
                    $dpi = 0;
                    //$loan_code_count=0;
                    $di = 0;

                }
                $loan_id = 0;
                $pquery = "SELECT id, loan_id,sub_branch_id FROM ".PLEDGE." WHERE cash_flow_id = ".$rows['cash_flow_id'];
                $pcid = $app->getrow($pquery);
                if((int)$pcid[0] > 0)
                {
                  $cash_purpose = CASHFLOW_PURPOSE_PLEDGED;
                  $loan_id = (int)$pcid[0];
                  $loan_code = (int)$pcid[1];
                  $order_id = '02';
                 // $aaa[]=$pcid[1];
                  $dpi++;
                }
                else
                {
                    //$test=array();
                  $rquery = "SELECT loan_id FROM ".REDEMPTION." WHERE cash_flow_id =  ".$rows['cash_flow_id']." AND redeem_status=0";

                  $rcid = $app->getrow($rquery);

                  if((int)$rcid[0] > 0)
                  {
                    $cash_purpose = CASHFLOW_PURPOSE_REDEEMED;
                    $loan_id = (int)$rcid[0];
                    $order_id = '03';
                    //$loan_code_count++;
                    if($loan_id > 0)     
                        {
                        
                       /* if(count($sub_branch_select) != 0){
                            $sub_branch_ids = implode(',',$sub_branch_select);
                          // $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id IN(".$sub_branch_ids."))":"";
                            $where=" AND sub_branch_id IN(".$sub_branch_ids.")";
                        }*/
                        
                        if($rows['sub_branch_id']){
                          $where=" AND sub_branch_id= ".$rows['sub_branch_id']."";

                        }
                        //$test = $loan_id;
                      $pquery1 = "SELECT loan_amount, loan_id FROM ".PLEDGE." WHERE id = ".$loan_id.$where;

                      //echo $pquery1;exit;
                      $pr1 = $app->getrow($pquery1);
//                      $redeem_count = count($pr1);
                      if($pr1[0]){$loan_amount1 += (int)$pr1[0];}
                      //$a[]=$pr1[1];
                      $loan_code1 = $pr1[1];
                    }
                  }
                  else
                  {
                      $apquery = "SELECT loan_id,amount_difference,profit_status,auction_amount FROM ".PRODUCT_AUCTION." WHERE cash_in_id =  ".$rows['cash_flow_id'];
                      $apcid = $app->getrow($apquery);
                      if((int)$apcid[0] > 0){
                          //$cash_purpose2 = CASHFLOW_PURPOSE_AUCTION_PROFIT;
                          $loan_id = (int)$apcid[0];
                          $auction_diff_amt = $apcid[1];
                          $auc_amount += (int)$apcid[3];
                          if($auc_status == 1)
                          {
                              $order_id = '05';
                              $cash_purpose = CASHFLOW_PURPOSE_AUCTION;
                          }else{
                              $order_id = '07';
                              $cash_purpose = CASHFLOW_PURPOSE_AUCTION;
                          }
                       }else{
                                $piquery = "SELECT loan_id, interest_amount FROM ".PRODUCT_INTERESTS." WHERE cash_flow_id = ".$rows['cash_flow_id'];
                                $picid = $app->getrow($piquery);
                                if((int)$picid[0] > 0){
                                  $cash_purpose = PAY_INTEREST;

                                  $loan_id = (int)$picid[0];
                                  $int_amt = (int)$picid[1];
                                  
                                  $order_id = '04';
                                }else{
                                    /*if service has any transcation*/

                                      $cash_purpose = $rows['particulars'];
                                      // $b[]=$cash_purpose;
                                      $loan_id = 0;
                                      $order_id = '08';

                                }
                            //}
                        }
                    }

                }

                    if($loan_id > 0)
                {
                    //print_r($sub_branch_select);exit;
//                     if(!is_superadmin($_SESSION['session_usergroupname'])){
//                        echo "<pre>";
//                        print_r($sub_branch_select);
                     if(($sub_branch_select!='')&&($sub_branch_select[$key]!='')&&($sub_branch_select[$key]!='Array')&&($sub_branch_select[$key] != 'undefined')){
                       $pquery = "SELECT loan_amount, loan_id,sub_branch_id FROM ".PLEDGE." WHERE id = ".$loan_id." AND sub_branch_id=".$sub_branch_select[$key];
                     }else{
                      $pquery = "SELECT loan_amount, loan_id,sub_branch_id FROM ".PLEDGE." WHERE id = ".$loan_id ;

                    }                    
//                }                   
                    $pr = $app->getrow($pquery);                               
                    $loan_amount += (int)$pr[0];
                     $loan_amount_auc = (int) $pr[0]; // added 05-MAR-2018
                    $loan_code = $pr[1];
                    $loan_branch_code =$pr[2];
                    $j=$key+1;
                    //$a[]=$loan_branch_code;
                    //$a=array($j=>$key);
                    
                }
                $different = '';

                        if($loan_id>0){
                           // $b[]=$cash_purpose;
                switch($cash_purpose){

                    case CASHFLOW_PURPOSE_PLEDGED:
                        $ax[]=$loan_id;
//                        $pledge_amount += (int)$pr[0];                        
                        $pledge_amount += (int)$pr[0]; //changed 25_OCT_2017                        
                        if(($dpi == $pledge_loan_count)&&(count($sub_branch_select)!=''))
                        {
                             
                              if(count($new_array)<=1)
                            {

                            	$element_first=current($new_array);                            	
                            	$test ="(".current($element_first)."-".end($element_first).")(".count($element_first).")";                                 

                            }
                            else{
                            	$element_first=current($new_array);
                            	$element_last=end($new_array);
                            	
                            		$test = "(".current($element_first)."-".end($element_first).")(".count($element_first).")";                                                      
                            		$test.= "(".current($element_last)."-".end($element_last).")(".count($element_last).")";
                            	
                            }
                            }                        
                        if($dpi == 1){
                          $inipledge_loanids = $loan_code;
                        }
                        elseif ($dpi == $pledge_loan_count) {
                              $last_pledge_loanids = '-'.$loan_code;
                            }


                          if($loan_code){$pledge_count ++;}

                        /*Manimaran*/                       
                        $xx[]=count($sub_branch_select);
                         if(($sub_branch_select!='')&&($sub_branch_select[$key]!='Array')&&($xx[$key]==2)){
                          $cash_purpose_format = $cash_purpose." (".$inipledge_loanids.''.$last_pledge_loanids.")";                         
                         }

                         //code for total count for pledge without slecting branch//
                         elseif($sub_branch_select=='')
                         {
                            $cash_purpose_format= $cash_purpose."(".$pledge_count.")";  
                         }elseif($_SESSION['session_userid'] == 1){
                             $cash_purpose_format= $cash_purpose."(".$pledge_count.")";
                         }
                         else{
//                             $cash_purpose_format= $cash_purpose." ".$test;
                              $cash_purpose_format= $cash_purpose."(".$pledge_count.")"; // DSSK changed 12_SEP_2017//
                         }

                          if($loan_code){
                          $a[$loan_branch_code]=array($loan_code);
                        }

                       /* $rows['particulars'] = $cash_purpose." (".$inipledge_loanids.''.$last_pledge_loanids.")";*/
                        $rows['particulars'] = $cash_purpose_format;
                        $rows['cash_in'] = '';
                        $rows['order_id'] = $order_id;
                        $rows['cash_out']= $pledge_amount;


                    break;

                    case CASHFLOW_PURPOSE_REDEEMED:
                         /*Manimaran*/
                         $z[]=$rows['redeem_del_status'];
                             $redeem_amount = $loan_amount1;                           
                            $redeem_count ++;                          
                        $rows['particulars'] = $cash_purpose." (".$redeem_count.")";
                        $rows['cash_in'] = $redeem_amount;
                        $rows['order_id'] = $order_id;
                        $rows['cash_out']= '';                    
                    break;

                    case CASHFLOW_PURPOSE_AUCTION:
                      $different = $cash_purpose;
                            $auction_amount = $loan_amount;
                            $auc_amnt = $auc_amount;
//                            $ttl_loan_amnt += $loan_amount_auc;
                     
                        $auction_count ++;
                      if($cash_purpose == 'Auction')
                        $cash_purpose = 'Auction Sale :- Principle';
                        $rows['particulars'] = $cash_purpose." (".$auction_count.")";
                        $rows['cash_in'] = $auction_amount;
                        $rows['order_id'] = $order_id;
                        $rows['cash_out']= '';

                     break;

                    case CASHFLOW_PURPOSE_AUCTION_PROFIT:

                        $auction_amount_profit += $auction_diff_amt;
                        $auc_amount += $auc_amount;
                        $order_id = '06';
                        $rows['particulars'] = $cash_purpose;
                        $rows['cash_in'] = '';
                        $rows['order_id'] = $order_id;
                        $rows['cash_out']= $auction_amount_lose;

                    break;

                    case CASHFLOW_PURPOSE_AUCTION_LOSS:

                        $auction_amount_lose += $auction_diff_amt;
                        $rows['particulars'] = $cash_purpose;
                        $rows['cash_in'] = '';
                        $rows['order_id'] = $order_id;
                        $rows['cash_out']= $auction_amount_lose;

                    break;

                    case PAY_INTEREST:
                        $io[]=$rows['cash_flow_id'];
                        $interest_amount += $int_amt;
                        $rows['particulars'] = $cash_purpose;
                        $rows['cash_in'] = $interest_amount;
                        $rows['order_id'] = $order_id;
                        $rows['cash_out']= '';
                    break;

                    default:

                        $cash_in = (int)$other_total_cash_in[$id.'_'.$order_id.'_'.$cash_purpose] + $rows['cash_in'];
                        $cash_out = (int)$other_total_cash_out[$id.'_'.$order_id.'_'.$cash_purpose] + $rows['cash_out'];

                        $other_total_cash_in[$id.'_'.$order_id.'_'.$cash_purpose] = $cash_in;
                        $other_total_cash_out[$id.'_'.$order_id.'_'.$cash_purpose] = $cash_out;

                        $rows['particulars'] = $cash_purpose;
                        $rows['cash_in'] = $cash_in;
                        $rows['order_id'] = $order_id;
                        $rows['cash_out']= $cash_out;


                    break;
                }
}
$ttl_loan_loop_count++;

                if($different == 'Auction') {
                      $ttl_loan_amnt += $loan_amount_auc;
                     $ttl_loan_amnt_c = $ttl_loan_amnt;

                      if ($ttl_loan_amnt1 == $ttl_loan_loop_count) {
                          $ttl_loan_amnt_c = $ttl_loan_amnt;

                     }

                    $different = 'Auction Sale :- Margin';
                    $amount_to_pay +=  $rows['amount_to_pay'];               

                    $cash_out = 0;


                    if($ttl_loan_amnt_c >= $auc_amnt)
//                      $cash_in = $auction_amount - $auc_amnt;
                      $cash_in = $ttl_loan_amnt_c - $auc_amnt;
                    else
//                      $cash_in = $auc_amnt - $auction_amount;                  
                      $cash_in = $auc_amnt - $ttl_loan_amnt_c;                  

                    
                    $tally[$id.'_'.$order_id.'_'.$different]['id'] = $id;
                    $tally[$id.'_'.$order_id.'_'.$different]['report_date'] = $rows['report_date'];
                    $tally[$id.'_'.$order_id.'_'.$different]['trans_date'] = $rows['trans_date'];
                    $tally[$id.'_'.$order_id.'_'.$different]['order_id'] = $rows['order_id'];
                    $tally[$id.'_'.$order_id.'_'.$different]['branch_id'] = $rows['branch_id'];
                    $tally[$id.'_'.$order_id.'_'.$different]['branch_name'] = $rows['branch_name'];
                    $tally[$id.'_'.$order_id.'_'.$different]['particulars'] = 'Margin ('.$auction_count.')';
                    $tally[$id.'_'.$order_id.'_'.$different]['cash_in'] = $cash_in;
                    $tally[$id.'_'.$order_id.'_'.$different]['cash_out'] = $cash_out;
                    
                    $different = '';                    
                    $rows['cash_in'] = $auction_amount;
                    if($cash_out != 0) {
                      $total_cash_out += $cash_out;
                    }
                    //Shan ends
                }
                
             if($cash_purpose_old == $cash_purpose){                          
//                     $cash_purpose = $cash_purpose.$key;  // changed 07_DEC_2017 by DSSK                     
                     $cash_purpose = $cash_purpose;                       
                
             }
                
                $cash_purpose_old = $cash_purpose;
                if(isset($tally[$id.'_'.$order_id.'_'.$cash_purpose])){

                    $tally[$id.'_'.$order_id.'_'.$cash_purpose]['particulars'] = $rows['particulars'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose]['cash_in'] = $rows['cash_in'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose]['cash_out'] = $rows['cash_out'];
//                    echo CASHFLOW_PURPOSE_AUCTION;
                    if($cash_purpose == "Auction Sale :- Principle"){
                        $tally[$id.'_'.$order_id.'_'.$cash_purpose]['cash_in'] = $ttl_loan_amnt_c;
                      }
                   
                    if($different == "Auction Sale :- Margin"){
                        $tally[$id.'_'.$order_id.'_'.$cash_purpose]['cash_in'] = '';
                      }
                      
                 
                }else{
                 //diffrent amount calculation when auction process
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose]['id'] = $id;
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose]['report_date'] = $rows['report_date'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose]['trans_date'] = $rows['trans_date'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose]['order_id'] = $rows['order_id'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose]['branch_id'] = $rows['branch_id'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose]['branch_name'] = $rows['branch_name'];
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose]['particulars'] = $rows['particulars']; //."-".$rows['cash_flow_id'];
                    
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose]['cash_in'] = $rows['cash_in'];
                    
                    if($cash_purpose == "Auction Sale :- Principle"){
                       
                        $tally[$id.'_'.$order_id.'_'.$cash_purpose]['cash_in'] = $ttl_loan_amnt_c;
                      }
                     
                    if($different == "Auction Sale :- Margin"){
                        $tally[$id.'_'.$order_id.'_'.$cash_purpose]['cash_in'] = '';
                      }
                      if(($cash_purpose=='Additional Charge')||($cash_purpose=='Service Charge'))
                    {
                        
                       if($rows['cash_in']==0)
                       {
                        $tally[$id.'_'.$order_id.'_'.$cash_purpose]['cash_in'] ='';

                       }
                   }
                    $tally[$id.'_'.$order_id.'_'.$cash_purpose]['cash_out'] = $rows['cash_out'];
                }

                $prebranch_id = $rows['branch_id'];
                $pretrans_date = $rows['trans_date'];


                $di++;

              }
          }
         if(!empty($tally)){ 
         ksort($tally);
         }
//         echo "<pre>"; print_r($tally);
          return $tally;
    }

}
?>
