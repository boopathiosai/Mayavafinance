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

class Subbranch {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('branch_id', 'name', 'description', 'license_no','location','address','contact_no');
        public $check_duplication = array('name', 'branch_code');

        public $check_required_labels = array('Branch  Name', 'Sub Branch Name', 'Sub Branch Description', 'License Number','Sub Branch Location','Sub Branch Address','Phone Number');
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
                                FROM ".SUB_BRANCH."
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
                    $query = "SELECT SB.id, BR.name as branch_name, SB.name, SB.sub_branch_code, SB.description, SB.status, SB.license_no,
                    IF(SB.status = 0, 'disable', 'enable') AS status_text
                    FROM ".SUB_BRANCH." as SB
                    LEFT JOIN ".BRANCH." as BR ON BR.id = SB.branch_id
                    WHERE 1 ".$search_condition."
                    ORDER BY SB.id DESC ". Paging::get_sql_limit();
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
                            FROM ".SUB_BRANCH."
                            WHERE id ='".$userid."'";
                return $app->getrows($query);
            }
    }
    
    
        /*public static function masterBranchList()
        {
           
            $app = $GLOBALS['app'];
            $query = "SELECT S.name AS sub_branch_name,S.id AS sub_branch_id,S.license_no, U.branch_id,U.id as user_id, U.user_group_id AS user_group,UG.name AS usergroupname
            FROM ".SUB_BRANCH." AS S
            LEFT JOIN ".USERS." AS U ON U.branch_id = S.id
            LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
            WHERE UG.name NOT LIKE 'Super Admin'
            GROUP BY S.id";
            $data =  $app->getrows($query);
            
            return convert_to_dropdown($data , 'sub_branch_id', 'sub_branch_name');

        }

    
        public static function DeleteCheckMapped($val,$field,$table) {
            
            $app = $GLOBALS['app'];
            $del_val = implode(",",$val);
            $query = "SELECT *  FROM ".$table." WHERE ".$field." in(".$del_val.")";
            $data = $app->getrows($query);
            $count = count($data);
            return $count;
        }



     public static function Superadminbranchid()
        {
            
            $app = $GLOBALS['app'];
            $query = "SELECT branch_id,user_group_id FROM
                      ".USERS."
                      WHERE user_group_id=1
                      GROUP BY user_group_id";
             return $app->getrows($query);

        }
        public static function Usergroupid()
        {
           
            $app = $GLOBALS['app'];
            $query = "SELECT S.name,S.id,U.branch_id,U.id as user_id, U.user_group_id AS user_group,UG.name AS usergroupname
            FROM ".SUB_BRANCH." AS S
            LEFT JOIN ".USERS." AS U ON U.branch_id = S.id
            LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
            WHERE UG.name LIKE 'Super Admin'
            GROUP BY S.id";
            return $app->getrows($query);

        }*/



    /*public static function transferBranchData($table_name, $branch_id, $head_branch, $branch_column='') {

        $app = $GLOBALS['app'];

        //echo "<br> inside data  ".$branch_id."  ".$head_branch; exit;

        $app->access_table = $table_name;

        if(is_array($branch_column)){

            foreach($branch_column as $col){

                $pval[$col] = $head_branch;
                $app->save($pval, array($col.' = ' => $branch_id));
            }

        }else{
            $pval['branch_id'] = $head_branch;
            $app->save($pval, array('branch_id = ' => $branch_id));

        }


    }*/

   /* public static function transferBranchCash($branch_id, $head_branch) {

        $app = $GLOBALS['app'];

        $query = "SELECT closing_balance FROM ".CASH_FLOW_TRANSACTIONS." WHERE branch_id = '".$branch_id."' ORDER BY id DESC LIMIT 0,1";
        $balance_rec = $app->getrow($query);

        $balance = floatval($balance_rec[0]);

        if($balance < 0){
            CashFlowTransaction::insertCashFlow($head_branch,'cashout',(-1*$balance),TRANSFER_CLOSED_BRANCH);
        }elseif($balance > 0){
            CashFlowTransaction::insertCashFlow($head_branch,'cashin',$balance,TRANSFER_CLOSED_BRANCH);
        }



        $app->access_table = CASH_FLOW_TRANSACTIONS;
        $res = $app->delete(array("branch_id"=>$branch_id));


    }*/

    /*public static function transferBranch($branch_id, $head_branch) {

        $app = $GLOBALS['app'];

        //echo "<br> inside tranfer  ".$branch_id."  ".$head_branch;

        //update branchid in CUSTOMERS tables
        self::transferBranchData(CUSTOMERS,$branch_id, $head_branch);
        //update branchid in USERS tables
        self::transferBranchData(USERS,$branch_id, $head_branch);
        //update branchid in DEPARTMENT tables
        self::transferBranchData(DEPARTMENT,$branch_id, $head_branch);
        //update branchid in MESSAGE_DETAILS tables
        self::transferBranchData(MESSAGE_DETAILS,$branch_id, $head_branch);


        //update branchid in Pledge tables
        self::transferBranchData(PLEDGE,$branch_id, $head_branch);
        //update branchid in PRODUCT_INTERESTS tables
        self::transferBranchData(PRODUCT_INTERESTS,$branch_id, $head_branch);


        //update branchid in MONEY_SHORTAGE tables
        self::transferBranchData(MONEY_SHORTAGE,$branch_id, $head_branch);
        //update branchid in PRODUCT_AUCTION tables
        self::transferBranchData(PRODUCT_AUCTION,$branch_id, $head_branch);
        //update branchid in BANK_TRANSACTION tables
        self::transferBranchData(BANK_TRANSACTION,$branch_id, $head_branch);
        //update branchid in AMOUNT_TRANSFER tables
        self::transferBranchData(AMOUNT_TRANSFER,$branch_id, $head_branch, array("request_branch_id","transfer_branch_id"));



        //Transfer balance cash in to head office
        self::transferBranchCash($branch_id, $head_branch);

    }*/





   /* public static function deleteValidation($branch_id)
    {
                $app = $GLOBALS['app'];

                $data = Subbranch::Usergroupid();
                $head_branch = $data[0]['branch_id'];

                $retval = true;




                if($branch_id == $head_branch)
                {
                    $retval=false;
                    $error_msg[] = BRANCH_HEAD_OFFICE_CANNOT_DELETE;
                }


                $query = "SELECT P.branch_id
                          FROM   ".PLEDGE." AS  P
                          LEFT JOIN ".SUB_BRANCH."  AS S ON  P.branch_id=S.id
                          where P.status NOT IN(0) and S.id='".$branch_id."'";
                          $sta=$app->getrows($query);



                if(is_array($sta)){
                    //$retval=false;
                    //$error_msg[] = BRANCH_HAVE_ACTIVE_PLEDGE;
                }

                return array("canact"=>$retval,"error"=>$error_msg);
    }*/


    public static function Delete($id) {
             $app = $GLOBALS['app'];
            $app->access_table = SUB_BRANCH;
            $department = $app->delete(array("id"=>$id));
            return array("success"=>true,"error"=>$status['error']);
    }


    public static function getSubBranchName($branch_id)
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = SUB_BRANCH;
        $res = $app->find(array("name"),array("conditions"=>array("id = "=>$branch_id)));
        return $res[0][0];
    }

    public static function getSubBranchCode($branch_id)
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = SUB_BRANCH;
        $res = $app->find(array("sub_branch_code"),array("conditions"=>array("id = "=>$branch_id)));
        return $res[0][0];
    }

}
