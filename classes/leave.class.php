<?php
//**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE
 // Module           :  Leave class
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Sathish santhosh kumar. D
 // Inprogress       :  
 // Backup           :  
 // Last Modified    :  
 //**************************************************************************
 // Description      :  This is the Leave class  for Mayavafinance.
// **************************************************************************

class Leave {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('branch_id', 'leave_date', 'description');
        public $check_duplication = array('branch_id', 'leave_date');

        public $check_required_labels = array('Branch Name', 'Leave date','Description');
//        public $check_duplication_labels = array('Company Branch Name', 'Company Branch Code');
        
        public static $check_required_text = 'Required Fields Missing!';
        public static $check_duplication_text = 'Details Already exists!';
       

       public static function ListviewCount() {
                    /*
                     * Get the function parameters , if exist . And make the search condition
                     */
                    $args = func_get_args();

                    $app = $GLOBALS['app'];
                    $s_key = $args[0];
                    $s_leave_group = $args[1];
                $search_condition .= ($s_key != "")? " AND  L.description  like '".$s_key."%'":"";
                $search_condition .= ($s_leave_group != "")? "AND L.branch_id=".$s_leave_group."":"";
                    
                    if(!is_superadmin($_SESSION['session_usergroupname'])){
                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND U.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                        $search_condition .= " AND U.user_group_id >= ".$_SESSION['session_usertype'];
                    }
                    /*
                     * Return the Leave list Count
                     */
                    $query = "SELECT L.id
                        FROM ".LEAVES." as L                      
                                WHERE 1
                                ".$search_condition."
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
                $s_key = $args[0];
                $s_leave_group = $args[1];
                $search_condition .= ($s_key != "")? " AND  L.description  like '".$s_key."%'":"";
                $search_condition .= ($s_leave_group != "")? "AND L.branch_id=".$s_leave_group."":"";
                if(!is_superadmin($_SESSION['session_usergroupname'])){
                    $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND U.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                    $search_condition .= " AND U.user_group_id >= ".$_SESSION['session_usertype'];
                }
                /*
                 * Return the Leave list
                 */
                $count = self::ListviewCount($s_key,$s_leave_group);
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                        $query = "SELECT L.id, B.name as branch_name ,L.leave_date, L.description as leave_description,
                        IF(L.status = 0, 'disable', 'enable') AS status_text , L.status
                        FROM ".LEAVES." as L                       
                        LEFT JOIN ".BRANCH." AS B ON B.id = L.branch_id
                        WHERE 1
                        ".$search_condition."
                        ORDER BY L.id DESC ". Paging::get_sql_limit();
                        return $app->getrows($query);
                }
    }
    public static function Detail($leaveid) {
            /*
             * Return the Leave details
             */
            $app = $GLOBALS['app'];
            $count = $app->find_count(array('conditions' => array('id = ' => $leaveid)));
            if ($count > 0) {
                $query = "SELECT *
                            FROM ".LEAVES."
                            WHERE id ='".$leaveid."'";
                return $app->getrows($query);
            }
    }
    
    
    public static function getSuperAdmin(){
    
            $app = $GLOBALS['app'];
            $query = "SELECT B.name,B.id,U.branch_id,U.id as user_id, U.user_group_id AS user_group,UG.name AS usergroupname
            FROM ".BRANCH." AS B
            LEFT JOIN ".USERS." AS U ON U.branch_id = B.id
            LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
            WHERE UG.name LIKE 'Super Admin' 
            GROUP BY B.id";    
            return $app->getrow($query);
    }

//    public static function deleteValidation($user_id) 
//    {
//                $app = $GLOBALS['app'];
//                
//                $data = Branch::Usergroupid();
//                $head_user = $data[0]['user_id'];
//                $retval = true;
//                if($user_id == $head_user)
//                {
//                    $retval=false;
//                    $error_msg[] = SUPER_ADMIN_CANNOT_DELETE;
//                }
//                   
//                return array("canact"=>$retval,"error"=>$error_msg);
//    }
    

    public static function Delete($leave_id) {
            /*
             * Return the Leave details
             */


            $app = $GLOBALS['app'];

//            $status = self::deleteValidation($leave_id);

//            if($status['canact']){

                $data = Branch::Usergroupid();
                $head_user = $data[0]['user_id'];                
                
                /*** Write Delete function below ****/ 
                $app->access_table = LEAVES;
                $app->delete(array("id"=>$leave_id));
                
                
                return array("success"=>true,"error"=>"Data Not deleted!");
            
//            }else{
//            
//                return array("success"=>false,"error"=>$status['error']);
//
//            }
            
    }

    public static function getLeaveName($leave_id) 
    {
        /*
         * Return the Leave details
         */
        $app = $GLOBALS['app'];
        $app->access_table = LEAVES;
        $res = $app->find(array("description"),array("conditions"=>array("id = "=>$leave_id)));
        return $res[0][0];
    }
    
    public static function getUserGroup($group_id) 
    {
        /*
         * Return the Leave details
         */
        $app = $GLOBALS['app'];
        $app->access_table = USER_GROUP;
        $res = $app->find(array("name"),array("conditions"=>array("id = "=>$group_id)));
        return $res[0][0];
    }

}


