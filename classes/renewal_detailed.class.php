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


class Renewal {
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
                            $s_key = $args[0];
                            $s_category = $args[1];
                            $search_condition .= ($s_key != "")? " AND  ( SELECT id FROM ".PRODUCTS." WHERE name LIKE '".$s_key."%')":"";
                            $search_condition .= ($s_category != "")? "AND PD.category_id=".$s_category."":"";
                            if(!is_superadmin($_SESSION['session_usergroupname'])){
                                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND P.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                            }
                            /*
                             * Return the Pledges list Count
                             */
                            $query = "SELECT P.id
                                FROM ".PLEDGE." as P LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
                                WHERE 1
                                ".$search_condition."
                                ORDER BY id DESC ";
                                $data = $app->getrows($query);
                                return $count = count($data);


     }

        public static function Listview() {
                /*
                 * Get the function parameters , if exist . And make the search condition
                 */

                $args = func_get_args();
                $app = $GLOBALS['app'];
                $s_key = $args[0];
                $s_category = $args[1];

                $search_condition .= ($s_key != "")? " AND  ( SELECT id FROM ".PRODUCTS." WHERE name LIKE '".$s_key."%')":"";
                $search_condition .= ($s_category != "")? "AND PD.category_id = ".$s_category."":"";
                
                if(!is_superadmin($_SESSION['session_usergroupname'])){
                    $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND P.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                }
                $count = self::ListviewCount($s_key,$s_category);
                /*
                 * Return the Pledge list
                 */
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT DISTINCT P.*, C.name as customer_name, IF(P.status = 0, 'disable', 'enable') AS status_text, B.name as branch_name
                                FROM ".PLEDGE." as P
                                LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
                                LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                                LEFT JOIN ".BRANCH." as B ON P.branch_id = B.id
                                WHERE 1 ".$search_condition."
                                ORDER BY P.id DESC ". Paging::get_sql_limit();
                    //echo "Iside Pledge = ".$query;
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
                $query = "SELECT DISTINCT P.*,DATE_FORMAT(P.loan_date,'%d-%M-%Y %I:%i %p') as loan_date ,PD.category_id, PRO.name as name,
                PRO.id as pro_id, PD.gross_weight, PD.net_weight, PD.market_rate, C.category as category_name,CR.id as cust_id,
                CR.name as customer_name, CR.street as customer_street,CR.location as customer_location,CR.postal_code as customer_postal_code,
                CR.contact_no as customer_contact_no,CR.photo as customer_photo,if(SC.interest_type,'Floating','Flat') as interest_type,
                SC.name as scheme_name,SC.description
                FROM ".PLEDGE." as P
                JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
                JOIN ".CATEGORIES." as C ON C.id = PD.category_id
                JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id
                JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
                JOIN ".PRODUCTS." as PRO on PD.product_id = PRO.id
                WHERE P.id ='".$pledge_id."' ORDER BY PD.ordering";

                return $app->getrows($query);
            }
    }

    public static function SchemeIntrestDetail($scheme_id) {
            /*
             * Return the Scheme Intrest details
             */

            $app = $GLOBALS['app'];
            $query = "SELECT *
                FROM ".SCHEME." AS SC
                JOIN ".SCHEME_DETAILS." AS SD ON SD.scheme_id = SC.id
                WHERE SC.id ='".$scheme_id."' ORDER BY SD.from, SD.to";
                return $app->getrows($query);
    }

    public static function Detail_With_Interest($userid) {
            /*
             * Return the Pledge details
             */

            $app = $GLOBALS['app'];
            $count = $app->find_count(array('conditions' => array('id =' => $userid)));
            if ($count > 0) {
                $query = "SELECT DISTINCT P.*,DATE_FORMAT(P.loan_date,'%d-%M-%Y %I:%i %p') as loan_date ,PD.category_id,
                PRO.name as name,PRO.id as pro_id, PD.gross_weight, PD.net_weight, PD.market_rate, C.category as category_name,CR.id as cust_id,
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
                $query = "SELECT DISTINCT P.*,DATE_FORMAT(P.loan_date,'%d-%M-%Y %I:%i %p') as loan_date ,PD.category_id, PRO.name as name,PRO.id as pro_id, PD.gross_weight, PD.net_weight, PD.market_rate, C.category as category_name,CR.id as cust_id, CR.name as customer_name, CR.street as customer_street,CR.location as customer_location,CR.postal_code as customer_postal_code, CR.contact_no as customer_contact_no,CR.photo as customer_photo,if(SC.interest_type,'Floating','Flat') as interest_type,SC.name as scheme_name,SC.description FROM ".PLEDGE." as P LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id LEFT JOIN ".CUSTOMERS." as CR ON CR.id = P.customer_id LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id LEFT JOIN ".PRODUCTS." as PRO on PD.product_id = PRO.id
                            WHERE P.id ='".$userid."' ORDER BY PD.ordering";

                return $app->getrows($query);
            }
    }


        public static function RenewalListviewCount() {
                            
                            $args = func_get_args();
                             
                            $app = $GLOBALS['app'];
                            
                            /*
                             * Return the Branch Count
                             */
                            $query = "SELECT id
                                FROM ".RENEWAL."
                                WHERE 1
                                ORDER BY id DESC ";
                                $data = $app->getrows($query);
                                return $count = count($data);

                             
     }

        public static function RenewalListview() {
                /*
                 * Get the function parameters , if exist . And make the search condition
                 */
                
                $args = func_get_args();
                 
                $app = $GLOBALS['app'];
                
                /*
                 * Return the branch list
                 */
                $query = "SELECT id
                          FROM ".RENEWAL."  
                          WHERE 1  
                          ORDER BY id DESC ";
                    $data = $app->getrows($query);
                    $count = count($data);
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT R.id, R.new_loan_id, R.interest_amount, R.status, IF(R.status = 0, 'disable', 'enable') AS status_text, 
                            GROUP_CONCAT(PD.product_id ORDER BY R.id SEPARATOR ',') AS product_id
                                FROM ".RENEWAL." as R
                                LEFT JOIN ".PLEDGE_DETAILS." as PD ON PD.pledge_id = R.old_loan_id  
                                WHERE 1 GROUP BY R.id ORDER BY R.id DESC ". Paging::get_sql_limit();
                                
        
                    return $app->getrows($query);
                }
    }

    public static function RenewalDetail($userid) {
            /*
             * Return the branch details
             */
            $app = $GLOBALS['app'];
            $count = $app->find_count(array('conditions' => array('id = ' => $userid)));
            if ($count > 0) {
                $query = "SELECT interest.*,pledge.loan_amount,pledge_details.product_id,scheme.name,scheme.description
                            FROM ".RENEWAL." as interest 
                            LEFT JOIN ".PLEDGE." as pledge on interest.old_loan_id = pledge.id
                            LEFT JOIN ".PLEDGE_DETAILS." as pledge_details on pledge_details.pledge_id = pledge.id
                            LEFT JOIN ".SCHEME." as scheme on scheme.id = pledge.scheme_id
                            WHERE interest.id ='".$userid."'";                  
                return $app->getrows($query);
            }
    }




}
