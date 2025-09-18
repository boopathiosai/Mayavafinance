<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE                   
// Module           :  Brand class          
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Vinothkuamr V
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  05-Feb-2010
//**************************************************************************
// Description      :  This is the Branch class  for Mayavafinance.
//**************************************************************************

class Renewal {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('old_loan_id','new_loan_id','loan_amount','new_scheme');
        
        public $check_required_labels = array('Old Loan Number', 'New Loan Number', 'Loan Amount', 'New Scheme');
        
        public static $check_required_text = 'Required Fields Missing!'; 
        
        public static function ListviewCount() {
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
                 * Return the Branch Count
                 */
                $query = "SELECT DISTINCT P.id
                    FROM ".PLEDGE." as P 
                    LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
                    LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                    LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
                    WHERE 1 ".$search_condition."
                    ORDER BY id DESC ";
                    $data = $app->getrows($query);
                    return $count = count($data);
       }

        public static function Listview() {

            #Rajesh Mar 5, 2021
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

             #Date Filter for two days
            $start_date = date('Y-m-d',strtotime("-1 days"));
            $end_date = date('Y-m-d', strtotime("+1 days"));        
            if($s_branch != "" && $s_loan == "") {
               $search_condition .= " AND  ( P.loan_date BETWEEN '".$start_date."' AND '".$end_date."')";
            }

            //$search_condition .= ($s_key != "")? " AND  ( C.name LIKE '".$s_key."%')":"";
            //$search_condition .= ($s_cus != "")? "AND (C.id LIKE '".$s_cus."' OR C.customer_id LIKE '".$s_cus."')":"";
            //$search_condition .= ($s_location != "")? "AND ( C.street LIKE '".$s_location."%' OR C.location LIKE '".$s_location."%')  ":"";
            //$search_condition .= ($s_fhw != "")? "AND ( C.relation_name LIKE '".$s_fhw."%' )  ":"";
            $search_condition .= ($s_branch != "")? "AND ( P.branch_id  = '".$s_branch."' )  ":"";
            $search_condition .= ($s_loan != "")? "AND ( P.loan_id  LIKE '".$s_loan."'  OR P.id  = '".$s_loan."')  ":"";

            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND P.branch_id  = '".$_SESSION['session_user_branch_id']."'":"";
            }
            /*
             * Return the branch list
             */
            $query_count = "SELECT 
                            P.id AS pledge_id
                            FROM mf_pledge as P LEFT JOIN mf_pledge_details as PD ON P.id = PD.pledge_id LEFT JOIN mf_categories as CT on CT.id = PD.category_id LEFT JOIN mf_products as PT on PT.id = PD.product_id LEFT JOIN mf_customers as C ON C.id = P.customer_id LEFT JOIN mf_branch as B ON P.branch_id = B.id 
                            WHERE 1 ".$search_condition;
            $data = $app->getrows($query_count);
            $count = count($data); 
            //$count = self::ListviewCount($s_key,$s_cus,$s_location,$s_fhw,$s_branch,$s_loan);
            if ($count > 0) {
                Paging::set_max( $count );
                Paging::calc();// calculation for paging
                $query = "SELECT 
                            P.id AS pledge_id, 
                            P.loan_id,
                            B.name AS branch_name, 
                            C.name AS customer_name, 
                            GROUP_CONCAT(CONCAT(CT.category,'-',PT.name) ORDER BY CT.category,PT.name ASC SEPARATOR '
                            ') as products, 
                            P.item_count,
                            P.total_gross_weight AS gross,
                            P.total_net_weight AS net, 
                            P.loan_amount AS loan_amount,
                            P.branch_id AS pledged_branch
                            FROM mf_pledge as P LEFT JOIN mf_pledge_details as PD ON P.id = PD.pledge_id LEFT JOIN mf_categories as CT on CT.id = PD.category_id LEFT JOIN mf_products as PT on PT.id = PD.product_id LEFT JOIN mf_customers as C ON C.id = P.customer_id LEFT JOIN mf_branch as B ON P.branch_id = B.id 
                            WHERE 1 ".$search_condition." 
                             GROUP BY P.id ORDER BY P.created_datetime DESC ". Paging::get_sql_limit();
                            
                //echo $query; 
                return $app->getrows($query);
            }
        }

