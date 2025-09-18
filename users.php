<?php


// cmd

session_start();

define ('ROOT_PATH', '');
$page_title = 'Users';
$module_name = 'users';

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = MASTERS_CAT_ID;

$menu = Privillege::getModuleDetail($_SESSION[$app->session_usertype1]);

/*
 * Redirect to index page if not logged in
 */
 if($app->is_logged_in() !== true) {

    if($mod == 'Ajax') {
        echo "session_timed_out";
        exit;
    } else {
        header('Location: index.php');
        exit;
    }
}

/*
 * Redirect to index page if user is not allowed to access the page
 */
if(!is_accessible($menu))
{
        //echo"not accessible";
        header('Location: index.php');
        exit;
}

/*
 * Get values from js
 */
$pval = $_POST['pval']; 
$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];
$disp = (isset($_GET['disp'])) ? $_GET['disp'] : $_POST['disp'];
$disp = ($disp!='') ? $disp : 'content_div';

$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];

/*
 * Get search options
 */
$search_key = (isset($_GET['search_key'])) ? $_GET['search_key'] : $_POST['search_key'];
$search_user_group = (isset($_GET['search_user_group'])) ? $_GET['search_user_group'] : $_POST['search_user_group'];
$search_args = "";
if($search_key != ""){
    $search_args.="search_key=".$search_key."&";
}
if($search_user_group != ""){
    $search_args.="search_user_group=".$search_user_group."&";
}

if($page_no <= 0) {
    $page_no = 1;
}
if($rg <= 0) {
    $rg = 1;
}
$page_no = (int)$page_no;

/*
 * Class Instances
 */

$app->access_table = USERS;

/*
 * Assign limit to display records
 */
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);

