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

class Allocationofbranch 
    {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('id','transfer_amount');
        #public $check_duplication = array('branch_code','name');       
       
        public static $check_required_text = 'Required Fields Missing!'; 
        #public static $check_duplication_text = 'Branch Already exists!';
        
        public static function ListviewCount() 
        {
                            
                            $args = func_get_args();
                             
                            $app = $GLOBALS['app'];
                            
                            /*
                             * Return the Branch Count
                             */
                                $query = "SELECT id
                                FROM ".BRANCH."
                                WHERE 1
                                ORDER BY id DESC ";
                                $data = $app->getrows($query);
                                return $count = count($data);

                             
        }

        public static function Listview() 
        {
                /*
                 * Get the function parameters , if exist . And make the search condition
                 */
                
                $args = func_get_args();
                 
                $app = $GLOBALS['app'];
                
                /*
                 * Return the branch list
                 */
                $query = "SELECT id
                          FROM ".BRANCH."  
                          WHERE 1  
                          ORDER BY id DESC ";
                    $data = $app->getrows($query);
                    $count = count($data);
                if ($count > 0) 
                {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                              $query = "SELECT   
                                          B.name,
                                          B.id,
                                          (SELECT closing_balance from ".CASH_FLOW_TRANSACTIONS." where branch_id = B.id ORDER BY created_datetime DESC, id DESC LIMIT 0,1) AS amt
                                          FROM  ".BRANCH." as B 
                                          
                                           ". Paging::get_sql_limit();
                                //echo $query;
                              return $app->getrows($query);
                   

                }
         }

        public static function Detail($userid) 
        {
                                /*
                                 * Return the branch details
                                 */
                        /*      $app = $GLOBALS['app'];
                                $count = $app->find_count(array('conditions' => array('id = ' => $userid)));
                                if ($count > 0) 
                                {
                                    $query = "SELECT *
                                                FROM ".ALLOCATION_BRANCH."
                                                WHERE id ='".$userid."'";               
                                    return $app->getrows($query);
                                }
                        */
        }

        public static function Shortage_branch_name($id) 
            {
            
                                /*
                                 * Return the branch name
                                 */
                                $app = $GLOBALS['app'];
                                //$id=$_GET['id'];
                                $query = "SELECT B.name,B.id,M.branch_id,M.id,M.amount_need,M.status
                                                FROM ".BRANCH." AS  B
                                                LEFT JOIN ".MONEY_SHORTAGE." AS  M on 
                                                M.branch_id=B.id WHERE M.status=1 and M.branch_id NOT IN($id) ";
                                                return $app->getrows($query);
             }

        public static function Shortage_branch_amt($id,$branch_id) 
            {
                                /*
                                 * Return the branch request anmount
                                 */
                                $app = $GLOBALS['app'];
                                $query = "SELECT M.amount_need
                                          FROM ".MONEY_SHORTAGE." AS  M
                                          WHERE M.id='".$id."' AND M.status=1 AND  M.branch_id NOT IN($branch_id)";             
                                    return $app->getrow($query);
            }


        public static function Get_branch_name($id) 
            {
                /*
                 * Return the branch name
                 */
                $app = $GLOBALS['app'];
                //$id=$_GET['id'];
                $query = "SELECT B.name,B.id
                                FROM ".BRANCH." AS  B
                                where B.id='".$id."'";              
                    return $app->getrows($query);
                                
            }

        public static function Msg_details($to_id) 
            {
                $app = $GLOBALS['app'];
                $query ="SELECT  U.name AS recipient_name,U.created_by AS recipient_id, U.email AS EMAIL,U.branch_id,UG.name as user,B.name AS  branch_name,A.transfer_amount,A.created_datetime,U.id as user_id
                FROM ".USERS." AS U
                LEFT JOIN ".BRANCH." AS B ON B.id = U.branch_id
                LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
                LEFT JOIN  ".AMOUNT_TRANSFER." AS A  ON U.user_group_id = A.transfer_branch_id
                WHERE U.branch_id ='".$to_id."'
                GROUP BY U.branch_id ";
                return $app->getrows($query);
            }
        
        public static function Msg_list($to_id) 
            {
                $app = $GLOBALS['app'];
                $query ="SELECT  U.name AS recipient_name,U.id as user_id,U.created_by AS recipient_id, U.email AS email,U.branch_id,UG.name as user,B.name as branch_name,A.transfer_amount
                FROM ".USERS." AS U
                LEFT JOIN ".BRANCH." AS B ON B.id = U.branch_id
                LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
                LEFT JOIN  ".AMOUNT_TRANSFER." AS A  ON U.user_group_id = A.transfer_branch_id
                WHERE U.branch_id ='".$to_id."'
                GROUP BY U.branch_id ";
                return $app->getrows($query);
            }
 


        /*public static function Allocation() 
        {
                            $app = $GLOBALS['app'];

                            echo  $query = "SELECT MS.id, B.name as branch_name, MS.amount_need, MT.status
                                                        FROM ".MONEY_SHORTAGE." as MS 
                                                        LEFT JOIN ".BRANCH." as B ON MS.branch_id = B.id  
                                                        LEFT JOIN mf_amount_transfer as MT ON MT.request_branch_id = MS.branch_id 
                                                        AND  MT.id=MS.id 
                                                        ORDER BY MS.id DESC ";
                                                        return $app->getrows($query);
        }*/
            /*public static function bank_details($to_id) 
            {
                $app = $GLOBALS['app'];
                $query ="SELECT B.name
                FROM ".AMOUNT_TRANSFER." AS A
                LEFT JOIN ".BRANCH." AS B ON B.id = A.transfer_branch_id
                WHERE A.transfer_branch_id ='".$to_id."'";
                return $app->getrows($query);
            }*/

      /*public static function Msg_list($tran_id) 
            {
                $app = $GLOBALS['app'];
                 $query ="SELECT * 
                        FROM 
                        ".CASH_FLOW_TRANSACTIONS." 
                        WHERE branch_id='".$tran_id."'";
                return $app->getrows($query);
            }*/



}