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

class Amountreceived {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('received_date');
        #public $check_duplication = array('branch_code','name');       
        public $check_required_labels = array('Amount Received Date');

        public static $check_required_text = 'Required Fields Missing!'; 
        #public static $check_duplication_text = 'Branch Already exists!';
        
        public static function ListviewCount() {
                            
            $args = func_get_args();

            $app = $GLOBALS['app'];

            /*
             * Return the Branch Count
             */
           if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND request_branch_id = '".$_SESSION['session_user_branch_id']."'":"";
            }
            $query = "SELECT T.id
                        FROM ".AMOUNT_TRANSFER." AS T
                        LEFT JOIN ".MONEY_SHORTAGE." AS  M ON M.id = T.request_id 
                        LEFT JOIN ".BRANCH." AS B ON T.request_branch_id = B.id
                        LEFT JOIN ".BRANCH." AS B1 ON T.transfer_branch_id = B1.id
                        WHERE 1 $search_condition
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
                 * Return the branch list
                 */
                if(!is_superadmin($_SESSION['session_usergroupname'])){
                
                    $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
                } 
                
                $count = self::ListviewCount();
                if ($count > 0) 
                {
                        
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query="SELECT 
                    
                    B.name AS request, B1.name AS transfer,T.request_branch_id, 
                    T.transfer_branch_id, M.amount_need AS request_amount, T.transfer_amount,
                    DATE_FORMAT(M.request_on,'%d-%b-%y') AS request_date, 
                    DATE_FORMAT(T.transfer_date,'%d-%b-%y') AS transfer_date, 
                    T.transfer_mode,DATE_FORMAT(T.received_date,'%d-%b-%y') AS received_date,T.id,T.status 
                    
                    FROM ".AMOUNT_TRANSFER." AS T
                    LEFT JOIN ".MONEY_SHORTAGE." AS  M ON M.id = T.request_id 
                    LEFT JOIN ".BRANCH." AS B ON T.request_branch_id = B.id
                    LEFT JOIN ".BRANCH." AS B1 ON T.transfer_branch_id = B1.id
                    WHERE 1 $search_condition
                    ORDER BY T.received_date DESC ".Paging::get_sql_limit();
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
                $query = "SELECT T.*,M.*,
                            B.name AS request_branch, B1.name AS transfer_branch
                            FROM ".AMOUNT_TRANSFER." as T 
                            LEFT JOIN ".MONEY_SHORTAGE." AS  M ON M.id = T.request_id 
                            LEFT JOIN ".BRANCH." AS B ON T.request_branch_id = B.id
                            LEFT JOIN ".BRANCH." AS B1 ON T.transfer_branch_id = B1.id
                            WHERE T.id ='".$userid."'";                
                return $app->getrows($query);
            }
    }

    public static function Msg_details($recv_id) 
            {
            
                $app = $GLOBALS['app'];
                $query ="SELECT U.name AS recipient_name,U.email  AS email, U.branch_id, UG.name,U.id as user_id
                         FROM ".USERS." AS U
                         LEFT JOIN ".BRANCH." AS B ON B.id = U.branch_id
                         LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
                         WHERE U.user_group_id = '1'
                         GROUP BY U.branch_id ";
                return $app->getrows($query);
            }
    /*public static function Msg_list($recv_id) 
            {
                $app = $GLOBALS['app'];
                $query ="SELECT  U.name  AS recipient_name,U.created_by AS recipient_id, U.email AS email, U.branch_id, UG.name,U.id as user_id
                FROM ".USERS." AS U
                LEFT JOIN ".BRANCH." AS B ON B.id = U.branch_id
                LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
                WHERE U.branch_id ='".$recv_id."'
                GROUP BY U.branch_id ";
                return $app->getrows($query);
            }*/
    public static function getTransferDate($t_id){
    
        $app = $GLOBALS['app'];
        $app->access_table = AMOUNT_TRANSFER;
        $res = $app->find(array("transfer_date"),array("conditions"=>array("id = "=>$t_id)));
        return $res[0][0];
    
    
    }
}