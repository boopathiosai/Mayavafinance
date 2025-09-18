<?php


class User {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('name','branch_id', 'user_group_id', 'password','email');
        public $check_valid_email = array('email');
        public $check_duplication = array('email');
        public $check_min_length = array(array('password'), 6);

        public $check_required_labels = array('User Name','Branch Name', 'User Type','Password', 'User Email');
        public $check_valid_email_labels = array('User Email');
        public $check_duplication_labels = array('User Email');
        public $check_min_length_labels = array('Password Length');
        
        public static $check_required_text = 'Required Fields Missing!';
        public static $check_valid_email_text = 'Invalid Email';
        public static $check_duplication_text = 'Users Details Already exists!';
        public static $check_min_length_text = 'Password length should be greater than 5!';

       public static function ListviewCount() {
                    /*
                     * Get the function parameters , if exist . And make the search condition
                     */


                    $args = func_get_args();

                    $app = $GLOBALS['app'];
                    $s_key = $args[0];
                    $s_user_group = $args[1];
                $search_condition .= ($s_key != "")? " AND  U.name  like '".$s_key."%'":"";
                $search_condition .= ($s_user_group != "")? "AND U.user_group_id=".$s_user_group."":"";
                    
                    if(!is_superadmin($_SESSION['session_usergroupname'])){
                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND U.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                        $search_condition .= " AND U.user_group_id >= ".$_SESSION['session_usertype'];
                    }
                    /*
                     * Return the User list Count
                     */
                    $query = "SELECT U.id
                        FROM ".USERS." as U
                        LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
                        LEFT JOIN ".BRANCH." AS B ON B.id = U.branch_id
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
                $s_user_group = $args[1];
                $search_condition .= ($s_key != "")? " AND  U.name  like '".$s_key."%'":"";
                $search_condition .= ($s_user_group != "")? "AND U.user_group_id=".$s_user_group."":"";
                if(!is_superadmin($_SESSION['session_usergroupname'])){
                    $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND U.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                    $search_condition .= " AND U.user_group_id >= ".$_SESSION['session_usertype'];
                }
                /*
                 * Return the User list
                 */
                $count = self::ListviewCount($s_key,$s_user_group);
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                        $query = "SELECT B.name as branch_name ,U.id, U.name, U.user_group_id,UG.name as user_group,U.email,                           U.status,U.last_login,
                        IF(U.status = 0, 'disable', 'enable') AS status_text
                        FROM ".USERS." as U
                        LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
                        LEFT JOIN ".BRANCH." AS B ON B.id = U.branch_id
                        WHERE 1
                        ".$search_condition."
                        ORDER BY U.id DESC ". Paging::get_sql_limit();
                        return $app->getrows($query);
                }
    }
    public static function Detail($userid) {
            /*
             * Return the user details
             */
            $app = $GLOBALS['app'];
            $count = $app->find_count(array('conditions' => array('id = ' => $userid)));
            if ($count > 0) {
                $query = "SELECT *
                            FROM ".USERS."
                            WHERE id ='".$userid."'";
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

    public static function deleteValidation($user_id) 
    {
                $app = $GLOBALS['app'];
                
                $data = Branch::Usergroupid();
                $head_user = $data[0]['user_id'];
                $retval = true;
                if($user_id == $head_user)
                {
                    $retval=false;
                    $error_msg[] = SUPER_ADMIN_CANNOT_DELETE;
                }
                   
                return array("canact"=>$retval,"error"=>$error_msg);
    }
    

    public static function Delete($user_id) {
            /*
             * Return the Customers details
             */


            $app = $GLOBALS['app'];

            $status = self::deleteValidation($user_id);

            if($status['canact']){

                $data = Branch::Usergroupid();
                $head_user = $data[0]['user_id'];
                
                /*** Write Delete function below ****/ 
                $app->access_table = USERS;
                $app->delete(array("id"=>$user_id));
                
                
                return array("success"=>true,"error"=>$status['error']);
            
            }else{
            
                return array("success"=>false,"error"=>$status['error']);

            }
            
    }

    public static function getUserName($user_id) 
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = USERS;
        $res = $app->find(array("name"),array("conditions"=>array("id = "=>$user_id)));
        return $res[0][0];
    }
    
    public static function getUserGroup($group_id) 
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = USER_GROUP;
        $res = $app->find(array("name"),array("conditions"=>array("id = "=>$group_id)));
        return $res[0][0];
    }

}


