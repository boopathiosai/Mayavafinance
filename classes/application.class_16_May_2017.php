<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Application class
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  SrinivasanM
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  02-Feb-2010
//**************************************************************************
// Description      :  This is the Application class  for Mayavafinance.
//**************************************************************************
/**
 * Application - The main class
 *
 */
Class Application extends DB
{

/*
 * Class Constants & Properties
*/
   const APPLICATION_SALT = 'mayavafinance';
    public $access_table = '';
    public $session_userid1 = 'session_userid';
    public $session_username1 = 'session_username';
    public $session_useremail1='session_useremail';
    public $session_usertype1 = 'session_usertype';
    public $session_userdepartmentid1 = 'session_userdepartmentid';
    public $session_usergroupname1 = 'session_usergroupname';
    public $session_user_modules1 = 'session_user_modules';
    public $session_user_branch_id = 'session_user_branch_id';
    public $session_user_branch_name = 'session_user_branch_name';
    public $table_primary_id = '';

/*
 * Public Methods
 */
    /*
     * Constructor
     */
    public function __construct() {
        parent::__construct($GLOBALS['db_host'], $GLOBALS['db_user'], $GLOBALS['db_pass']);
        $this->selectdb($GLOBALS['db_name']);

    }

    /*
     * method to validate login
     */
     public function login() {

        $_SESSION[$this->session_name] = false;
        $_SESSION[$this->session_userid1] = '';
        $_SESSION['notice'] = '';
        $return_array = array();

        if(func_num_args() > 1) {
            $args = func_get_args();
            $return_array = $this->authenticate($args[0], $args[1]);

            if(is_array($return_array)) {


                $_SESSION[$this->session_name] = true;
                $_SESSION[$this->session_userid1] = $return_array[0]['id'];
                $_SESSION[$this->session_username1] = $return_array[0]['name'];
                $_SESSION[$this->session_useremail1] = $return_array[0]['email'];
                $_SESSION[$this->session_usertype1] = $return_array[0]['user_group_id'];
                $_SESSION[$this->session_userdepartmentid1] = $return_array[0]['department_id'];
                $_SESSION[$this->session_user_branch_id] = $return_array[0]['branch_id'];
                $return_branch_name = $this->get_branch_name();
                $_SESSION[$this->session_user_branch_name] = $return_branch_name['name'];

                $return_group_name = $this->get_group_name();
                $_SESSION[$this->session_usergroupname1] = $return_group_name['name'];

                $_SESSION[$this->session_user_modules1] = $this->get_user_modules();
                $_SESSION['mcatid'] = '0';

                return true;
            }
            return false;
        }
        return false;
    }

    /*
     * method for logout operation
     */

     public function logout() {

        $_SESSION[$this->session_name] = false;
        $_SESSION[$this->session_userid1] = '';
        $_SESSION[$this->session_username1] = '';
        $_SESSION[$this->session_useremail1] = '';
        $_SESSION[$this->session_usertype1] ='';
        $_SESSION[$this->session_userdepartmentid1] ='';
        session_destroy();
    }

    /*
     * method used to check whether the user logged in or not
     */

     public function is_logged_in() {

        if($_SESSION[$this->session_name] === true) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * method used for SELECT query operations
     */

    public function find($select = array('*'), $params = array('conditions' => array(), 'sort' => '', 'limit' => '', 'group' => '')) {

        $where_condition = array();
        $sort_order = '';
        $limit = '';
        $group_by = '';
        $params['conditions'] = (count($params['conditions']) > 0) ? $params['conditions'] : array('1 = ' => '1');

        foreach ($params['conditions'] as $k => $v ) $where_condition[] = "$k '".$this->escape($v)."'";

        if(!empty($params['sort'])) {
            $sort_order .= 'ORDER BY ' . $params['sort'];
        }

        if(!empty($params['limit'])) {
            $limit .= 'LIMIT ' . $params['limit'];
        }

        if(!empty($params['group'])) {
            $group_by .= 'GROUP BY ' . $params['group'];
        }
        //echo $where_condition;

        //echo '<br> SELECT '.implode(', ', $select).' FROM '.$this->access_table.' WHERE '.implode(' AND ', $where_condition) .' '.$sort_order.' '.$limit.' '.$group_by;
        //exit;
        $result_array = $this->getrows('SELECT '
                        .implode(', ', $select).'
                        FROM
                        '.$this->access_table.'
                        WHERE
                        '.implode(' AND ', $where_condition)
                        .' '.$sort_order
                        .' '.$limit
                        .' '.$group_by
                       );
        return $result_array;
    }

    /*
     * method used for COUNT() query operations
     */

   public function find_count($params = array('conditions' => array())) {

        $where_condition = array();
        $params['conditions'] = (count($params['conditions']) > 0) ? $params['conditions'] : array('1 = ' => '1');
        foreach ($params['conditions'] as $k => $v ) $where_condition[] = "$k '".$this->escape($v)."'";

       $result_array = $this->getrows('SELECT COUNT(id) AS count FROM '
                        .$this->access_table.'
                        WHERE
                        '.implode(' AND ', $where_condition)
                       );
        return $result_array[0]['count'];
    }


    /*
     * method used to INSERT records into database
     */

    public function add($data) {


        foreach ($data as $k => $v ) {
            if(strpos($k, 'password') !== false) {
                $v = $this->encrypt_password(trim($v));
            }
            $data[$k] = "'".$this->escape(trim($v))."'";
        }
        /*echo "INSERT INTO
                        ".$this->access_table."
                        (`".implode('`, `', array_keys($data))."`)
                        VALUES (".implode(", ", $data).")";
                        exit;*/

        $this->execute("INSERT INTO
                        ".$this->access_table."
                        (`".implode('`, `', array_keys($data))."`)
                        VALUES (".implode(", ", $data).")");
    }

    /*
     * method used to UPDATE records in the database
     */

     public function save($data, $where = array(1 => 1)) {

        foreach ($data as $k => $v ) {
            if(strpos($k, 'password') !== false) {
                $v = $this->encrypt_password(trim($v));
            }
            $update_data[] = "$k = '".$this->escape(trim($v))."'";
        }

        foreach ($where as $k => $v ) $where_condition[] = "$k '".$this->escape($v)."'";




        $res = $this->execute("UPDATE  ".$this->access_table." SET
                       ".implode(', ', $update_data)." WHERE
                                     ".implode(' AND ', $where_condition));
               //"UPDATE  ".$this->access_table." SET ".implode(', ', $update_data)." WHERE ".implode(' AND ', $where_condition)
        return $res;
    }

    /*
     * method used to DELETE records from the database
     */

    public function delete($where) {
        //echo $where;

        foreach ($where as $k => $v )  $where_condition[] = "$k IN (".$v.")";

        $this->execute("DELETE FROM ".$this->access_table." WHERE ".implode(' AND ', $where_condition));

    }

    public function __destruct() {
        parent::__destruct();
    }




/*
 * Private Methods
 */

    /*
     * method used to authenticate user login
     */
   private function authenticate($username, $password)
    {
         $user = $this->find(array('*'), $params = array('conditions' => array('email = ' => $username)));
        // check Valid IP based on branch creation

//         if($user[0]['id']!='1'){
//             $sql1="select system_ip from ".BRANCH." where id =".$user[0]['branch_id']." ORDER BY system_ip DESC LIMIT 1;" ;
//             $result_array = $this->getrows($sql1);
//             $current_system_ip=$_SERVER['REMOTE_ADDR'];
//             $user_system_ip = $result_array[0][0];
//         }
//echo $user_system_ip; exit;
         
         
        $current_system_ip_data = array("117.218.85.214", "183.82.251.215");
        //echo array_key_exists($current_system_ip, $current_system_ip_data); exit;
        if(count($user) > 0)
        {
            // echo "here";exit;
            $expected_password = $this->encrypt_password($password);

            if(strcmp($user[0]['password'], $expected_password) != 0)
            {
                $_SESSION['notice'] = 'Invalid Password!';
                return false;
            }
             //else if(($current_system_ip != $user_system_ip)){             
                 //else if((($current_system_ip != $user_system_ip) && ($current_system_ip != '117.218.85.214')) || (($current_system_ip != $user_system_ip) && ($current_system_ip != '183.82.251.215')) ){
                //else if(($current_system_ip != $user_system_ip) && (!array_key_exists($current_system_ip, $current_system_ip_data))){
//            else if(($current_system_ip != $user_system_ip) && ($current_system_ip != '117.218.85.214')){
//                 $_SESSION['notice'] = 'Please Use valid machine to login!';
//                 return false;
//             }
            else
            {
                if($user[0]['status'] != '1')
                {
                    $_SESSION['notice'] = 'Account Blocked!';
                    return false;
                }
                else
                {
                    return $user;
                }
            }
        }
        else
        {
            $_SESSION['notice'] = 'Invalid Username!';
            return false;
        }
    }

    /*
     * method used to encrypt passwords
     */

     public function encrypt_password($password) {


        return base64_encode($password);
    }

    /*
     * method used to decrypt passwords
     */

    public function decrypt_password($password) {
        $decode_password = explode("-", base64_decode($password));
        return $decode_password[0];
    }

    /*
    * method used to get group name
    */
    public function get_group_name() {
          $query = "SELECT name FROM ".USER_GROUP." WHERE id ='".$_SESSION['session_usertype']."'";
          return $this->getrow($query);
    }

    /*
    * method used to get branch name
    */
    public function get_branch_name() {
          $query = "SELECT name FROM ".BRANCH." WHERE id ='".$_SESSION['session_user_branch_id']."'";
          return $this->getrow($query);
    }

    /*
    * method used to get user modules
    */
    public function get_user_modules() {
          return Privillege::getModuleDetail($_SESSION['session_usertype']);
    }

}
?>
