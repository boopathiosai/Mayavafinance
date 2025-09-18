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
$module_name = 'schemes';

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

$app->access_table = SCHEME;

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
            
            $missing_labels = Validator::validate_json('Scheme');
            

            
            
            if($pval['interest_type']=='0'){
            
                
                if(floatval($pval['service_charge'])>1000){
                
                $missing_labels["service_charge"] = "Service Charge should not be greater than Rs 1000";
                
                }

                $intesrest_chk =  trim($ival[0]);

                if(floatval($intesrest_chk) <= 0){
                
                $missing_labels["interest"] = "Interest Rate - should be greater than zero";
                
                }
                
                if(floatval($intesrest_chk) > 100){
                
                $missing_labels["interest"] = "Interest Rate - should not be greater than 100%";
                
                }
                
                if( $intesrest_chk == ''){

                    $missing_labels["interest"] = "Interest - Required Fields Missing!";

                }
                
            }else{
            
                if(floatval($pval['service_charge'])>100){
                
                $missing_labels["service_charge"] = "Service Charge Percentage should be less than 100%";
                
                }
                
                $f_ival = $ival;
                
                array_shift($f_ival);
            
                $intesrest_chk =  implode("",$f_ival);
                $intesrest_chk =  trim($intesrest_chk);
                
                $above_chk =  trim(implode("",$mval));
                $upto_chk =  trim(implode("",$nval));
                
                if( $above_chk == ''){
            
                    $missing_labels["above"] = "Slab Above value - Required Fields Missing!";
            
                }
                
                if( $upto_chk == ''){
            
                    $missing_labels["upto"] = "Slab Above value - Required Fields Missing!";
            
                }
                
                $tm_nval = array_filter($nval);
                if(count($mval) != count($tm_nval)){
                
                    $missing_labels["above"] = "Slab Above - Not matching with upto count!";
                    $missing_labels["upto"] = "Slab Upto - Not matching with above count!";
                }
                
                $tm_mval = array_unique($mval);
                if(count($mval) != count($tm_mval)){
                
                    $missing_labels["above"] = "Slab Above - Duplicate entry not allowed";
                    
                }
                
                $tm_nval = array_unique($nval);
                if(count($nval) != count($tm_nval)){
                
                    $missing_labels["upto"] = "Slab Upto - Duplicate entry not allowed";
                    
                }                
                
                
                $f_mval = array_map('floatval', $mval);
                $tm_mval = $f_mval;
                sort($tm_mval);
                if($tm_mval !== $f_mval ){
                
                    $missing_labels["above"] = "Slab Above - Improper entry. The value should not be less than previous slab";
                    
                }
                
                
                $f_nval = array_map('floatval', $nval);
                $tm_nval = $f_nval;
                sort($tm_nval);
                if($tm_nval !== $f_nval ){
                
                    $missing_labels["upto"] = "Slab Upto - Improper entry. The value should not be less than previous slab";
                    
                }
                
                $ii = 1; 
                foreach($f_ival as $it){
                    if(floatval($it) > 100){
                        $missing_labels["interest_".$ii] = "Slab Interest Rate - Percentage should be less than 100% on Row ".$ii;    
                    }
                    $ii++;
                }

                $f_ival = array_map('floatval', $f_ival);
                $tm_ival = $f_ival;
                sort($tm_ival);
                if($tm_ival !== $f_ival ){
                
                    $missing_labels["interest"] = "Slab Interest Rate - Improper entry. The value should not be less than previous slab";
                    
                }
                
                if(in_array(0,$f_ival)){
                
                    $missing_labels["interest"] = "Slab Interest Rate - The value should be greater than zero";
                
                }

                $tm_ival = array_unique($f_ival);
                if(count($f_ival) != count($tm_ival)){
                
                    $missing_labels["interest"] = "Slab Interest - Duplicate entry not allowed";
                    
                }
                
                $tm_ival = array_filter($f_ival);
                if(count($mval) != count($tm_ival)){
                
                    $missing_labels["interest"] = "Slab Interest - Not matching with above or upto count!";
                    
                }

                if( $intesrest_chk == ''){

                    $missing_labels["interest"] = "Slab Interest - Required Fields Missing!";

                }


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
                \"content\" : \"".SCHEME_MANAGEMENT_VALIDATE_ERROR."\", 
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
            if($pval['interest_type'] == 0)
            {
                $app->access_table = SCHEME_DETAILS;
                $dval['scheme_id'] = $uval['id'];
                $dval['rate_of_interest'] = $ival[0];
                $dval['above'] = 0;
                $dval['upto'] = 12;
                $dval['created_datetime'] = date('Y-m-d H:i:s');
                $dval['created_by'] = $_SESSION['session_userid'];
                $app->add($dval);
            }
            else
            {
                $app->access_table = SCHEME_DETAILS;
                $dval['scheme_id'] = $uval['id'];
                $total = count($ival);
                $dval['created_datetime'] = date('Y-m-d H:i:s');
                $dval['created_by'] = $_SESSION['session_userid'];
                for($i=1;$i<$total;$i++)
                {
                    $dval['rate_of_interest'] = $ival[$i];
                    $dval['above'] = $mval[$i-1];
                    $dval['upto'] = $nval[$i-1];
                    $app->add($dval);
                }
            }
             $success_msg = SCHEME_SAVED;
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
            if($pval['interest_type'] == 0)
            {
                $dval['scheme_id'] = $uval['id'];
                $dval['rate_of_interest'] = $ival[0];
                $dval['above'] = 0;
                $dval['upto'] = 12;
                $dval['created_datetime'] = date('Y-m-d H:i:s');
                $dval['created_by'] = $_SESSION['session_userid'];
                $app->add($dval);
               
            }
            else
            {
                $dval['scheme_id'] = $uval['id'];
                $total = count($ival);
                $dval['created_datetime'] = date('Y-m-d H:i:s');
                $dval['created_by'] = $_SESSION['session_userid'];
                for($i=1;$i<$total;$i++)
                {
                    $dval['rate_of_interest'] = $ival[$i];
                    $dval['above'] = $mval[$i-1];
                    $dval['upto'] = $nval[$i-1];
                    $app->add($dval);
                }
              

            }
            $success_msg = SCHEME_UPDATED;
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
        $scheme_details = Scheme::Detail($uval['id']);
        $scheme_data = $scheme_details[0];
        include ADMIN_LAYOUT_PATH . 'forms/scheme.html';
        break;

   case 'paging':
   case 'back':
   case 'refresh':  
   case 'search':
        $data = Scheme::Listview();
        $count = Scheme::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'scheme.html';
        exit;
        break;

   case 'delete':
        /*
         * Delete from database
         */
        
        if($uval['id']!="")
        {
            
            $name = Scheme::getSchemeName($uval['id']);
            
            $status = Scheme::Delete($uval['id']);
            
            if($status["success"])
            {
                $message[] = $name."( ".$uval['id']." ) - ".SCHEME_DELETED;
            }
            else
            {
                if(is_array($status['error'])){
                
                    foreach($status['error'] as $err){

                        $message[] .= $name."( ".$uval['id']." ) - ".SCHEME_NOT_DELETED." - ".$err;

                    }
                
                }else{
                    
                    $message[] .= $name."( ".$uval['id']." ) - ".SCHEME_NOT_DELETED." - ".$status['error'];
                
                }
            }
        }
        
       if(is_array($_POST["list"]))
            {
                $i=0;
                
                foreach($_POST["list"] as $key=>$val)
                {
                    
                    $name = Scheme::getSchemeName($val);
                    $status = Scheme::Delete($val);
                    
                    if($status["success"])
                    {
                        $message[] = $name."( ".$val." ) - ".SCHEME_DELETED;
                    }
                    else
                    {
                        
                        if(is_array($status['error'])){
                            
                            foreach($status['error'] as $err){
                                
                                $message[] .= $name."( ".$val." ) - ".SCHEME_NOT_DELETED." - ".$err;
                            
                            }
                        
                        }else{
                        
                            $message[] .= $name."( ".$val." ) - ".SCHEME_NOT_DELETED." - ".$status['error'];
                        
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
        $data = Scheme::Listview();
        $count = Scheme::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'scheme.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>