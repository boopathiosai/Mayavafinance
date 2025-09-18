<?php
//**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE
 // Module           :  Department class
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Vinothkuamr V
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  05-Feb-2010
 //**************************************************************************
 // Description      :  This is the Department class  for Mayavafinance.
//**************************************************************************


class Department {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('organization_name','branch_id', 'department_name');

        public $check_duplication = array('department_name');
        public $check_required_labels = array('Organization Name','Branch Name','Department Name');
        public static $check_required_text = 'Required Fields Missing!';
        public static $check_duplication_text = 'Department Details Already exists!';

        public static function ListviewCount() {

                            $args = func_get_args();

                            $app = $GLOBALS['app'];

                    if(!is_superadmin($_SESSION['session_usergroupname'])){
                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                    }

                            /*
                             * Return the Branch Count
                             */
                               $query = "SELECT id
                                FROM ".DEPARTMENT."
                                WHERE 1 ".$search_condition."
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

                /*
                 * Return the Department list Count
                 */
                $count = self::ListviewCount();

                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    
                    if(!is_superadmin($_SESSION['session_usergroupname'])){
                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                    }
                    $query = "SELECT DP.id, BR.name as branch_name, DP.department_name, DP.description, DP.status,
                    IF(DP.status = 0, 'disable', 'enable') AS status_text
                    FROM ".DEPARTMENT." as DP
                    LEFT JOIN ".BRANCH." as BR ON BR.id = DP.branch_id
                    WHERE 1 ".$search_condition."
                    ORDER BY DP.id DESC ". Paging::get_sql_limit();
                    return $app->getrows($query);
                }
    }
    public static function Detail($userid) {
            /*
             * Return the Department details
             */
            $app = $GLOBALS['app'];
            $count = $app->find_count(array('conditions' => array('id = ' => $userid)));
            if ($count > 0) {
                $query = "SELECT *
                            FROM ".DEPARTMENT."
                            WHERE id ='".$userid."'";
                return $app->getrows($query);
            }
    }


    public static function WriteInfo() {

        $app = $GLOBALS['app'];

        $app->access_table = PLEDGE_DETAILS;
        $data = $app->find(array('id', 'name'));
        $count = count($data);
        $pledge_data = array();

        if($count > 0) {

            foreach($data as $option) {
                $pledge_data[] = "{name:".str_replace('"', '\"', $option['name']).", id:".str_replace('"', '\"', $option['id'])."";
                //$pledge_values[] = str_replace('"', '\"', $option['id']);
            }
        }

        $app->access_table = CUSTOMERS;
        $data = $app->find(array('id ', 'name'));
        $count = count($data);
        $customer_data = array();
        echo "<PRE>";
        print_r($pledge_data);
        exit;
        if($count > 0) {

            foreach($data as $attribute) {
                $customer_data[] = "{name:'".str_replace('"', '\"', $attribute['name'])."', id:'".str_replace('"', '\"', $attribute['id'])."'";
                //$customer_data[] .= str_replace('"', '\"', $attribute['id']);
            }
        }

        //echo "<PRE>";
        //print_r($customer_values);
        //exit;

        if(count($pledge_data) != 0 && count($pledge_values) != 0 ) {
            $json_content = 'var pledge_names = ["' . implode('", "', $pledge_data) . '"];' . "\n\n";
        }
        else {
            $json_content = 'var pledge_names = [];' . "\n\n";
        }

        /*if(count($pledge_values) != 0 ) {
            $json_content = 'var pledge_values = ["' . implode('", "', $pledge_values) . '"];' . "\n\n";
        }
        else {
            $json_content = 'var pledge_values = [];' . "\n\n";
        }


        if(count($customer_data) != 0 ) {
            $json_content .= 'var customer_names = ["' . implode('", "', $customer_data) . '"];' . "\n\n";
        }
        else {
            $json_content .= 'var customer_names = [];' . "\n\n";
        }*/

        if(count($customer_names) != 0 && count($pledge_values) != 0 ) {
            $json_content .= 'var customer_names = [' . implode(', ', $customer_names) .'];' . "\n\n";
        }
        else {
            $json_content .= 'var customer_names = [];' . "\n\n";
        }
        /*
        if(count($customer_values) != 0 ) {
            $json_content .= 'var customer_values = ["' . implode('", "', $customer_values) . '"];' . "\n\n";
        }
        else {
            $json_content .= 'var customer_values = [];' . "\n\n";
        }*/


        file_put_contents("js/data/localdata.js", stripslashes($json_content));
    }
    
    public static function Delete($id) {
            /*
             * Return the Departments details
             */
            $app = $GLOBALS['app'];
            $app->access_table = DEPARTMENT;
            $department = $app->delete(array("id"=>$id));
            return array("success"=>true,"error"=>$status['error']);
        }


public static function getDepartmentName($dept_id) 
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = DEPARTMENT;
        $res = $app->find(array("department_name"),array("conditions"=>array("id = "=>$dept_id)));
        return $res[0][0];
    }
}
