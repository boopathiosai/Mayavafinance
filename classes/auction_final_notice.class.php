<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE                   
// Module           :  Brand class          
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  ----------------
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  05-Feb-2010
//**************************************************************************
// Description      :  This is the Branch class  for Mayavafinance.
//**************************************************************************

class Auction_Final_Notice {
     /*
         * Define the required fields and return text
         */
    
        

        public static function ListviewCount() {
            /*
             * Get the function parameters , if exist . And make the search condition
             */

            $args = func_get_args();

            $app = $GLOBALS['app'];

        /* Search Query */                
        //$s_date = $args[0];
        $s_sdate = $args[0];
        $s_edate = $args[1];
        $s_key = $args[2];
        $s_cus = $args[3];
        $s_location = $args[4];
        $s_fhw = $args[5];
        $s_branch = $args[6];
        $s_loan = $args[7];

        
            
        //Grace Period Checking - After 1 year + 0 Day Grace Period 
         $search_condition  = " AND  (
        DATE_FORMAT(
        DATE_ADD(
            DATE_ADD(
            IF(PI.paid_on <> '', PI.paid_on, P.loan_date),
            INTERVAL ".AUCTION_GRACE_MONTH." MONTH
            ),
            INTERVAL ".AUCTION_GRACE_DAYS." DAY
        ),
        '%Y-%m-%d'
        )

    ) <= CURRENT_DATE";
          
