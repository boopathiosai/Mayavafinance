<?php

session_start();

define ('ROOT_PATH', '');
$page_title = 'Leaves';
$module_name = 'leaves';

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
$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_args = "";
if($search_key != ""){
    $search_args.="search_key=".$search_key."&";
}
if($search_branch_id != ""){
    $search_args.="search_branch_id=".$search_branch_id."&";
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

$app->access_table = LEAVES;

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
          
             $missing_labels = Validator::validate_json('Leave');
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
                \"content\" : \"".LEAVE_VALIDATE_ERROR."\", 
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
               }";

            exit;
            break;

   case 'add':
   case 'update':

        if($type == 'add') 
        {            
            $pval['leave_date'] = date('Y-m-d',strtotime($pval['leave_date']));
            $pval['created_datetime'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];
            $app->add($pval);

            //update the database with the new details
            $uval['id'] = $app->last_inserted_id();
            /*$pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];*/
            $status = $app->save($pval, array('id = ' => $uval['id']));
            $success_msg = LEAVE_SAVED;
        } 
        
        else {
           
             $pval['leave_date'] = date('Y-m-d',strtotime($pval['leave_date']));
            $pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];
            $status = $app->save($pval, array('id = ' => $uval['id']));
            $success_msg = LEAVE_UPDATED;
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
         * Get the leave details, if type is edit
         */
        $leave_data = Leave::Detail($uval['id']);
        $leave_data = $leave_data[0];
        include ADMIN_LAYOUT_PATH . 'forms/leave.html';
        break;

   case 'refresh':  
   case 'search':   
   case 'paging':
   case 'back':
         $data = Leave::Listview($search_key,$search_branch_id);
         $count = Leave::ListviewCount($search_key,$search_branch_id);
         include ADMIN_LAYOUT_PATH . 'leave.html';
         exit;
         break;
    // edit / view Privilege
   case 'updateprivilege':
   case 'viewprivilege':
        $assignedprivilege = explode(",",Leave::getprivillege($uval['id']));
        $data = Leave::getmodules(0);
        include ADMIN_LAYOUT_PATH . 'forms/privillege.html';
        exit;

   case 'delete':
        
        
        
        /*
         * Delete from database
         */
        
        if($uval['id']!="")
        {
            
            $name = Leave::getLeaveName($uval['id']);
            
            $status = Leave::Delete($uval['id']);
            
            if($status["success"])
            {
                $message[] = $name."( ".$uval['id']." ) - ".LEAVE_DELETED;
            }
            else
            {
                if(is_array($status['error'])){
                
                    foreach($status['error'] as $err){

                        $message[] .= $name."( ".$uval['id']." ) - ".LEAVE_NOT_DELETED." - ".$err;

                    }
                
                }else{
                    
                    $message[] .= $name."( ".$uval['id']." ) - ".LEAVE_NOT_DELETED." - ".$status['error'];
                
                }
            }
        }
        
       if(is_array($_POST["list"]))
            {
                $i=0;
                
                foreach($_POST["list"] as $key=>$val)
                {
                    
                    $name = Leave::getLeaveName($val);
                    $status = Leave::Delete($val);
                    
                    if($status["success"])
                    {
//                        $message[] = $name."( ".$val." ) - ".LEAVE_DELETED;
                        $message[] = $name." - ".LEAVE_DELETED;
                    }
                    else
                    {
                        
                        if(is_array($status['error'])){
                            
                            foreach($status['error'] as $err){
                                
//                                $message[] .= $name."( ".$val." ) - ".LEAVE_NOT_DELETED." - ".$err;
                                $message[] .= $name." - ".LEAVE_NOT_DELETED." - ".$err;
                            
                            }
                        
                        }else{
                        
                            $message[] .= $name." - ".LEAVE_NOT_DELETED." - ".$status['error'];
                        
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
        

        break;

   case 'change_status':
        /*
         * Change the stauts in database
         */
//        echo "<pre>"; print_r($change_status_array); print_r($uval); 
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
        $data = Leave::Listview($search_key,$search_branch_id);
        $count = Leave::ListviewCount($search_key,$search_branch_id);
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'leave.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
    break;
}

?>