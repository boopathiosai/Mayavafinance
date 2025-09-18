<?php

class Message {

    public $check_required = array('branch_id','subject','message','to');
    public static $check_required_text = 'Required Fields Missing!';
    public $check_required_labels = array('Branch Name','Subject','Message','To');

    public static function ListviewCount() 
        {
                            
                            $args = func_get_args();
                            $app = $GLOBALS['app'];
                            $s_branch = $args[0];
                            $search_condition .= ($s_branch != "")? "AND ( user.branch_id  = '".$s_branch."' )  ":"";
            
            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND users.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
            }
                            
                            $userid = $_SESSION[$app->session_userid1]; 
                            $query = "SELECT message_details.id,
                            message_details.from,
                            message_details.subject,
                            message_details.message,
                            message_details.sent_date,
                            message_details.status,
                            users.id as uid,users.name,
                            message_list.email,message_list.recipient_id,
                            message_list.message_id,GROUP_CONCAT(message_list.recipient_name) AS Username,
                            IF(message_details.status = 0, 'read', 'unread') AS status_text
                            FROM ".MESSAGE_DETAILS." message_details
                            INNER JOIN ".MESSAGE_LIST." message_list ON (message_details.id = message_list.message_id)
                            INNER JOIN ".USERS." users ON (users.id = message_list.recipient_id)
                            WHERE users.id = $userid ".$search_condition."
                            GROUP BY message_details.id 
                            ORDER BY message_details.sent_date DESC
                            ";
                            $data = $app->getrows($query);
                            return $count = count($data);
     }


    
     public static function Listview() {
        $app = $GLOBALS['app'];
        $app->access_table = MESSAGE_DETAILS;
        
    
        $s_branch = $args[0];

        $count = $app->find_count();
        $user_id = $_SESSION[$app->session_userid1];
        
        if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND users.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
        }

        $count = self::ListviewCount($s_branch);

        if ($count > 0) {
            Paging::set_max($count);
            Paging::calc();// calculation for paging
                      $query = "SELECT message_details.id,
                        message_details.from,
                        message_details.subject,
                        message_details.message,
                        message_details.sent_date,
                        message_details.status,
                        users.id as uid,users.name,
                        message_list.email,message_list.recipient_id,
                        message_list.message_id,GROUP_CONCAT(message_list.recipient_name) AS Username,
                        IF(message_details.status = 0, 'read', 'unread') AS status_text
                        FROM ".MESSAGE_DETAILS." message_details
                        INNER JOIN ".MESSAGE_LIST." message_list ON (message_details.id = message_list.message_id)
                        INNER JOIN ".USERS." users ON (users.id = message_list.recipient_id)
                        INNER JOIN ".BRANCH." as B ON  B.id=users.branch_id   
                        WHERE users.id = $user_id  
                        ".$search_condition."
                        GROUP BY message_details.id 
                        ORDER BY message_details.sent_date DESC
                        ". Paging::get_sql_limit();
                        return $app->getrows($query);
        }
    }

    public static function SentviewCount() {
                            
                        $args = func_get_args();
                        $app = $GLOBALS['app'];
                        $user_id = $_SESSION[$app->session_userid1]; 
                        $query = "SELECT message_details.id
                        
                        FROM ".MESSAGE_DETAILS." message_details
                        INNER JOIN ".MESSAGE_LIST." message_list ON (message_details.id = message_list.message_id)
                        INNER JOIN ".USERS." users ON (users.id = message_details.sender)
                        WHERE users.id = '".$user_id."' AND  message_list.recipient_id != '".$user_id."' 
                        GROUP BY message_details.id 
                        ORDER BY message_details.sent_date DESC
                        ";
                        $data = $app->getrows($query);
                        return $count = count($data);

                               
     }

     public static function Sentview() {
        $app = $GLOBALS['app'];
        $app->access_table = MESSAGE_DETAILS;

        $count = self::SentviewCount();
        $user_id = $_SESSION[$app->session_userid1];

        //echo $user_id ;
        if ($count > 0) {
            Paging::set_max($count);
            Paging::calc();// calculation for paging
        
                        $query = "SELECT message_details.id,
                        message_details.from,
                        message_details.subject,
                        message_details.message,
                        message_details.sent_date,
                        message_details.status,
                        users.id as uid,users.name,
                        message_list.email,message_list.recipient_id,message_details.sender,
                        message_list.message_id,GROUP_CONCAT(message_list.recipient_name) AS Username,
                        IF(message_details.status = 0, 'read', 'unread') AS status_text
                        FROM ".MESSAGE_DETAILS." message_details
                        INNER JOIN ".MESSAGE_LIST." message_list ON (message_details.id = message_list.message_id)
                        INNER JOIN ".USERS." users ON (users.id = message_details.sender)
                        WHERE users.id = '".$user_id."'  
                    
                    GROUP BY message_details.id 
                    ORDER BY message_details.sent_date DESC
                    ". Paging::get_sql_limit();
         // echo $query;
            return $app->getrows($query);
        }
    }


    public static function AnnouncementDetail(){
        $app = $GLOBALS['app'];
       $data = User::getSuperAdmin();
       $user_id = $data['user_id'];

                      $query = "SELECT message_details.id,
                        message_details.from,
                        message_details.subject,
                        message_details.message,
                        message_details.sent_date,
                        message_details.status,
                        message_details.sender,
                        users.id as uid,users.name,
                        message_list.email,message_list.recipient_id,
                        message_list.message_id,GROUP_CONCAT(message_list.recipient_name) AS Username,
                        IF(message_details.status = 1, 'Open', 'New') AS status_text
                        FROM ".MESSAGE_DETAILS." message_details
                        INNER JOIN ".MESSAGE_LIST." message_list ON (message_details.id = message_list.message_id)
                        INNER JOIN ".USERS." users ON (users.id = message_list.recipient_id)
                        INNER JOIN ".BRANCH." as B ON  B.id=users.branch_id   
                        WHERE users.id = ".$_SESSION["session_userid"]."   AND message_details.sender = '".$user_id."'
                        
                        GROUP BY message_details.id 
                        ORDER BY message_details.status, message_details.sent_date DESC LIMIT 0,5";
            
        return $app->getrows($query);
        
    }
  
    public static function Delete($msg_id) 
    {
            /*
             * Return the Customers details
             */
            $app = $GLOBALS['app'];
            /*** Write Delete function below ****/ 
            $app->access_table = MESSAGE_DETAILS;
            $app->delete(array("id"=>$msg_id));

            return array("success"=>true,"error"=>$status['error']);
               
    }

     public static function getMessageId($msg_id) 
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = MESSAGE_DETAILS;
        $res = $app->find(array("id"),array("conditions"=>array("id = "=>$msg_id)));
        return $res[0][0];
    }

 }
?>





