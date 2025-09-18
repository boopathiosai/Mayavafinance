<?php
 //**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE
 // Module           :  Category class
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Karthick B
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  29-Oct-2011
 //**************************************************************************
 // Description      :  This is the Category class  for Mayavafinance.
//**************************************************************************
class CategoryWiseReport {
          
        public static function ListviewCount() 
        {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
            $app = $GLOBALS['app'];
            $paging = $GLOBALS['paging'];
            $args = func_get_args();
            $s_sdate = $args[0];
            $s_edate = $args[1];
            $s_cid = $args[2];
			$s_sub_branch = $args[3];
            
            
            if($s_cid != '') {
                $search_condition .= " AND PD.category_id IN (".$s_cid.")";
            }

            if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
                    
             if($_SESSION['session_usergroupname'] != 'Super Admin')
             {
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND P.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }
			 $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
        /*
         * Return the Category list
         */
            
                /*$query="SELECT 
                    P.id,
                    COUNT(DISTINCT P.loan_id) AS total_loanid,
                    C.id AS cat_id,
                    SUM(DISTINCT P.loan_amount) AS total_loan_amount,
                    DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS loan_date, 
                    SUM(DISTINCT(COALESCE(P.total_net_weight, 0.00 ))) AS total_pledged_weight,
                    SUM(PD.product_items ) AS total_item_count,
                    GROUP_CONCAT(PC.name) AS product_name,
                    COUNT(PC.name) as total_product_name,   
                    GROUP_CONCAT(PC.product_with) AS product_with, 
                    SUM(product_with) AS total_product_with,
                    GROUP_CONCAT(PC.product_without) AS product_without, 
                    SUM(product_without) AS total_product_without,
                    GROUP_CONCAT(C.category) as category,
                    COUNT(DISTINCT category) AS category_count
                    FROM ".PLEDGE_DETAILS." as PD 
                    INNER JOIN ".PLEDGE." P ON P.id = PD.pledge_id 
                    INNER JOIN ".PRODUCTS." PC ON PC.id = PD.product_id 
                    INNER JOIN ".CATEGORIES." C ON C.id = PC.category_id
                    INNER JOIN ".BRANCH." AS B ON B.id = P.branch_id 
                    INNER JOIN ".SCHEME." AS SC ON SC.id = P.scheme_id 
                    WHERE P.status =1 ".$search_condition." 
                    GROUP BY DATE_FORMAT(P.loan_date,'%Y-%m-%d') ";*/
                    
                $query = "SELECT COUNT(distinct DATE_FORMAT(P.loan_date,'%Y-%m-%d')) FROM mf_pledge AS P JOIN mf_pledge_details AS PD ON PD.pledge_id = P.id WHERE P.status =1 ".$search_condition." ";    
                $data = $app->getrow($query);
                return (int)$data[0];
       }

