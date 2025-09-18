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
class ProductPledge {
    /*
     * Define the required fields and return text
     */

    //public $check_required = array('customer_id', 'scheme_id', 'loan_amount','service_charge','total_gross_weight', 'total_net_weight', 'item_count');
//		public $check_required = array( 'customer_id', 'scheme_id', 'loan_amount','total_gross_weight', 'total_net_weight', 'item_count');
    public $check_required = array('sub_branch_id', 'customer_id', 'scheme_id', 'loan_amount', 'total_gross_weight', 'total_net_weight', 'item_count');
    public $check_duplication = array('loan_id');
    //public $check_required_labels = array('Customer Information', 'Loan Scheme', 'Loan Amount', 'Loan Service Charge', 'Total Gross Weight','Total Net Weight', 'Total Item Count');
    public $check_required_labels = array('Sub Branch', 'Customer Information', 'Loan Scheme', 'Loan Amount', 'Total Gross Weight', 'Total Net Weight', 'Total Item Count');
    public $check_duplication_labels = array('Pledge Code');
    public static $check_required_text = 'Required Fields Missing!';
    public static $check_duplication_text = 'Already exists!';
    
      public function SubbranchSet() {
        
        // Changed dssk sub branch privilege
        /*
         * Get Sub-Branch list
         */
//        echo $set_sb;
        $app = $GLOBALS['app'];
        if ($_SESSION['session_usertype'] != 1) {
            $query_sub_branch = "SELECT id, branch_id, sub_branch_code FROM mf_sub_branch WHERE status = 1 AND branch_id  = " . $_SESSION['session_user_branch_id'];
            $data = $app->getrows($query_sub_branch);
            $sub_count = count($data);
            $find_id = "";
            for ($i = 0; $i < $sub_count; $i++) {
                $find_id .= $data[$i]['id'] . ',';
            }
            $find_id = rtrim($find_id, ',');

            return "AND (P.sub_branch_id IN($find_id))";
        }
        
    }

    public static function ListviewCount() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();

        $app = $GLOBALS['app'];
        $s_key = $args[0];
        /* $s_category = $args[1];
          $search_condition .= ($s_key != "")? " AND  ( SELECT id FROM ".PRODUCTS." WHERE name LIKE '".$s_key."%')":"";
          $search_condition .= ($s_category != "")? "AND PD.category_id=".$s_category."":""; */

        /* Search Query */
        $s_key = $args[0];
        $s_cus = $args[1];
        $s_location = $args[2];
        $s_fhw = $args[3];
        $s_branch = $args[4];
        $s_loan = $args[5];

        /* changes on 15-12-2016 */
        $type_of_page = $args[6];
        $s_mobile = $args[7];

        if($type_of_page=="" && $s_key == "" && $s_cus == "" &&  $s_location == "" && $s_fhw == "" &&  $s_branch == "" &&  $s_loan == "" && $type_of_page == "" && $s_mobile == "" )
         {
            //   $condition =$args[6];
            $search_condition .= " AND (P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW() + INTERVAL 3 DAY)";
            //      print_r($args)."<br>";
        }
        /* changes done */

// $apple=$search_condition;
        //$search_condition  = " AND  ( P.status = 1)";
        $search_condition .= ($s_key != "") ? " AND  ( C.name LIKE '" . $s_key . "%')" : "";
        $search_condition .= ($s_cus != "") ? "AND (C.id LIKE '" . $s_cus . "' OR C.customer_id LIKE '" . $s_cus . "')" : "";
        $search_condition .= ($s_location != "") ? "AND ( C.street LIKE '" . $s_location . "%' OR C.location LIKE '" . $s_location . "%')  " : "";
        $search_condition .= ($s_fhw != "") ? "AND ( C.relation_name LIKE '" . $s_fhw . "%' )  " : "";
        $search_condition .= ($s_branch != "") ? "AND ( C.branch_id  = '" . $s_branch . "' )  " : "";
        $search_condition .= ($s_loan != "") ? "AND ( P.loan_id  LIKE '" . $s_loan . "'  OR P.id  = '" . $s_loan . "')  " : "";
        $search_condition .= ($s_mobile != "") ? "AND ( C.contact_no  = '" . $s_mobile . "' )  " : "";
        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND P.branch_id  = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }
        // Changed dssk sub branch privilege
        /*
         * Get Sub-Branch list
         */
        $search_condition .=  self::SubbranchSet();
        /* if($s_branch == 0) {

          $query_sub_branch = "SELECT id,sub_branch_id FROM ".PLEDGE." WHERE branch_id = '".$_SESSION['session_user_branch_id']."'";
          $data = $app->getrows($query_sub_branch);


          $sub_count = count($data);
          $find_id = "";
          for($i=0; $i<$sub_count;$i++)
          {

          $find_id.= $data[$i]['id'].',';

          }
          $find_id= rtrim($find_id, ',');
          $search_condition .= "AND (P.sub_branch_id IN($find_id))";


          } */

        /*
         * Return the Pledges list Count
         */
        $query = "SELECT DISTINCT P.id
                FROM " . PLEDGE . " as P
                LEFT JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id
                LEFT JOIN " . CUSTOMERS . " as C ON C.id = P.customer_id
                LEFT JOIN " . BRANCH . " as B ON C.branch_id = B.id
                WHERE 1
                " . $search_condition . "
                ORDER BY id DESC ";
