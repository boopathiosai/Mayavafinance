<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Scheme
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Subash S
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  09-Jun-2011
//**************************************************************************
// Description      :  This is the Scheme php file  for Scheme module,used to process the view,add,edit and delete in Scheme module.
//**************************************************************************


session_start();

define ('ROOT_PATH', '');
$page_title = 'Schemes Management';
$module_name = 'service_charges';  

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = MASTERS_CAT_ID;

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
        //echo"notaccessible";
        header('Location: index.php');
        exit;
}


/*
 * Get values from js
 */
$pval = $_POST['pval'];  
$ival = $_POST['ival'];
$mval = $_POST['mval'];
$nval = $_POST['nval'];

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

/*
 * Class Instances
 */

$app->access_table = SERVICE_CHARGES;

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
        /*$missing_vals = Validator::validate_data('Scheme');
        if (!empty( $missing_vals )) {
        $missing_data = explode('@@@', $missing_vals);
        $require_data = Scheme::${$missing_data[1]};
        echo $require_data.'~~' . $missing_data[0].'~~0';
        exit;
        }
        echo '~~~~1';
        exit;*/
        
        //p_arr($ival);
        
        $missing_labels = Validator::validate_json('ServiceCharge');
        if(floatval($pval['service_charge_amount'])>1000){            
            $missing_labels["service_charge_amount"] = "Service Charge should not be greater than Rs 1000";            
        }
        if(floatval($pval['starting_amount'])>($pval['ending_amount'])){
            $missing_labels["starting_amount"] = "Starting Amount Should Not greater than ending amount";        
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
            \"content\" : \"".SERVICE_CHARGE_VALIDATE_ERROR."\", 
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
                     $uval['id'] = $app->last_inserted_id();
                     //$dval['service_charge']=$pval['service_charge'];
                     // update the scheme details table accordingly..
                                          $success_msg = SERVICE_CHARGE_SAVED;
                 } else {
                     $pval['modified_datetime'] = date('Y-m-d H:i:s');
                     $pval['modified_by'] = $_SESSION['session_userid'];
                     $app->save($pval, array('id = ' => $uval['id']));
         
                     // update Scheme details table
                     $app->access_table = SCHEME_DETAILS;
                     $val['scheme_id'] = "'".$uval['id']."'";
                     $app->delete($val);
                     //$dval['service_charge']=$pval['service_charge'];
                     // update the scheme details table accordingly..
                    $success_msg = SERVICE_CHARGE_UPDATED;
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
         * Get the Scheme details, if type is edit
         */
        $service_charge_details = ServiceCharge::Detail($uval['id']);
        $service_charge_data = $service_charge_details[0];
        include ADMIN_LAYOUT_PATH . 'forms/service_charges.html';
        break;

   case 'paging':
   case 'back':
   case 'refresh':  
   case 'search':
        $data = ServiceCharge::Listview();
        $count = ServiceCharge::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'service_charges.html';
        exit;
        break;

   case 'delete':
        /*
         * Delete from database
         */
        
        if($uval['id']!="")
        {
            
           // $name = ServiceCharge::getSchemeName($uval['id']);
            
            $status = ServiceCharge::Delete($uval['id']);
            
            if($status["success"])
            {
                $message[] = "( ".$uval['id']." ) - ".SERVICE_CHARGE_DELETED;
            }
            else
            {
                if(is_array($status['error'])){
                
                    foreach($status['error'] as $err){

                        $message[] .= "( ".$uval['id']." ) - ".SERVICE_CHARGE_CANNOT_DELETED." - ".$err;

                    }
                
                }else{
                    
                    $message[] .= "( ".$uval['id']." ) - ".SERVICE_CHARGE_CANNOT_DELETED." - ".$status['error'];
                
                }
            }
        }
        
       if(is_array($_POST["list"]))
            {
                $i=0;
                
                foreach($_POST["list"] as $key=>$val)
                {
                    
                    //$name = Scheme::getSchemeName($val);
                    $status = ServiceCharge::Delete($val);
                    
                    if($status["success"])
                    {
                        $message[] = "( ".$val." ) - ".SERVICE_CHARGE_DELETED;
                    }
                    else
                    {
                        
                        if(is_array($status['error'])){
                            
                            foreach($status['error'] as $err){
                                
                                $message[] .= "( ".$val." ) - ".SERVICE_CHARGE_CANNOT_DELETED." - ".$err;
                            
                            }
                        
                        }else{
                        
                            $message[] .= "( ".$val." ) - ".SERVICE_CHARGE_CANNOT_DELETED." - ".$status['error'];
                        
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
       
        $data = ServiceCharge::Listview();
        
        $count = ServiceCharge::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'service_charges.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>