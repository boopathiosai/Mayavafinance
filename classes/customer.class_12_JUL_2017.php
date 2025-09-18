<?php
 //**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE
 // Module           :  Customer class
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Vinothkuamr V
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  05-Feb-2010
 //**************************************************************************
 // Description      :  This is the Customer class  for Mayavafinance.
//**************************************************************************

class Customer {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('name', 'branch_id','relationship_type', 'relationship_category', 'relation_name', 'street', 'location','district','postal_code', 'contact_no'); //,'photo','sign');
        public $check_duplication = array('customer_id','email','contact_no','id_proof_code');
        public $check_valid_email = array('email');
    public $check_min_length = array(array('contact_no'), 10);
      public $check_min_length_labels = array('Contact Length');

    //public $check_valid_contact_no = array('contact_no');
      //public $check_valid_contact_no_text = array('Invalid Contact number');

        public $check_required_labels = array('Customer Name', 'Customer Branch','Customer Relationship', 'Customer Relationship Category','Customer Relation Name', 'Customer Street', 'Customer Location','Customer District','Customer Postal Ccode', 'Customer Phone Number','Customer Photo','Customer Sign');

        public $check_duplication_labels = array('Customer Code','Customer Email','Contact Number');
        public $check_valid_email_labels = array('Customer Email');

        public static $check_valid_email_text = 'Invalid Email';
     public static $check_min_length_text = 'Contact number length should be 10!';

        public static $check_required_text = 'Required Fields Missing!';
        public static $check_duplication_text = 'Field Already exists!';