//exit;
        // echo $query."<br>";
        // $query =   "SELECT  P.id
        //                    FROM mf_pledge AS P USE INDEX (  `pri_cust_loc` )
        //                    INNER JOIN mf_pledge_details AS PD ON P.id = PD.pledge_id
        //                    INNER JOIN mf_customers AS C ON C.id = P.customer_id
        //                    INNER JOIN mf_branch AS B ON B.id = C.branch_id
        //                    WHERE 1
        //                    ORDER BY P.`id` DESC";
        $data = $app->getrows($query);

        //print_r($condition);
        //unset($condition);
        //unset($condition);


        return $count = count($data);
    }

    public static function Listview() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];
        $paging = $GLOBALS['paging'];

        /* $s_key = $args[0];
          $s_category = $args[1];

          $search_condition .= ($s_key != "")? " AND  ( SELECT id FROM ".PRODUCTS." WHERE name LIKE '".$s_key."%')":"";
          $search_condition .= ($s_category != "")? "AND PD.category_id = ".$s_category."":""; */


        /* Search Query */
        $s_key = $args[0];
        $s_cus = $args[1];
        $s_location = $args[2];
        $s_fhw = $args[3];
        $s_branch = $args[4];
        $s_loan = $args[5];

        /* changes on 15-12-2016 */
        $type_of_page = $args[6];
        // echo "dd".$type_of_page;
        $s_mobile = $args[7];
        // Changed dssk sub branch privilege
        if($_SESSION['session_usertype'] != 1){
        if($type_of_page=="" && $s_key == "" && $s_cus == "" &&  $s_location == "" && $s_fhw == "" &&  $s_branch == "" &&  $s_loan == "" && $type_of_page == "" && $s_mobile == "" )
         {
            $search_condition = " AND  (P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW() + INTERVAL 3 DAY)";
        }
        }
        /* changes done */


        $search_condition .= ($s_key != "") ? " AND  ( C.name LIKE '" . $s_key . "%')" : "";
        $search_condition .= ($s_cus != "") ? "AND (C.id LIKE '" . $s_cus . "' OR C.customer_id LIKE '" . $s_cus . "')" : "";
        $search_condition .= ($s_location != "") ? "AND ( C.street LIKE '" . $s_location . "%' OR C.location LIKE '" . $s_location . "%')  " : "";
        $search_condition .= ($s_fhw != "") ? "AND ( C.relation_name LIKE '" . $s_fhw . "%' )  " : "";
        $search_condition .= ($s_branch != "") ? "AND ( C.branch_id  = '" . $s_branch . "' )  " : "";
        $search_condition .= ($s_loan != "") ? "AND ( P.loan_id  LIKE '" . $s_loan . "'  OR P.id  = '" . $s_loan . "')  " : "";
        $search_condition .= ($s_mobile != "") ? "AND ( C.contact_no  = '" . $s_mobile . "' )  " : "";
        if (!is_superadmin($_SESSION['session_usergroupname'])) {
            $search_condition .= ($_SESSION['session_user_branch_id'] != "") ? " AND P.branch_id  = '" . $_SESSION['session_user_branch_id'] . "'" : "";
        }
