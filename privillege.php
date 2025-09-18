<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Privillege
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Subash S
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  15-Jul-2010
//**************************************************************************
// Description      :  This is the Privillege php file  for Privillege module,used to process the view,add,edit and delete in Privillege module.
//**************************************************************************


session_start();

define ('ROOT_PATH', '');
$page_title = 'Privillege Master';
$module_name = 'privillege';

include ROOT_PATH . 'includes/config.php';

$mod = (isset($_GET['mod'])) ? $_GET['mod'] : $_POST['mod'];

if($app->is_logged_in() !== true || $_SESSION[$app->logged_session_id] <> 1) {
    if($mod == 'Ajax') {
            echo "session_timed_out";exit;
    }
    else{
        header('Location: index.php');exit;
    }
}

/**
 * Get values from js
 **/

$pval = $_POST['pval'];



$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$uval['branch_id'] = (isset($_GET['branch_id'])) ? $_GET['branch_id'] : $_POST['branch_id'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];
$disp = (isset($_GET['disp'])) ? $_GET['disp'] : $_POST['disp'];
$disp = ($disp!='') ? $disp : 'content_div';

$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
if($page_no <= 0) {
    $page_no = 1;
}
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];


//if($type=="add" || $type=="update"){

//Get Modules Category  param
$app->access_table = MODULE_CATEGORY;
$category_rec = $app->find(array("*"));
if(count($category_rec)){
foreach($category_rec as $category){

    $modules[$category["id"]] = $_POST[$category["name"]];
    if(is_array($modules[$category["id"]])){
        $module_select[] = $modules[$category["id"]];
    }
    
    if(!isset($_POST[$category["name"]."_action"]) || empty($_POST[$category["name"]."_action"])){
        $actions[$category["id"]] = array("4");
    }else{
        $actions[$category["id"]] = $_POST[$category["name"]."_action"]; 
    }
    $module_Category[] = $category["name"];

}
}

//p_arr($module_select);

//}


$app->access_table = MODULE_PRIVILLEGE;

