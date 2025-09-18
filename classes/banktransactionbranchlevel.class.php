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

class Banktransactionbranchlevel {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('branch_id','emp_name','mode_of_deposit','transaction_date');
        #public $check_duplication = array('branch_code','name');       
        public $check_required_labels = array('Bank Name','Employee Name','Mode of Deposit','Transaction Date');
        public static $check_required_text = 'Required Fields Missing!'; 
        #public static $check_duplication_text = 'Branch Already exists!';
        
        public static function ListviewCount() {
                            
                            $args = func_get_args();
                             
                            $app = $GLOBALS['app'];
                            
                            /*
                             * Return the Branch Count
                             */
                            if(!is_superadmin($_SESSION['session_usergroupname'])){
                                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                            } 
                                $query = "SELECT id
                                FROM ".BANK_TRANSACTION."
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
                 * Return the branch list
                 */
                if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND BT.branch_id = '".$_SESSION['session_user_branch_id']."'":"";            }
                
                    $query = "SELECT BT.id
                    FROM ".BANK_TRANSACTION." AS BT
                    WHERE 1 ".$search_condition." 
                    ORDER BY BT.id DESC ";
                    $data = $app->getrows($query);
                    $count = count($data);
                if ($count > 0) {
                     if(!is_superadmin($_SESSION['session_usergroupname'])){
                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND BT.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                     }
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                        $query = "SELECT BT.id,B.name,BT.emp_name ,BT.branch_id, BT.mode_of_deposit, 
                        BT.transaction_date,BT.status,
                        IF(BT.status = 0, 'disable', 'enable') AS status_text ,B.name as branch_name
                        FROM ".BANK_TRANSACTION." as BT 
                        LEFT JOIN ".BRANCH." as B ON B.id = BT.branch_id
                        WHERE 1 ".$search_condition." 
                        ORDER BY BT.id DESC ". Paging::get_sql_limit();
                        return $app->getrows($query);
                }
    }

    public static function Detail($userid) {
            /*
             * Return the branch details
             */
            $app = $GLOBALS['app'];
            $count = $app->find_count(array('conditions' => array('id = ' => $userid)));
            if ($count > 0) {
                $query = "SELECT *
                            FROM ".BANK_TRANSACTION."
                            WHERE id ='".$userid."'";               
                return $app->getrows($query);
            }
    }



    public static function Get_branch_name() 
                {
                        /*
                         * Return the branch details
                         */
                        $app = $GLOBALS['app'];
                        //$req_id=$_GET['req_id'];
                        //$branch_id=$_GET['branch_id'];
                            $query="SELECT B.name,B.id
                            FROM ".BRANCH." AS B
                            WHERE 1
                            ORDER BY B.id DESC ";           
                            return $app->getrows($query);
                        
                        }

public static function Delete($id) {
            /*
             * Return the Customers details
             */
            $app = $GLOBALS['app'];
            $app->access_table = BANK_TRANSACTION;
            $bank=$app->delete(array("id"=>$id));
            return array("success"=>true,"error"=>$status['error']);

           
    }


        public static function getBankName($branch_id) 
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = BANK_TRANSACTION;
        $res = $app->find(array("branch_id"),array("conditions"=>array("id = "=>$branch_id)));
        return $res[0][0];
    }

}