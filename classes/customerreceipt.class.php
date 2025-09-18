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


class CustomerReceipt {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('customer_id','customer_name', 'scheme_id', 'loan_amount','reference_name', 'service_charge','category_id','product_name','reference_address','gross_weight','net_weight');
        public $check_duplication = array('code');


        public static $check_required_text = 'Required Fields Missing!';
        public static $check_duplication_text = 'Pledge Code Name Already exists!';

        public function SubbranchSet($set_sb) {
        
            // Changed dssk sub branch privilege
            /*
             * Get Sub-Branch list
             */
            $app = $GLOBALS['app'];
            if ($_SESSION['session_usertype'] != 1 && $set_sb == 0) {
                 $query_sub_branch = "SELECT id, branch_id, sub_branch_code FROM mf_sub_branch WHERE status = 1 AND branch_id  = " . $_SESSION['session_user_branch_id'];
                $data = $app->getrows($query_sub_branch);
                $sub_count = count($data);
                $find_id = "";
                for ($i = 0; $i < $sub_count; $i++) {
                    $find_id .= $data[$i]['id'] . ',';
                }
                $find_id = rtrim($find_id, ',');
                return ($find_id == "" ? "":" AND (P.sub_branch_id IN($find_id))");
            }
            
        }

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

             // Sub Branch Privilege
            $set_sb = 0;
            $search_condition .= self::SubbranchSet($set_sb);

            /*
             * Return the Pledges list Count
             */
            $query = "SELECT DISTINCT P.id 
            FROM ".PLEDGE." as P 
            LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
            LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
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

