<?php
 //**************************************************************************
  //**************************************************************************
  // Project          :  MAYAVA FINANCE
  // Module           :  ProductCategory class
  // Domain           :  http://wwa.mayavafinance.com
  // Programmer       :  Vinothkuamr V
  // Inprogress       :  \\offlinev5\Mayavafinance
  // Backup           :  \\dataserver\EIT\Projects\In Progress\
  // Last Modified    :  05-Feb-2010
  //**************************************************************************
  // Description      :  This is the ProductCategory class  for Mayavafinance.
 //**************************************************************************


class ProductCategory {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('category');
        public $check_duplication = array('category');
        public $check_required_labels = array('Category Name');
        public static $check_required_text = 'Required Fields Missing!';
        public static $check_duplication_text = 'Category Name Already exists!';

        public static function ListviewCount() {
                        /*
                         * Get the function parameters , if exist . And make the search condition
                         */

                        $args = func_get_args();

                        $app = $GLOBALS['app'];

                        /*
                         * Return the ProductCategory list Count
                         */
                        $query = "SELECT  id
                                  FROM ".CATEGORIES."
                                  WHERE 1
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
                 * Return the ProductCategory list
                 */
                $count = self::ListviewCount($s_key, $s_cus_id);
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT id,description,status,IF(status = 0, 'disable', 'enable') AS status_text
                             FROM ".EXPENSES_CATEGORY." WHERE 1 ORDER BY id DESC ". Paging::get_sql_limit();
                    return $app->getrows($query);
                     }
    }
    public static function Detail($userid) {
            /*
             * Return the ProductCategory details
             */
            $app = $GLOBALS['app'];
            $count = $app->find_count(array('conditions' => array('id = ' => $userid)));
            if ($count > 0) {
                $query = "SELECT *
                            FROM ".CATEGORIES."
                            WHERE id ='".$userid."'";
                return $app->getrows($query);
            }
    }
    public static function DeleteCheckMapped($val,$field,$table) {
            /*
             * Return the Department details
             */
            $app = $GLOBALS['app'];
            $del_val =implode(",",$val);
            $query = "SELECT *  FROM ".$table." WHERE ".$field." in(".$del_val.")";
            $count = count($app->getrows($query));
            return $count;
    }
    public static function ValidateDuplication($pval) {
            /*
             * Return the Department details
             */
            $app = $GLOBALS['app'];
            $require_data = 'Category Name Already exists!';
            $query = "SELECT *  FROM ".CATEGORIES." WHERE category = '".trim($pval['category'])."' and product_with = ".$pval['product_with'];
            $count = count($app->getrows($query));
            return $count;
    }
    
    public static function WriteInfo() {

        $app = $GLOBALS['app'];

        $app->access_table = PLEDGE_DETAILS;
        $data = $app->find(array('id'));
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
        /*echo "<PRE>";
        print_r($pledge_data);
        exit;*/
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

    public static function getProductCategoryOptData(){
        $app = $GLOBALS['app'];
        $query = "SELECT (if(P.product_with=1, 'With Stone', 'Without')) AS parent, P.id AS id, 
                CONCAT(CT.category,'-',P.name) as name FROM ".PRODUCTS." AS P JOIN ".CATEGORIES." AS CT ON CT.id = P.category_id
                ORDER BY P.product_with, CT.category, P.name ";
                //echo $query;
        return $app->getrows($query);       
    }


    
    
    public static function deleteValidation($category_id) 
    {
                $app = $GLOBALS['app'];
                
                $retval = true;
                
                $query = "SELECT P.status
                FROM ".PLEDGE_DETAILS." AS PD
                LEFT JOIN ".PLEDGE." AS P ON P.id = PD.pledge_id
                LEFT JOIN ".CATEGORIES." AS CAT ON CAT.id = PD.category_id
                WHERE CAT.id='".$category_id."'";
                
                $sta=$app->getrows($query);
                
                if(is_array($sta))
                {       
                    $retval=false;
                    $error_msg[] = CATEGORIES_USED_IN_PLEDGE;
                }
                
                        
                return array("canact"=>$retval,"error"=>$error_msg);
    }
    

    public static function Delete($category_id) {
            /*
             * Return the Customers details
             */


            $app = $GLOBALS['app'];

            $status = self::deleteValidation($category_id);

            if($status['canact']){


                

                $app->access_table = CATEGORIES;
                $app->delete(array("id"=>$category_id));
                
                
                return array("success"=>true,"error"=>$status['error']);
            
            }else{
            
                return array("success"=>false,"error"=>$status['error']);

            }
            
    }




    public static function getCategoryName($category_id) 
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = CATEGORIES;
        $res = $app->find(array("category"),array("conditions"=>array("id = "=>$category_id)));
        return $res[0][0];
    }
    
}