        public static function Listview() 
        {
            /*
             * Get the function parameters , if exist . And make the search condition
             */
            
            $app = $GLOBALS['app'];
            $paging = $GLOBALS['paging'];
            $args = func_get_args();
            $s_sdate = $args[0];
            $s_edate = $args[1];
            $s_cid = $args[2];
			$s_sub_branch = $args[3];
            
            if($s_cid != '') {
                $search_condition .= " AND PD.category_id IN (".$s_cid.")";
            }

            if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
                    
             if($_SESSION['session_usergroupname'] != 'Super Admin')
             {
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
             }
             $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
             
             /*
              * Return the Category list count
              */
            $count = self::ListviewCount($s_sdate,$s_edate,$s_cid,$s_sub_branch);

            if ($count > 0) 
               {
                    /*
                    * Return the Category list
                    */
                    Paging::set_max( $count );
                    Paging::calc();// calculation for pagings
            
                    $query="SELECT 
                    P.id,
                    COUNT(DISTINCT P.loan_id) AS total_loanid,
                    C.id AS cat_id,
                    SUM(DISTINCT P.loan_amount) AS total_loan_amount,
                    DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS loan_date, 
                    SUM(DISTINCT(COALESCE(P.total_net_weight, 0.00 ))) AS total_pledged_weight,
                    SUM(PD.product_items ) AS total_item_count,
                    GROUP_CONCAT(PC.name) AS product_name,
                    COUNT(PC.name) as total_product_name,   
                    GROUP_CONCAT(PC.product_with) AS product_with, 
                    SUM(product_with) AS total_product_with,
                    GROUP_CONCAT(PC.product_without) AS product_without, 
                    SUM(product_without) AS total_product_without,
                    GROUP_CONCAT(C.category) as category,
		    CONCAT(SB.name,' - (',SB.sub_branch_code,')') AS sub_details,
                    COUNT(DISTINCT category) AS category_count
                    FROM ".PLEDGE_DETAILS." as PD 
                    INNER JOIN ".PLEDGE." P ON P.id = PD.pledge_id 
                    INNER JOIN ".PRODUCTS." PC ON PC.id = PD.product_id 
                    INNER JOIN ".CATEGORIES." C ON C.id = PC.category_id
                    INNER JOIN ".BRANCH." AS B ON B.id = P.branch_id 
                    INNER JOIN ".SCHEME." AS SC ON SC.id = P.scheme_id 
						LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id
                    WHERE P.status =1 ".$search_condition." 
                    GROUP BY DATE_FORMAT(P.loan_date,'%Y-%m-%d') ". Paging::get_sql_limit();
                    
                    /*$query="SELECT 
                    P.id,
                    COUNT(DISTINCT P.loan_id) AS total_loanid,
                    C.id AS cat_id,
                    SUM(P.loan_amount) AS total_loan_amount,
                    DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS loan_date, 
                    SUM((COALESCE(P.total_net_weight, 0.00 ))) AS total_pledged_weight,
                    SUM(PD.product_items ) AS total_item_count,
                    GROUP_CONCAT(PC.name) AS product_name,
                    COUNT(PC.name) as total_product_name,   
                    GROUP_CONCAT(PC.product_with) AS product_with, 
                    SUM(product_with) AS total_product_with,
                    GROUP_CONCAT(PC.product_without) AS product_without, 
                    SUM(product_without) AS total_product_without,
                    
                    GROUP_CONCAT(C.category) as category
                    FROM ".PLEDGE_DETAILS." as PD 
                    INNER JOIN ".PLEDGE." P ON P.id = PD.pledge_id 
                    INNER JOIN ".PRODUCTS." PC ON PC.id = PD.product_id 
                    INNER JOIN ".CATEGORIES." C ON C.id = PC.category_id
                    INNER JOIN ".BRANCH." AS B ON B.id = P.branch_id 
                    INNER JOIN ".SCHEME." AS SC ON SC.id = P.scheme_id 
                    WHERE P.status =1 ".$search_condition." 
                    GROUP BY DATE_FORMAT(P.loan_date,'%Y-%m-%d') ". Paging::get_sql_limit();*/

                    return $app->getrows($query);
                                   
               }
        }
          
        public static function subListviewCount() {

            /*
             * Get the function parameters , if exist . And make the search condition
             */
            $app = $GLOBALS['app'];
            $args = func_get_args();
            $s_sdate = $args[0];
            $s_edate = $args[1];
            $s_cid = $args[2];
            
           if($s_cid != '' ) {
                        $search_condition .= " AND PD.category_id IN (".$s_cid.")";
                    }

            if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }

            if($_SESSION['session_usergroupname'] != 'Super Admin')
             {
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
             }
             
             
            /*
             * Return the loan list
             */

               
                $query="SELECT DATE_FORMAT( P.loan_date, '%Y-%m-%d' ) AS loan_date, 
                CT.category AS category, CT.id AS cat_id
                FROM ".PLEDGE." AS P
                LEFT JOIN mf_pledge_details AS PD ON P.id = PD.pledge_id
                LEFT JOIN mf_categories AS CT ON CT.id = PD.category_id
                LEFT JOIN mf_products AS PR ON PR.id = PD.product_id
                LEFT JOIN mf_branch AS B ON P.branch_id = B.id
                LEFT JOIN mf_schemes AS SC ON P.scheme_id = SC.id
                WHERE P.status =1 ".$search_condition."";
            