            //$search_condition  .= " AND  ( P.status = 1)"; 
            if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
            }
            
            $search_condition .= ($s_key != "")? " AND  ( C.name LIKE '".$s_key."%')":"";
            $search_condition .= ($s_cus != "")? " AND (C.id LIKE '".$s_cus."' OR C.customer_id LIKE '".$s_cus."')":"";
            $search_condition .= ($s_location != "")? " AND ( C.street LIKE '".$s_location."%' OR C.location LIKE '".$s_location."%')  ":"";
            $search_condition .= ($s_fhw != "")? " AND ( C.relation_name LIKE '".$s_fhw."%' )  ":"";
            $search_condition .= ($s_branch != "")? " AND ( C.branch_id  = '".$s_branch."' )  ":"";
            $search_condition .= ($s_loan != "")? " AND ( P.loan_id  LIKE '".$s_loan."'  OR P.id  = '".$s_loan."' OR P.loan_id LIKE 'MFL".$s_loan."')  ":"";
           
            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND P.branch_id  = '".$_SESSION['session_user_branch_id']."'":"";
            }
                
            /*
             * Return the Pledges list Count
             */
            $query = "
						SELECT DISTINCT P.id 
                                FROM ".PLEDGE." as P 
                                LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
                                LEFT JOIN ".PRODUCT_INTERESTS." as PI ON P.id = PI.loan_id 
                                LEFT JOIN ".PRODUCT_AUCTION." as PA ON P.id = PA.loan_id 
                                LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                                LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
                                LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id 
                                WHERE  P.status=1 ".$search_condition." 
                                ORDER BY P.id DESC, PI.paid_on DESC ";
            //echo $query;
            $data = $app->getrows($query);
            return $count = count($data);
     }

        public static function Listview() {
                /*
                 * Get the function parameters , if exist . And make the search condition
                 */
								
                $args = func_get_args();
                $app = $GLOBALS['app'];

        /* Search Query */                
        //$s_date = $args[0];
        $s_sdate = $args[0];
        $s_edate = $args[1];
        $s_key = $args[2];
        $s_cus = $args[3];
        $s_location = $args[4];
        $s_fhw = $args[5];
        $s_branch = $args[6];
        $s_loan = $args[7];
        
             
        //Grace Period Checking - After 1 year + 0 Day Grace Period 
         $search_condition  = " AND  (
        DATE_FORMAT(
        DATE_ADD(
            DATE_ADD(
            IF(PI.paid_on <> '', PI.paid_on, P.loan_date),
            INTERVAL ".AUCTION_GRACE_MONTH." MONTH
            ),
            INTERVAL ".AUCTION_GRACE_DAYS." DAY
        ),
        '%Y-%m-%d'
        )

    ) <= CURRENT_DATE";

        //Monthly Period Checking - After due date month exceed
         $search_condition1  = " AND  (
        DATE_FORMAT(
        DATE_ADD(
            DATE_ADD(
            IF(PI.paid_on <> '', PI.paid_on, P.loan_date),
            INTERVAL 1 MONTH
            ),
            INTERVAL 1 DAY
        ),
        '%Y-%m-%d'
        )

    ) >= CURRENT_DATE";

            //$search_condition  .= " AND  ( P.status = 1)"; 
            if($s_sdate != "" && $s_edate != ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){  
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){ 
                $search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
            }
            $search_condition .= ($s_key != "")? " AND  ( C.name LIKE '".$s_key."%')":"";
            $search_condition .= ($s_cus != "")? " AND (C.id LIKE '".$s_cus."' OR C.customer_id LIKE '".$s_cus."')":"";
            $search_condition .= ($s_location != "")? " AND ( C.street LIKE '".$s_location."%' OR C.location LIKE '".$s_location."%')  ":"";
            $search_condition .= ($s_fhw != "")? " AND ( C.relation_name LIKE '".$s_fhw."%' )  ":"";
            $search_condition .= ($s_branch != "")? " AND ( C.branch_id  = '".$s_branch."' )  ":"";
            $search_condition .= ($s_loan != "")? " AND ( P.loan_id  LIKE '".$s_loan."'  OR P.id  = '".$s_loan."' OR P.loan_id LIKE 'MFL".$s_loan."')  ":"";
                
            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND P.branch_id  = '".$_SESSION['session_user_branch_id']."'":"";
            }

                
                $count = self::ListviewCount($s_sdate,$s_edate,$s_key,$s_cus,$s_location,$s_fhw,$s_branch, $s_loan);


                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging

                    $query = "SELECT DISTINCT P.*, P.id AS pledge_id,
                                B.name AS branch_name,
                                C.branch_id AS customer_branch,
                                P.branch_id AS pledged_branch,
                                C.customer_id AS customer_code,
                                P.loan_amount AS loan_amount,
                                DATE_FORMAT(
                    DATE_ADD(
                        DATE_ADD(
                        IF(PI.paid_on <> '', PI.paid_on, P.loan_date),
                        INTERVAL ".AUCTION_GRACE_MONTH." MONTH
                        ),
                        INTERVAL ".AUCTION_GRACE_DAYS." DAY
                    ),
                    '%d-%b-%Y'
                ) AS auction_due_date,
                                DATE_FORMAT(
                    DATE_ADD(
                        DATE_ADD(
                        IF(PI.paid_on <> '', PI.paid_on, P.loan_date),
                        INTERVAL 1 MONTH
                        ),
                        INTERVAL 1 DAY
                    ),
                    '%d-%b-%Y'
                ) AS pay_due_date,
                                P.total_gross_weight AS gross, 
                                P.total_net_weight AS net, 
                                P.item_count AS item, 
                                DATE_FORMAT(P.loan_date,'%d-%M-%y') as loan_date,
                                DATE_FORMAT(PI.paid_on,'%d-%M-%y') as last_pay_date,
                                SC.name as scheme_name,
                                C.name as customer_name, IF(P.status = 0, 'disable', 'enable') AS status_text                                
                                FROM ".PLEDGE." as P 
                                LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
                                LEFT JOIN ".PRODUCT_INTERESTS." as PI ON P.id = PI.loan_id
                                LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                                LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
                                LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id 
                                WHERE P.status=1 ".$search_condition." 
								GROUP BY P.loan_id								
                                ORDER BY P.loan_date ASC ". Paging::get_sql_limit();
                    //echo $query;
                    //exit;
                    return $app->getrows($query);
                  
                  
                  
                    
                }
    }
    
    public static function Detail($pledge_id) {
            /*
             * Return the Pledge details
             */
						 
            
            $app = $GLOBALS['app'];
            //$count = $app->find_count(array('conditions' => array('id =' => $pledge_id)));
            //echo $count;
            //if ($count > 0) 
            {
                $query = "SELECT DISTINCT  
                            DATE_FORMAT(P.loan_date,'%d-%b-%Y') as loan_date ,PD.category_id, 
                            PD.category_id, PD.gross_weight, PD.net_weight,
                            PD.product_items, P.total_net_weight, P.item_count, P.loan_amount,
                            P.loan_id,P.total_gross_weight,
                            CR.branch_id AS customer_branch,
                            P.branch_id AS pledged_branch,
                            CR.customer_id AS customer_code,
							BR.organization_name AS org_name,BR.name AS branch,BR.address AS branch_address, 
                            PRO.name as product_name,PRO.product_with,PRO.product_without,
                            PRO.id as pro_id, PD.gross_weight, PD.net_weight, P.market_rate, C.category as category_name,CR.id as cust_id, 
                            CR.id as customer_id, CR.name as customer_name, 
                            CR.street as customer_street,CR.location as customer_location,
                            CR.postal_code as customer_postal_code, CR.email,CR.contact_no,
                            CR.religion,CR.religion_category, if(CR.land_holding=1,'Yes','No') AS land_holding,
                            CR.contact_no as customer_contact_no,CR.photo as customer_photo,
                            if(SC.interest_type,'Floating','Flat') as interest_type,
                            SC.name as scheme_name,SC.description , SC.loan_rate
                            FROM ".PLEDGE." as P 
                            LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
                            LEFT JOIN ".PRODUCT_INTERESTS." as PI ON P.id = PI.loan_id 
                            LEFT JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id
														LEFT JOIN ".BRANCH." as BR ON BR.id = CR.branch_id
                            LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
                            LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id 
                            LEFT JOIN ".PRODUCTS." as PRO on PD.product_id = PRO.id
                            WHERE P.id ='".$pledge_id."' ORDER BY PD.ordering";
                //echo $query;
                return $app->getrows($query);
            }
    }
    
    
    public function getNextAuctionDate(){
    
        $app = $GLOBALS['app'];
        $query = "SELECT 
                    
                    
                    DATE_FORMAT(
                                    DATE_ADD(
                                        DATE_ADD(
                                            auction_date,
                                            INTERVAL ".AUCTION_GRACE_MONTH." MONTH
                                        ),
                                        INTERVAL ".AUCTION_GRACE_DAYS." DAY
                                    ),
                                    '%d-%b-%Y'
                                ) AS auction_due_date
                    
                    FROM ".PRODUCT_AUCTION." as PA 
                    
                    WHERE auction_date >= DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL ".AUCTION_ENTRY_GRACE_DAYS." DAY ),'%Y-%m-%d')
                    ORDER BY auction_date DESC LIMIT 0,1";  
        $data  = $app->getrow($query);
        return $data[0];
        
    }

		public function getOverDueDate(){
				
				$now   = new DateTime;
				$clone = clone $now;   
				$clone->modify( '-92 day' );

				$date = $clone->format( '%d-%b-%Y' );
        $app = $GLOBALS['app'];
        $query = "SELECT 	usr.last_login,usr.email FROM ".PRODUCT_INTERESTS." as PI	WHERE PI.paid_on < '" .$date."'";
        $data  = $app->getrow($query);
        return $data[0];
        
    }
    
    public function getAuctionFilterDate(){
   
        $app = $GLOBALS['app'];
        $la_date = self::getLastAuctionDate();
        
        
        
        $s_date = date("Y-m-d",mktime(0, 0, 0, date("m"),   date("d")-AUCTION_ENTRY_GRACE_DAYS,   date("Y")));
        $e_date = date("Y-m-d",mktime(0, 0, 0, date("m"),   date("d")+AUCTION_ENTRY_GRACE_DAYS,   date("Y")));
        
        /*
        $d = date("d",$la_date);
        $m = date("d",$la_date);
        $Y = date("d",$la_date);
        
        $s_date = date("Y-m-d",mktime(0, 0, 0, $m,   $d-AUCTION_ENTRY_GRACE_DAYS, $y));
        $e_date = date("Y-m-d",mktime(0, 0, 0, $m,   $d+AUCTION_ENTRY_GRACE_DAYS, $y));
        */
        

        //echo $la_date."   ".$s_date."   ".$e_date."   ".LAST_AUCTION_DATE."   ".PRE_AUCTION_DATE; //exit;
        if($la_date > $s_date ){
            
            return PRE_AUCTION_DATE;
            
        
        }else{

            $app->access_table = SETTINGS;
            $app->save(array("config_value"=>LAST_AUCTION_DATE),array("config_name ="=>'PRE_AUCTION_DATE')); 
            
            return LAST_AUCTION_DATE;
        }
        
        
        
    
    }
    
    public function getLastAuctionDate(){
   
        $app = $GLOBALS['app'];
   
        $query = "SELECT 
                    
                    DATE_FORMAT(auction_date, '%Y-%m-%d')
                    
                    FROM ".PRODUCT_AUCTION." as PA 
                    
                    WHERE 1
                    ORDER BY auction_date DESC LIMIT 0,1";  
                    
        $data  = $app->getrow($query);

        if(count($data)>0){

            
            return $data[0];
        
        }else{
        
            return PRE_AUCTION_DATE;
        }
        
    
    }

    
    public function getProductSerialize($pd_ids, $auction_charge, $auction_amount,$serial_no){
    
        $app = $GLOBALS['app'];
         
        if(is_array($pd_ids)){
        
            $src_ids =  implode(",",$pd_ids);
        }
        elseif($prod_ids != ""){
            $src_ids = $pd_ids;
        }
        
        $query = "SELECT 

                    PD.id AS pd_id,
                    C.id AS c_id,
                    PRO.id AS pro_id,
                    
                    C.category AS category_name,
                    PRO.name AS product_name,
                    
                    PRO.product_with AS product_with,
                    PRO.product_without AS product_without

                    
                    FROM ".PLEDGE_DETAILS." as PD 
                    JOIN ".CATEGORIES." as C ON C.id = PD.category_id 
                    JOIN ".PRODUCTS." as PRO on PD.product_id = PRO.id
                    WHERE PD.id IN (".$src_ids.")";
        
        //echo $query;
                    
        $auct_pro_recds = $app->getrows($query);
        
        //echo "<br><br>Before<br>";
        //print_r($auct_pro_recds);
        
        
        for($i=0;$i<count($auct_pro_recds);$i++){
            $auct_pro_recds[$i]["auction_charge"] = $auction_charge[$auct_pro_recds[$i]["pd_id"]];
            $auct_pro_recds[$i]["auction_amount"] = $auction_amount[$auct_pro_recds[$i]["pd_id"]];
        }
        
        //echo "<br><br>After<br>";
        //print_r($auct_pro_recds);

        return serialize($auct_pro_recds);
    
    }
    
    public function getProductUnserialize($pa_id){
    
        $app = $GLOBALS['app'];
        
        $app->access_table = PRODUCT_AUCTION;
        $records = $app->find(array("auction_products"),array("conditions"=>array("id = "=>$pa_id)));
        
        return unserialize($records[0]["auction_products"]);

    }
    

}