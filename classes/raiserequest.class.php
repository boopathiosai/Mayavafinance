<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Brach class
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Vinothkuamr V
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  05-Feb-2010
//**************************************************************************
// Description      :  This is the Branch class  for Mayavafinance.
//**************************************************************************

class Raiserequest {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('branch_id','amount_need','request_on');
        #public $check_duplication = array('branch_code','name');
        public $check_required_labels = array('Branch Name', 'Amount Needed','Request Date');
        public static $check_required_text = 'Required Fields Missing!';
        #public static $check_duplication_text = 'Branch Already exists!';

        public static function ListviewCount()
        {

                                $args = func_get_args();

                                $app = $GLOBALS['app'];

                                /*
                                 * Return the Branch Count
                                 */
                                /*$query = "SELECT id
                                    FROM ".MONEY_SHORTAGE."
                                    WHERE 1
                                    ORDER BY id DESC ";
                                    $data = $app->getrows($query);
                                    return $count = count($data);*/

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
                                  if($_SESSION['session_usergroupname'] != 'Super Admin')
                                    {
                                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                                    }
                                             $query = "SELECT id
                                              FROM ".MONEY_SHORTAGE."
                                              WHERE 1 ".$search_condition."
                                              ORDER BY id DESC ";
                                    $data = $app->getrows($query);
                                    $count = count($data);
                                    if ($count > 0)
                                    {
                                        Paging::set_max( $count );
                                        Paging::calc();// calculation for paging

                                        if($_SESSION['session_usergroupname'] != 'Super Admin')
                                        {
                                            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND    B.id = '".$_SESSION['session_user_branch_id']."'":"";
                                        }

                                        $query = "SELECT MS.id, B.name as branch_name,MS.amount_need,
                                        IF(T.id<>'',0,1) AS editable,
                                        DATE_FORMAT(MS.request_on,'%D %M %Y')AS date,MS.status
                                        FROM ".MONEY_SHORTAGE." as MS
                                        LEFT JOIN ".AMOUNT_TRANSFER." AS T ON T.request_id = MS.id
                                        LEFT JOIN ".BRANCH." as B ON MS.branch_id = B.id
                                        WHERE 1 ".$search_condition."
                                        ORDER BY date DESC ". Paging::get_sql_limit();
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
                                    if ($count > 0)
                                    {
                                        $query = "SELECT *
                                                    FROM ".MONEY_SHORTAGE."
                                                    WHERE id ='".$userid."'";
                                        return $app->getrows($query);
                                    }
    }

    public static function Getemail()
    {
                                    /*
                                     * Return the branch name
                                     */
                                    $app = $GLOBALS['app'];
                                    $query = "SELECT U.email
                                              FROM ".USERS." As U
                                              Where U.user_group_id=1
                                              GROUP BY U.user_group_id";

                                    return $app->getrows($query);

    }

    public static function Msg_details()
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



    public static function Delete($branch_id) {
            /*
             * Return the Customers details
             */
            $app = $GLOBALS['app'];
            $app->access_table = MONEY_SHORTAGE;
            $app->delete(array("id"=>$branch_id));
            return array("success"=>true,"error"=>$status['error']);
    }

    public static function getBranchName($branch_id)
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = MONEY_SHORTAGE;
        $res = $app->find(array("branch_id"),array("conditions"=>array("id = "=>$branch_id)));
        return $res[0][0];
    }

    public static function getRequestAmount($rr_id){

        $app = $GLOBALS['app'];
        $app->access_table = MONEY_SHORTAGE;
        $res = $app->find(array("amount_need"),array("conditions"=>array("id = "=>$rr_id)));
        return $res[0][0];


    }

    public static function getRequestDate($rr_id){

        $app = $GLOBALS['app'];
        $app->access_table = MONEY_SHORTAGE;
        $res = $app->find(array("request_on"),array("conditions"=>array("id = "=>$rr_id)));
        return $res[0][0];


    }


    public static function getRequestStatus($rr_id){

        $app = $GLOBALS['app'];
        $app->access_table = AMOUNT_TRANSFER;
        $res = $app->find(array("status"),array("conditions"=>array("request_id = "=>$rr_id)));
        return count($res);


    }

}