///echo "Branch :".$s_branch;
        //if($s_branch == 0) {

        /* echo $query_sub_branch = "SELECT id,sub_branch_id,branch_id FROM ".PLEDGE." WHERE branch_id = '".$_SESSION['session_user_branch_id']."'"; */

        /* $query_sub_branch = "SELECT id,sub_branch_id,branch_id FROM ".PLEDGE." WHERE branch_id in( '".$_SESSION['session_user_branch_id']."','".$s_branch."')";

          $data = $app->getrows($query_sub_branch);


          $sub_count = count($data);
          $find_id = "";
          for($i=0; $i<$sub_count;$i++)
          {

          $find_id.= $data[$i]['sub_branch_id'].',';

          }
          $find_id= rtrim($find_id, ',');
          $search_condition .= "OR (P.sub_branch_id IN($find_id))";


          } */

        $count = self::ListviewCount($s_key, $s_cus, $s_location, $s_fhw, $s_branch, $s_loan, $type_of_page, $s_mobile);
        
        /*if($_SESSION['session_usertype'] != 1){
        $query_sub_branch = "SELECT id, branch_id, sub_branch_code FROM mf_sub_branch WHERE status = 1 AND branch_id  = ".$_SESSION['session_user_branch_id'];
        $data = $app->getrows($query_sub_branch); 
        $sub_count = count($data);
        $find_id = "";
        for($i=0; $i<$sub_count;$i++){
            $find_id.= $data[$i]['id'].',';
        }
        $find_id= rtrim($find_id, ',');
        
        $search_condition .= "AND (P.sub_branch_id IN($find_id))";
        
        }*/
        // Changed dssk sub branch privilege
        /*
         * Get Sub-Branch list
         */
        $search_condition .=  self::SubbranchSet();
        /*
         * Return the Pledge list
         */
        if ($count > 0 || $sub_count > 0) {
            Paging::set_max($count);
            Paging::calc(); // calculation for paging
            //$paging->setupPaging( $count );
            // BEFORE INDEXING /12-Aug-2016/
            // $query = "SELECT DISTINCT
            //             P.*, C.name as customer_name,
            //             C.branch_id AS customer_branch,
            //             P.branch_id AS pledged_branch,
            //             C.customer_id AS customer_code,
            //             -- IF(DATE_FORMAT(P.loan_date,'%Y-%m-%d')=DATE_FORMAT(CURRENT_DATE,'%Y-%m-%d'), '1','0') AS editable,
            //             B.name AS branch_name,C.street,C.location,C.postal_code,
            //             IF(P.status = 1, 'enable', 'disable') AS status_text
            //             FROM ".PLEDGE." as P
            //             LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
            //             LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
            //             LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
            //             WHERE 1 ".$search_condition."
            //             ORDER BY P.created_datetime DESC ". Paging::get_sql_limit();



          

            $query = "SELECT  P.id,P.loan_id,P.status,P.total_gross_weight,P.loan_amount,C.name AS customer_name,P.loan_date,  C.street, C.location, C.postal_code
                                    FROM mf_pledge AS P 
                                    INNER JOIN mf_customers AS C ON C.id = P.customer_id
                                    WHERE 1 " . $search_condition . "
                                    ORDER BY P.`id` DESC" . Paging::get_sql_limit();




          // echo $query;
           
           // exit;
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
            $query = "SELECT DISTINCT P.*,P.loan_date AS pledge_date,
                CR.branch_id AS customer_branch,
                P.branch_id AS pledged_branch,
                CR.customer_id AS customer_code,
                DATE_FORMAT(P.loan_date,'%d-%b-%Y') as loan_date ,PD.category_id, PD.pre_name_id, PD.product_id, PD.gross_weight, PD.net_weight, PD.market_rate , PD.product_items, PRO.name as name,
                PRO.id as pro_id, PRO.product_with, PRO.product_without ,  PD.gross_weight, PD.net_weight,
                P.market_rate,P.item_count, C.category as category_name,CR.id as cust_id,
                CR.name as customer_name, CR.street as customer_street,CR.location as customer_location,
                CR.postal_code as customer_postal_code,
                CR.contact_no as customer_contact_no,
                CR.photo as customer_photo, CR.sign as customer_sign,
                SC.id as scheme_id, SC.service_charge, SC.service_charge as service_rate,
                if(SC.interest_type=1,'Floating','Flat') as interest_type,
                SC.name as scheme_name,SC.description, SC.loan_rate
                FROM " . PLEDGE . " as P
                JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id
                JOIN " . CATEGORIES . " as C ON C.id = PD.category_id
                JOIN " . CUSTOMERS . " as CR ON CR.id = P.customer_id
                JOIN " . SCHEME . " as SC ON P.scheme_id = SC.id
                JOIN " . PRODUCTS . " as PRO on PD.product_id = PRO.id
                WHERE P.id ='" . $pledge_id . "' ORDER BY PD.ordering";
            return $app->getrows($query);
        }
    }

    public static function InsteadCustDetail($pledge_id) {
        /*
         * Return the Pledge details
         */

        $app = $GLOBALS['app'];
        $count = $app->find_count(array('conditions' => array('id =' => $pledge_id)));

        if ($count > 0) {
            $query = "SELECT DISTINCT P.*,P.loan_date AS pledge_date,
                CR.branch_id AS customer_branch,
                P.branch_id AS pledged_branch,
                CR.customer_id AS customer_code,
                DATE_FORMAT(P.loan_date,'%d-%b-%Y') as loan_date ,PD.category_id, PD.pre_name_id, PD.product_id, PD.product_items, PRO.name as name,
                PRO.id as pro_id, PRO.product_with, PRO.product_without ,  PD.gross_weight, PD.net_weight,
                P.market_rate,P.item_count, C.category as category_name,CR.id as cust_id,
                CR.name as customer_name, CR.street as customer_street,CR.location as customer_location,
                CR.postal_code as customer_postal_code,
                CR.contact_no as customer_contact_no,
                CR.photo as customer_photo, CR.sign as customer_sign,
								SCR.photo as instead_photo, SCR.sign as instead_sign,
                SC.id as scheme_id, SC.service_charge, SC.service_charge as service_rate,
                if(SC.interest_type=1,'Floating','Flat') as interest_type,
                SC.name as scheme_name,SC.description, SC.loan_rate
                FROM " . PLEDGE . " as P
                JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id
                JOIN " . CATEGORIES . " as C ON C.id = PD.category_id
                JOIN " . CUSTOMERS . " as CR ON CR.id = P.customer_id
								JOIN " . SUB_CUSTOMERS . " as SCR ON SCR.customer_id = CR.id
                JOIN " . SCHEME . " as SC ON P.scheme_id = SC.id
                JOIN " . PRODUCTS . " as PRO on PD.product_id = PRO.id
                WHERE P.id ='" . $pledge_id . "' ORDER BY PD.ordering";
            return $app->getrows($query);
        }
    }

    public static function PledgeProductDetails() {

        // Common Pledge Product Details
    }

    public static function SchemeIntrestDetail($scheme_id) {
        /*
         * Return the Scheme Intrest details
         */

        $app = $GLOBALS['app'];
        $query = "SELECT *
                FROM " . SCHEME . " AS SC
                JOIN " . SCHEME_DETAILS . " AS SD ON SD.scheme_id = SC.id
                WHERE SC.id ='" . $scheme_id . "' ORDER BY SD.above, SD.upto";
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
                PRO.name as name,PRO.id as pro_id, PD.gross_weight, PD.net_weight, PD.market_rate,
                C.category as category_name,CR.id as cust_id,
                CR.name as customer_name, CR.street as customer_street,CR.location as customer_location,
                CR.postal_code as customer_postal_code,
                CR.contact_no as customer_contact_no,CR.photo as customer_photo,
                if(SC.interest_type,'Floating','Flat') as interest_type,
                SC.name as scheme_name,SC.description,sum(PI.interest_amount) as interest_amount
                FROM " . PLEDGE . " as P
                LEFT JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id
                LEFT JOIN " . CATEGORIES . " as C ON C.id = PD.category_id
                LEFT JOIN " . CUSTOMERS . " as CR ON CR.id = P.customer_id
                LEFT JOIN " . SCHEME . " as SC ON P.scheme_id = SC.id
                LEFT JOIN " . PRODUCTS . " as PRO on PD.product_id = PRO.id
                LEFT JOIN " . PRODUCT_INTERESTS . " as PI on PI.loan_id = P.id

                WHERE P.id ='" . $userid . "'
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
            $query = "SELECT DISTINCT P.*,DATE_FORMAT(P.loan_date,'%d-%b-%Y %I:%i %p') as loan_date ,PD.category_id, PRO.name as name,PRO.id as pro_id, PD.gross_weight, PD.net_weight, PD.market_rate, C.category as category_name,CR.id as cust_id, CR.name as customer_name, CR.street as customer_street,CR.location as customer_location,CR.postal_code as customer_postal_code, CR.contact_no as customer_contact_no,CR.photo as customer_photo,if(SC.interest_type,'Floating','Flat') as interest_type,SC.name as scheme_name,SC.description FROM " . PLEDGE . " as P LEFT JOIN " . PLEDGE_DETAILS . " as PD ON P.id = PD.pledge_id LEFT JOIN " . CATEGORIES . " as C ON C.id = PD.category_id LEFT JOIN " . CUSTOMERS . " as CR ON CR.id = P.customer_id LEFT JOIN " . SCHEME . " as SC ON P.scheme_id = SC.id LEFT JOIN " . PRODUCTS . " as PRO on PD.product_id = PRO.id
                            WHERE P.id ='" . $userid . "' ORDER BY PD.ordering";

            return $app->getrows($query);
        }
    }

    public static function getLoanAmount($loan_id) {

        $app = $GLOBALS['app'];

        $query = "SELECT P.loan_amount
                    FROM " . PLEDGE . " as P
                    WHERE P.id = '" . $loan_id . "'";

        //echo $query;

        $retval = $app->getrow($query);

        return ($retval) ? $retval["loan_amount"] : '';
    }

    public static function getLoanDate($loan_id) {

        $app = $GLOBALS['app'];

        $query = "SELECT P.loan_date
                    FROM " . PLEDGE . " as P
                    WHERE P.id = '" . $loan_id . "'";

        //echo $query;

        $retval = $app->getrow($query);

        return ($retval) ? $retval[0] : '';
    }

    public static function Delete($pledgeid) {

        /*
         * Return the Customers details
         */


        $app = $GLOBALS['app'];

        $status = self::deleteValidation($pledgeid);

        if ($status['canact']) {

            self::deleteCashFlow($pledgeid);

            $app->access_table = PLEDGE;
            $app->delete(array("id" => $pledgeid));

            return array("success" => true, "error" => $status['error']);
        } else {

            return array("success" => false, "error" => $status['error']);
        }
    }

    public static function deleteValidation($pledgeid) {
        $app = $GLOBALS['app'];

        $data = $app->find(array("loan_id"), array("conditions" => array("id = " => $pledgeid)));
        $pledgecode = $data[0][0];

        $retval = true;

        //Pay Interest Checking
        $app->access_table = PRODUCT_INTERESTS;
        $pay_int = $app->find(array("id"), array("conditions" => array("loan_id = " => $pledgeid)));

        if (is_array($pay_int)) {
            $retval = false;
            $error_msg[] = VALIDATE_PRODUCT_PLEDGE_PAY_INTEREST;
        }

        //PRODUCT_AUCTION Checking
        $app->access_table = PRODUCT_AUCTION;
        $pay_int = $app->find(array("id"), array("conditions" => array("loan_id = " => $pledgeid)));

        if (is_array($pay_int)) {
            $retval = false;
            $error_msg[] = VALIDATE_PRODUCT_PLEDGE_PRODUCT_AUCTION;
        }

        //REDEMPTION Checking
        $app->access_table = REDEMPTION;
        $pay_int = $app->find(array("id"), array("conditions" => array("loan_id = " => $pledgeid)));

        if (is_array($pay_int)) {
            $retval = false;
            $error_msg[] = VALIDATE_PRODUCT_PLEDGE_REDEMPTION;
        }



        //RENEWAL Old Loan Checking
        $app->access_table = RENEWAL;
        $pay_det = $app->find(array("id"), array("conditions" => array("old_loan_id = " => $pledgeid)));

        if (is_array($pay_det)) {
            $retval = false;
            $error_msg[] = VALIDATE_PRODUCT_PLEDGE_RENEWAL_OLD;
        }

        //RENEWAL New Loan Checking
        $app->access_table = RENEWAL;
        $pay_det = $app->find(array("id"), array("conditions" => array("new_loan_id = " => $pledgecode)));

        if (is_array($pay_det)) {
            $retval = false;
            $error_msg[] = VALIDATE_PRODUCT_PLEDGE_RENEWAL_NEW;
        }

        //PAY_EXCESS_AMOUNT Old Loan Checking
        $app->access_table = PAY_EXCESS_AMOUNT;
        $pay_det = $app->find(array("id"), array("conditions" => array("old_loan_id = " => $pledgeid)));

        if (is_array($pay_det)) {
            $retval = false;
            $error_msg[] = VALIDATE_PRODUCT_PLEDGE_PAY_EXCESS_OLD;
        }

        //PAY_EXCESS_AMOUNT New Loan Checking
        $app->access_table = PAY_EXCESS_AMOUNT;
        $pay_det = $app->find(array("id"), array("conditions" => array("new_loan_id = " => $pledgecode)));

        if (is_array($pay_det)) {
            $retval = false;
            $error_msg[] = VALIDATE_PRODUCT_PLEDGE_PAY_EXCESS_NEW;
        }

        //PARTIAL_AMOUNT_PAYMENT Old Loan Checking
        $app->access_table = PARTIAL_AMOUNT_PAYMENT;
        $pay_det = $app->find(array("id"), array("conditions" => array("old_loan_id = " => $pledgeid)));

        if (is_array($pay_det)) {
            $retval = false;
            $error_msg[] = VALIDATE_PRODUCT_PLEDGE_PARTIAL_AMOUNT_OLD;
        }

        //PARTIAL_AMOUNT_PAYMENT New Loan Checking
        $app->access_table = PARTIAL_AMOUNT_PAYMENT;
        $pay_det = $app->find(array("id"), array("conditions" => array("new_loan_id = " => $pledgecode)));

        if (is_array($pay_det)) {
            $retval = false;
            $error_msg[] = VALIDATE_PRODUCT_PLEDGE_PARTIAL_AMOUNT_NEW;
        }

        return array("canact" => $retval, "error" => $error_msg);
    }

    public static function deleteCashFlow($pledgeid) {

        $app = $GLOBALS['app'];

        $app->access_table = PLEDGE;
        $res = $app->find(array("cash_flow_id"), array("conditions" => array("id = " => $pledgeid)));

        $cash_flow_id = ($res) ? $res[0]['cash_flow_id'] : 0;

        return CashFlowTransaction::deleteCashFlow($cash_flow_id);
    }

    public static function getNewLoanCode($branch_id) {
        /*
         * Return the Department details
         */



        $branch_code = Branch::getBranchCode($branch_id);


        $app = $GLOBALS['app'];
        $app->access_table = PLEDGE;
        $res = $app->find(
                array("loan_id"), array(
            "conditions" => array("branch_id = " => $branch_id, "sub_branch_id = " => 0),
            "sort" => " id DESC",
            "limit" => "0,1"
        ));
        //print_r($res);
        //p_arr($res);

        $loan_id = ($res) ? $res[0][0] : 0;
        //echo "<br>".$loan_id;
        //echo "<br>".$branch_code;
        if (strpos($loan_id, $branch_code) === false)
            $loan_num = $branch_code . "1";
        else
            $loan_num = preg_replace("/" . $branch_code . "/i", "", $loan_id);
        //echo "<br>".$loan_num;
        $num = intval($loan_num);
        $num++;
        // echo "mani".$loan_num;
        $form_num = $num; //sprintf("%04d", $num);
        // echo "<br>".$form_num."<br>";
        $new_loan_id = strtoupper($branch_code . $form_num);

        return $new_loan_id;
    }

    public static function getNewSubLoanCode($sub_branch_id) {
        /*
         * Return the Department details
         */

        $sub_branch_code = Subbranch::getSubBranchCode($sub_branch_id);
////////echo $sub_branch_code;"<br>";



        $app = $GLOBALS['app'];
        $app->access_table = PLEDGE;
        $res = $app->find(
                array("loan_id"), array(
            "conditions" => array("sub_branch_id = " => $sub_branch_id),
            "sort" => " id DESC",
            "limit" => "0,1"
        ));

        $app = $GLOBALS['app'];
        $app->access_table = SUB_BRANCH_LOAN_NO;
        $res1 = $app->find(
                array("loan_no", "branch_id"), array(
            "conditions" => array("sub_branch_id = " => $sub_branch_id),
            "sort" => " id DESC",
            "limit" => "0,1"
        ));
        // $app->access_table = SUB_BRANCH;
        //        $res2 = $app->find(
        //                        array("branch_id"),
        //                        array(
        //                            "conditions"=>array("id = "=>$sub_branch_id),
        //                            "sort"=>" id DESC",
        //                            "limit"=>"0,1"
        //                              ));
        //        $app->access_table = BRANCH;
        //        $pledge_status = $app->find(
        //                        array("pledge_status"),
        //                        array(
        //                            "conditions"=>array("id = "=>$res2[0][0]),
        //                            "sort"=>" id DESC",
        //                            "limit"=>"0,1"
        //                              ));


        $app->access_table = SUB_BRANCH;
        $res2 = $app->find(
                array("pledge_status"), array(
            "conditions" => array("id = " => $sub_branch_id),
            "sort" => " id DESC",
            "limit" => "0,1"
        ));

        // $app->access_table = BRANCH;
        // $pledge_status = $app->find(
        //                 array("pledge_status"),
        //                 array(
        //                     "conditions"=>array("id = "=>$res2[0][0]),
        //                     "sort"=>" id DESC",
        //                     "limit"=>"0,1"
        //                       ));


        $pledge_branch_status = $res2[0][0];
        $loan_id = ($res) ? $res[0][0] : $res1[0][0];

        if (strpos($loan_id, $sub_branch_code) === false)
            $loan_num = $sub_branch_code . "1";
        else
            $loan_num = preg_replace("/" . $sub_branch_code . "/i", "", $loan_id);

        $num = intval($loan_num);

        if ($res) {
            $num++;
        }
        $form_num = $num;
        $new_loan_id = strtoupper($sub_branch_code . $form_num);

        return array($new_loan_id, $pledge_branch_status, $sub_branch_code);
    }

    public static function getLoanCode($loan_id) {
        /*
         * Return the Department details
         */


        $loan_id = preg_replace("/mfc/i", '', $loan_id);

        $app = $GLOBALS['app'];
        $app->access_table = PLEDGE;
        $res = $app->find(array("loan_id"), array("conditions" => array("id = " => $loan_id)));
        //p_arr($res);
        return ($res) ? $res[0][0] : $loan_id;
    }

    public static function getBranchId($loan_id) {
        /*
         * Return the Department details
         */


        $loan_id = preg_replace("/mfc/i", '', $loan_id);

        $app = $GLOBALS['app'];
        $app->access_table = PLEDGE;
        $res = $app->find(array("branch_id"), array("conditions" => array("id = " => $loan_id)));
        //p_arr($res);
        return $res[0][0];
    }

    public static function getCustomerId($loan_id) {
        /*
         * Return the Department details
         */


        $loan_id = preg_replace("/mfc/i", '', $loan_id);

        $app = $GLOBALS['app'];
        $app->access_table = PLEDGE;
        $res = $app->find(array("customer_id"), array("conditions" => array("id = " => $loan_id)));
        //p_arr($res);
        return $res[0][0];
    }

}