        public static function ListviewCount() {
            /*
             * Get the function parameters , if exist . And make the search condition
             */
            //Modified by karthik
            $args = func_get_args();

            $app = $GLOBALS['app'];
            $s_key = $args[0];
            $s_cus_id = $args[1];
            $search_fhw=$args[2];
            $search_key_location=$args[3];
            $s_branch = $args[4];

            $search_condition .= ($s_key != "")? " AND  C.name  like '".$s_key."%'":"";
            $search_condition .= ($s_cus_id != "")? " AND  C.customer_id   like '".$s_cus_id."%'":"";
            $search_condition .= ($search_fhw != "")? " AND  C.relation_name   like '".$search_fhw."%'":"";
            $search_condition .= ($search_key_location != "")? " AND  C.location   like '".$search_key_location."%'":"";
            $search_condition .= ($s_branch != "")? "AND ( C.branch_id  = '".$s_branch."' )  ":"";

            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id  = '".$_SESSION['session_user_branch_id']."'":"";
            }
            /*
             * Return the Customers list
             */
                     $query = "SELECT C.id
                      FROM ".CUSTOMERS." AS C
                      LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
                      WHERE 1 ".$search_condition."
                      ORDER BY id DESC ";
                      $data = $app->getrows($query);
                      return $count = count($data);

     }
    public static function Listview() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $args = func_get_args();

        $app = $GLOBALS['app'];
        $paging = $GLOBALS['paging'];
        $s_key = $args[0];
        $s_cus_id = $args[1];
        $search_fhw=$args[2];
        $search_key_location=$args[3];
        $s_branch = $args[4];

        $search_condition .= ($s_key != "")? " AND  C.name  like '".$s_key."%'":"";
        $search_condition .= ($s_cus_id != "")? " AND  C.customer_id   like '".$s_cus_id."%'":"";
        $search_condition .= ($search_fhw != "")? " AND  C.relation_name   like '".$search_fhw."%'":"";
        $search_condition .= ($search_key_location != "")? " AND  C.location   like '".$search_key_location."%'":"";
        $search_condition .= ($s_branch != "")? "AND ( C.branch_id  = '".$s_branch."' )  ":"";

            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id  = '".$_SESSION['session_user_branch_id']."'":"";
            }
         /*
          * Return the Customers list count
          */
        $count = self::ListviewCount($s_key, $s_cus_id,$search_fhw,$search_key_location,$s_branch);



        if ($count > 0) {
         /*
          * Return the Customers list
          */
            Paging::set_max( $count );
            Paging::calc();// calculation for paging


                $query = "SELECT C.id,C.customer_id,C.name,C.branch_id, B.name as branch,C.status, C.other_details,
                CONCAT(C.relationship_type,' Name - ',C.relation_name) as relation,C.location, C.reference_name,C.contact_no,C. photo,
                IF(C.status = 0, 'disable', 'enable') AS status_text
                FROM ".CUSTOMERS." AS C
                LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
                WHERE 1
                ".$search_condition." ORDER BY id DESC ". Paging::get_sql_limit();

                return $app->getrows($query);
        }
    }

    public static function CustomerAutoList() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $search_condition = "";
        if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_condition = ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
        }

        $app = $GLOBALS['app'];
        $query = "SELECT id, name, status
                      FROM ".CUSTOMERS."
                      WHERE status = 1  ".$search_condition." ORDER BY name";
        return $app->getrows($query);
    }


    public static function Detail($userid) {
            /*
             * Return the Customers details
             */
            $app = $GLOBALS['app'];
            // $count = $app->find_count(array('conditions' => array('id = ' => $userid)));
            $query = "SELECT *  FROM ".CUSTOMERS." WHERE id ='".$userid."' OR customer_id LIKE '".$userid."'";
            $count = count($app->getrows($query));
            if ($count > 0) {
                $query = "SELECT *
                            FROM ".CUSTOMERS."
                            WHERE id ='".$userid."' OR customer_id LIKE '".$userid."'";
                return $app->getrows($query);
            }
    }

    public static function getDetail($userid) {
            /*
             * Return the Customers details
             */
            $app = $GLOBALS['app'];
            // $count = $app->find_count(array('conditions' => array('id = ' => $userid)));
            $query = "SELECT *  FROM ".CUSTOMERS." WHERE id ='".$userid."' OR customer_id LIKE '".$userid."'";
            $count = count($app->getrows($query));
            if ($count > 0) {
                $query = "SELECT id, branch_id, name, customer_id, street, location, district, state, postal_code, contact_no,email, reference_name, reference_address, photo, sign
                            FROM ".CUSTOMERS."
                            WHERE id ='".$userid."' OR customer_id LIKE '".$userid."'";
                $data =  $app->getexport($query);
                return $data[0];
            }else
                return false;
    }

    public static function Delete($custid) {
            /*
             * Return the Customers details
             */


            $app = $GLOBALS['app'];

            $isvalid = self::deleteValidation($custid);

            if($isvalid){


                $app->access_table = CUSTOMERS;
                $customer = $app->delete(array("id"=>$custid));

                // Delete Customer Photo and Signatures
                deleteFolder(CUSTOMER_IMAGE_PATH.$custid);

                return array("success"=>true,"error"=>"");

            }else{

                return array("success"=>false,"error"=>CUSTOMER_STATUS_NOT_CLOSED_IN_PLEDGED_ITEMS);
            }

    }


    public static function deleteValidation($custid)
    {
                $app = $GLOBALS['app'];
                $query = "SELECT P.customer_id
                          FROM   ".PLEDGE." AS  P
                          LEFT JOIN ".CUSTOMERS."  AS C ON  P.customer_id=C.id
                          where C.id='".$custid."'";
                          $sta=$app->getrows($query);

                if(is_array($sta))
                        return false;
                else
                        return true;
    }

    public static function getCustomerName($cus_id)
    {
        /*
         * Return the Department details
         */


        $cus_id = preg_replace("/mfc/i",'',$cus_id);

        $app = $GLOBALS['app'];
        $app->access_table = CUSTOMERS;
        $res = $app->find(array("name"),array("conditions"=>array("id = "=>$cus_id)));
        //p_arr($res);
        return $res[0][0];
    }

  public static function getCustomerCode($cus_id)
    {
        /*
         * Return the Department details
         */


        $cus_id = preg_replace("/mfc/i",'',$cus_id);

        $app = $GLOBALS['app'];
        $app->access_table = CUSTOMERS;
        $res = $app->find(array("customer_id"),array("conditions"=>array("id = "=>$cus_id)));
        //p_arr($res);
        return $res[0][0];
    }

}
