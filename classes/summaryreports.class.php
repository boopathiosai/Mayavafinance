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


class SummaryReports {


        public static function ListviewCount() {
            /*
             * Get the function parameters , if exist . And make the search condition
             */

            $args = func_get_args();

            $app = $GLOBALS['app'];
            
            $s_wise = $args[0];
            $s_branch = $args[1];
            $s_sdate = $args[2];
            $s_edate = $args[3];

        #Date Filter for two days
        $start_date = date('Y-m-d',strtotime("-1 days"));
        $end_date = date('Y-m-d', strtotime("+1 days"));
        $search_condition = ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";
        if($s_sdate != "" && $s_edate != ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
        }else{
            $search_condition = " AND  ( CF.date_transaction BETWEEN '".$start_date."' AND '".$end_date."')";
        }
        
            /*
             * Return the Pledges list Count
             */
            
           
            $count = 0;
            if(!isset($_SESSION["summary_report_count"]))
                $count = (int)$_SESSION["summary_report_count"];
            if($count == 0){    
                $cquery = self::getListCountQuery($s_wise, $search_condition); 
                $crows = $app->getrows($cquery);

               // print_r($cquery ); exit;
              
                $count = 0;
                /*foreach($crows as $crow){
                    $count += (int)$crow[0];
                }*/
                $_SESSION["summary_report_count"] = $count;
				$count = count($crows);

            } 
            
            return $count;
     
       }

