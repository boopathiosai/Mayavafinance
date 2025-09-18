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

class Branch {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('organization_name', 'name', 'address', 'location','description','address','system_ip');
        public $check_duplication = array('name', 'branch_code');
        public $check_valid_ip = array('system_ip');

        public $check_required_labels = array('Company  Name', 'Branch Name', 'Branch Location','Branch Description', 'Branch Address','System Ip');
        public $check_duplication_labels = array('Company Branch Name', 'Company Branch Code');

        public static $check_required_text = 'Required Fields Missing!';
        public static $check_valid_ip_text = 'Invalid IP';
        public static $check_duplication_text = 'Details Already exists!';

        public static function ListviewCount() {

                            $args = func_get_args();

                            $app = $GLOBALS['app'];

                            if(!is_superadmin($_SESSION['session_usergroupname'])){
                                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND id = '".$_SESSION['session_user_branch_id']."'":"";
                             }
                            /*
                             * Return the Branch Count
                             */
                            $query = "SELECT id
                                FROM ".BRANCH."
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
                                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND id = '".$_SESSION['session_user_branch_id']."'":"";
                             }

                $count = self::ListviewCount();
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT id, name, branch_code, description, address, status,
                              IF(status = 0, 'disable', 'enable') AS status_text
                                FROM ".BRANCH."
                                WHERE 1 $search_condition
                                ORDER BY id DESC ". Paging::get_sql_limit();
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
                            FROM ".BRANCH."
                            WHERE id ='".$userid."'";
                return $app->getrows($query);
            }
    }


        public static function masterBranchList()
        {
            /*
             * Return the Department details
             */
            $app = $GLOBALS['app'];
            $query = "SELECT B.name AS branch_name,B.id AS branch_id, U.branch_id,U.id as user_id, U.user_group_id AS user_group,UG.name AS usergroupname
            FROM ".BRANCH." AS B
            LEFT JOIN ".USERS." AS U ON U.branch_id = B.id
            LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
            WHERE UG.name NOT LIKE 'Super Admin'
            GROUP BY B.id";
            $data =  $app->getrows($query);

            return convert_to_dropdown($data , 'branch_id', 'branch_name');

        }


        public static function DeleteCheckMapped($val,$field,$table) {
            /*
             * Return the Department details
             */
            $app = $GLOBALS['app'];
            $del_val = implode(",",$val);
            $query = "SELECT *  FROM ".$table." WHERE ".$field." in(".$del_val.")";
            $data = $app->getrows($query);
            $count = count($data);
            return $count;
        }



     public static function Superadminbranchid()
        {
            /*
             * Return the Department details
             */
            $app = $GLOBALS['app'];
            $query = "SELECT branch_id,user_group_id FROM
                      ".USERS."
                      WHERE user_group_id=1
                      GROUP BY user_group_id";
             return $app->getrows($query);

        }
        public static function Usergroupid()
        {
            /*
             * Return the Department details
             */
            $app = $GLOBALS['app'];
            $query = "SELECT B.name,B.id,U.branch_id,U.id as user_id, U.user_group_id AS user_group,UG.name AS usergroupname
            FROM ".BRANCH." AS B
            LEFT JOIN ".USERS." AS U ON U.branch_id = B.id
            LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
            WHERE UG.name LIKE 'Super Admin'
            GROUP BY B.id";
            return $app->getrows($query);

        }



    public static function transferBranchData($table_name, $branch_id, $head_branch, $branch_column='') {

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


    }

    public static function transferBranchCash($branch_id, $head_branch) {

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


    }

    public static function transferBranch($branch_id, $head_branch) {

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

    }





    public static function deleteValidation($branch_id)
    {
                $app = $GLOBALS['app'];

                $data = Branch::Usergroupid();
                $head_branch = $data[0]['branch_id'];

                $retval = true;




                if($branch_id == $head_branch)
                {
                    $retval=false;
                    $error_msg[] = BRANCH_HEAD_OFFICE_CANNOT_DELETE;
                }


                $query = "SELECT P.branch_id
                          FROM   ".PLEDGE." AS  P
                          LEFT JOIN ".BRANCH."  AS B ON  P.branch_id=B.id
                          where P.status NOT IN(0) and B.id='".$branch_id."'";
                          $sta=$app->getrows($query);



                if(is_array($sta)){
                    //$retval=false;
                    //$error_msg[] = BRANCH_HAVE_ACTIVE_PLEDGE;
                }

                return array("canact"=>$retval,"error"=>$error_msg);
    }


    public static function Delete($branch_id) {
            /*
             * Return the Customers details
             */


            $app = $GLOBALS['app'];

            $status = self::deleteValidation($branch_id);

            if($status['canact']){

                $data = Branch::Usergroupid();
                $head_branch = $data[0]['branch_id'];
                //echo $head_branch; exit;
                self::transferBranch($branch_id, $head_branch);

                $app->access_table = MODULE_PRIVILLEGE;
                $branch = $app->delete(array("branch_id"=>$branch_id));

                $app->access_table = BRANCH;
                $branch = $app->delete(array("id"=>$branch_id));


                return array("success"=>true,"error"=>$status['error']);

            }else{

                return array("success"=>false,"error"=>$status['error']);

            }

    }


    public static function getBranchName($branch_id)
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = BRANCH;
        $res = $app->find(array("name"),array("conditions"=>array("id = "=>$branch_id)));
        return $res[0][0];
    }

    public static function getBranchCode($branch_id)
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = BRANCH;
        $res = $app->find(array("branch_code"),array("conditions"=>array("id = "=>$branch_id)));
        return $res[0][0];
    }

}
