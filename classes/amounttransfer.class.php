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

class Amounttransfer {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('transfer_mode','transfer_user_id','cheque_details','transfer_date');
        #public $check_duplication = array('branch_code','name');       
        public $check_required_labels = array('Amount Transfer Mode','Transfer User name','Cheque Details', 'Amount To Be Transferred Date');

        public static $check_required_text = 'Required Fields Missing!'; 
        #public static $check_duplication_text = 'Branch Already exists!';
        
        public static function ListviewCount() {
                            
                            $args = func_get_args();
                             
                            $app = $GLOBALS['app'];
                            
                            /*
                             * Return the Branch Count
                             */
                            if(!is_superadmin($_SESSION['session_usergroupname'])){
                                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND T.transfer_branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                            }
                            $query = "SELECT B.name AS request, B1.name AS transfer,T.id,T.request_branch_id, T.transfer_branch_id, T.transfer_amount,DATE_FORMAT(T.transfer_date,'%D %M %Y') AS transfer_date,T.status,DATE_FORMAT( T.created_datetime,'%d-%m-%Y') AS allocate_date
                    FROM ".AMOUNT_TRANSFER." AS T
                    LEFT JOIN ".BRANCH." AS B ON T.request_branch_id = B.id
                    LEFT JOIN ".BRANCH." AS B1 ON T.transfer_branch_id = B1.id
                    WHERE 1 $search_condition
                    ORDER BY T.id DESC ";
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
                 * Return the branch list
                 */
                if(!is_superadmin($_SESSION['session_usergroupname'])){
                    $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND T.transfer_branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                }
                $count = self::ListviewCount();
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                     $query="SELECT 
                                
                                B.name AS request, B1.name AS transfer,
                                T.id,T.request_branch_id, T.transfer_branch_id, 
                                M.amount_need AS request_amount,
                                T.id AS trans_id, T.request_id AS request_id,
                                T.transfer_amount,DATE_FORMAT(T.transfer_date,'%D %M %Y') AS transfer_date,
                                T.status,DATE_FORMAT( T.created_datetime,'%d-%m-%Y') AS allocate_date

                                
                                FROM ".AMOUNT_TRANSFER." AS T
                                LEFT JOIN ".MONEY_SHORTAGE." AS  M ON M.id = T.request_id 
                                LEFT JOIN ".BRANCH." AS B ON T.request_branch_id = B.id
                                LEFT JOIN ".BRANCH." AS B1 ON T.transfer_branch_id = B1.id
                                WHERE 1 $search_condition
                                ORDER BY T.id DESC ".Paging::get_sql_limit();
                                
                    return $app->getrows($query);
                }
    }

    public static function Detail($userid) 
        {
            /*
             * Return the branch details
             */
            $app = $GLOBALS['app'];
            $count = $app->find_count(array('conditions' => array('id = ' => $userid)));
            if ($count > 0) {
                $query = "SELECT AT.*,B.name as branch_name,
                                 AT.transfer_amount,
                                 M.amount_need
                            FROM ".AMOUNT_TRANSFER." as AT
                            LEFT JOIN ".MONEY_SHORTAGE." AS  M ON M.id = AT.request_id 
                            LEFT JOIN ".BRANCH." as B ON M.branch_id = B.id
                            WHERE AT.id ='".$userid."'";                
                return $app->getrows($query);
        }

            

    }



public static function Get_branch_name($req_id,$trans_id) 
                {
                        //echo $req_id."  ".$trans_id;
                        /*
                         * Return the branch details
                         */
                        $app = $GLOBALS['app'];
                        //$req_id=$_GET['req_id'];
                        //$branch_id=$_GET['branch_id'];
        
                        $query="SELECT B.name AS request, B1.name AS transfer,T.request_branch_id, T.transfer_branch_id, T.transfer_amount, T.transfer_date,T.status
                        FROM ".AMOUNT_TRANSFER." AS T
                        LEFT JOIN ".BRANCH." AS B ON T.request_branch_id = B.id
                        LEFT JOIN ".BRANCH." AS B1 ON T.transfer_branch_id = B1.id
                        WHERE request_branch_id='".$req_id."' AND   transfer_branch_id='".$trans_id."'
                        ORDER BY T.id DESC ";           
                        return $app->getrows($query);
                        
                }
public static function Get_branch_transfer_users($id) 
                {
                       
                        /*
                         * Return the branch details
                         */
                        $app = $GLOBALS['app'];
                       
                        $query="SELECT U.name AS transfer_user_name,U.branch_id,U.id
                        FROM ".USERS." AS U
                        WHERE branch_id='".$id."' 
                        ORDER BY U.id DESC ";           
                        return $app->getrows($query);
                        
                }

public static function Get_branch_received_users($branch_id) 
                {
              
                        /*
                         * Return the branch details
                         */
                        $app = $GLOBALS['app'];
                        $query="SELECT U.name AS transfer_user_name,U.branch_id,U.id
                        FROM ".USERS." AS U
                        WHERE  branch_id='".$branch_id."'
                        ORDER BY U.id DESC ";           
                        return $app->getrows($query);
                        
                }

public static function Msg_details($req_id) 
    {
                                    $app = $GLOBALS['app'];
                                    
                                    $query ="SELECT  U.name  AS recipient_name,U.created_by AS recipient_id,U.email AS EMAIL, U.branch_id, UG.name,A.transfer_date,A.transfer_mode,B.name as branch_name,U.id as user_id
                                    FROM ".USERS." AS U
                                    LEFT JOIN ".BRANCH." AS B ON B.id = U.branch_id
                                    LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
                                    LEFT JOIN  ".AMOUNT_TRANSFER." AS A  ON U.user_group_id = A.transfer_branch_id
                                    WHERE U.branch_id ='".$req_id."'
                                    GROUP BY U.branch_id ";
                                    return $app->getrows($query);
    }

public static function Msg_list($req_id) 
    {
                                    $app = $GLOBALS['app'];
                                    $query ="SELECT  U.name  AS recipient_name,U.created_by AS recipient_id, U.email AS email, U.branch_id, UG.name,U.id as user_id
                                    FROM ".USERS." AS U
                                    LEFT JOIN ".BRANCH." AS B ON B.id = U.branch_id
                                    LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
                                    WHERE U.branch_id ='".$req_id."'
                                    GROUP BY U.branch_id ";
                                    return $app->getrows($query);
    }
    

    public static function getTransferAmount($t_id){
    
        $app = $GLOBALS['app'];
        $app->access_table = AMOUNT_TRANSFER;
        $res = $app->find(array("transfer_amount"),array("conditions"=>array("id = "=>$t_id)));
        return count($res);
    
    
    }


}