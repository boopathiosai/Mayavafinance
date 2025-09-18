<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Settings
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Subash S
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  07-Jun-2011
//**************************************************************************
// Description      :  This is the Settings php file  for Settings module,used to process the view,add,edit and delete in Settings module.
//**************************************************************************
session_start();
define ('ROOT_PATH', '');
$page_title = 'Settings';
$module_name = 'settings';
include ROOT_PATH . 'includes/config.php';

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
/*$pval = $_POST['pval'];
$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];
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


if($page_no <= 0) {
    $page_no = 1;
}
if($rg <= 0) {
    $rg = 1;
}
$page_no = (int)$page_no;

Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);

$app->access_table = SETTINGS;
switch($type) {

   case 'formvalidation':

            /*  * Form validation here . notify error, if exists.*/
            /*$missing_vals = Validator::validate_data('Settings');
            if (!empty( $missing_vals )) {
            $missing_data = explode('@@@', $missing_vals);
            $require_data = Settings::${$missing_data[1]};
            echo $require_data.'~~' . $missing_data[0] . '~~0';
            exit;
            }
            echo '~~~~1';
            exit;
            break;*/
 $missing_labels = Validator::validate_json('Settings');
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
            
            //p_arr($missing_labels);
            
              echo "
               { 
                \"page\" : \"".$page."\", 
                \"success\" : \"".$success."\", 
                \"disp\" : \"".$disp."\", 
                \"content\" : \"".SETTINGS_VALIDATE_ERROR."\", 
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
               }";

            exit;
    break;


  case 'add':
   case 'update':

        if($type == 'add') {
            $pval['editable']=1;
            $app->add($pval);
            $uval['id'] = $app->last_inserted_id();
            //header("Location: settings.php");
            $_SESSION['success_msg'] = SETTINGS_SAVE;
        } else {

            //$pval['editable']=1;
            $status=$app->save($pval, array('id = ' => $uval['id']));
            //header("Location: settings.php?pageno=".$page_no."");
        $_SESSION['success_msg'] = SETTINGS_UPDATE;
        }
        
         echo "
        { 
            \"page\" : \"".$page."\", 
            \"status\" : \"".$status."\", 
            \"disp\" : \"".$disp."\", 
            \"content\" : \"".SETTINGS_UPDATE."\", 
            \"isAlert\" : \"".false."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
        }";
        exit;
        break;

   case 'create':
   case 'edit':
        /*
         * Get the Branch details, if type is edit
         */

        $settings_data = Settings::Detail($uval['id']);
        $settings_data = $settings_data[0];
        include ADMIN_LAYOUT_PATH . 'forms/settings.html';
        break;

   case 'paging':
   case 'refresh':
   case 'back':
        $data = Settings::Listview(); 
        $count = Settings::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'settings.html';
        exit;
        break;


   case 'delete':
        /*
         * Delete from database
         */
    //  $app->delete($uval);
    
        /*  $app->delete($uval);
            $_SESSION['success_msg'] = SETTINGS_DELETE;
            $data  = Settings::Listview();
            $count = Settings::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'settings.html';
        exit;*/
           if($uval['id']!="")
        {
            
            $name = Settings::getSettingsName($uval['id']);
            
            $status = Settings::Delete($uval['id']);
            
            if($status["success"])
            {
                $message[] = $name."( ".$uval['id']." ) - ".SETTINGS_DELETED;
            }
            else
            {
                if(is_array($status['error'])){
                
                    foreach($status['error'] as $err){

                        $message[] .= $name."( ".$uval['id']." ) - ".SETTINGS_NOT_DELETED." - ".$err;

                    }
                
                }else{
                    
                    $message[] .= $name."( ".$uval['id']." ) - ".SETTINGS_NOT_DELETED." - ".$status['error'];
                
                }
            }
        }
        
       if(is_array($_POST["list"]))
            {
                $i=0;
                
                foreach($_POST["list"] as $key=>$val)
                {
                    
                    $name = Settings::getSettingsName($val);
                    $status = Settings::Delete($val);
                    
                    if($status["success"])
                    {
                        $message[] = $name."( ".$val." ) - ".SETTINGS_DELETED;
                    }
                    else
                    {
                        
                        if(is_array($status['error'])){
                            
                            foreach($status['error'] as $err){
                                
                                $message[] .= $name."( ".$val." ) - ".SETTINGS_NOT_DELETED." - ".$err;
                            
                            }
                        
                        }else{
                        
                            $message[] .= $name."( ".$val." ) - ".SETTINGS_NOT_DELETED." - ".$status['error'];
                        
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
        $app->save($change_status_array, array('id = ' => $uval['id']));
        $notice = STATUS_SUCCESS;
        echo $change_status_array['status'].'~~'.$notice;
        exit;
        break;

    default:
        /*
         * Make the pages with header and footer
         */

        include ADMIN_LAYOUT_PATH . 'common/header.html';
        $data = Settings::Listview();
        $count = Settings::ListviewCount();
        
        include ADMIN_LAYOUT_PATH . 'settings.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>