switch($type) {

    case 'formvalidation':

            /*
             * Form validation here . notify error, if exists.
             */
            /*$missing_vals = Validator::validate_data('User');
            if (!empty( $missing_vals )) {
            $missing_data = explode('@@@', $missing_vals);
            $require_data = User::${$missing_data[1]};
            echo $require_data.'~~' . $missing_data[0] . '~~0';
            exit;
            }
            echo '~~~~1';
            exit;*/
             $missing_labels = Validator::validate_json('User');
            //Common Code for all
            if (!empty( $missing_labels )) { 
            
                $fields = implode(",#",array_keys($missing_labels));
                $labels = implode(",",$missing_labels);
                $success = false;
                
            }else{
                $fields ='';
                $labels ='';
                $success = true;
            }
            
            $isMsgDisplay = true;
            echo "
               { 
                \"page\" : \"".$page."\", 
                \"success\" : \"".$success."\", 
                \"disp\" : \"".$disp."\", 
                \"content\" : \"".USER_VALIDATE_ERROR."\", 
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
               }";

            exit;
            break;

   case 'add':
   case 'update':

        if($type == 'add') 
        {
            $pval['created_datetime'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];
            $app->add($pval);

            //update the database with the new details
            $uval['id'] = $app->last_inserted_id();
            /*$pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];*/
            $status = $app->save($pval, array('id = ' => $uval['id']));
            $success_msg = USER_SAVED;
        } 
        
        else {
            $pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];
            $status = $app->save($pval, array('id = ' => $uval['id']));
            $success_msg = USER_UPDATED;
        }

            $_SESSION['success_msg'] = $success_msg;

         echo "
        { 
            \"page\" : \"".$page."\", 
            \"status\" : \"".$status."\", 
            \"disp\" : \"".$disp."\", 
            \"content\" : \"".$success_msg."\", 
            \"isAlert\" : \"".false."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
        }";
        exit;
        break;

   case 'create':
   case 'edit':
        /*
         * Get the user details, if type is edit
         */
        $user_data = User::Detail($uval['id']);
        $user_data = $user_data[0];
        include ADMIN_LAYOUT_PATH . 'forms/user.html';
        break;

   case 'refresh':  
   case 'search':   
   case 'paging':
   case 'back':
         $data = User::Listview($search_key,$search_user_group);
         $count = User::ListviewCount($search_key,$search_user_group);
         include ADMIN_LAYOUT_PATH . 'user.html';
         exit;
         break;
    // edit / view Privilege
   case 'updateprivilege':
   case 'viewprivilege':
        $assignedprivilege = explode(",",User::getprivillege($uval['id']));
        $data = User::getmodules(0);
        include ADMIN_LAYOUT_PATH . 'forms/privillege.html';
        exit;

   case 'delete':
        
        
        
        /*
         * Delete from database
         */
        
        if($uval['id']!="")
        {
            
            $name = User::getUserName($uval['id']);
            
            $status = User::Delete($uval['id']);
            
            if($status["success"])
            {
                $message[] = $name."( ".$uval['id']." ) - ".USER_DELETED;
            }
            else
            {
                if(is_array($status['error'])){
                
                    foreach($status['error'] as $err){

                        $message[] .= $name."( ".$uval['id']." ) - ".USER_NOT_DELETED." - ".$err;

                    }
                
                }else{
                    
                    $message[] .= $name."( ".$uval['id']." ) - ".USER_NOT_DELETED." - ".$status['error'];
                
                }
            }
        }
        
       if(is_array($_POST["list"]))
            {
                $i=0;
                
                foreach($_POST["list"] as $key=>$val)
                {
                    
                    $name = User::getUserName($val);
                    $status = User::Delete($val);
                    
                    if($status["success"])
                    {
                        $message[] = $name."( ".$val." ) - ".USER_DELETED;
                    }
                    else
                    {
                        
                        if(is_array($status['error'])){
                            
                            foreach($status['error'] as $err){
                                
                                $message[] .= $name."( ".$val." ) - ".USER_NOT_DELETED." - ".$err;
                            
                            }
                        
                        }else{
                        
                            $message[] .= $name."( ".$val." ) - ".USER_NOT_DELETED." - ".$status['error'];
                        
                        }
                    }
                    
                }

            }
    
         
         $alert_message = (is_array($message)) ? implode("<br> * ",$message) : $message;
         
         echo "
           { 
            \"page\" : \"".$page."\", 
            \"status\" : \"".$status."\", 
            \"disp\" : \"".$disp."\", 
            \"content\" : \" *  ".$alert_message."\", 
            \"isAlert\" : \"".true."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\",\"isAlert\":\"".true."\"} 
            }";
         exit;
        
        /*
         * Delete from database
         */
             //$data=Branch::Usergroupid();
             //$pval['branch_id']=$data[0]['branch_id'];
            /*
             if($uval['id']!="")
                {
                  if(!isset($pval['branch_id']))
                    {
                        $status = User::Delete($uval['id']);
                    }
                }
                
        if(is_array($_POST["list"]))
                    {
            $i=0;
            foreach($_POST["list"] as $key=>$val)
                        {
                $status = User::Delete($val);
                $i++;
            }
                    }
        

        $_SESSION['success_msg'] =USERS_DELETE;
         echo "
           { 
            \"page\" : \"".$page."\", 
            \"status\" : \"".$status."\", 
            \"disp\" : \"".$disp."\", 
            \"content\" : \"".USER_DELETE."\", 
            \"isAlert\" : \"".false."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
            }";
   
        exit;*/
        /*$app->delete($uval);
        $notice = USER_DELETE;
          $data = User::Listview($search_key,$search_user_group);
          $count = User::ListviewCount($search_key,$search_user_group);
            $_SESSION['success_msg'] = $notice;
          include ADMIN_LAYOUT_PATH . 'user.html';
          exit;*/

        break;

   case 'change_status':
        /*
         * Change the stauts in database
         */
         
          $app->save($change_status_array, array('id = ' => $uval['id']));
          $notice = STATUS_SUCCESS;
          echo $change_status_array['status'].'~~'.$notice;
          exit;
        break;

    default:
        /*
         * Make the pages with header and footer
         */
        $menu = Privillege::getModuleDetail($_SESSION[$app->session_usertype1]);
        $data = User::Listview($search_key,$search_user_group);
        $count = User::ListviewCount($search_key,$search_user_group);
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'user.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
    break;
}

?>