/*
 * Assign limit for display record
 */

Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);
switch($type)
{

   case 'formvalidation':
             /*
             * Form validation here . notify error, if exists.
             */
            /*$missing_vals = Validator::validate_data('Privillege');
            if (!empty( $missing_vals )) {
            $missing_data = explode('@@@', $missing_vals);
            $require_data = Privillege::${$missing_data[1]};
            echo $require_data.'~~' . $missing_data[0] . '~~0';
            exit;
            }            
            echo '~~~~1';
            exit;*/
            $missing_labels = Validator::validate_json('Privillege');
            $name = Privillege::getPrivilegeGroup($pval['branch_id']." ".$pval['user_group_id']);
            
            if($uval['id']  == ''){
                
                $isDuplicate = Privillege::privilegeDuplicated($pval['branch_id'], $pval['user_group_id']);
                
                if($isDuplicate){
                    
                    $missing_labels['branch_id'] = $name. " - Branch duplicated for user group. You can modify the exist".$uval['id'];
                    $missing_labels['user_group_id'] = $name. " - User Group duplicated for the Branch. You can modify the exist".$uval['id'];
                }
            
            }


            if(!is_array($module_Category)){
                $missing_labels['module_category'] = ((trim($name)=='') ? "" : $name. " - ") . "No module category found. Please add module category for this";
            }



            if(!is_array($module_select)){
                $missing_labels['module_pages'] = ((trim($name)=='') ? "" : $name. " - ") . "No module pages selected for this, Please select any pages";
            }
            
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
                \"content\" : \"".PRIVILEGE_VALIDATE_ERROR."\", 
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
               }";

            exit;
            
            
            break;

   case 'create':
   case 'edit':
        /*
         * Get the Branch details, if type is edit
         */
        $data = Privillege::getModuleDetail($uval['id'],$uval['branch_id']);
        $data1 = Privillege::Detail($uval['id'],$uval['branch_id']);
        //echo"<PRE>";
        //print_r(explode(",",$data['module_id']));
        //exit;
        //$data = $data[0];
        include ADMIN_LAYOUT_PATH . 'forms/privillege.html';
        break;
        
    case 'save':
    case 'add':
    case 'update':
       
            //  update values in database
                $app->delete($pval); //Delete Existing
              foreach($modules as $cat_id=>$submodules)
            {
                if(count($submodules) > 0){
                foreach($submodules as $module)
                {
                    
                    foreach($actions[$cat_id] as $val)
                    {    
                        $pval['module_category_id']   = $cat_id;
                        $pval['module_id']   = $module;
                        $pval['action_id']   = $val;
                        $pval['status']   = 1;
                        $app->add($pval);
                    }
               }
               }
             
             }
                   
        $notice = ($type == 'add') ? PRIVILEGE_ADDED : PRIVILEGE_UPDATED; //"Privillege Saved successfully! ";
        $_SESSION['success_msg'] = $notice;
         echo "
        { 
            \"page\" : \"".$page."\", 
            \"status\" : \"".$status."\", 
            \"disp\" : \"".$disp."\", 
            \"content\" : \"".$notice."\", 
            \"isAlert\" : \"".false."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
        }";
        exit;
        


    case 'delete':
     
        if($uval['id']!="")
        {
            
            $name = Privillege::getPrivilegeGroup($uval['id']);
            
            $status = Privillege::Delete($uval['id']);
            
            if($status["success"])
            {
                $message[] = $name."( ".$uval['id']." ) - ".PRIVILEGE_DELETED;
            }
            else
            {
                if(is_array($status['error'])){
                
                    foreach($status['error'] as $err){

                        $message[] .= $name."( ".$uval['id']." ) - ".PRIVILEGE_NOT_DELETED." - ".$err;

                    }
                
                }else{
                    
                    $message[] .= $name."( ".$uval['id']." ) - ".PRIVILEGE_NOT_DELETED." - ".$status['error'];
                
                }
            }
        }
        
       if(is_array($_POST["list"]))
            {
                $i=0;
                
                foreach($_POST["list"] as $key=>$val)
                {
                    
                    $name = Privillege::getPrivilegeGroup($val);
                    $status = Privillege::Delete($val);
                    
                    if($status["success"])
                    {
                        $message[] = $name."( ".$val." ) - ".PRIVILEGE_DELETED;
                    }
                    else
                    {
                        
                        if(is_array($status['error'])){
                            
                            foreach($status['error'] as $err){
                                
                                $message[] .= $name."( ".$val." ) - ".PRIVILEGE_NOT_DELETED." - ".$err;
                            
                            }
                        
                        }else{
                        
                            $message[] .= $name."( ".$val." ) - ".PRIVILEGE_NOT_DELETED." - ".$status['error'];
                        
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

    case 'paging':
    case 'back':
    case 'refresh':
    case 'search':
        $data = Privillege::Listview();
        $count = Privillege::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'privillege.html';
        if($type == 'delete') echo '~~'.$notice;
        exit;

    case 'change_status':
        //$app->access_table = USER_GROUP;
        $app->save($change_status_array, array('user_group_id = ' => $uval['id'], 'branch_id = ' => $uval['branch_id']));
        $app->access_table = USERS;
        $app->save($change_status_array, array('user_group_id = ' => $uval['id'], 'branch_id = ' => $uval['branch_id']));
        //print_r($change_status_array);
        $notice = STATUS_SUCCESS;
        echo $change_status_array['status'].'~~'.$notice;
        exit;
   // edit / view Privilege
   case 'updateprivilege':
   case 'viewprivilege':
        $assignedprivilege = explode(",",Rolemaster::getprivilege($uval['id']));
        $data = Rolemaster::getmenus(0);
        include ADMIN_LAYOUT_PATH . 'forms/privilllege.html';
        exit;
   case 'saveprivillege':
    
   
    default:
        //echo "<PRE>";
        //print_r($_SESSION);
        $count = Privillege::ListviewCount();
        $menu = Privillege::getModuleDetail($_SESSION[$app->session_usertype1],$uval['branch_id']);
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        $data = Privillege::Listview();
        include ADMIN_LAYOUT_PATH . 'privillege.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}
?>