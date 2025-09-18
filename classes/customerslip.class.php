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


class CustomerSlip {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('customer_id','customer_name', 'scheme_id', 'loan_amount','reference_name', 'service_charge','category_id','product_name','reference_address','gross_weight','net_weight');
        public $check_duplication = array('code');


        public static $check_required_text = 'Required Fields Missing!';
        public static $check_duplication_text = 'Pledge Code Name Already exists!';

        public static function ListviewCount() {
            /*
             * Get the function parameters , if exist . And make the search condition
             */

            $args = func_get_args();

            $app = $GLOBALS['app'];
            
            /* Search Query */                
            $s_key = $args[0];
            $s_cus = $args[1];
            $s_location = $args[2];
            $s_fhw = $args[3];
            $s_branch = $args[4];
            $s_loan = $args[5];

            $search_condition  = " AND  ( P.status = 1)"; 
            $search_condition .= ($s_key != "")? " AND  ( C.name LIKE '".$s_key."%')":"";
            $search_condition .= ($s_cus != "")? "AND (C.id LIKE '".$s_cus."' OR C.customer_id LIKE '".$s_cus."')":"";
            $search_condition .= ($s_location != "")? "AND ( C.street LIKE '".$s_location."%' OR C.location LIKE '".$s_location."%')  ":"";
            $search_condition .= ($s_fhw != "")? "AND ( C.relation_name LIKE '".$s_fhw."%' )  ":"";
            $search_condition .= ($s_branch != "")? "AND ( C.branch_id  = '".$s_branch."' )  ":"";
            $search_condition .= ($s_loan != "")? "AND ( P.loan_id  LIKE '".$s_loan."'  OR P.id  = '".$s_loan."')  ":"";

            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND P.branch_id  = '".$_SESSION['session_user_branch_id']."'":"";
            }


            /*
             * Return the Pledges list Count
             */
            $query = "SELECT DISTINCT P.id 
            FROM ".PLEDGE." as P 
            LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
            LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
            LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
            LEFT JOIN ".CATEGORIES." as CT ON PD.category_id = CT.id 
            WHERE 1 ".$search_condition." ORDER BY id DESC ";
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
            $s_key = $args[0];
            $s_cus = $args[1];
            $s_location = $args[2];
            $s_fhw = $args[3];
            $s_branch = $args[4];
            $s_loan = $args[5];

