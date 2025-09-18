 
<?php
//**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE
 // Module           :  Department class
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Subash S
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  10-Jun-2011
 //**************************************************************************
 // Description      :  This is the Department class  for Mayavafinance.
//**************************************************************************

 
     /*
         * Define the required fields and return text
         */
        //public $check_required = array('name','service_charge','loan_rate');
	
class ServiceCharge {
    public $check_required = array('service_charge_amount', 'starting_amount', 'ending_amount');
    public $check_duplication = array('starting_amount', 'ending_amount');

    public $check_required_labels = array('Service Charge Amount', 'Starting Amount', 'Ending Amount');
    public $check_duplication_labels = array( 'Starting Amount', 'Ending Amount');

    public static $check_required_text = 'Required Fields Missing!';
    public static $check_duplication_text = 'Details Already exists!';
    public static function ListviewCount() {
        $args = func_get_args();
        $app = $GLOBALS['app'];

        /*
        * Return the Scheme Count
        */
        $query = "SELECT SC.id
        FROM ".SERVICE_CHARGES." as SC

        WHERE 1 GROUP BY SC.id
        ORDER BY SC.id DESC ";
        $data = $app->getrows($query);
        return $count = count($data);


     }

     public static function Listview() {
        
                /*
                 * Get the function parameters , if exist . And make the search condition
                 */

               // $args = func_get_args();

                $app = $GLOBALS['app'];
              
                /*
                 * Return the Scheme list Count
                 */
                
               
                    $count = self::ListviewCount();
                    
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    
                    $query = "SELECT 
                                SC.id, SC.service_charge_amount,SC.starting_amount,SC.ending_amount, SC.description, 
                               
                                IF(SC.status = 0, 'disable', 'enable') AS status_text
                                FROM ".SERVICE_CHARGES." as SC 
                                WHERE 1  
                                ORDER BY SC.id DESC ". Paging::get_sql_limit();
                    return $app->getrows($query);
                }
    }
    
    public static function Detail($service_charge_id) {
        $app   = $GLOBALS['app']; 
        $query = "SELECT *  FROM ".SERVICE_CHARGES." WHERE id ='".$service_charge_id."'";
        $count = count($app->getrows($query));
        if ($count > 0) {
            $query = "SELECT SC.id, SC.service_charge_amount,SC.starting_amount,SC.ending_amount, SC.description,SC.status
                            FROM ".SERVICE_CHARGES." as SC
                            WHERE SC.id ='".$service_charge_id."' ORDER BY SC.id";
            return $app->getrows($query);
        }
    }


    public static function Delete($service_charge_id) {
       
        $app = $GLOBALS['app']; 
        $app->access_table = SERVICE_CHARGES;
        $app->delete(array("id"=>$service_charge_id));
        return array("success"=>true,"error"=>$status['error']);

    }

    public static function service_charge_amount($loan_amount) {
        $app   = $GLOBALS['app']; 
        $query = "SELECT service_charge_amount  FROM ".SERVICE_CHARGES." WHERE starting_amount <='".$loan_amount."' and ending_amount >='".$loan_amount."'";
   
   return $app->getrows($query);
         
    }

}
?>