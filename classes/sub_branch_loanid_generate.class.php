<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Sub Branch class
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Mohanapriya T
// Inprogress       :  192.168.0.77\mayava
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  8-7-2015
//**************************************************************************
// Description      :  This is the Sub Branch class  for Mayavafinance.
//**************************************************************************

class Sub_branch_loanid_generate {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('branch_id', 'sub_branch_id', 'loan_no');
        public $check_duplication = array('loan_no');

        public $check_required_labels = array('Branch  Name', 'Sub Branch Name', 'loan_no');
        public $check_duplication_labels = array('Company Sub Branch Name', 'Company Sub Branch Code');

        public static $check_required_text = 'Required Fields Missing!';
        public static $check_duplication_text = 'Details Already exists!';

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
                                FROM ".SUB_BRANCH_LOAN_NO."
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
                                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                             }

                $count = self::ListviewCount();
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT SBL.id, SBL.branch_id, SBL.sub_branch_id, BR.name as branch_name, SB.name as sub_branch_name, SBL.loan_no
                    FROM ".SUB_BRANCH_LOAN_NO." as SBL
                    LEFT JOIN ".BRANCH." as BR ON BR.id = SBL.branch_id
					LEFT JOIN ".SUB_BRANCH." as SB ON SB.id = SBL.sub_branch_id
                    WHERE 1 ".$search_condition."
                    ORDER BY SBL.id DESC ". Paging::get_sql_limit();
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
                            FROM ".SUB_BRANCH_LOAN_NO."
                            WHERE id ='".$userid."'";
                return $app->getrows($query);
            }
    }   
       

    public static function Delete($id) {
             $app = $GLOBALS['app'];
            $app->access_table = SUB_BRANCH_LOAN_NO;
            $department = $app->delete(array("id"=>$id));
            return array("success"=>true,"error"=>$status['error']);
    }


    public static function getSubBranchName($branch_id)
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = SUB_BRANCH_LOAN_NO;
        $res = $app->find(array("name"),array("conditions"=>array("id = "=>$branch_id)));
        return $res[0][0];
    }

    public static function getSubBranchCode($branch_id)
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = SUB_BRANCH_LOAN_NO;
        $res = $app->find(array("sub_branch_code"),array("conditions"=>array("id = "=>$branch_id)));
        return $res[0][0];
    }

}
