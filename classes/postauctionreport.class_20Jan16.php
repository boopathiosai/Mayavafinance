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


class PostAuctionReport {
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
            $s_cid = $args[0];
            $s_branch = $args[1];
            $s_sdate = $args[2];
            $s_edate = $args[3];
			$s_sub_branch = $args[4];

            $s_cid = preg_replace("/mfc/i",'',$s_cid);
            $search_condition .= ($s_cid != "") ? "AND (C.id = '".$s_cid."' )  ":"";
            $search_condition .= ($s_branch != "")? "AND ( B.id  = '".$s_branch."' )  ":"";
			$search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
            if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
            }

            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  B.id   = '".$_SESSION['session_user_branch_id']."'":"";
            }
            /*
             * Return the Customers list
            */
        $query = "SELECT 
        DATE_FORMAT(PA.paid_on,'%Y-%m-%d') AS report_date
        FROM ".PRODUCT_AUCTION." as PA 
        LEFT JOIN ". PLEDGE." AS P  ON PA.loan_id = P.id
        LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
        LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id 
        LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
		LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id
        WHERE 1  ".$search_condition." 
        GROUP BY DATE_FORMAT(PA.paid_on,'%Y-%m-%d')
        ORDER BY DATE_FORMAT(PA.paid_on,'%Y-%m-%d') DESC ";
        $data = $app->getrows($query);
        return $count = count($data);

   }

    public static function Listview() 
    {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        
        $args = func_get_args();

        $app = $GLOBALS['app'];
        $paging = $GLOBALS['paging'];

        $s_cid = $args[0];
        $s_branch = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
		$s_sub_branch = $args[4];

        $s_cid = preg_replace("/mfc/i",'',$s_cid);
        

        $search_condition .= ($s_cid != "") ? "AND (C.id = '".$s_cid."' )  ":"";

                          
            
            $search_condition .= ($s_branch != "")? "AND ( B.id  = '".$s_branch."' )  ":"";
			$search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
            if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
            }

            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  B.id   = '".$_SESSION['session_user_branch_id']."'":"";
            }

         /*
          * Return the Customers list count
          */
        $count = self::ListviewCount($s_cid, $s_branch , $s_sdate,$s_edate,$s_sub_branch);

        if ($count > 0) 
           {
                /*
                * Return the Customers list
                */
                Paging::set_max( $count );
                Paging::calc();// calculation for paging

                $query = "SELECT 
                DATE_FORMAT(PA.paid_on,'%Y-%m-%d') AS id,
                B.name AS branch_name,
                DATE_FORMAT(PA.paid_on,'%d-%b-%Y') AS report_date,
                DATE_FORMAT(PA.paid_on,'%d-%b-%Y') AS auction_date,
		CONCAT(SB.name,' - (',SB.sub_branch_code,')') AS sub_details,
                SUM(P.loan_amount) AS total_loan_amount,
                ROUND(SUM(P.total_net_weight),3) AS total_weight,
                SUM(P.item_count) AS total_item,
                COUNT(DISTINCT C.id) AS no_of_customer,
                COUNT(DISTINCT P.id) AS no_of_loan,
                SUM(PA.auction_amount) AS auction_amount,  
                SUM(IF(PA.amount_difference < 0 , (-1 * PA.amount_difference),0.00)) AS loss,
                SUM(IF(PA.amount_difference >= 0 , PA.amount_difference  ,0.00)) AS profit
                FROM ".PRODUCT_AUCTION." as PA 
                LEFT JOIN ". PLEDGE." AS P  ON PA.loan_id = P.id
                LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
                LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id 
                LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
				LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id
                WHERE 1 ".$search_condition." 
                GROUP BY DATE_FORMAT(PA.paid_on,'%Y-%m-%d') 
                ORDER BY DATE_FORMAT(PA.paid_on,'%Y-%m-%d') DESC ". Paging::get_sql_limit();
                //echo "<pre>"; echo $query."<hr>";die;
				//echo $query;
                return $app->getrows($query);

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

        $s_cid = preg_replace("/mfc/i",'',$s_cid);
        

        $search_condition .= ($s_cid != "") ? "AND (C.id = '".$s_cid."' )  ":"";
                            
            
            $search_condition .= ($s_branch != "")? "AND ( B.id  = '".$s_branch."' )  ":"";
            if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
            }

            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  B.id   = '".$_SESSION['session_user_branch_id']."'":"";
            }
            
            /*
             * Return the Pledges list Count
             */
            $query = "SELECT DISTINCT P.id 
                        FROM ".PRODUCT_AUCTION." as PA 
                        LEFT JOIN ".PLEDGE." as P ON PA.loan_id = P.id
                        LEFT JOIN ".BRANCH." as B ON P.branch_id = B.id
                        LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                        LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
                        LEFT JOIN ".CATEGORIES." as CT ON CT.id = PD.category_id
                        LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
                        LEFT JOIN ".PRODUCT_INTERESTS." as PT ON P.id = PT.loan_id 
                        LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id
                        WHERE 1 ".$search_condition." 
                        GROUP BY P.loan_id
                        ORDER BY id DESC ";
                        
                       //echo $query; 
            $data = $app->getrows($query);
            
            
            //$dates = self::ListDateRange('2011-10-01','2011-11-08');
            //print_r($dates);
            
            
            return $count = count($data);
     
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
        $s_cid = preg_replace("/mfc/i",'',$s_cid);
        $search_condition .= ($s_cid != "") ? "AND (C.id = '".$s_cid."' )  ":"";
            $search_condition .= ($s_branch != "")? "AND ( B.id  = '".$s_branch."' )  ":"";
            if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
            }

            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  B.id   = '".$_SESSION['session_user_branch_id']."'":"";
            }

        $count = self::subListviewCount($s_cid, $s_branch , $s_sdate,$s_edate);
        /*
         * Return the Pledge list
         */
        if ($count > 0) {
            Paging::set_max( $count );
            Paging::calc();// calculation for paging
            $query = "SELECT DISTINCT 
            
                        B.name as branch_name,
                        DATE_FORMAT(PA.paid_on,'%d-%b-%Y') AS report_date,

                        P.loan_id AS loan_no,
                        P.loan_amount AS loan_amount,
                        PA.serial_no, PA.interest_amount,

                        CONCAT(
                            C.name,' - (',C.customer_id,')','\n',
                            C.street,'\n',
                            C.location, '-', C.postal_code, '\n',
                            'PH:',C.contact_no,'\n',
                            'Email:',C.email,'\n'
                        ) AS customer_details,

                        GROUP_CONCAT(
                            CT.category,'-',PRO.name,'-[',PRO.product_with,',',PRO.product_without,']'
                            
                            ORDER BY CT.category DESC SEPARATOR '\n'
                        ) AS product_details,
                        


                        P.total_gross_weight AS gross, 
                        ROUND(P.total_net_weight,3) AS net_weight, 
                        P.item_count AS quantity, 
                        
                        SC.name as scheme_name,
                        
                        PA.auction_amount AS auction_amount,  
                        
                        IF(PA.amount_difference < 0 , (-1 * PA.amount_difference),0.00) AS loss,
                        IF(PA.amount_difference >= 0 , PA.amount_difference  ,0.00) AS profit,
                        
                        IF(PA.amount_difference < 0 , 'Loss','Profit') AS auction_status,
                        
                        DATE_FORMAT(PA.paid_on,'%d-%b-%y') AS auction_date

                        FROM ".PRODUCT_AUCTION." as PA 
                        LEFT JOIN ".PLEDGE." as P ON PA.loan_id = P.id 
                        LEFT JOIN ".BRANCH." as B ON P.branch_id = B.id
                        LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                        LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
                        LEFT JOIN ".CATEGORIES." as CT ON CT.id = PD.category_id
                        LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
                        LEFT JOIN ".PRODUCT_INTERESTS." as PT ON P.id = PT.loan_id 
                        LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id
                        WHERE 1 ".$search_condition." 
                        GROUP BY P.loan_id
                        ORDER BY P.loan_id ASC ";
            //echo $query."<hr>";
            //exit;
            return $app->getrows($query);

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

        $s_cid = preg_replace("/mfc/i",'',$s_cid);
        

        $search_condition .= ($s_cid != "") ? "AND (C.id = '".$s_cid."' )  ":"";

                                        
            
            $search_condition .= ($s_branch != "")? "AND ( B.id  = '".$s_branch."' )  ":"";
			$search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
            if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( DATE_FORMAT(PA.paid_on,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
            }

            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND  B.id   = '".$_SESSION['session_user_branch_id']."'":"";
            }
        //echo "src = ".$search_condition;

        $count = self::subListviewCount($s_cid, $s_branch , $s_sdate,$s_edate);
        /*
         * Return the Pledge list
         */
        if ($count > 0) 
        {

            $query = "SELECT DISTINCT 
            
                        B.name as branch_name,
                        DATE_FORMAT(PA.paid_on,'%d-%b-%Y') AS report_date,

                        P.loan_id AS loan_no,
                        P.loan_amount AS loan_amount,PA.serial_no,
                        C.name As cust_name,
                       CONCAT( C.street,', ', C.location, ' - ', C.postal_code ) AS customer_details,

                        GROUP_CONCAT(
                            CT.category,'-',PRO.name

                            ORDER BY CT.category DESC SEPARATOR '  ,  '
                        ) AS product_details,
                        
                        
                        P.item_count AS quantity, 
                        P.total_net_weight AS net_weight, 
                        
                        PA.auction_amount AS auction_amount,  
                        IF(PA.amount_difference < 0 , (-1 * PA.amount_difference),0.00) AS loss,
                        IF(PA.amount_difference >= 0 , PA.amount_difference,0.00) AS profit,PA.interest_amount,
                        
                        IF(PA.amount_difference < 0 , 'Loss','Profit') AS auction_status
                        
                        

                        FROM ".PRODUCT_AUCTION." as PA 
                        LEFT JOIN ".PLEDGE." as P ON PA.loan_id = P.id 
                        LEFT JOIN ".BRANCH." as B ON P.branch_id = B.id
                        LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                        LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
                        LEFT JOIN ".CATEGORIES." as CT ON CT.id = PD.category_id
                        LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
                        LEFT JOIN ".PRODUCT_INTERESTS." as PT ON P.id = PT.loan_id 
                        LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id
                        LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id
                        WHERE 1 ".$search_condition." 
                        GROUP BY P.loan_id
                        ORDER BY P.loan_id ASC ";
                        
            //echo $query;
            //exit;
        $preresult = $app->getexport($query);
		//echo $app;
		//echo "<pre>";print_r($preresult);
        $result = self::resultData($preresult);


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
          
            $result[] = $rows;
          }
        }else{
          $result = $reports;
        }
        
        return $result;
        
    }
 


}