    public static function Listview() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_wise = $args[0];
        $s_branch = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];

        #Rajesh Mar 03, 21
        #Date Filter for two days
        $start_date = date('Y-m-d',strtotime("-1 days"));
        $end_date = date('Y-m-d', strtotime("+1 days"));        
        $search_condition = ($s_branch != "")? "AND ( B.id  = '".$s_branch."' )  ":"";
        if($s_sdate != "" && $s_edate != ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
        }else{
            $search_condition = " AND  ( CF.date_transaction BETWEEN '".$start_date."' AND '".$end_date."')";
        }
        
        
        

        $count = self::ListviewCount($s_wise, $s_branch, $s_sdate, $s_edate);
        /*
         * Return the Pledge list
         */
         
         
        if ($count > 0) {
            Paging::set_max( $count );
            Paging::calc();// calculation for paging
            $query = self::getListQuery($s_wise,$search_condition). Paging::get_sql_limit();
            //echo $query;
            //return $app->getrows($query);
            
            $preresults = $app->getrows($query);
            $results = self::getResultData($s_wise, $preresults);
            
            return $results;

        }
        
    }
    
        public static function subListviewCount() {
            /*
             * Get the function parameters , if exist . And make the search condition
             */

            $args = func_get_args();

            $app = $GLOBALS['app'];
            
            $s_wise = $args[0];
            $s_branch = $args[1];
            $s_sdate = $args[2];
            $s_edate = $args[3];

            
            $search_condition = ($s_branch != "")? "AND ( B.id  = '".$s_branch."' )  ":"";
            #Rajesh Mar 03, 21
            #Date Filter for two days
            $start_date = date('Y-m-d',strtotime("-1 days"));
            $end_date = date('Y-m-d', strtotime("+1 days"));
            if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                $search_condition = " AND  ( CF.date_transaction BETWEEN '".$start_date."' AND '".$end_date."')";
            }

            
            /*
             * Return the Pledges list Count
             */
            $query = self::getSubListQuery($s_wise, $search_condition);
            
            $data = $app->getrows($query);
            
            
            return $count = count($data);
     
       }

	//Sublist View
	public static function subListview(){

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_wise = $args[0];
        $s_branch = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];

        $search_condition = ($s_branch != "")? "AND ( B.id  = '".$s_branch."' )  ":"";

        #Rajesh Mar 03, 21
        #Date Filter for two days
        $start_date = date('Y-m-d',strtotime("-1 days"));
        $end_date = date('Y-m-d', strtotime("+1 days")); 
        if($s_sdate != "" && $s_edate != ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
        }else{
            $search_condition = " AND  ( CF.date_transaction BETWEEN '".$start_date."' AND '".$end_date."')";
        }
            
           
        
        
        

        $count = self::subListviewCount($s_wise,$s_branch, $s_sdate, $s_edate);
        /*
         * Return the Pledge list
         */
         
         
        //if ($count > 0) 
        {
            Paging::set_max( $count );
            Paging::calc();// calculation for paging
            $query = self::getSubListQuery($s_wise,$search_condition);
            //echo "<pre>"; echo $query."<hr>";die;
            //return $app->getrows($query);
            
            $preresults = $app->getrows($query);
            $results = self::getSubResultData($s_wise, $preresults);
            
            return $results;

        }
        
    }



    public static function Listexport() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_wise = $args[0];
        $s_branch = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];

        $search_condition = ($s_branch != "")? "AND ( CF.branch_id  = '".$s_branch."' )  ":"";

        #Rajesh Mar 03, 21
        #Date Filter for two days
        $start_date = date('Y-m-d',strtotime("-1 days"));
        $end_date = date('Y-m-d', strtotime("+1 days"));  

        if($s_sdate != "" && $s_edate != ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_condition .= " AND  ( DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
        }else{
            $search_condition = " AND  ( CF.date_transaction BETWEEN '".$start_date."' AND '".$end_date."')";
        }

        //echo "src = ".$search_condition;

       $count = self::ListviewCount($s_wise, $s_branch, $s_sdate, $s_edate);
        /*
         * Return the Pledge list
         */
        if ($count > 0) 
        {
			$query = self::getListQuery($s_wise,$search_condition);
            //echo $query;
            //exit;
            //return $app->getexport($query);
            
            $preresults = $app->getexport($query);
            $results = self::getResultData($s_wise, $preresults);
            
            return $results;
        }
    }
    
       public static function getListCountQuery($wise_type, $search_condition) {
   
           //echo $wise_type;
           
   
           
            
          switch(trim(strtolower($wise_type))){

            case "items":
            
				$query = "SELECT 
				COUNT(CF.id)
				FROM ".CASH_FLOW_TRANSACTIONS." AS CF
				LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
				WHERE CF.editable = 0 AND CF.module_type IN (1,2)  ".$search_condition." 
				GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'), B.id
				ORDER BY CF.date_transaction, B.name ";
                       
            break;   
            
            case "pay_interest":
                
				$query = "SELECT COUNT(CF.id) FROM ".CASH_FLOW_TRANSACTIONS." AS CF
				WHERE CF.editable = 0  AND CF.module_type IN (3)  ".$search_condition." 
				GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d')";
           
            break;
            
            case "cashflow":

				$query = "SELECT COUNT(DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'))
				FROM ".CASH_FLOW_TRANSACTIONS." AS CF
				LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
				WHERE 1 ".$search_condition." 
				GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'), B.id
				ORDER BY CF.date_transaction ASC , B.name ";

			break;
            
            
            case "stock_report":
                
				$query = "SELECT COUNT(CF.id) FROM ".CASH_FLOW_TRANSACTIONS." AS CF
			   WHERE CF.editable = 0  AND CF.module_type IN (1,2,4,5)  ".$search_condition." 
			   GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'), CF.branch_id ";
                
            break;

        }
    
        return $query;

       }
 
    

     public static function getListQuery($wise_type, $search_condition) {

        //echo $wise_type;


        switch(trim(strtolower($wise_type))){
        
            case "items":

                
              $query = "SELECT 
                MAX(CF.id) AS cash_flow_id,
                DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,                
                CONCAT(B.id,'-',DATE_FORMAT(CF.date_transaction,'%Y-%m-%d')) AS id,
                CONCAT(C.name,'- (',C.customer_id,')') AS customer_name,
                C.customer_id,
                B.name AS branch_name,
                B.id AS branch_id,C.id as cid,                
                COUNT(P.item_count) AS pledged_count,
                CONCAT(MIN(P.loan_id),'-',MAX(P.loan_id)) AS loan_no_range,
                
                SUM(P.loan_amount)  AS pledged_amount,

               SUM(P.item_count),0.00
                AS pledged_items, 

                SUM(P.total_net_weight)
                AS pledged_weight

                FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
                LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
                LEFT JOIN ".REDEMPTION." as R ON R.cash_flow_id = CF.id
                LEFT JOIN ".CUSTOMERS." as C ON P.customer_id = C.id OR R.customer_id = C.id
                WHERE CF.editable = 0 AND CF.module_type IN (1,2)  ".$search_condition." 
                GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'), B.id
                ORDER BY CF.date_transaction ASC, B.name";
                
               // echo $query;
                //exit;
                       
            break;           

        

            
            case "pay_interest":
                
                $query = "SELECT 
                            CF.id AS cash_flow_id,
                            GROUP_CONCAT(DISTINCT B.name SEPARATOR ', ' ) as branch_name,
                            DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS id, 
                            DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date, 

                            SUM(PI.total_paid_amount) AS total_paid,
                            SUM(PI.interest_amount) AS interest_paid,
                            SUM(PI.principle_amount) AS principle_paid,
                            SUM(PI.additional_charge) AS other_charges,
                            SUM(PI.wavier_amount) AS wavier_amount,
                            PI.wavier_days,
                            
                            CF.opening_balance AS opening_balance,
                            CF.closing_balance  AS closing_balance

                            FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                            LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
                            LEFT JOIN ".PLEDGE."  AS P ON P.cash_flow_id = CF.id 
                            LEFT JOIN ".PRODUCT_INTERESTS." as PI ON PI.cash_flow_id = CF.id
                            WHERE ( PI.id <> '' ) ".$search_condition." 
                            GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') 
                            ORDER BY CF.date_transaction ASC ";
            break;
            
            case "cashflow":

                $query = "SELECT 
                            CF.id AS cash_flow_id,
                            B.name as branch_name,
                            B.id as branch_id,
                            CONCAT(B.id,'-',DATE_FORMAT(CF.date_transaction,'%Y-%m-%d')) AS id,
                            DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date, 
                            DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date, 
                
                            CF.cash_purpose AS purpose
                            
                            FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                            LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
                            WHERE 1 ".$search_condition." 
                            GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'), B.id
                            ORDER BY CF.date_transaction ASC , B.name ";
                            break;
            
            
            case "stock_report":

                
                    $query = "SELECT 
                          CF.id AS cash_flow_id,      
                        CONCAT(B.id,'-',DATE_FORMAT(CF.date_transaction,'%Y-%m-%d')) AS id,
                        CONCAT(C.name,'- (',C.customer_id,')') AS customer_name,
                        C.customer_id,
                        GROUP_CONCAT(DISTINCT B.name SEPARATOR ', ' ) AS branch_name,
                        B.id AS branch_id,CF.module_type,
                        
                        P.loan_id AS loan_code,
                        P.id AS pledged_id,
                        CF.id AS cash_flow_id,
                        B.id AS branch_id,
                            
                        DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                        DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date_new,
                        DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                        DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,




                        SUM(P.loan_amount)  AS loan_amount_out,



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
                        
                        SUM(P.loan_amount) AS total_pledged,

                        
                        COUNT(DISTINCT C.id) AS no_of_customer,
                        COUNT(DISTINCT P.id) AS no_of_issued

                                
                                

                        FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                        LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
                        LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
                        LEFT JOIN ".CUSTOMERS." as C ON P.customer_id = C.id 

                        WHERE CF.editable = 0 ".$search_condition." AND CF.module_type IN(1,2,4)
                        GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'),CF.module_type 
                        ORDER BY CF.date_transaction,CF.module_type DESC
                        ";
              
            break;

        }
    
        return $query;
    }
    public static function getListQueryOld($wise_type, $search_condition) {

        //echo $wise_type;


        switch(trim(strtolower($wise_type))){
        
            case "items":

                
              $query = "SELECT 
                MAX(CF.id) AS cash_flow_id,
                DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,
                
                CONCAT(B.id,'-',DATE_FORMAT(CF.date_transaction,'%Y-%m-%d')) AS id,
				CONCAT(C.name,'- (',C.customer_id,')') AS customer_name,
                C.customer_id,
                B.name AS branch_name,
                B.id AS branch_id,C.id as cid,
                
                COUNT(P.item_count) AS pledged_count,
                CONCAT(MIN(P.loan_id),'-',MAX(P.loan_id)) AS loan_no_range,
                
                SUM(P.loan_amount)  AS pledged_amount,

                (
                    COALESCE(SUM(P.loan_amount),0.00)
                ) AS pledged_amount,


                (
                COALESCE(SUM(P.item_count),0.00)
                )
                AS pledged_items, 

                 (
                COALESCE(SUM(P.total_net_weight),0.00)
                )
                AS pledged_weight

				FROM ".CASH_FLOW_TRANSACTIONS." AS CF
				LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
				LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
				LEFT JOIN ".REDEMPTION." as R ON R.cash_flow_id = CF.id
				LEFT JOIN ".CUSTOMERS." as C ON P.customer_id = C.id OR R.customer_id = C.id
				WHERE CF.editable = 0 AND CF.module_type IN (1,2)  ".$search_condition." 
				GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'), B.id
				ORDER BY CF.date_transaction ASC, B.name";
				
				//echo $query;
                //exit;
                       
            break;           

        

            
            case "pay_interest":
                
                $query = "SELECT 
                            CF.id AS cash_flow_id,
                            GROUP_CONCAT(DISTINCT B.name SEPARATOR ', ' ) as branch_name,
                            DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS id, 
                            DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date, 

                            SUM(PI.total_paid_amount) AS total_paid,
                            SUM(PI.interest_amount) AS interest_paid,
                            SUM(PI.principle_amount) AS principle_paid,
                            SUM(PI.additional_charge) AS other_charges,
                            SUM(PI.wavier_amount) AS wavier_amount,
                            PI.wavier_days,
                            
                            CF.opening_balance AS opening_balance,
                            CF.closing_balance  AS closing_balance

                            FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                            LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
                            LEFT JOIN ".PLEDGE."  AS P ON P.cash_flow_id = CF.id 
                            LEFT JOIN ".PRODUCT_INTERESTS." as PI ON PI.cash_flow_id = CF.id
                            WHERE ( PI.id <> '' ) ".$search_condition." 
                            GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') 
                            ORDER BY CF.date_transaction ASC ";
            break;
            
            case "cashflow":

				$query = "SELECT 
                            CF.id AS cash_flow_id,
                            B.name as branch_name,
                            B.id as branch_id,
                            CONCAT(B.id,'-',DATE_FORMAT(CF.date_transaction,'%Y-%m-%d')) AS id,
                            DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date, 
                            DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date, 
                
                            CF.cash_purpose AS purpose
                            
                            FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                            LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
                            WHERE 1 ".$search_condition." 
                            GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'), B.id
                            ORDER BY CF.date_transaction ASC , B.name ";
                            break;
            
            
            case "stock_report":

                
					$query = "SELECT 
						  CF.id AS cash_flow_id,      
						CONCAT(B.id,'-',DATE_FORMAT(CF.date_transaction,'%Y-%m-%d')) AS id,
						CONCAT(C.name,'- (',C.customer_id,')') AS customer_name,
						C.customer_id,
						GROUP_CONCAT(DISTINCT B.name SEPARATOR ', ' ) AS branch_name,
						B.id AS branch_id,CF.module_type,
						
						P.loan_id AS loan_code,
						P.id AS pledged_id,
						CF.id AS cash_flow_id,
						B.id AS branch_id,
							
						DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
						DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date_new,
						DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
						DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,




						SUM(P.loan_amount)  AS loan_amount_out,



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
						
						SUM(P.loan_amount) AS total_pledged,

						
						COUNT(DISTINCT C.id) AS no_of_customer,
						COUNT(DISTINCT P.id) AS no_of_issued

								
								

						FROM ".CASH_FLOW_TRANSACTIONS." AS CF
						LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
						LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
						LEFT JOIN ".CUSTOMERS." as C ON P.customer_id = C.id 

						WHERE CF.editable = 0 ".$search_condition." AND CF.module_type IN(1,2,4)
						GROUP BY DATE_FORMAT(CF.date_transaction,'%Y-%m-%d'),CF.module_type 
						ORDER BY CF.date_transaction,CF.module_type DESC
						";
              
            break;

        }
    
        return $query;
    }
 
