<?php


class User {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('branch_id','name', 'user_group_id', 'email', 'password');
        public $check_valid_email = array('email');
        public $check_duplication = array('email');
        public $check_min_length = array(array('password'), 6);

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
                    $search_condition .= ($s_key != "")? " AND  name  like '".$s_key."%'":"";
                    $search_condition .= ($s_user_group != "")? "AND user_group_id=".$s_user_group."":"";
                    if(!is_superadmin($_SESSION['session_usergroupname'])){
                        $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND branch_id = '".$_SESSION['session_user_branch_id']."'":"";
                        $search_condition .= " AND user_group_id != 1";
                    }
                    /*
                     * Return the User list Count
                     */
                    $query = "SELECT id
                                FROM ".USERS."
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
                    $search_condition .= " AND user_group_id != 1";
                }
                /*
                 * Return the User list
                 */
                $count = self::ListviewCount($s_key,$s_user_group);
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT U.id, U.name, U.user_group_id, UG.name as user_group, U.email, U.status,U.last_login,
                              IF(U.status = 0, 'disable', 'enable') AS status_text
                              FROM ".USERS." as U
                              LEFT JOIN ".USER_GROUP." as UG ON UG.id = U.user_group_id
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


}