            $search_condition  = " AND  ( P.status = 1)"; 
            $search_condition .= ($s_key != "")? " AND  ( C.name LIKE '".$s_key."%')":"";
            $search_condition .= ($s_cus != "")? "AND (C.id LIKE '".$s_cus."' OR C.customer_id LIKE '".$s_cus."')":"";
            $search_condition .= ($s_location != "")? "AND ( C.street LIKE '".$s_location."%' OR C.location LIKE '".$s_location."%')  ":"";
            $search_condition .= ($s_fhw != "")? "AND ( C.relation_name LIKE '".$s_fhw."%' )  ":"";
            $search_condition .= ($s_branch != "")? "AND ( C.branch_id  = '".$s_branch."' )  ":"";
            $search_condition .= ($s_loan != "")? "AND ( P.loan_id  LIKE '".$s_loan."'  OR P.id  = '".$s_loan."')  ":"";

            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND P.branch_id  = '".$_SESSION['session_user_branch_id']."'":"";
            }

                
                 $count = self::ListviewCount($s_key,$s_cus,$s_location,$s_fhw,$s_branch, $s_loan);
                 
                /*
                 * Return the Pledge list
                 */
              

                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT DISTINCT P.*,
                                B.name AS branch_name,
                                C.branch_id AS customer_branch,
                                P.branch_id AS pledged_branch,
                                C.customer_id AS customer_code,
                                GROUP_CONCAT(CT.category,'-',PRO.name ORDER BY CT.category DESC SEPARATOR ', ') AS catgroup,
                                P.item_count AS item , 
                                DATE_FORMAT(P.loan_date,'%d-%M-%Y') as loan_date, 
                                PD.product_items, P.total_net_weight, P.item_count,
                                C.name as customer_name, IF(P.status = 0, 'disable', 'enable') AS status_text, CT.category as category_name
                                FROM ".PLEDGE." as P 
                                LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
                                LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                                LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
                                LEFT JOIN ".CATEGORIES." as CT ON PD.category_id = CT.id            
                                LEFT JOIN ".PRODUCTS." as PRO ON PD.product_id = PRO.id            
                                WHERE 1 ".$search_condition." GROUP BY P.id
                                ORDER BY P.created_datetime DESC  ". Paging::get_sql_limit();
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
                            DATE_FORMAT(P.loan_date,'%d-%b-%Y %I:%i %p') as loan_date ,PD.category_id, 
                            PD.category_id, PD.gross_weight, PD.net_weight,
                            PD.product_items, P.total_net_weight, P.item_count, P.loan_amount,
                            P.loan_id,P.total_gross_weight,
                            CR.branch_id AS customer_branch,
                            P.branch_id AS pledged_branch,
                            CR.customer_id AS customer_code,
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
                            LEFT JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id 
                            LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
                            LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id 
                            LEFT JOIN ".PRODUCTS." as PRO on PD.product_id = PRO.id
                            WHERE P.id ='".$pledge_id."' AND P.status = 1 ORDER BY PD.ordering";
                //echo $query;
                return $app->getrows($query);
            }
    }

    public static function SchemeIntrestDetail($scheme_id, $loandate) {
            /*
             * Return the Scheme Intrest details
             */
            
            $app = $GLOBALS['app'];
            $query = "SELECT 
                    DATE_FORMAT(DATE_ADD('".$loandate."', INTERVAL SD.from MONTH),'%M-%Y') AS start ,
                    DATE_FORMAT(DATE_ADD('".$loandate."', INTERVAL SD.to MONTH),'%M-%Y') AS end ,
                    SD.rate_of_interest
                    FROM ".SCHEME." AS SC 
                    JOIN ".SCHEME_DETAILS." AS SD ON SD.scheme_id = SC.id 
                    WHERE SC.id ='".$scheme_id."' ORDER BY SD.above, SD.upto";
            return $app->getrows($query);
    }

    public static function Detail_With_Interest($userid) {
            /*
             * Return the Pledge details
             */
            
            $app = $GLOBALS['app'];
            $count = $app->find_count(array('conditions' => array('id =' => $userid)));
            if ($count > 0) {
                $query = "SELECT DISTINCT P.*,DATE_FORMAT(P.loan_date,'%d-%b-%Y %I:%i %p') as loan_date ,PD.category_id, 
                PRO.name as name,PRO.id as pro_id, PD.gross_weight, PD.net_weight, P.market_rate, C.category as category_name,CR.id as cust_id, 
                CR.name as customer_name, CR.street as customer_street,CR.location as customer_location,CR.postal_code as customer_postal_code, 
                CR.contact_no as customer_contact_no,CR.photo as customer_photo,if(SC.interest_type,'Floating','Flat') as interest_type,
                SC.name as scheme_name,SC.description,sum(PI.interest_amount) as interest_amount 
                FROM ".PLEDGE." as P 
                LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
                LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id 
                LEFT JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id 
                LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
                LEFT JOIN ".PRODUCTS." as PRO on PD.product_id = PRO.id
                LEFT JOIN ".PRODUCT_INTERESTS." as PI on PI.loan_id = P.id
                
                WHERE P.id ='".$userid."' 
                ORDER BY PD.ordering";

                return $app->getrows($query);
            }
    }
    
    public static function Detail_Renewal($userid) {
            /*
             * Return the Pledge details
             */
            
            $app = $GLOBALS['app'];
            $count = $app->find_count(array('conditions' => array('id =' => $userid)));
            if ($count > 0) {
                $query = "SELECT DISTINCT P.*,DATE_FORMAT(P.loan_date,'%d-%M-%Y %I:%i %p') as loan_date ,PD.category_id, PRO.name as name,PRO.id as pro_id, PD.gross_weight, PD.net_weight, P.market_rate, C.category as category_name,CR.id as cust_id, CR.name as customer_name, CR.street as customer_street,CR.location as customer_location,CR.postal_code as customer_postal_code, CR.contact_no as customer_contact_no,CR.photo as customer_photo,if(SC.interest_type,'Floating','Flat') as interest_type,SC.name as scheme_name,SC.description FROM ".PLEDGE." as P LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id LEFT JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id LEFT JOIN ".PRODUCTS." as PRO on PD.product_id = PRO.id
                            WHERE P.id ='".$userid."' ORDER BY PD.ordering";

                return $app->getrows($query);
            }
    }


}
