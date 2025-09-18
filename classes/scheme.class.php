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


class Scheme {
     /*
         * Define the required fields and return text
         */
        //public $check_required = array('name','service_charge','loan_rate');
		public $check_required = array('name','loan_rate');

        public $check_duplication = array('name');
        public $check_required_labels = array('Scheme Name', 'Loan Rate', 'Interest');

        public static $check_required_text = 'Required Fields Missing!';
        public static $check_duplication_text = 'Already exists!';

        public static function ListviewCount() {

                            $args = func_get_args();

                            $app = $GLOBALS['app'];

                            /*
                             * Return the Scheme Count
                             */
                            $query = "SELECT SC.id
                                      FROM ".SCHEME." as SC
                                      LEFT JOIN ".SCHEME_DETAILS." as SI ON SC.id = SI.scheme_id
                                      WHERE 1 GROUP BY SC.id
                                      ORDER BY SC.id DESC ";
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
                 * Return the Scheme list Count
                 */
                
                    
                    $count = self::ListviewCount();
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT 
                                SC.id, SC.name as scheme_name, SC.description, 
                                IF(SC.interest_type=0,'Flat','Floting') AS scheme_type,
                                SC.interest_type, SC.service_charge, 
                                SC.loan_rate, 
                                if(SC.interest_type=0,CONCAT('Rs. ',SC.service_charge), CONCAT(SC.service_charge,'%')) AS service_charge, 
                                if(SC.interest_type=0,CONCAT(SI.rate_of_interest,'%'), CONCAT(MIN(SI.rate_of_interest),'% - ',MAX(SI.rate_of_interest),'%')) AS roi, 
                                SC.status,
                                IF(SC.status = 0, 'disable', 'enable') AS status_text
                                FROM ".SCHEME." as SC
                                LEFT JOIN ".SCHEME_DETAILS." as SI ON SC.id = SI.scheme_id
                                WHERE 1 GROUP BY SC.id
                                ORDER BY SC.id DESC ". Paging::get_sql_limit();
                    return $app->getrows($query);
                }
    }

    public static function Detail($scheme_id) {
            /*
             * Return the Scheme details order by the details id
             */
            $app = $GLOBALS['app'];

            //$count = $app->find_count(array('conditions' => array('id = ' => $scheme_id)));
            $query = "SELECT *  FROM ".SCHEME." WHERE id ='".$scheme_id."'";
            $count = count($app->getrows($query));
            if ($count > 0) {
                $query = "SELECT SC.id, SC.loan_rate, SC.name as name, SC.description, SC.interest_type, SC.service_charge,
                                SC.status, SD.above, SD.upto, SD.rate_of_interest,if(SC.interest_type=0,'Flat','Floating') AS scheme_type, SC.loan_rate
                                FROM ".SCHEME." as SC
                                LEFT JOIN ".SCHEME_DETAILS." as SD ON SC.id = SD.scheme_id
                                WHERE SC.id ='".$scheme_id."' ORDER BY SD.id";
                //return $query;

                

                return $app->getrows($query);

            }
    }



    public static function SchemeInterestDetail($scheme_id, $loan_date='', $loan_amount=0, $loan_id=0) {
            /*
             * Return the Scheme Intrest details
             */
            
            $loan_date = ($loan_date=="") ? date("Y-m-d") : date("Y-m-d",strtotime($loan_date));
            
            
            $app = $GLOBALS['app'];
            
            if($loan_id == 0){

            $query = "SELECT 
                    SC.name AS scheme_name,
                    if(SC.interest_type=0,'Flat','Floating') AS scheme_type,
                    SC.id AS scheme_id,
                    SC.loan_rate,
                    SC.service_charge,
                    
                    '".$loan_amount."' AS loan_amount,
                    
                    DATEDIFF(
                            DATE_FORMAT(
                                DATE_ADD(
                                    '".$loan_date."', INTERVAL SD.upto MONTH
                                ),
                                '%Y-%m-%d'
                            ),
                            '".$loan_date."'
                    ) AS no_days,
                    
                    DATEDIFF(
                            DATE_FORMAT(
                                DATE_ADD(
                                    '".$loan_date."', INTERVAL SD.upto MONTH
                                ),
                                '%Y-%m-%d'
                            ),
                            DATE_FORMAT(
                                DATE_ADD(
                                    DATE_ADD(
                                        '".$loan_date."', INTERVAL SD.above MONTH
                                    ),
                                    INTERVAL 0 DAY
                                ),    
                                '%Y-%m-%d'
                            )
                    ) AS slab_days,
                    
                    DATEDIFF(
                        DATE_FORMAT(DATE_ADD('".$loan_date."', INTERVAL 12 MONTH),'%Y-%m-%d'),
                        '".$loan_date."'
                    )  AS div_days,      
                    
                    DATE_FORMAT(DATE_ADD(DATE_ADD('".$loan_date."', INTERVAL SD.above MONTH), INTERVAL 0 DAY),'%Y-%m-%d') AS start_date,
                    DATE_FORMAT(DATE_SUB(DATE_ADD('".$loan_date."', INTERVAL SD.upto MONTH), INTERVAL 1 DAY),'%Y-%m-%d') AS end_date,
                    DATE_FORMAT(DATE_ADD(DATE_ADD('".$loan_date."', INTERVAL SD.above MONTH), INTERVAL 0 DAY),'%d-%b-%Y') AS start ,
                    DATE_FORMAT(DATE_SUB(DATE_ADD('".$loan_date."', INTERVAL SD.upto MONTH), INTERVAL 1 DAY),'%d-%b-%Y') AS end ,
                    SD.rate_of_interest
                    
                    
                    FROM ".SCHEME." AS SC 
                    JOIN ".SCHEME_DETAILS." AS SD ON SD.scheme_id = SC.id 
                    WHERE SC.id ='".$scheme_id."' ORDER BY SD.above, SD.upto";
            
            }else{
            
            $query = "SELECT 

                    SC.loan_rate,
                    SC.name AS scheme_name,
                    SC.service_charge,
                    if(SC.interest_type=0,'Flat','Floating') AS scheme_type,
                    SC.id AS scheme_id,
                    
                    P.loan_amount AS loan_amount,
                    DATEDIFF(
                            DATE_FORMAT(
                                DATE_ADD(
                                    IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                    INTERVAL SD.upto MONTH
                                ),
                                '%Y-%m-%d'
                            ),
                            '".$loan_date."'
                    ) AS no_days,
                    DATEDIFF(
                            DATE_FORMAT(
                                DATE_ADD(
                                    IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                    INTERVAL SD.upto MONTH
                                ),
                                '%Y-%m-%d'
                            ),
                            DATE_FORMAT(
                                DATE_ADD(
                                    DATE_ADD(
                                        IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                        INTERVAL SD.above MONTH
                                    ),
                                    INTERVAL 1 DAY
                                ),    
                                '%Y-%m-%d'
                            )
                    ) AS slab_days,
                    
                    DATEDIFF(
                        DATE_FORMAT(
                            DATE_ADD( 
                                IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                INTERVAL 12 MONTH),'%Y-%m-%d'
                            ),
                            IF(PT.paid_on <> '', PT.paid_on, P.loan_date)
                    )  AS div_days,      
                    
                    DATE_FORMAT(DATE_ADD(DATE_ADD('".$loan_date."', INTERVAL SD.above MONTH), INTERVAL 0 DAY),'%Y-%m-%d') AS start_date,
                    DATE_FORMAT(DATE_SUB(DATE_ADD('".$loan_date."', INTERVAL SD.upto MONTH), INTERVAL 1 DAY),'%Y-%m-%d') AS end_date,
                    DATE_FORMAT(DATE_ADD(DATE_ADD('".$loan_date."', INTERVAL SD.above MONTH), INTERVAL 0 DAY),'%d-%b-%Y') AS start ,
                    DATE_FORMAT(DATE_SUB(DATE_ADD('".$loan_date."', INTERVAL SD.upto MONTH), INTERVAL 1 DAY),'%d-%b-%Y') AS end ,
                    SD.rate_of_interest

                    FROM ".PLEDGE." as P  
                    LEFT JOIN ".PRODUCT_INTERESTS." as PT ON PT.loan_id = P.id
                    LEFT JOIN ".SCHEME." AS SC 
                    JOIN ".SCHEME_DETAILS." AS SD ON SD.scheme_id = SC.id 
                    WHERE SC.id ='".$scheme_id."' ORDER BY SD.above, SD.upto, PT.paid_on DESC";
            }        
                   // echo($query);                    //exit;
            return $app->getrows($query);
    }
    
    
     public static function getSchemeLoanRate($scheme_id) {
     
        $app = $GLOBALS['app'];
        
        $app->access_table = SCHEME;
        $retval = $app->find(array("loan_rate"),array("conditions"=>array("id = "=>$scheme_id)));
        return  ($retval) ? $retval[0]["loan_rate"] : 0;
     
     }

     public static function getSchemeType($loan_id) {
     
        $app = $GLOBALS['app'];
        
        $query = "SELECT IF(SC.interest_type = 1,'floating','flat') AS scheme_type 
                    FROM ".PLEDGE." as P  
                    LEFT JOIN ".SCHEME." AS SC ON P.scheme_id = SC.id 
                    WHERE P.id = '".$loan_id."'";
        
        //echo $query;
        
        $retval = $app->getrow($query);
        
        return  ($retval) ? $retval["scheme_type"] : '';
     
     }




    public static function deleteValidation($scheme_id) 
    {
                $app = $GLOBALS['app'];
                
                $retval = true;
                
                $query = "SELECT P.status
                FROM ".PLEDGE." AS P
                LEFT JOIN ".SCHEME." AS  SC ON SC.id = P.scheme_id
                WHERE SC.id='".$scheme_id."'";
                
                $sta=$app->getrows($query);
                
                if(is_array($sta))
                {       
                    $retval=false;
                    $error_msg[] = SCHEME_USED_IN_PLEDGE;
                }
                
                        
                return array("canact"=>$retval,"error"=>$error_msg);
    }
    

    public static function Delete($scheme_id) {
            /*
             * Return the Customers details
             */


            $app = $GLOBALS['app'];

            $status = self::deleteValidation($scheme_id);

            if($status['canact']){


                $app->access_table = SCHEME;
                $app->delete(array("id"=>$scheme_id));
                
                
                return array("success"=>true,"error"=>$status['error']);
            
            }else{
            
                return array("success"=>false,"error"=>$status['error']);

            }
            
    }




    public static function getSchemeName($scheme_id) 
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = SCHEME;
        $res = $app->find(array("name"),array("conditions"=>array("id = "=>$scheme_id)));
        return $res[0][0];
    }


}