            ///echo $query;
            $data = $app->getrows($query);
            return $count = count($data);

     }

        public static function subListview() {
            /*
             * Get the function parameters , if exist . And make the search condition
             */
            //Modified by -------
            $app = $GLOBALS['app'];
            $args = func_get_args();
            $s_sdate = $args[0];
            $s_edate = $args[1];
            $s_cid = $args[2];
            
             if($s_cid != '' ) {
                        $search_condition .= " AND PD.category_id IN (".$s_cid.")";
                    }

            if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }

             if($_SESSION['session_usergroupname'] != 'Super Admin')
             {
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
             }

             /*
              * Return the Category list count
              */
            $count = self::subListviewCount($s_sdate,$s_edate,$s_cid);

            if ($count > 0) {
             /*
              * Return the loan list
              */
                Paging::set_max( $count );
                Paging::calc();// calculation for paging
                
                /*echo $query="SELECT 
                P.loan_id,P.loan_amount,DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS loan_date,
                COALESCE(P.total_net_weight,0.00) AS pledged_weight,SC.loan_rate,P.item_count,PR.name,
                SC.name AS scheme_name ,CT.category as category,PR.product_with as product_with,
                PR.product_without AS product_without
                FROM ".PLEDGE." AS P 
                INNER JOIN ".PLEDGE_DETAILS." AS PD ON P.id = PD.pledge_id
                LEFT JOIN ".CATEGORIES." AS CT on CT.id = PD.category_id
                INNER JOIN ".PRODUCTS." AS PR ON PR.id = PD.product_id
                LEFT JOIN ".BRANCH." AS B ON P.branch_id = B.id 
                LEFT JOIN ".SCHEME." AS SC ON P.scheme_id = SC.id
                WHERE P.status =1 ".$search_condition." 
                ". Paging::get_sql_limit();*/


                $query="SELECT P.loan_id,P.loan_amount,DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS loan_date,
                COALESCE(P.total_net_weight,0.00) AS pledged_weight,
                SUM(PD.product_items ) AS item_count,
                GROUP_CONCAT(PC.name) AS product_name,
                GROUP_CONCAT(PC.product_with) AS product_with,
                GROUP_CONCAT(PC.product_without) AS product_without,
                SC.name AS scheme_name,SC.loan_rate,
                GROUP_CONCAT(DISTINCT C.category) AS category

                FROM mf_pledge_details  as  PD
                INNER JOIN mf_pledge  P ON P.id = PD.pledge_id
                INNER JOIN mf_products PC ON PC.id = PD.product_id
                INNER JOIN mf_categories C ON C.id = PC.category_id
                INNER JOIN mf_branch AS B ON  B.id = P.branch_id 
                INNER JOIN mf_schemes AS SC ON SC.id =  P.scheme_id 
                WHERE P.status =1 ".$search_condition." 
                GROUP BY DATE_FORMAT(P.loan_date,'%Y-%m-%d'),P.loan_id". Paging::get_sql_limit();



                return $app->getrows($query);
            }
    }

                            

    public static function Listexport() 
    {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by -------
            $app = $GLOBALS['app'];
            $args = func_get_args();
            $s_sdate = $args[0];
            $s_edate = $args[1];
            $s_branchid = $args[2];
			$s_sub_branch = $args[3];
            
            
            if($s_branchid!= '' && $s_branchid!= 'null') {
                $search_condition .= " AND PD.category_id IN (".$s_branchid.")";
            }

            if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
            $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";

             if($_SESSION['session_usergroupname'] != 'Super Admin')
             {
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
             }

             /*
              * Return the Category list count
              */
            $count = self::subListviewCount($s_sdate,$s_edate,$s_cid);

            if ($count > 0) {
             /*
              * Return the loan list
              */
                Paging::set_max($count );
                Paging::calc();// calculation for paging
                $query="SELECT 
                DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date ,P.loan_id,CA.category,PR.product_with,
                PR.product_without,PR.name AS product,
                COALESCE(P.total_net_weight,0.00) AS pledged_weight,P.item_count,P.loan_amount,
                ((P.loan_amount)/COALESCE(P.total_net_weight,0.00)) AS loan_rate
                FROM ".PLEDGE." AS P 
                LEFT JOIN ".BRANCH." AS B ON P.branch_id = B.id 
                INNER JOIN ".PLEDGE_DETAILS." AS PD ON P.id = PD.pledge_id
                LEFT JOIN ".SCHEME." AS SC ON P.scheme_id = SC.id 
                INNER JOIN ".PRODUCTS." AS PR ON PR.id = PD.product_id
                LEFT JOIN ".CUSTOMERS." AS C ON C.id = P.customer_id
				LEFT JOIN ".CATEGORIES." AS CA ON CA.id = PD.category_id 
                WHERE P.status=1  ".$search_condition." 
                ORDER BY P.loan_date DESC ";
                
                /*$query="SELECT P.loan_id,P.loan_amount,DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS loan_date,
                COALESCE(P.total_net_weight,0.00) AS pledged_weight,
                SUM(PD.product_items ) AS item_count,
                GROUP_CONCAT(PC.name) AS product_name,
                GROUP_CONCAT(PC.product_with) AS product_with,
                GROUP_CONCAT(PC.product_without) AS product_without,
                SC.name AS scheme_name,SC.loan_rate,
                GROUP_CONCAT(DISTINCT C.category) AS category

                FROM mf_pledge_details  as  PD
                INNER JOIN mf_pledge  P ON P.id = PD.pledge_id
                INNER JOIN mf_products PC ON PC.id = PD.product_id
                INNER JOIN mf_categories C ON C.id = PC.category_id
                INNER JOIN mf_branch AS B ON  B.id = P.branch_id 
                INNER JOIN mf_schemes AS SC ON SC.id =  P.scheme_id 
                WHERE P.status =1 ".$search_condition." 
                GROUP BY DATE_FORMAT(P.loan_date,'%Y-%m-%d'),P.loan_id";*/
                
        $preresult = $app->getexport($query);
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
