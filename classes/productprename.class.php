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

class ProductPrename {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('pre_name');
        public $check_duplication = array('pre_name');
        public $check_required_labels = array('Product Pre Name');

        public static $check_required_text = 'Required Fields Missing!';
        public static $check_duplication_text = 'Product Pre Name Already exists!';

        public static function ListviewCount() {

                            $args = func_get_args();

                            $app = $GLOBALS['app'];

                            $s_key = $args[0];
                            $search_condition .= ($s_key != "")? " AND  pre_name  like '".$s_key."%'":"";
                           

                            /*
                             * Return the Product Count
                             */
                            $query = "SELECT id
                                FROM ".PRENAME."
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
                
                $search_condition .= ($s_key != "")? " AND  P.pre_name  like '".$s_key."%'":"";
                $count = self::ListviewCount($s_key);

                /*
                 * Return the Product list
                 */
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT P.id, P.pre_name as name, P.status as status,
                              IF(P.status = 0, 'disable', 'enable') AS status_text
                                FROM ".PRENAME." as P  WHERE 1 ".$search_condition."
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
                            FROM ".PRENAME."
                            WHERE id ='".$product_id."'";
                return $app->getrows($query);
            }
  }
  
   public static function ProductAutoList($category_id) {
          /*
           * Get the function parameters , if exist . And make the search condition
           */
          $app = $GLOBALS['app'];
          $query = "SELECT PRO.id,PRO.category_id,PRO.name,PRO.product_with,PRO.product_without,PRO.status
                        FROM ".PRODUCTS." AS PRO
                        WHERE  PRO.category_id = '".$category_id."' AND PRO.status = 1 AND PRO.product_flag = '1' ORDER BY PRO.name ASC";
          return $app->getrows($query);
    }


 
    
    
    public static function deleteValidation($product_id) 
    {
                $app = $GLOBALS['app'];
                
                $retval = true;
                
                $query = "SELECT P.status
                FROM ".PLEDGE_DETAILS." AS PD
                LEFT JOIN ".PLEDGE." AS P ON P.id = PD.pledge_id
                WHERE PD.pre_name_id='".$product_id."'";
               
                
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


                

                $app->access_table = PRENAME;
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
        $app->access_table = PRENAME;
        $res = $app->find(array("pre_name"),array("conditions"=>array("id = "=>$product_id)));
        return $res[0][0];
    }
    
}
