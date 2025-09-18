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


class CustomerWiseTransactionReport {
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
        //$s_branch = $args[0];
        
        $s_cid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];

        $s_cid = preg_replace("/mfc/i",'',$s_cid);

        $search_condition = "AND (C.id <> '' )";
        $search_condition .= ($s_cid != "")? "AND (C.id = '".$s_cid."' )  ":"";

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
         
         
            $search_having = ($s_cusid != "") ? "(customer_no LIKE '".$s_cusid."')" : "(customer_no <> '' AND customer_no <> '-')";

            $search_condition ="";
            if($s_sdate != "" && $s_edate != ""){  
                $search_having .= " AND  ( report_date BETWEEN '".date("d-M-y",strtotime($s_sdate))."' AND '".date("d-M-y",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_having .= " AND  ( report_date >= '".date("d-M-y",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_having .= " AND  ( report_date <= '".date("d-M-y",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
            }

             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_having .= ($_SESSION['session_user_branch_id'] != "")?" AND  branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }

        /*
         * Return the Customers list
         */
        /*$query = "SELECT 

        C.id

        FROM ".CUSTOMERS." as C 
        LEFT JOIN ". PLEDGE." AS P ON P.customer_id = C.id 
        LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
        LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
        LEFT JOIN ".CATEGORIES." as CT ON CT.id = PD.category_id 
        LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
        LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
        LEFT JOIN ".PRODUCT_INTERESTS." AS PT ON PT.loan_id = P.id
        LEFT JOIN ".REDEMPTION." as PR on PR.loan_id = P.id
        LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." as PAP ON PAP.old_loan_id = P.id
        LEFT JOIN ".PAY_EXCESS_AMOUNT." as PEA ON PEA.old_loan_id = P.id
        LEFT JOIN ".PRODUCT_AUCTION." as PA ON PA.loan_id = P.id
        WHERE 1 ".$search_condition." 
        GROUP BY C.id
        ORDER BY C.id, P.id ";*/
        
         $query = "SELECT 

                    DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date,

                    CF.branch_id AS branch_id,

                    (                       
                    IF(P.loan_id <> '',
                        P.customer_id,
                        IF(PI.loan_id <> '',
                            (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PI.loan_id),
                            IF(PN.old_loan_id <> '',
                            (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PN.old_loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PR.loan_id),
                                    IF(PAP.old_loan_id <> '',
                                        (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PAP.old_loan_id),
                                        IF(PEAI.old_loan_id <> '',
                                            (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PEAI.old_loan_id),
                                            IF(PEAO.old_loan_id <> '',
                                                (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PEAO.old_loan_id),
                                                IF(PAI.loan_id <> '',
                                                    (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PAI.loan_id),
                                                    IF(PAO.loan_id <> '',
                                                        (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PAO.loan_id),
                                                        '-'    
                                                    )
                                                )
                                            )
                                        )

                                    )
                                )
                            )
                        )

                    )
                    ) AS customer_no

                    FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                    
                    LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id 
                    LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id 
                    
                    LEFT JOIN ".PRODUCT_INTERESTS." as PI ON PI.cash_flow_id = CF.id
                    LEFT JOIN ".REDEMPTION." as PR ON PR.cash_flow_id = CF.id
                    
                    LEFT JOIN ".RENEWAL." as PNI ON PNI.cash_in_id = CF.id
                    LEFT JOIN ".RENEWAL." as PNO ON PNO.cash_out_id = CF.id
                    
                    LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." as PAPI ON PAPI.cash_in_id = CF.id
                    LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." as PAPO ON PAPO.cash_out_id = CF.id
                    
                    LEFT JOIN ".PAY_EXCESS_AMOUNT." as PEAI ON PEAI.cash_in_id = CF.id
                    LEFT JOIN ".PAY_EXCESS_AMOUNT." as PEAO ON PEAO.cash_out_id = CF.id
                    
                    LEFT JOIN ".PRODUCT_AUCTION." as PAI ON PAI.cash_in_id = CF.id 
                    LEFT JOIN ".PRODUCT_AUCTION." as PAO ON PAO.cash_out_id = CF.id 
                    
                    GROUP BY customer_no 
                    HAVING  ".$search_having;
        
        
        
        
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
            
        //$s_branch = $args[0];
        
        $s_cid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];

            $s_cid = preg_replace("/mfc/i",'',$s_cid);

            $search_condition = "AND (C.id <> '' )";
            $search_condition .= ($s_cid != "")? "AND (C.id = '".$s_cid."' )  ":"";

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
             
             
             
            $search_having = ($s_cusid != "") ? "(customer_no LIKE '".$s_cusid."')" : "(customer_no <> '' AND customer_no <> '-')";

            $search_condition ="";
            if($s_sdate != "" && $s_edate != ""){  
                $search_having .= " AND  ( report_date BETWEEN '".date("d-M-y",strtotime($s_sdate))."' AND '".date("d-M-y",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_having .= " AND  ( report_date >= '".date("d-M-y",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_having .= " AND  ( report_date <= '".date("d-M-y",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
            }

             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_having .= ($_SESSION['session_user_branch_id'] != "")?" AND  branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }
             
             
             
             /*
              * Return the Customers list count
              */
            $count = self::ListviewCount($s_cid,$s_sdate,$s_edate);

            if ($count > 0) 
               {
                    /*
                    * Return the Customers list
                    */
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging


                    
                $query = "SELECT 

                        DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date, 
                        
                        
                        P.customer_id, 
                        P.loan_id, 
                        
                        CF.branch_id AS branch_id,
                        
                                                
                        (
                        IF(P.loan_id <> '',
                            P.loan_id,
                            IF(PI.loan_id <> '',
                                (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PR.loan_id),
                                    '-' 
                                )
                            )

                        )
                        ) AS loan_no,


                        (
                        IF(P.loan_id <> '',
                            P.customer_id,
                            IF(PI.loan_id <> '',
                                (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PR.loan_id),
                                    '-' 
                                )
                            )

                        )
                        ) AS customer_no,



                        (
                        IF(P.loan_id <> '',
                            CONCAT(C.name,' - (',C.id,')',' ,  ',C.street,' ,  ',C.location, '-', C.postal_code),
                            IF(PI.loan_id <> '',
                                (SELECT CONCAT(sC.name,' - (',sC.id,')',' ,  ',sC.street,' ,  ',sC.location, '-', sC.postal_code ) FROM ".CUSTOMERS." AS sC JOIN ".PLEDGE." AS sP  ON sP.customer_id = sC.id WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT  CONCAT(sC.name,' - (',sC.id,')',' ,  ',sC.street,' ,  ',sC.location, '-', sC.postal_code )  FROM ".CUSTOMERS." AS sC JOIN ".PLEDGE." AS sP  ON sP.customer_id = sC.id WHERE sP.id = PR.loan_id),
                                    '-' 
                                )
                            )
                        )
                        ) AS customer_details,


                        (
                        IF(P.loan_id <> '',
                            (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name  ORDER BY sCT.category DESC SEPARATOR ' ,  ')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = P.id GROUP BY sP.id),
                            IF(PI.loan_id <> '',
                                (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name  ORDER BY sCT.category DESC SEPARATOR ' ,  ')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = PI.loan_id GROUP BY sP.id),
                                IF(PR.loan_id <> '',
                                    (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name  ORDER BY sCT.category DESC SEPARATOR ' ,  ')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = PR.loan_id GROUP BY sP.id),
                                    '-' 
                                 )
                            )
                        )
                        ) AS product_details,
                        
                        (
                        IF(P.loan_id <> '',
                            P.loan_id,
                            IF(PI.loan_id <> '',
                                (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PR.loan_id),
                                    '-' 
                                )
                            )

                        )
                        ) AS id,
                        
                        (
                        IF(P.loan_id <> '',
                            CONCAT(C.name,' - (',C.id,')',' ,  ',C.street,' ,  ',C.location, '-', C.postal_code),
                            IF(PI.loan_id <> '',
                                (SELECT CONCAT(sC.name,' - (',sC.id,')',' ,  ',sC.street,' ,  ',sC.location, '-', sC.postal_code ) FROM ".CUSTOMERS." AS sC JOIN ".PLEDGE." AS sP  ON sP.customer_id = sC.id WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT  CONCAT(sC.name,' - (',sC.id,')',' ,  ',sC.street,' ,  ',sC.location, '-', sC.postal_code )  FROM ".CUSTOMERS." AS sC JOIN ".PLEDGE." AS sP  ON sP.customer_id = sC.id WHERE sP.id = PR.loan_id),
                                    '-'    
                                )
                            )
                        )
                        ) AS customer_name,
                        
                        SUM(P.loan_amount) AS total_pledged,
                        SUM(PI.interest_amount) AS total_interest,
                        SUM(PR.principle_amount) AS total_redeem

                        
                    FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                    
                    LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id 
                    LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id 
                    
                    LEFT JOIN ".PRODUCT_INTERESTS." as PI ON PI.cash_flow_id = CF.id
                    LEFT JOIN ".REDEMPTION." as PR ON PR.cash_flow_id = CF.id
                        
                         
                        GROUP BY customer_no 
                        
                        HAVING  ".$search_having."  
                        
                        ORDER BY customer_no ASC, CF.id ". Paging::get_sql_limit();
                    
                    
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
            
            $s_cusid = $args[0];
            $s_sdate = $args[1];
            $s_edate = $args[2];
            
            $s_cusid = preg_replace("/mfc/i",'',$s_cusid);
            
            $search_having = ($s_cusid != "") ? "(customer_no LIKE '".$s_cusid."')" : "(customer_no <> '' AND customer_no <> '-')";

            $search_condition ="";
            if($s_sdate != "" && $s_edate != ""){  
                $search_having .= " AND  ( report_date BETWEEN '".date("d-M-y",strtotime($s_sdate))."' AND '".date("d-M-y",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_having .= " AND  ( report_date >= '".date("d-M-y",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_having .= " AND  ( report_date <= '".date("d-M-y",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
            }

             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_having .= ($_SESSION['session_user_branch_id'] != "")?" AND  branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }

            
            /*
             * Return the Pledges list Count
             */
             $query = "SELECT 
                        
                        DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date,
                        
                        CF.branch_id AS branch_id,
                        
                        (
                        IF(P.loan_id <> '',
                            P.loan_id,
                            IF(PI.loan_id <> '',
                                (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PR.loan_id),
                                    '-' 
                                )
                            )
                        
                        )
                        ) AS loan_no,
                        
                        
                        (
                        IF(P.loan_id <> '',
                            P.customer_id,
                            IF(PI.loan_id <> '',
                                (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PR.loan_id),
                                    '-' 
                                )
                            )
                        
                        )
                        ) AS customer_no
            
                    FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                    
                    LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id 
                    LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id 
                    
                    LEFT JOIN ".PRODUCT_INTERESTS." as PI ON PI.cash_flow_id = CF.id
                    LEFT JOIN ".REDEMPTION." as PR ON PR.cash_flow_id = CF.id

                        
                        HAVING  ".$search_having;   
                       // echo $query;

                        
                        
            $data = $app->getrows($query);
            
            
            
            
            
            return $count = count($data);
     
       }

    public static function subListview() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_cusid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        
        //echo $s_sdate . "  " . $s_edate;
        $s_cusid = preg_replace("/mfc/i",'',$s_cusid);
        
        $search_having = ($s_cusid != "") ? "(customer_no LIKE '".$s_cusid."')" : "(customer_no <> '' AND customer_no <> '-')";
        

        
        $search_condition ="";
        if($s_sdate != "" && $s_edate != ""){  
            $search_having .= " AND  ( report_date BETWEEN '".date("d-M-Y",strtotime($s_sdate))."' AND '".date("d-M-Y",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_having .= " AND  ( report_date >= '".date("d-M-Y",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_having .= " AND  ( report_date <= '".date("d-M-Y",strtotime($s_edate))."')";
        }else{
            //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
        }

         if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_having .= ($_SESSION['session_user_branch_id'] != "")?" AND  branch_id = '".$_SESSION['session_user_branch_id']."'":"";
         }

        $count = self::subListviewCount($s_cusid, $s_sdate, $s_edate);
        /*
         * Return the Pledge list
         */
         

         
        if ($count > 0) {
            Paging::set_max( $count );
            Paging::calc();// calculation for paging
            $query = "SELECT 

                        DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date, 
                        
                        
                        P.customer_id, 
                        P.loan_id, 
                        
                        CF.branch_id AS branch_id,
                        
                        (
                        IF(P.loan_id <> '',
                            P.loan_id,
                            IF(PI.loan_id <> '',
                                (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PR.loan_id),
                                    '-' 
                                )
                            )
                        
                        )
                        ) AS loan_no,
                        
                        
                        (
                        IF(P.loan_id <> '',
                            P.customer_id,
                            IF(PI.loan_id <> '',
                                (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PR.loan_id),
                                    '-' 
                                )
                            )
                        
                        )
                        ) AS customer_no,
                        
                        
                        
                        (
                        IF(P.loan_id <> '',
                            CONCAT(C.name,' - (',C.id,')',' ,  ',C.street,' ,  ',C.location, '-', C.postal_code),
                            IF(PI.loan_id <> '',
                                (SELECT CONCAT(sC.name,' - (',sC.id,')',' ,  ',sC.street,' ,  ',sC.location, '-', sC.postal_code ) FROM ".CUSTOMERS." AS sC JOIN ".PLEDGE." AS sP  ON sP.customer_id = sC.id WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT  CONCAT(sC.name,' - (',sC.id,')',' ,  ',sC.street,' ,  ',sC.location, '-', sC.postal_code )  FROM ".CUSTOMERS." AS sC JOIN ".PLEDGE." AS sP  ON sP.customer_id = sC.id WHERE sP.id = PR.loan_id),
                                    '-' 
                                )
                            )
                        )
                        ) AS customer_details,
                        
                        
                        (
                        IF(P.loan_id <> '',
                            (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name  ORDER BY sCT.category DESC SEPARATOR ' ,  ')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = P.id GROUP BY sP.id),
                            IF(PI.loan_id <> '',
                                (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name  ORDER BY sCT.category DESC SEPARATOR ' ,  ')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = PI.loan_id GROUP BY sP.id),
                                IF(PR.loan_id <> '',
                                    (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name  ORDER BY sCT.category DESC SEPARATOR ' ,  ')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = PR.loan_id GROUP BY sP.id),
                                    '-' 
                                 )
                            )
                        )
                        ) AS product_details,

                        
                        
                        
                        
                        
                        CF.cash_purpose AS transaction_detail,
                        

                        
                             
                        IF(CF.id=P.cash_flow_id,
                            P.loan_amount,
                            0.00
                        ) AS pleged_amount,

                        IF(CF.id=PI.cash_flow_id,
                            PI.interest_amount,
                            0.00
                        ) AS interest_amount,
                        
                        IF(CF.id=PR.cash_flow_id,
                            PR.principle_amount,
                            0.00
                        ) AS redeemed_amount

                    FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                    
                    LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id 
                    LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id 
                    
                    LEFT JOIN ".PRODUCT_INTERESTS." as PI ON PI.cash_flow_id = CF.id
                    LEFT JOIN ".REDEMPTION." as PR ON PR.cash_flow_id = CF.id

                        
                        HAVING  ".$search_having."   
                        
                        
                        ORDER BY customer_no ASC, CF.id ";
                        
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

        $s_cusid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
    
        $s_cusid = preg_replace("/mfc/i",'',$s_cusid);
        $s_cusid = ($s_cusid == '') ? '0' : $s_cusid;
        
        $search_having = ($s_cusid != "") ? "(customer_no LIKE '".$s_cusid."')" : "(customer_no <> '' AND customer_no <> '-')";
                
        

        $search_condition ="";
        if($s_sdate != "" && $s_edate != ""){  
            $search_having .= " AND  ( report_date BETWEEN '".date("d-M-Y",strtotime($s_sdate))."' AND '".date("d-M-Y",strtotime($s_edate))."')";
        }elseif($s_sdate != "" && $s_edate == ""){  
            $search_having .= " AND  ( report_date >= '".date("d-M-Y",strtotime($s_sdate))."')";
        }elseif($s_sdate == "" && $s_edate != ""){ 
            $search_having .= " AND  ( report_date <= '".date("d-M-Y",strtotime($s_edate))."')";
        }else{
            //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
        }

             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_having .= ($_SESSION['session_user_branch_id'] != "")?" AND  branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }

        //echo "src = ".$search_condition;

        
        $count = self::subListviewCount($s_cusid, $s_sdate, $s_edate);
        
        /*
         * Return the Pledge list
         */
        if ($count > 0) 
        {

            $query = "SELECT 

                        DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date, 
                        
                        
                        P.customer_id, 
                        P.loan_id, 
                        
                        CF.branch_id AS branch_id,
                        
                        (
                        IF(P.loan_id <> '',
                            P.loan_id,
                            IF(PI.loan_id <> '',
                                (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PR.loan_id),
                                    '-' 
                                )
                            )
                        
                        )
                        ) AS loan_no,
                        
                        
                        (
                        IF(P.loan_id <> '',
                            P.customer_id,
                            IF(PI.loan_id <> '',
                                (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PR.loan_id),
                                    '-' 
                                )
                            )
                        
                        )
                        ) AS customer_no,
                        
                        
                        
                        (
                        IF(P.loan_id <> '',
                            CONCAT(C.name,' - (',C.id,')',' ,  ',C.street,' ,  ',C.location, '-', C.postal_code),
                            IF(PI.loan_id <> '',
                                (SELECT CONCAT(sC.name,' - (',sC.id,')',' ,  ',sC.street,' ,  ',sC.location, '-', sC.postal_code ) FROM ".CUSTOMERS." AS sC JOIN ".PLEDGE." AS sP  ON sP.customer_id = sC.id WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT  CONCAT(sC.name,' - (',sC.id,')',' ,  ',sC.street,' ,  ',sC.location, '-', sC.postal_code )  FROM ".CUSTOMERS." AS sC JOIN ".PLEDGE." AS sP  ON sP.customer_id = sC.id WHERE sP.id = PR.loan_id),
                                    '-' 
                                )
                            )
                        )
                        ) AS customer_details,
                        
                        
                        (
                        IF(P.loan_id <> '',
                            (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name  ORDER BY sCT.category DESC SEPARATOR ' ,  ')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = P.id GROUP BY sP.id),
                            IF(PI.loan_id <> '',
                                (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name  ORDER BY sCT.category DESC SEPARATOR ' ,  ')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = PI.loan_id GROUP BY sP.id),
                                IF(PR.loan_id <> '',
                                    (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name  ORDER BY sCT.category DESC SEPARATOR ' ,  ')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = PR.loan_id GROUP BY sP.id),
                                    '-' 
                                 )
                            )
                        )
                        ) AS product_details,

                        
                        
                        
                        
                        
                        CF.cash_purpose AS transaction_detail,
                        

                        
                             
                        IF(CF.id=P.cash_flow_id,
                            P.loan_amount,
                            0.00
                        ) AS pleged_amount,

                        IF(CF.id=PI.cash_flow_id,
                            PI.interest_amount,
                            0.00
                        ) AS interest_amount,
                        
                        IF(CF.id=PR.cash_flow_id,
                            PR.principle_amount,
                            0.00
                        ) AS redeemed_amount
                        

                    FROM ".CASH_FLOW_TRANSACTIONS." AS CF
                    
                    LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id 
                    LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id 
                    
                    LEFT JOIN ".PRODUCT_INTERESTS." as PI ON PI.cash_flow_id = CF.id
                    LEFT JOIN ".REDEMPTION." as PR ON PR.cash_flow_id = CF.id

                        
                        
                        HAVING  ".$search_having."   
                        
                        
                        ORDER BY customer_no ASC, CF.date_transaction DESC, CF.id DESC". ( ($s_cusid != "")  ? " LIMIT 0,15" : "");            //echo $query;
            //exit;
            return $app->getexport($query);

        }
    }
    

 


}