////starting
 public static function getSubListQuery($wise_type, $search_condition) {
    
        switch(trim(strtolower($wise_type))){

            case "items":
            
                $query = "SELECT 
                        CF.id AS cash_flow_id,
                CONCAT(B.id,'-',DATE_FORMAT(CF.date_transaction,'%Y-%m-%d')) AS id,
                CONCAT(C.name,'- (',C.customer_id,')') AS customer_name,
                C.customer_id,
                B.name AS branch_name,
                B.id AS branch_id,
                
                P.loan_id AS loan_code,
                P.id AS pledged_id,

 
                DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,
                
                CF.cash_purpose AS purpose,
                
                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date, 

                (P.loan_amount)  AS pledged_amount,


                (
                    COALESCE((P.loan_amount),0.00)
                ) AS pledged_amount,


                (
                COALESCE((P.item_count),0.00)
                )
                AS pledged_items,



                 (
                COALESCE((P.total_net_weight),0.00)
                )
                AS pledged_weight

       


                FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
                LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
                LEFT JOIN ".CUSTOMERS." as C ON P.customer_id = C.id 
                WHERE CF.editable = 0  
                ".$search_condition." 

                ORDER BY CF.date_transaction, B.name ";
                
            break;
        
            case "pay_interest": 
                
                $query = "SELECT 
                            P.loan_id AS loan_code,
                            P.id AS pledged_id,
                            CF.id AS cash_flow_id,
                            B.id AS branch_id,
                            B.name as branch_name,
                            DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date, 
                            CF.id AS cash_flow_id,
                            (PI.total_paid_amount) AS total_paid,
                            (PI.interest_amount) AS interest_paid,
                            (PI.principle_amount) AS principle_paid,
                            (PI.additional_charge) AS other_charges,
                            PI.wavier_amount,PI.wavier_days,
                            CF.cash_purpose AS purpose,
                            CF.opening_balance AS opening_balance,
                            CF.closing_balance  AS closing_balance

                            FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                            LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
                            LEFT JOIN ".PLEDGE." as P ON CF.id = P.cash_flow_id  
                            LEFT JOIN ".PRODUCT_INTERESTS." as PI ON PI.cash_flow_id = CF.id
                            WHERE CF.editable = 0 ".$search_condition."
                            ORDER BY CF.date_transaction DESC ";
            break;
        
            case "cashflow":
                
                $query = "SELECT 
                            CF.id AS cash_flow_id,
                            B.name as branch_name,
                            CF.id AS cash_flow_id,
                            DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date, 

                            B.id AS branch_id,
                    
                            COALESCE((CF.opening_balance),0.00) AS opening_balance,
                            COALESCE((CF.closing_balance),0.00) AS closing_balance,
                            
                            
                            IF(CF.transaction_type = 'cashin',CF.amount,0.00) AS cashin_amount,
                            IF(CF.transaction_type = 'cashout',CF.amount,0.00) AS cashout_amount,

                            IF(CF.transaction_type = 'cashin',CF.cash_purpose,'-') AS cashin_purpose,
                            IF(CF.transaction_type = 'cashout',CF.cash_purpose,'-') AS cashout_purpose,
                            CF.cash_purpose AS purpose


                            FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                            LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
                            WHERE 1 ".$search_condition."

                            ORDER BY CF.date_transaction DESC ";
                 break;           
            

            
            case "stock_report":
                
                $query = "SELECT 
                    CF.id AS cash_flow_id,
                    DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS id,
                    CONCAT(C.name,'- (',C.customer_id,')') AS customer_name,
                    C.customer_id,
                    B.name AS branch_name,

                    CF.opening_balance AS  opening_balance,
                    CF.closing_balance AS  closing_balance,

                    P.loan_id AS loan_code,
                    P.id AS pledged_id,
                    CF.id AS cash_flow_id,
                    B.id AS branch_id,

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
                    ) AS weight_in
            

            
                    FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                    LEFT JOIN ".BRANCH." as B ON CF.branch_id = B.id
                    LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
                    LEFT JOIN ".CUSTOMERS." as C ON P.customer_id = C.id 
                    WHERE CF.editable = 0  ".$search_condition." 
                        ORDER BY CF.date_transaction ";
            break;
          

        }
   
        return $query;
    }
 
    public static function getResultData($wise_type, $reports) {
        
        global $app;
        //echo $wise_type;

		//edited by vinoth (Note : Opening balance calculation)
		$start_date = $reports[0]['report_date'];
		

        $results = array();
        switch(trim(strtolower($wise_type))){
        
            case "items":

				$st_ob_query = "SELECT SUM(loan_amount) as lamount FROM ".PLEDGE." WHERE loan_date < '".$start_date."'";
				$st_res = $app->getrow($st_ob_query);
				$st_ob = $st_res[0] == "" ? 0 : $st_res[0];
           
               if(count($reports)>0){

                  $dpi = 0;
                  $di = 1;

                  foreach($reports as $rows)
                  {
                    
                    $branch_id = $rows['branch_id'];
                    $report_date = $rows['report_date'];

					$redeem_ids = "";
					//if($rows['loan_no_range'] == '') {

						$loan_query = "SELECT CONCAT(MIN(P.loan_id),'-',MAX(P.loan_id)) AS released_loans FROM ".CASH_FLOW_TRANSACTIONS." as CF LEFT JOIN ". PLEDGE." AS P ON P.cash_flow_id=CF.parent_id LEFT JOIN ".REDEMPTION." AS R ON R.loan_id=P.id WHERE  CF.transaction_type = 'cashin' AND CF.editable = 0 AND DATE_FORMAT(R.paid_on,'%Y-%m-%d') = '".$report_date."' AND CF.branch_id = ".$branch_id;
						$add_data = $app->getrow($loan_query);
						$rows['released_loans'] = $add_data['released_loans'];

				            
						$ao_qry = "SELECT COALESCE(SUM(principle_amount)), GROUP_CONCAT(loan_id SEPARATOR ',') AS redeem_id, COUNT(loan_id) AS rcount  FROM ".REDEMPTION." WHERE DATE_FORMAT(paid_on,'%Y-%m-%d') =  '".$report_date."' AND branch_id = ".$branch_id." GROUP BY DATE_FORMAT(paid_on,'%Y-%m-%d'), branch_id"; 
						// loan_id = ".$loan_id;
						$ao_res = $app->getrow($ao_qry);
						//print_r($ao_res);
						$rows['amount_in'] = (int)$ao_res[0];
						$redeem_ids = $ao_res[1];
						$no_of_return = (int)$ao_res[2];

                    //}
                    
                    if($ao_res[1] != ''){
                         $ao_qry = "SELECT COALESCE(SUM(item_count)), COALESCE(SUM(total_net_weight)) AS wi_out, CONCAT(MIN(loan_id),'-',MAX(loan_id)) FROM ".PLEDGE." WHERE id IN (".$redeem_ids.")";
                        $ao_res = $app->getrow($ao_qry);
						
						//print_r($ao_res);

                        $rows['released_item'] = $ao_res[0];
                        $rows['released_weight'] = $ao_res[1];
                        $rows['released_count'] = $no_of_return;
                        //$rows['released_loans'] = $no_of_return.' Nos.'; //$ao_res[2];
                    }else{
                        $rows['released_item'] = 0;
                        $rows['released_weight'] = 0;
                        $rows['released_count'] = 0;
                        //$rows['released_loans'] = ' None';
                    }

				
                    $ob_query = "SELECT sCF.opening_balance 
                        FROM mf_cash_flow_transactions AS sCF 
                        WHERE sCF.branch_id = ".$rows['branch_id']." 
                        AND sCF.id = ".$rows['cash_flow_id']." AND DATE_FORMAT(sCF.date_transaction,'%Y-%m-%d') = DATE_FORMAT('".$rows['report_date']."','%Y-%m-%d')
                        ORDER BY sCF.date_transaction ASC, sCF.id ASC
                        LIMIT 0,1";

					$cash_flow_id = $rows['cash_flow_id'] + 1;
                    $cb_query = "SELECT sCF.closing_balance 
                        FROM mf_cash_flow_transactions AS sCF 
                        WHERE sCF.branch_id = ".$rows['branch_id']."
                        AND sCF.id = ".$cash_flow_id." AND  DATE_FORMAT(sCF.date_transaction,'%Y-%m-%d') = DATE_FORMAT('".$rows['report_date']."','%Y-%m-%d')
                        ORDER BY sCF.date_transaction DESC, sCF.id DESC
                        LIMIT 0,1"; 

                    $ob_row = $app->getrow($ob_query);
                    $cb_row = $app->getrow($cb_query);
					
                    if($closing_balance == $ob_row[0] || $closing_balance == "")
						$rows['opening_balance'] = $ob_row[0];
					else 
						$rows['opening_balance'] = $closing_balance;

                    $rows['closing_balance'] = $cb_row[0];
					$closing_balance = $cb_row[0];
                    
                    
                    $weight_in = floatval($rows['pledged_weight']);
                    $weight_out = floatval($rows['released_weight']);
                    
                    
                    $weight_in_gs =  intval($weight_in);
                    $weight_in_mgs =  round(floatval($weight_in - intval($weight_in)) * 1000);
                    
                    $weight_out_gs =  intval($weight_out);
                    $weight_out_mgs =  round(floatval($weight_out - intval($weight_out)) * 1000);
                    
                    
                    /*$item_in = intval($rows['pledged_items']);
                    $item_out = intval($rows['released_items']);*/

        
                    $pledged_count = intval($rows['pledged_count']);
                    $released_count = intval($rows['released_count']);
                    
                    
                    $rows['weight_in_gs'] = $weight_in_gs;
                    $rows['weight_in_mgs'] = $weight_in_mgs;
                    $rows['weight_out_gs'] = $weight_out_gs;
                    $rows['weight_out_mgs'] = $weight_out_mgs;
                    $rows['pledged_count'] = $pledged_count;
                    $rows['released_count'] = $released_count;					
					$rows['opening_balance'] = ($old_closing_opening)? $old_closing_opening : $st_ob;
					$final_amount = $rows['opening_balance']+$rows['pledged_amount']-$rows['amount_in'];
					$rows['closing_balance'] = $final_amount;

					$old_closing_opening = $rows['closing_balance'];

                    $results[] = $rows;
                }
                
            }else{
            
                $results = $reports;
            }    
            break;
            
            case "pay_interest":
				
				$start_date = date("Y-m-d", strtotime($start_date));
				
				$st_ob_query = "SELECT SUM(total_paid_amount) FROM ".PRODUCT_INTERESTS." WHERE paid_on < '".$start_date."'";
				$st_res = $app->getrow($st_ob_query);
				$st_ob = $st_res[0] == "" ? 0 : $st_res[0];
            
               if(count($reports)>0){
                  foreach($reports as $rows)
                  {
                    
                    $rows['principle_paid'] = convertToCurrency($rows['principle_paid']);
                    $rows['interest_paid'] = convertToCurrency(stripslashes($rows['interest_paid']),false);
                    $rows['wavier_amount'] = convertToCurrency(stripslashes($rows['wavier_amount']),false);
                    $rows['other_charges'] = convertToCurrency($rows['other_charges']);
                    $rows['total_paid'] = $rows['total_paid'];
					$rows['st_ob'] = $st_ob;
                    
                    $results[] = $rows; 
                  }
                }else{
                    $results = $reports;
                }

            break;
            
            case "cashflow":
            
				$st_ob_cash_in_query = "SELECT SUM(amount) FROM ".CASH_FLOW_TRANSACTIONS." WHERE date_transaction < '".$start_date."' AND transaction_type = 'cashin'";
				$st_ob_cash_out_query = "SELECT SUM(amount) FROM ".CASH_FLOW_TRANSACTIONS." WHERE date_transaction < '".$start_date."' AND transaction_type = 'cashout'";
				$st_cash_in_res = $app->getrow($st_ob_cash_in_query);
				$st_cash_out_res = $app->getrow($st_ob_cash_out_query);
				$st_res = $st_cash_in_res[0] - $st_cash_out_res[0];
				$st_ob = $st_res == "" ? 0 : $st_res;

               if(count($reports)>0){
                  foreach($reports as $key => $rows)
                  {
           
                    $ci_query = "SELECT SUM(sCF.amount) 
                            FROM ".CASH_FLOW_TRANSACTIONS." AS sCF 
                            WHERE 
                                sCF.branch_id = ".$rows['branch_id']."
                                AND DATE_FORMAT(sCF.date_transaction,'%Y-%m-%d') = DATE_FORMAT('".$rows['report_date']."','%Y-%m-%d')
                                AND sCF.transaction_type = 'cashin'
                            GROUP BY DATE_FORMAT(sCF.date_transaction,'%Y-%m-%d')
                            LIMIT 0,1";
                    $co_query = "SELECT SUM(sCF.amount) 
                            FROM ".CASH_FLOW_TRANSACTIONS." AS sCF 
                            WHERE 
                                sCF.branch_id = ".$rows['branch_id']."
                                AND DATE_FORMAT(sCF.date_transaction,'%Y-%m-%d') = DATE_FORMAT('".$rows['report_date']."','%Y-%m-%d')
                                AND sCF.transaction_type = 'cashout'
                            GROUP BY DATE_FORMAT(sCF.date_transaction,'%Y-%m-%d')
                            LIMIT 0,1";
                    $ob_query = "SELECT sCF.opening_balance 
                        FROM mf_cash_flow_transactions AS sCF 
                        WHERE sCF.branch_id = ".$rows['branch_id']." 
                        AND DATE_FORMAT(sCF.date_transaction,'%Y-%m-%d') = DATE_FORMAT('".$rows['report_date']."','%Y-%m-%d')
                        ORDER BY sCF.date_transaction ASC, sCF.id ASC
                        LIMIT 0,1";
                    $cb_query = "SELECT sCF.closing_balance 
                        FROM mf_cash_flow_transactions AS sCF 
                        WHERE sCF.branch_id = ".$rows['branch_id']."
                        AND  DATE_FORMAT(sCF.date_transaction,'%Y-%m-%d') = DATE_FORMAT('".$rows['report_date']."','%Y-%m-%d')
                        ORDER BY sCF.date_transaction DESC, sCF.id DESC
                        LIMIT 0,1";
                    $ob_row = $app->getrow($ob_query);
                    $cb_row = $app->getrow($cb_query);
                    $ci_row = $app->getrow($ci_query);
                    $co_row = $app->getrow($co_query);
                    

					if($last_trans_cls_bal == $rows['opening_balance']) {
				
						if($key == 0) { 
							$rows['opening_balance'] = $st_ob;
						}else{
							$rows['opening_balance'] = $ob_row[0];
						}
		                $rows['closing_balance'] = $rows['opening_balance'] + $ci_row[0] - $co_row[0];

					}else {
						$rows['opening_balance'] = $last_trans_cls_bal;
						$cash_in_tot = $rows['opening_balance'] + $ci_row[0];
						$rows['closing_balance'] = $cash_in_tot - $co_row[0];
					}
					
                    $rows['cash_in'] = $ci_row[0];
                    $rows['cash_out'] = $co_row[0];
					
					$last_trans_cls_bal = $rows['closing_balance'];
                    $results[] = $rows; 
                  }
                }else{
                    $results = $reports;
                }
            break;
            
            case "stock_report":
               

				$st_ob_query = "SELECT SUM(loan_amount) as lamount,COALESCE(SUM(total_net_weight),0.00),COUNT(DISTINCT id) AS opening_item FROM ".PLEDGE." WHERE loan_date < '".$start_date."'";
				$st_res = $app->getrow($st_ob_query);
				$st_ob = $st_res[0] == "" ? 0 : $st_res[0];
				$st_ow = $st_res[1] == "" ? 0 : $st_res[1];
				$st_oc = $st_res[2] == "" ? 0 : $st_res[2];

               if(count($reports)>0){

                  foreach($reports as $key => $rows)
                  {
                    /** Set Result data for the record **/

                    $branch_id = $rows['branch_id'];
                    $report_date = $rows['report_date'];

                   if($rows['module_type'] == 2) {
						$ao_qry = "SELECT COALESCE(SUM(principle_amount)), GROUP_CONCAT(loan_id SEPARATOR ',') AS redeem_id, COUNT(loan_id) AS rcount  FROM ".REDEMPTION." WHERE DATE_FORMAT(paid_on,'%Y-%m-%d') =  '".$report_date."' AND branch_id = ".$branch_id." GROUP BY DATE_FORMAT(paid_on,'%Y-%m-%d'), branch_id"; // loan_id = ".$loan_id;
						$ao_res = $app->getrow($ao_qry);
						$rows['amount_in'] = (int)$ao_res[0];
						$redeem_id = $ao_res[1];
						$no_of_return = (int)$ao_res[2];
				   }
				   if($rows['module_type'] == 4) {
                    
                    $ao_qry = "SELECT COALESCE(SUM(auction_amount)), GROUP_CONCAT(loan_id SEPARATOR ',') AS auction_id, COUNT(loan_id) AS acount  FROM ".PRODUCT_AUCTION." WHERE DATE_FORMAT(paid_on,'%Y-%m-%d') =  '".$report_date."' AND branch_id = ".$branch_id." GROUP BY DATE_FORMAT(paid_on,'%Y-%m-%d'), branch_id"; // loan_id = ".$loan_id;
                    $ao_res = $app->getrow($ao_qry);
                    $rows['amount_in'] += (int)$ao_res[0];
                    $auction_id = $ao_res[1];
                    $no_of_return += (int)$ao_res[2];
				   }
        
                    $io_loanids = array();
                    $io_loanids = array_merge(explode(',',$redeem_id),explode(',',$auction_id));
                    $io_loanids = array_filter($io_loanids);
					$count_loanids = count($io_loanids);

					if($rows['module_type'] != 1)
						$rows['no_of_return'] = $no_of_return;
					else
						$rows['no_of_return'] = 0;
					
					//$ao_qry = "SELECT COALESCE(SUM(item_count)), COALESCE(SUM(total_net_weight)) AS wi_out FROM ".PLEDGE." WHERE id IN (".implode(',',$io_loanids).")";
					if($count_loanids > 0) {

						if($rows['module_type'] == 1) {

							$ao_qry = "SELECT COALESCE(SUM(item_count)), COALESCE(SUM(total_net_weight)) AS wi_out FROM ".PLEDGE." AS P LEFT JOIN ".CASH_FLOW_TRANSACTIONS." AS CF ON CF.id=P.cash_flow_id WHERE P.status = 1 AND P.id IN (".implode(',',$io_loanids).")";
						} else {
							$ao_qry = "SELECT COALESCE(SUM(item_count)), COALESCE(SUM(total_net_weight)) AS wi_out FROM ".PLEDGE." AS P LEFT JOIN ".CASH_FLOW_TRANSACTIONS." AS CF ON CF.id=P.cash_flow_id WHERE P.id IN (".implode(',',$io_loanids).")";
						}
						

						$ao_res = $app->getrow($ao_qry);
						$rows['item_out'] = $ao_res[0];
						$rows['weight_out'] = $ao_res[1];
					}
                    /*$rows['no_of_return'] = $no_of_return;
                    
                    $ao_qry = "SELECT COALESCE(SUM(item_count)), COALESCE(SUM(total_net_weight)) AS wi_out FROM ".PLEDGE." WHERE id IN (".implode(',',$io_loanids).")";
                    $ao_res = $app->getrow($ao_qry);
                    $rows['item_out'] = $ao_res[0];
                    $rows['weight_out'] = $ao_res[1];*/
                    
                    $item_in = floatval($rows['no_of_issued']);
                    $item_out = floatval($rows['no_of_return']);
                    
        
                    /*$ow_query = "SELECT COALESCE(SUM(sP.total_net_weight),0.00), COUNT(DISTINCT sP.id) AS opening_item , COALESCE(SUM(sP.loan_amount),0.00) as opening_amount
                        FROM ".PLEDGE." AS sP 
                        WHERE sP.branch_id = ".$rows['branch_id']." AND DATE_FORMAT(sP.loan_date ,'%Y-%m-%d') < DATE_FORMAT('".$rows['report_date']."','%Y-%m-%d')
                        AND sP.id NOT IN (SELECT loan_id FROM ".REDEMPTION." AS sR WHERE sR.paid_on < DATE_FORMAT('".$rows['report_date']."','%Y-%m-%d'))
                        AND sP.id NOT IN (SELECT loan_id FROM ".PRODUCT_AUCTION." AS sA WHERE sA.paid_on < DATE_FORMAT('".$rows['report_date']."','%Y-%m-%d'))";
                        
                    $cw_query = "SELECT COALESCE(SUM(sP.total_net_weight),0.00), COUNT(DISTINCT sP.id) AS opening_item , COALESCE(SUM(sP.loan_amount),0.00) as closing_amount
                        FROM ".PLEDGE." AS sP 
                        WHERE sP.branch_id = ".$rows['branch_id']." AND DATE_FORMAT(sP.loan_date ,'%Y-%m-%d') < DATE_FORMAT((DATE_ADD('".$rows['report_date']."', INTERVAL 1 DAY)) ,'%Y-%m-%d')
                        AND sP.id NOT IN (SELECT loan_id FROM ".REDEMPTION." AS sR WHERE sR.paid_on < DATE_FORMAT((DATE_ADD('".$rows['report_date']."', INTERVAL 1 DAY)) ,'%Y-%m-%d'))
                        AND sP.id NOT IN (SELECT loan_id FROM ".PRODUCT_AUCTION." AS sA WHERE sA.paid_on < DATE_FORMAT((DATE_ADD('".$rows['report_date']."', INTERVAL 1 DAY)) ,'%Y-%m-%d'))";
                    */
                    
                    $ow_query = "SELECT COALESCE(SUM(sP.total_net_weight),0.00), COUNT(DISTINCT sP.id) AS opening_item , COALESCE(SUM(sP.loan_amount),0.00) as opening_amount
                        FROM ".PLEDGE." AS sP 
                        WHERE sP.branch_id = ".$rows['branch_id']." AND DATE_FORMAT(sP.loan_date ,'%Y-%m-%d') < DATE_FORMAT('".$rows['report_date']."','%Y-%m-%d') AND sP.status = 1";
                        
                    $cw_query = "SELECT COALESCE(SUM(sP.total_net_weight),0.00), COUNT(DISTINCT sP.id) AS opening_item , COALESCE(SUM(sP.loan_amount),0.00) as closing_amount
                        FROM ".PLEDGE." AS sP 
                        WHERE sP.branch_id = ".$rows['branch_id']." AND DATE_FORMAT(sP.loan_date ,'%Y-%m-%d') < DATE_FORMAT((DATE_ADD('".$rows['report_date']."', INTERVAL 1 DAY)) ,'%Y-%m-%d') AND sP.status = 1";

                    
                    $ow_row = $app->getrow($ow_query);
                    $cw_row = $app->getrow($cw_query);
            
                    $rows['opening_weight'] = $ow_row[0];
                    $rows['closing_weight'] = $cw_row[0]; 
                    $rows['opening_item'] = $ow_row[1];
                    $rows['closing_item'] = $cw_row[1];
                    $rows['opening_balance'] = $ow_row[2];
                    $rows['closing_balance'] = $cw_row[2];
                  
                  
					if($rows['module_type'] == 1) {
						$rows['amount_in'] = 0;
					}
                    $amount_in = floatval($rows['amount_in']);
                    $amount_out = floatval($rows['amount_out']);
                    
                    $weight_in = floatval($rows['weight_in']);
                    $weight_out = floatval($rows['weight_out']);
                    
                    $weight_in_gs =  intval($weight_in);
                    $weight_in_mgs =  intval(floatval($weight_in - intval($weight_in)) * 1000);
                    $weight_out_gs =  intval($weight_out);
                    $weight_out_mgs =  intval(floatval($weight_out - intval($weight_out)) * 1000);
                    
                    $opening_weight = floatval($rows['opening_weight']);
					$opening_weight = ($closing_to_opening_weight) ? $closing_to_opening_weight : floatval($st_ow);

                    //$closing_weight = floatval($rows['closing_weight']);
					if($weight_in > 0) 
						$closing_weight = $opening_weight + $weight_in;
					
					if($weight_out > 0) 
						$closing_weight = $opening_weight - $weight_out;
        
                    $opening_weight_gs =  intval($opening_weight);
                    $opening_weight_mgs =  intval(floatval($opening_weight - intval($opening_weight)) * 1000);
                    $closing_weight_gs =  intval($closing_weight);
                    $closing_weight_mgs =  intval(floatval($closing_weight - intval($closing_weight)) * 1000);
                    
                    $pitem_in = floatval($rows['item_in']);
                    $pitem_out = floatval($rows['item_out']);
                    
                    //$opening_amount = floatval($rows['opening_balance']);
					$opening_amount = $closing_to_opening_amount? $closing_to_opening_amount : $st_ob;
                    //$closing_amount = floatval($rows['closing_balance']);
					if($amount_out > 0) 
						$closing_amount += $amount_out;

					if($amount_in > 0) 
						$closing_amount = $closing_amount - $amount_in;
					
					if($key == 0 ) {
						$closing_amount = $opening_amount + $amount_out - $amount_in;
					}
                  
                    $opening_item = ($closing_item) ? $closing_item : $st_oc;
                    $closing_item = $opening_item + floatval($rows['no_of_issued'])- floatval($rows['no_of_return']);
                    
                    $item_in = floatval($rows['no_of_issued']);
                    $item_out = floatval($rows['no_of_return']);                 
                  
                  
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
                   
                    $results[] = $rows;

					//echo '<pre>';
					//print_r($rows);
                    
                  }
                  
                }else{
                  $results = $reports;
                }

            //print_r($results);
            break;
        }
        return $results;    
    }


    public static function getSubResultData($wise_type, $reports) {
        
        global $app;
        //print_r($reports);
        $results = array();
        switch(trim(strtolower($wise_type))){
            case "items":
               if(count($reports)>0){
                  foreach($reports as $rows)
                  {
                    
                    $branch_id = $rows['branch_id'];
                    $report_date = $rows['report_date'];
                    $pledged_id = (int)$rows['pledged_id'];
                    $cash_flow_id = (int)$rows['cash_flow_id'];
        
                    if($pledged_id == 0){
                        $ao_qry = "SELECT principle_amount, loan_id  FROM ".REDEMPTION." WHERE cash_flow_id = {$cash_flow_id}"; // OR ( DATE_FORMAT(paid_on,'%Y-%m-%d') =  '".$report_date."')) AND branch_id = ".$branch_id." GROUP BY DATE_FORMAT(paid_on,'%Y-%m-%d'), branch_id"; // loan_id = ".$loan_id;
                        $ao_res = $app->getrow($ao_qry);
                        $rows['amount_in'] = (int)$ao_res[0];
                        $redeem_id = (int)$ao_res[1];
                        
                        $ao_qry = "SELECT loan_id, item_count, total_net_weight AS wi_out  FROM ".PLEDGE." WHERE id = ".$redeem_id."";
                        $ao_res = $app->getrow($ao_qry);
                        $rows['loan_code'] = $ao_res[0];
                        $rows['released_item'] = $ao_res[1];
                        $rows['released_weight'] = $ao_res[2];
                    }
                    
                    $weight_in = floatval($rows['pledged_weight']);
                    $weight_out = floatval($rows['released_weight']);
                    
                    
                    $weight_in_gs =  intval($weight_in);
                    $weight_in_mgs =  intval(floatval($weight_in - intval($weight_in)) * 1000);
                    
                    $weight_out_gs =  intval($weight_out);
                    $weight_out_mgs =  intval(floatval($weight_out - intval($weight_out)) * 1000);
                    
                    
                    $item_in = intval($rows['pledged_items']);
                    $item_out = intval($rows['released_items']);
                    
                    $rows['weight_in'] = $weight_in;
                    $rows['weight_out'] = $weight_out;
                    $rows['weight_in_gs'] = $weight_in_gs;
                    $rows['weight_in_mgs'] = $weight_in_mgs;
                    $rows['weight_out_gs'] = $weight_out_gs;
                    $rows['weight_out_mgs'] = $weight_out_mgs;
                    $rows['item_in'] = $item_in;
                    $rows['item_out'] = $item_out;
                    
                    $results[] = $rows;
                  }
               }else{     
                    $results = $reports;
               }
            break;
            case "pay_interest":
               if(count($reports)>0){
                  foreach($reports as $rows)
                  {
                    
                    $rows['principle_paid'] = convertToCurrency($rows['principle_paid']);
                    $rows['interest_paid'] = convertToCurrency(stripslashes($rows['interest_paid']),false);
                    $rows['wavier_amount'] = convertToCurrency(stripslashes($rows['wavier_amount']),false);
                    $rows['other_charges'] = convertToCurrency($rows['other_charges']);
                    $rows['total_paid'] = convertToCurrency(stripslashes($rows['total_paid']),false);
                    
                    $results[] = $rows; 
                  }
                }else{
                    $results = $reports;
                }
            break;
            case "cashflow":
            
               if(count($reports)>0){
                  foreach($reports as $rows)
                  {
            
                    $opening_amount = floatval($rows['opening_balance']);
                    $closing_amount = floatval($rows['closing_balance']);
                    
                    $cashin_amount = floatval($rows['cashin_amount']);
                    $cashout_amount = floatval($rows['cashout_amount']);
                    
                    $cashin_purpose = $rows['cashin_purpose'];
                    $cashout_purpose = $rows['cashout_purpose'];
                    $purpose = $rows['purpose'];
                    
                    
                    $cash_purpose = '';
                    $loan_id = 0;
                    $pquery = "SELECT id FROM mf_pledge WHERE cash_flow_id = ".$rows['cash_flow_id'];
                    $pcid = $app->getrow($pquery);
                    if((int)$pcid[0] > 0){
                      $cash_purpose = CASHFLOW_PURPOSE_PLEDGED;
                      $loan_id = (int)$pcid[0];  
                    }else{
                    
                        $rquery = "SELECT loan_id FROM mf_redemption WHERE cash_flow_id =  ".$rows['cash_flow_id'];
                        $rcid = $app->getrow($rquery);
                        if((int)$rcid[0] > 0){
                          $cash_purpose = CASHFLOW_PURPOSE_REDEEMED;
                          $loan_id = (int)$rcid[0];  
                        }else{
                        
                            $apquery = "SELECT loan_id FROM mf_product_auction WHERE cash_in_id =  ".$rows['cash_flow_id'];
                            $apcid = $app->getrow($apquery);
                            if((int)$apcid[0] > 0){
                              $cash_purpose = AUCTION_AMOUNT;
                              $loan_id = (int)$apcid[0];  
                            }else{
                            
                                $alquery = "SELECT loan_id FROM mf_product_auction WHERE cash_out_id = ".$rows['cash_flow_id'];
                                $alcid = $app->getrow($alquery);
                                if((int)$alcid[0] > 0){
                                  $cash_purpose = AUCTION_AMOUNT_LOSS;
                                  $loan_id = (int)$alcid[0];  
                                }else{
                                    
                                    //
                                    $piquery = "SELECT loan_id FROM mf_product_interests WHERE cash_flow_id = ".$rows['cash_flow_id'];
                                    $picid = $app->getrow($piquery);
                                    if((int)$picid[0] > 0){
                                      $cash_purpose = PAY_INTEREST;
                                      $loan_id = (int)$picid[0];  
                                    }
                                }
                            
                            }
                        }
                    }
                    if((int)$loan_id > 0){
                        $pcquery = "SELECT loan_id FROM mf_pledge WHERE id = ".(int)$loan_id;
                        $pcode = $app->getrow($pcquery);
                        $loan_code = $pcode[0];
                    }
                    if($cash_purpose != '' && (int)$loan_id > 0 && (int)$cashin_amount > 0)
                        $cashin_purpose = $cash_purpose." - (Loan ID : ".$loan_code.")";
                        
                    if($cash_purpose != '' && (int)$loan_id > 0 && (int)$cashout_amount > 0)    
                        $cashout_purpose = $cash_purpose." - (Loan ID : ".$loan_code.")";


                    $rows['cashin_purpose'] = $cashin_purpose;
                    $rows['cashout_purpose'] = $cashout_purpose;
                    $rows['loan_code'] = $loan_code;
                    $rows['opening_amount'] = $opening_amount;
                    $rows['closing_amount'] = $closing_amount;
            
                    $results[] = $rows; 
                  }
                }else{
                    $results = $reports;
                }


            break;
            case "stock_report":

               if(count($reports)>0){

                  foreach($reports as $rows)
                  {
                    /** Set Result data for the record **/

                    $branch_id = $rows['branch_id'];
                    $cash_flow_id = $rows['cash_flow_id'];
                    $report_date = $rows['report_date'];
                    $loan_id = $rows['pledge_id'];
                    $cash_purpose = $rows['process_type'];
                    
                    if($loan_id <= 0){
                    
                        $rquery = "SELECT loan_id FROM ".REDEMPTION." WHERE cash_flow_id =  ".$cash_flow_id;
                        $rcid = $app->getrow($rquery);
                        if((int)$rcid[0] > 0){
                          $cash_purpose = CASHFLOW_PURPOSE_REDEEMED;
                          $loan_id = (int)$rcid[0];  
                        }else{
                        
                            $apquery = "SELECT loan_id FROM ".PRODUCT_AUCTION." WHERE cash_in_id =  ".$cash_flow_id;
                            $apcid = $app->getrow($apquery);
                            if((int)$apcid[0] > 0){
                              $cash_purpose = AUCTION_AMOUNT;
                              $loan_id = (int)$apcid[0];  
                            }else{
                            
                                $alquery = "SELECT loan_id FROM ".PRODUCT_AUCTION." WHERE cash_out_id = ".$cash_flow_id;
                                $alcid = $app->getrow($alquery);
                                if((int)$alcid[0] > 0){
                                  $cash_purpose = AUCTION_AMOUNT_LOSS;
                                  $loan_id = (int)$alcid[0];  
                                }else{
                                    
                                    //
                                    /*$piquery = "SELECT loan_id FROM ".PRODUCT_INTERESTS." WHERE cash_flow_id = ".$cash_flow_id;
                                    $picid = $app->getrow($piquery);
                                    if((int)$picid[0] > 0){
                                      $cash_purpose = PAY_INTEREST;
                                      $loan_id = (int)$picid[0];  
                                    }*/
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
                    
                   //if($loan_id > 0)
                   {
                  
                    /*$amount_out = floatval($rows['amount_in']);
                    $amount_in = floatval($rows['amount_out']);
                    
                    $weight_in = floatval($rows['weight_in']);
                    $weight_out = floatval($rows['weight_out']);
                    
                    $weight_in_gs =  intval($weight_in);
                    $weight_in_mgs =  intval(floatval($weight_in - intval($weight_in)) * 1000);
                    $weight_out_gs =  intval($weight_out);
                    $weight_out_mgs =  intval(floatval($weight_out - intval($weight_out)) * 1000);
                    
                    
                    $item_in = floatval($rows['item_in']);
                    $item_out = floatval($rows['item_out']);
                    
                    $opening_amount = floatval($rows['opening_balance']);
                    $closing_amount = floatval($rows['closing_balance']);*/
                    
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

                    $results[] = $rows;
                    }
                  }
                }else{
                  $results = $reports;
                }

            

            break;
        }
        return $results;    
    }
    

    public static function getExportData($wise_type, $data) {
		global $app;
        switch(trim(strtolower($wise_type))){

            case "items":
            
                if(count($data) > 0){
                foreach($data as $row){
					
					if($row['loan_no_range'] == '') {

						$loan_query = "SELECT CONCAT(MIN(P.loan_id),'-',MAX(P.loan_id)) AS loan_no_range FROM ".CASH_FLOW_TRANSACTIONS." as CF LEFT JOIN ". PLEDGE." AS P ON P.cash_flow_id=CF.parent_id LEFT JOIN ".REDEMPTION." AS R ON R.loan_id=P.id WHERE  CF.transaction_type = 'cashin' AND CF.editable = 0 AND R.cash_flow_id=".$row['cash_flow_id'];
						$add_data = $app->getrow($loan_query);
						$row['loan_no_range'] = $add_data['loan_no_range'];

					}

					$row['loan_date'] = ($row['loan_date'] == '') ? $row['trans_date'] : $row['loan_date'];
                    
                    $row['pledged_weight'] = $row['weight_in_gs']." G & ".$row['weight_in_mgs']." Mg";
                    $row['released_weight'] = $row['weight_out_gs']." G & ".$row['weight_out_mgs']." Mg";

					$row['stock_opening'] = $row['opening_balance'];
					$row['stock_closing'] = $row['closing_balance'];
					
					$exp_row['date']            = $row['trans_date'];
					//$exp_row['customer_name']   = $row['customer_name'];
					$exp_row['branch_name']     = $row['branch_name'];
					$exp_row['pledged_loan_no_range']   = $row['loan_no_range'];
					$exp_row['release_loan_no_range']   = $row['released_loans'];
					$exp_row['pledged_weight']  = $row['pledged_weight'];
					$exp_row['released_weight'] = $row['released_weight'];
					$exp_row['pledged_count']   = $row['pledged_count'];
					$exp_row['released_count']  = $row['released_count'];
					$exp_row['pledged_amount']  = $row['pledged_amount'];
					$exp_row['amount_in']       = $row['amount_in'];
					$exp_row['stock_opening_balance'] = $row['opening_balance'];
					$exp_row['stock_closing_balance'] = $row['closing_balance'];         


                    $export_data[] = $exp_row;
					}
				
				}
            
                $width = array(25,50,40,40,30,30,30,30,30,25,32,30);  
                $page_width = 408;
            
            break;



            case "pay_interest":
            
            
                if(count($data) > 0){
                foreach($data as $row){

                    /*$row['opening_balance'] = convertToCurrency($row['opening_balance']);
                    $row['closing_balance'] = convertToCurrency($row['closing_balance']);
                    $row['principle_paid'] = convertToCurrency($row['principle_paid']);
                    $row['other_charges'] = convertToCurrency($row['other_charges']);
                    $row['interest_paid'] = convertToCurrency($row['interest_paid']);
                    $row['total_paid'] = convertToCurrency($row['total_paid']);*/
					$row['opening_balance'] = $closing_to_opening_balance ? $closing_to_opening_balance : $row['st_ob'];
					$opening_balance        = convertToCurrency($row['opening_balance']);
                    $row['cash_opening']    = $opening_balance;
					$closing_balance        = $row['total_paid']+$row['opening_balance'];
					$row['total_paid']		= convertToCurrency(stripslashes($row['total_paid']),false);					
                    $row['cash_closing']    = convertToCurrency($closing_balance);
			
					$closing_to_opening_balance = $closing_balance;

					unset($row['opening_balance']);
					unset($row['closing_balance']);
					unset($row['st_ob']);
                    unset($row['id']);
                    unset($row['cash_flow_id']);
                    $export_data[] = $row;
					} 
				}
            
            
                $width = array(60, 30, 50,50,50,40,60,25,40,40,40,40); 
                $page_width = 500;
                
            break;
            
            

            case "cashflow":
            
                if(count($data) > 0){
                foreach($data as $row){

                    /*$row['opening_balance'] = convertToCurrency($row['opening_balance']);
                    $row['closing_balance'] = convertToCurrency($row['closing_balance']);
                    $row['report_date'] = $row['trans_date'];*/ 
					$row['report_date'] = date("d-M-Y",strtotime($row['report_date']));
					$row['cash_opening'] = convertToCurrency($row['opening_balance']);
                    $row['cash_closing'] = convertToCurrency($row['closing_balance']);
					$row['cash_in'] = ($row['cash_in']) ? $row['cash_in'] : 0;
					$row['cash_out'] = ($row['cash_out']) ? $row['cash_out'] : 0;
					
					unset($row['opening_balance']);
					unset($row['closing_balance']);
                    unset($row['id']);
                    unset($row['branch_id']);
                    unset($row['customer_id']);
                    unset($row['trans_date']);
                    unset($row['loan_date']);
                    unset($row['cash_flow_id']);
                    unset($row['purpose']);

                    $export_data[] = $row;
					}
				
				}

                $width = array(40,40,40,40, 60,60 ); 
                $page_width = 400;
                
            break;
            
            case "stock_report":
            
                if(count($data) > 0){
                foreach($data as $row){


                    /*$row['opening_balance'] = convertToCurrency($row['opening_balance']);
                    $row['closing_balance'] = convertToCurrency($row['closing_balance']);

                    $weight_in = floatval($row['weight_in']);
                    $weight_out = floatval($row['weight_out']);

                    $weight_in_gs =  intval($weight_in);
                    $weight_in_mgs =  intval(floatval($weight_in - intval($weight_in)) * 1000);
                    $weight_out_gs =  intval($weight_out);
                    $weight_out_mgs =  intval(floatval($weight_out - intval($weight_out)) * 1000);

                    $row['weight_in'] = $weight_in_gs." G & ".$weight_in_mgs." Mg";
                    $row['weight_out'] = $weight_out_gs." G & ".$weight_out_mgs." Mg";

                    $opening_weight = floatval($row['opening_weight']);
                    $closing_weight = floatval($row['closing_weight']);

                    $opening_weight_gs =  intval($opening_weight);
                    $opening_weight_mgs =  intval(floatval($opening_weight - intval($opening_weight)) * 1000);
                    $closing_weight_gs =  intval($closing_weight);
                    $closing_weight_mgs =  intval(floatval($closing_weight - intval($closing_weight)) * 1000);

                    $row['opening_weight'] = $opening_weight_gs." G & ".$opening_weight_mgs." Mg";
                    $row['closing_weight'] = $closing_weight_gs." G & ".$closing_weight_mgs." Mg";

                    $row['amount_in'] = convertToCurrency($row['amount_in']);
                    $row['amount_out'] = convertToCurrency($row['amount_out']);

                    
                    $row['report_date'] = $row['trans_date']; */


					$rows['date'] = date("d-M-Y",strtotime($row['report_date']));
					$rows['branch_name'] = $row['branch_name'];
					$rows['weight_in'] = $row['weight_in_gs']." G & ".$row['weight_in_mgs']." Mg";
					$rows['weight_out'] = $row['weight_out_gs']." G & ".$row['weight_out_mgs']." Mg";  
					$rows['opening_weight'] = $row['opening_weight_gs']." G & ".$row['opening_weight_mgs']." Mg";
                    $rows['closing_weight'] = $row['closing_weight_gs']." G & ".$row['closing_weight_mgs']." Mg";					
					$rows['pledged_count'] = $row['item_in']; 
					$rows['released_count'] = $row['item_out'];
					$rows['opening_count'] = $row['opening_item'];
					$rows['closing_count'] = $row['closing_item'];
					$rows['stock_values_in'] = $row['amount_out'];
                    $rows['stock_values_out'] = $row['amount_in'];
					$rows['stock_opening_amount'] = $row['opening_amount'];
					$rows['stock_closing_amount'] = $row['closing_amount'];
					/*$rows['amount_in'] = $row['amount_in'];
                    $rows['amount_out'] = $row['amount_out'];
					$rows['amount_in'] = $row['amount_in'];
                    $rows['amount_out'] = $row['amount_out'];*/
                                      					
					unset($row['branch_name']);
					unset($row['opening_item']);
					unset($row['closing_item']);
					unset($row['opening_balance']);
					unset($row['closing_balance']);
					unset($row['opening_amount']);
					unset($row['closing_amount']);

                    unset($row['weight_in_gs']); 
                    unset($row['weight_in_mgs']);
                    unset($row['weight_out_gs']);
                    unset($row['weight_out_mgs']); 
                                        
                    unset($row['opening_weight_gs']); 
                    unset($row['opening_weight_mgs']);
                    unset($row['closing_weight_gs']);
                    unset($row['closing_weight_mgs']); 
                    
                    unset($row['trans_date']);
                    unset($row['loan_date']);
                    
                    unset($row['customer_name']);
                    unset($row['interest_amount_in']);
                    unset($row['partial_amount_in']);
                    unset($row['redeam_amount_in']);
                    unset($row['auction_amount_in']);
                    unset($row['loan_amount_out']);
                    unset($row['excess_amount_out']);
                    unset($row['auction_amount_out']);
                    
                    unset($row['total_pledged']);
                    unset($row['total_redeem']);
                    unset($row['total_auction_amount']);
                    unset($row['total_auction_lose']);
                    unset($row['total_interest']);
                    unset($row['total_partial']);
                    unset($row['total_excess']);
                    
                    unset($row['loan_date']);
                    unset($row['customer_id']);
                    unset($row['branch_id']);
                    unset($row['id']);
 
                    unset($row['no_of_customer']);
                    unset($row['no_of_issued']);
                    unset($row['no_of_return']);
                    unset($row['no_of_bidded']);
                    
                    unset($row['cash_flow_id']); 
                    unset($row['loan_code']);
                    unset($row['pledged_id']);
                    unset($row['report_date_new']); 
                    

                    $export_data[] = $rows;
                }
                }
                $width = array(50, 30, 40,20,60,40,20,60,60,60,30,30,40,40,40,40,40,40); 
                $page_width = 700;
                
            break;
            
            
            default:
            case "pledged_item":
            
                if(count($data) > 0){
                foreach($data as $row){


                    $row['opening_balance'] = convertToCurrency($row['opening_balance']);
                    $row['closing_balance'] = convertToCurrency($row['closing_balance']);

                    $weight_in = floatval($row['pledged_weight']);
                    

                    $weight_in_gs =  intval($weight_in);
                    $weight_in_mgs =  intval(floatval($weight_in - intval($weight_in)) * 1000);
                    

                    $row['pledged_weight'] = $weight_in_gs." G & ".$weight_in_mgs." Mg";
                    

                    $row['pledged_amount'] = convertToCurrency($row['pledged_amount']);
                    

                    unset($row['id']);

                    $export_data[] = $row;
                }
                }
            
                $width = array(60, 40, 60,25,80,60,60,60,40,40,40,40);
                
                $page_width = 450;
                
            break;

        }

		if(count($export_data) > 0) {
			$export_data = array_reverse($export_data);
		}
    
    return array("data"=>$export_data,"width"=>$width, "pwidth"=>$page_width);
    
    }


}