    public static function ListviewOld() {
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
                 * Return the branch list
                 */
                $count = self::ListviewCount($s_key,$s_cus,$s_location,$s_fhw,$s_branch,$s_loan);
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT DISTINCT
                                B.name AS branch_name,
                                C.branch_id AS customer_branch,
                                P.branch_id AS pledged_branch,
                                C.customer_id AS customer_code,
                                P.id AS pledge_id, P.loan_id,
                                P.total_gross_weight AS gross, P.total_net_weight AS net, 
                                DATE_FORMAT(P.loan_date,'%d-%b-%y') as loan_date, P.loan_amount AS loan_amount,
                                P.loan_amount,P.item_count,P.total_gross_weight AS gross_weight,
                                PD.product_id, C.name AS customer_name, SC.name AS scheme, SC.interest_type, 
                                SC.name as scheme_name,
                                GROUP_CONCAT(CONCAT(CT.category,'-',PT.name) ORDER BY CT.category,PT.name ASC SEPARATOR ' <br> ') as products,
                                C.name as customer_name, IF(P.status = 0, 'disable', 'enable') AS status_text                                
                                FROM ".PLEDGE." as P 
                                LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
                                LEFT JOIN ".CATEGORIES." as CT on CT.id = PD.category_id
                                LEFT JOIN ".PRODUCTS." as PT on PT.id = PD.product_id
                                LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                                LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
                                LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id 
                                WHERE 1 ".$search_condition." 
                                 GROUP BY P.id ORDER BY P.created_datetime DESC ". Paging::get_sql_limit();
                                
                    //echo $query; 
                    return $app->getrows($query);
                }
    }


    public static function Detail($pledge_id, $renew_id='') {
            /*
             * Return the branch details
             */
            $app = $GLOBALS['app'];
            
            $rQuery = ($renew_id!='') ? "AND RN.id ='".$renew_id."'" : "";
            $query = "SELECT DISTINCT 
                    P.id AS pledge_id,  
                    P.total_amount_given, P.market_rate, PD.product_items,
                    CR.branch_id AS customer_branch,
                    P.branch_id AS pledged_branch,
                    CR.customer_id AS customer_code,
                    P.*,P.loan_date AS pledge_date, DATE_FORMAT(P.loan_date,'%d-%b-%Y %I:%i %p') as loan_date ,
                    PD.category_id, 
                    CONCAT(PRO.name,' - [ With: ' , PRO.product_with,' , Without: ', PRO.product_without,' ] ') as name,
                    PRO.id as pro_id, PD.gross_weight, PD.net_weight, 
                    C.category as category_name,CR.id as cust_id, 
                    CR.name as customer_name, CR.street as customer_street,
                    CR.location as customer_location,CR.postal_code as customer_postal_code, 
                    CR.contact_no as customer_contact_no,
                    CR.photo as customer_photo, CR.sign as customer_sign,
                    if(SC.interest_type,'Floating','Flat') as interest_type,
                    SC.id AS scheme_id,SC.name as scheme_name,SC.description , SC.service_charge,SC.loan_rate 
                    FROM ".PLEDGE." as P 
                    JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
                    
                    LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id 
                    LEFT JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id 
                    LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
                    LEFT JOIN ".PRODUCTS." as PRO on PD.product_id = PRO.id
                    WHERE P.id ='".$pledge_id."' ".$rQuery." ORDER BY PD.ordering";
                                //echo $query; 
                return $app->getrows($query);
            
    }
    
    public static function ProductDetail($pledge_id) {
            /*
             * Return the Pledge details
             */
            
            $app = $GLOBALS['app'];
            
            
                $query = "SELECT DISTINCT P.*,P.loan_date AS pledge_date, P.id AS old_loan_id, 
                DATE_FORMAT(P.loan_date,'%d-%b-%Y %I:%i %p') as loan_date ,PD.category_id, PRO.name as name,
                PRO.id as pro_id, PD.gross_weight, PD.net_weight, PD.market_rate, 
                C.category as category_name,CR.id as cust_id, 
                CR.name as customer_name, CR.street as customer_street,CR.location as customer_location,
                CR.postal_code as customer_postal_code, 
                CR.contact_no as customer_contact_no,CR.photo as customer_photo,
                if(SC.interest_type,'Floating','Flat') as interest_type,
                SC.id as scheme_id,SC.name as scheme_name,SC.description , P.loan_amount
                FROM ".PLEDGE." as P 
                JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id 
                JOIN ".CATEGORIES." as C ON C.id = PD.category_id 
                JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id 
                JOIN ".SCHEME." as SC ON P.scheme_id = SC.id 
                JOIN ".PRODUCTS." as PRO on PD.product_id = PRO.id
                WHERE P.id ='".$pledge_id."' ORDER BY PD.ordering";
                return $app->getrows($query);
            
    }
    
    // Getting all require Intrest and amount payment for the renewal process
    public function getRenewalDetails($pledge_id){
        
        $retData = PayInterest::getInterestDetails($pledge_id);
        
        return $retData; 
    }

}