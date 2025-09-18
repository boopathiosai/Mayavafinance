<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Product class
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Subash S
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  07-Jul-2010
//**************************************************************************
// Description      :  This is the Product class  for Mayavafinance.
//**************************************************************************

class Product {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('name', 'category_id' ,'description');
        public $check_duplication = array('name');
        public $check_required_labels = array('Product Name', 'Category', 'Product Description');

        public static $check_required_text = 'Required Fields Missing!';
        public static $check_duplication_text = 'Product Name Already exists!';

        public static function ListviewCount() {

                            $args = func_get_args();

                            $app = $GLOBALS['app'];

                            $s_key = $args[0];
                            $s_category = $args[1];
                            $search_condition .= ($s_key != "")? " AND  name  like '".$s_key."%'":"";
                            $search_condition .= ($s_category != "")? "AND category_id = ".$s_category."":"";

                            /*
                             * Return the Product Count
                             */
                            $query = "SELECT id
                                FROM ".PRODUCTS."
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

                $s_key = $args[0];
                $s_category = $args[1];
                $search_condition .= ($s_key != "")? " AND  P.name  like '".$s_key."%'":"";
                $search_condition .= ($s_category != "")? "AND P.category_id = ".$s_category."":"";
                $count = self::ListviewCount($s_key,$s_category);

                /*
                 * Return the Product list
                 */
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT P.id, P.product_with,P.product_without, P.name, P.status as status, C.category as category_name,
                              IF(P.status = 0, 'disable', 'enable') AS status_text
                                FROM ".PRODUCTS." as P LEFT JOIN  ".CATEGORIES." as C ON P.category_id = C.id  WHERE 1 ".$search_condition."
                                ORDER BY id DESC ". Paging::get_sql_limit();
                    return $app->getrows($query);
                }
    }
    
    public static function Detail($product_id) {
            /*
             * Return the Product details
             */
            $app = $GLOBALS['app'];
            $count = $app->find_count(array('conditions' => array('id = ' => $product_id)));
            if ($count > 0) {
                $query = "SELECT *
                            FROM ".PRODUCTS."
                            WHERE id ='".$product_id."'";
                return $app->getrows($query);
            }
  }
  
   public static function ProductAutoList($category_id) {
          /*
           * Get the function parameters , if exist . And make the search condition
           */
          $app = $GLOBALS['app'];
          /* $query = "SELECT PRO.id,PRO.category_id,PRO.name,PRO.product_with,PRO.product_without,PRO.status
                        FROM ".PRODUCTS." AS PRO
                        WHERE PRO.status = 1 AND PRO.category_id = '".$category_id."'  GROUP BY PRO.id,PRO.name 
						ORDER BY PRO.name ASC"; */
		  $query = "SELECT PRO.id,PRO.category_id,PRO.name,PRO.product_with,PRO.product_without,PRO.status
                        FROM ".PRODUCTS." AS PRO
                        WHERE PRO.status = 1 AND PRO.category_id = '".$category_id."' AND PRO.product_flag = '1'  GROUP BY PRO.id,PRO.name 
						ORDER BY PRO.name ASC";
		  
          return $app->getrows($query);
    }

    
    public static function deleteValidation($product_id) 
    {
                $app = $GLOBALS['app'];
                
                $retval = true;
                
                $query = "SELECT P.status
                FROM ".PLEDGE_DETAILS." AS PD
                LEFT JOIN ".PLEDGE." AS P ON P.id = PD.pledge_id
                LEFT JOIN ".PRODUCTS." AS PRO ON PRO.id = PD.product_id
                WHERE PRO.id='".$product_id."'";
               
                
                $sta=$app->getrows($query);
                
                if(is_array($sta))
                {       
                    $retval=false;
                    $error_msg[] = PRODUCT_USED_IN_PLEDGE;
                }
                
                        
                return array("canact"=>$retval,"error"=>$error_msg);
    }
    

    public static function Delete($product_id) {
            /*
             * Return the Customers details
             */


            $app = $GLOBALS['app'];

            $status = self::deleteValidation($product_id);

            if($status['canact']){


                

                $app->access_table = PRODUCTS;
                $app->delete(array("id"=>$product_id));
                
                
                return array("success"=>true,"error"=>$status['error']);
            
            }else{
            
                return array("success"=>false,"error"=>$status['error']);

            }
            
    }




    public static function getProductName($product_id) 
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = PRODUCTS;
        $res = $app->find(array("name"),array("conditions"=>array("id = "=>$product_id)));
        return $res[0][0];
    }
    
}