                 // Sub Branch Privilege
                $set_sb = 0;
                $search_condition .= self::SubbranchSet($set_sb);
                
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
                                P.total_gross_weight AS gross, P.total_net_weight AS net, 
                                P.item_count AS item , DATE_FORMAT(P.loan_date,'%d-%b-%Y') as loan_date, 
                                SC.name as scheme_name, if(SC.interest_type,'Floating','Flat') as interest_type, 
                                C.name as customer_name, IF(P.status = 0, 'disable', 'enable') AS status_text
                                FROM ".PLEDGE." as P 
                                LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                                LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
                                LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
                                WHERE 1 ".$search_condition."
                                ORDER BY P.created_datetime DESC ". Paging::get_sql_limit();
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
            $count = $app->find_count(array('conditions' => array('id =' => $pledge_id)));
            if ($count > 0) {
                /*  $query = "SELECT DISTINCT P.*,DATE_FORMAT(P.loan_date,'%d-%b-%Y %I:%i %p') as loan_date ,
                                P.loan_date as date_of_loan, P.item_count,
                                CR.branch_id AS customer_branch,
                                P.branch_id AS pledged_branch,
								B.organization_name,B.address,B.location,
                                CR.customer_id AS customer_code,
                                PD.category_id, PD.gross_weight, PD.net_weight, PD.product_items,
                                PRO.name as product_name,PRO.product_with,PRO.product_without,
                                PRO.id as pro_id, PD.gross_weight, PD.net_weight, P.market_rate, C.category as category_name,CR.id as cust_id, 
                                CR.id as customer_id, CR.name as customer_name, CR.street as customer_street,CR.location as customer_location,CR.district,
                                CR.postal_code as customer_postal_code, CR.email,CR.contact_no,CR.relation_name,CR.relationship_type,
                                CR.religion,CR.religion_category, if(CR.land_holding=1,'Yes','No') AS land_holding,
                                CR.contact_no as customer_contact_no,CR.photo as customer_photo,
                                if(SC.interest_type,'Floating','Flat') as interest_type,
                                SC.name as scheme_name,SC.description, SC.loan_rate 
                                FROM ".PLEDGE." as P 
                                LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
								LEFT JOIN ".BRANCH." as B ON B.id = P.branch_id
                                LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id 
                                LEFT JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id 
                                LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
                                LEFT JOIN ".PRODUCTS." as PRO on PD.product_id = PRO.id
                                WHERE P.id ='".$pledge_id."' AND P.status = 1 ORDER BY PD.ordering"; */
								
                                //Rajesh 26-05-2020 live query
								/*$query = "SELECT DISTINCT P.*,DATE_FORMAT(P.loan_date,'%d-%b-%Y %I:%i %p') as loan_date ,
                                P.loan_date as date_of_loan, P.item_count,
                                CR.branch_id AS customer_branch,
                                P.branch_id AS pledged_branch,
								B.organization_name,B.address,B.location,
                                CR.customer_id AS customer_code,
                                PD.category_id, PD.gross_weight, PD.net_weight, PD.product_items,
                                CONCAT(PN.pre_name,' ',PRO.name) as product_name,PRO.product_with,PRO.product_without,
                                PRO.id as pro_id, PD.gross_weight, PD.net_weight, P.market_rate, C.category as category_name,CR.id as cust_id, 
                                CR.id as customer_id, CR.name as customer_name, CR.street as customer_street,CR.location as customer_location,CR.district,
                                CR.postal_code as customer_postal_code, CR.email,CR.contact_no,CR.relation_name,CR.relationship_type,
                                CR.religion,CR.religion_category, if(CR.land_holding=1,'Yes','No') AS land_holding,
                                CR.contact_no as customer_contact_no,CR.photo as customer_photo,
                                if(SC.interest_type,'Floating','Flat') as interest_type,
                                SC.name as scheme_name,SC.description, SC.loan_rate 
                                FROM ".PLEDGE." as P 
                                LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
								LEFT JOIN ".BRANCH." as B ON B.id = P.branch_id
                                LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id 
                                LEFT JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id 
                                LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
								LEFT JOIN ".PRENAME." as PN ON PN.id = PD.pre_name_id								
                                LEFT JOIN ".PRODUCTS." as PRO on PD.product_id = PRO.id
                                WHERE P.id ='".$pledge_id."' AND P.status = 1 ORDER BY PD.ordering";*/

                                //Rajesh 26-05-2020 staging query updated

                                $query = "SELECT DISTINCT P.*,DATE_FORMAT(P.loan_date,'%d-%b-%Y %I:%i %p') as loan_date ,
                                P.loan_date as date_of_loan, P.item_count,
                                CR.branch_id AS customer_branch,
                                P.branch_id AS pledged_branch,
                                B.organization_name,B.address,B.location,
                                CR.customer_id AS customer_code,
                                PD.category_id, PD.product_with as pledge_product_with, PD.gross_weight, PD.net_weight, PD.product_items, PN.pre_name,PW.pre_name as pledge_product_with_name,
                                CONCAT(PRO.name) as product_name,PRO.product_with,PRO.product_without,
                                PRO.id as pro_id, PD.gross_weight, PD.net_weight, P.market_rate, C.category as category_name,CR.id as cust_id, 
                                CR.id as customer_id, CR.name as customer_name, CR.street as customer_street,CR.location as customer_location,CR.district,
                                CR.postal_code as customer_postal_code, CR.email,CR.contact_no,CR.relation_name,CR.relationship_type,
                                CR.religion,CR.religion_category, if(CR.land_holding=1,'Yes','No') AS land_holding,
                                CR.contact_no as customer_contact_no,CR.photo as customer_photo,
                                if(SC.interest_type,'Floating','Flat') as interest_type,
                                SC.name as scheme_name,SC.description, SC.loan_rate 
                                FROM ".PLEDGE." as P 
                                LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
                                LEFT JOIN ".BRANCH." as B ON B.id = P.branch_id
                                LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id 
                                LEFT JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id 
                                LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
                                LEFT JOIN ".PRENAME." as PN ON PN.id = PD.pre_name_id   

                                LEFT JOIN ".PRODUCTS." as PRO on PD.product_id = PRO.id

                                LEFT JOIN ".PRODUCT_WITH." as PW ON PW.id = PD.product_with   
                                WHERE P.id ='".$pledge_id."' AND P.status = 1 ORDER BY PD.ordering";
                
                return $app->getrows($query);
            }
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
                
                WHERE P.id ='".$userid."'  AND P.status = 1 
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
                $query = "SELECT DISTINCT P.*,DATE_FORMAT(P.loan_date,'%d-%b-%Y %I:%i %p') as loan_date ,PD.category_id, PRO.name as name,PRO.id as pro_id, PD.gross_weight, PD.net_weight, P.market_rate, C.category as category_name,CR.id as cust_id, CR.name as customer_name, CR.street as customer_street,CR.location as customer_location,CR.postal_code as customer_postal_code, CR.contact_no as customer_contact_no,CR.photo as customer_photo,if(SC.interest_type,'Floating','Flat') as interest_type,SC.name as scheme_name,SC.description FROM ".PLEDGE." as P LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id LEFT JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id LEFT JOIN ".PRODUCTS." as PRO on PD.product_id = PRO.id
                            WHERE P.id ='".$userid."' AND P.status = 1  ORDER BY PD.ordering";

                return $app->getrows($query);
            }
    }


}
